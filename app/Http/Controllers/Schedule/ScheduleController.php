<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripDay;
use App\Models\TripActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Trip $trip): View
    {
        $this->authorizeMember($trip);
        $this->ensureDaysExist($trip);

        $days = $trip->days()
            ->with([
                'activities.creator',
                'activities.votes',
                'activities.comments.user',
            ])
            ->orderBy('day_number')
            ->get();

        $totalCost       = $days->sum('total_cost');
        $totalActivities = $days->flatMap->activities->count();
        $approvedCount   = $days->flatMap->activities->where('status', 'approved')->count();

        return view('schedule.index', compact(
            'trip', 'days', 'totalCost', 'totalActivities', 'approvedCount'
        ));
    }

    public function updateDay(Request $request, Trip $trip, TripDay $day): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($day->trip_id === $trip->id, 404);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'note'  => 'nullable|string|max:1000',
        ]);

        $day->update($request->only('title', 'note'));

        return back()->with('success', 'Đã cập nhật thông tin ngày.');
    }

    public function reorder(Request $request, Trip $trip): JsonResponse
    {
        $this->authorizeMember($trip);

        $request->validate([
            'activities'         => 'required|array',
            'activities.*.id'    => 'required|integer|exists:trip_activities,id',
            'activities.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->activities as $item) {
            TripActivity::where('id', $item['id'])
                ->whereHas('day', fn($q) => $q->where('trip_id', $trip->id))
                ->update(['sort_order' => $item['order']]);
        }

        return response()->json(['message' => 'OK']);
    }

    private function authorizeMember(Trip $trip): void
    {
        abort_unless($trip->isMember(Auth::user()), 403, 'Bạn không có quyền truy cập lịch trình này.');
    }

    private function ensureDaysExist(Trip $trip): void
    {
        if ($trip->days()->exists()) return;

        $date      = $trip->start_date->copy();
        $dayNumber = 1;

        while ($date->lte($trip->end_date)) {
            TripDay::create([
                'trip_id'    => $trip->id,
                'date'       => $date->toDateString(),
                'day_number' => $dayNumber,
            ]);
            $date->addDay();
            $dayNumber++;
        }
    }
}
