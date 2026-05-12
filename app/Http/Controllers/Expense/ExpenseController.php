<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Models\ExpenseSplit;
use App\Models\Trip;
use App\Models\TripExpense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Trip $trip): View
    {
        $this->authorizeMember($trip);

        $expenses = $trip->expenses()
            ->with(['payer', 'splits.user', 'activity'])
            ->latest()
            ->get();

        $members  = $trip->members;
        $balance  = $this->calculateBalance($trip, $members, $expenses);
        $settlements = $this->minimumSettlements($balance);
        $totalExpense = $expenses->sum('amount');

        return view('expense.index', compact(
            'trip', 'expenses', 'members', 'balance', 'settlements', 'totalExpense'
        ));
    }

    public function store(StoreExpenseRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorizeMember($trip);
        $data = $request->validated();

        DB::transaction(function () use ($data, $trip) {
            $expense = TripExpense::create([
                'trip_id'      => $trip->id,
                'title'        => $data['title'],
                'amount'       => $data['amount'],
                'paid_by'      => $data['paid_by'],
                'split_method' => $data['split_method'],
                'note'         => $data['note'] ?? null,
                'created_by'   => Auth::id(),
            ]);

            $this->saveSplits($expense, $data, $trip);
        });

        return back()->with('success', 'Đã thêm khoản chi tiêu.');
    }

    public function update(UpdateExpenseRequest $request, Trip $trip, TripExpense $expense): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($expense->trip_id === $trip->id, 404);

        $data = $request->validated();

        DB::transaction(function () use ($expense, $data, $trip) {
            $expense->update([
                'title'        => $data['title'],
                'amount'       => $data['amount'],
                'paid_by'      => $data['paid_by'],
                'split_method' => $data['split_method'],
                'note'         => $data['note'] ?? null,
            ]);

            $expense->splits()->delete();
            $this->saveSplits($expense, $data, $trip);
        });

        return back()->with('success', 'Đã cập nhật khoản chi tiêu.');
    }

    public function destroy(Trip $trip, TripExpense $expense): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($expense->trip_id === $trip->id, 404);
        abort_unless(
            $expense->created_by === Auth::id() || $trip->isOwner(Auth::user()),
            403
        );

        $expense->delete();

        return back()->with('success', 'Đã xoá khoản chi tiêu.');
    }

    public function importFromSchedule(Trip $trip): RedirectResponse
    {
        $this->authorizeMember($trip);

        $activities = $trip->days()
            ->with('activities')
            ->get()
            ->flatMap->activities
            ->where('status', 'approved')
            ->where('estimated_cost', '>', 0)
            ->filter(fn($a) => !TripExpense::where('trip_activity_id', $a->id)->exists());

        if ($activities->isEmpty()) {
            return back()->with('info', 'Không có hoạt động nào mới để nhập.');
        }

        $members    = $trip->members;
        $memberIds  = $members->pluck('id');
        $memberCount = $members->count();

        DB::transaction(function () use ($activities, $trip, $memberIds, $memberCount) {
            foreach ($activities as $activity) {
                $expense = TripExpense::create([
                    'trip_id'          => $trip->id,
                    'trip_activity_id' => $activity->id,
                    'title'            => $activity->title,
                    'amount'           => $activity->estimated_cost,
                    'paid_by'          => $trip->owner_id,
                    'split_method'     => 'equal',
                    'created_by'       => Auth::id(),
                ]);

                if ($memberCount > 0) {
                    $share = (int) floor($activity->estimated_cost / $memberCount);
                    $remainder = $activity->estimated_cost - ($share * $memberCount);

                    foreach ($memberIds as $i => $userId) {
                        ExpenseSplit::create([
                            'expense_id' => $expense->id,
                            'user_id'    => $userId,
                            'amount'     => $share + ($i === 0 ? $remainder : 0),
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Đã nhập ' . $activities->count() . ' khoản chi từ lịch trình.');
    }

    private function saveSplits(TripExpense $expense, array $data, Trip $trip): void
    {
        $members = $trip->members;

        if ($data['split_method'] === 'equal') {
            $count    = $members->count();
            $share    = $count > 0 ? (int) floor($data['amount'] / $count) : 0;
            $remainder = $data['amount'] - ($share * $count);

            foreach ($members as $i => $member) {
                ExpenseSplit::create([
                    'expense_id' => $expense->id,
                    'user_id'    => $member->id,
                    'amount'     => $share + ($i === 0 ? $remainder : 0),
                ]);
            }
        } else {
            foreach ($data['splits'] as $split) {
                ExpenseSplit::create([
                    'expense_id' => $expense->id,
                    'user_id'    => $split['user_id'],
                    'amount'     => $split['amount'],
                ]);
            }
        }
    }

    private function calculateBalance(Trip $trip, $members, $expenses): array
    {
        $balance = [];
        foreach ($members as $member) {
            $balance[$member->id] = 0;
        }

        foreach ($expenses as $expense) {
            if (isset($balance[$expense->paid_by])) {
                $balance[$expense->paid_by] += $expense->amount;
            }
            foreach ($expense->splits as $split) {
                if (isset($balance[$split->user_id])) {
                    $balance[$split->user_id] -= $split->amount;
                }
            }
        }

        return $balance;
    }

    private function minimumSettlements(array $balance): array
    {
        $creditors = [];
        $debtors   = [];

        foreach ($balance as $userId => $amount) {
            if ($amount > 0) $creditors[] = ['id' => $userId, 'amount' => $amount];
            if ($amount < 0) $debtors[]   = ['id' => $userId, 'amount' => -$amount];
        }

        usort($creditors, fn($a, $b) => $b['amount'] <=> $a['amount']);
        usort($debtors,   fn($a, $b) => $b['amount'] <=> $a['amount']);

        $settlements = [];
        $i = 0;
        $j = 0;

        while ($i < count($creditors) && $j < count($debtors)) {
            $transferable = min($creditors[$i]['amount'], $debtors[$j]['amount']);

            $settlements[] = [
                'from'   => $debtors[$j]['id'],
                'to'     => $creditors[$i]['id'],
                'amount' => $transferable,
            ];

            $creditors[$i]['amount'] -= $transferable;
            $debtors[$j]['amount']   -= $transferable;

            if ($creditors[$i]['amount'] === 0) $i++;
            if ($debtors[$j]['amount'] === 0)   $j++;
        }

        return $settlements;
    }

    private function authorizeMember(Trip $trip): void
    {
        abort_unless($trip->isMember(Auth::user()), 403, 'Bạn không có quyền truy cập.');
    }
}
