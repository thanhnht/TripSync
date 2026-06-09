<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checklist\StoreItemRequest;
use App\Http\Requests\Checklist\UpdateItemRequest;
use App\Models\ChecklistItem;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChecklistController extends Controller
{
    public function index(Trip $trip): View
    {
        $this->authorizeMember($trip);

        $items = $trip->checklistItems()
            ->with('assignee', 'creator')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $pending         = $items->whereNull('assigned_to');
        $assigned        = $items->whereNotNull('assigned_to');
        $groupedAssigned = $assigned->groupBy('category');
        $total           = $items->count();
        $done            = $items->where('is_done', true)->count();
        $members         = $trip->members;

        return view('checklist.index', compact(
            'trip', 'pending', 'groupedAssigned', 'total', 'done', 'members'
        ));
    }

    public function store(StoreItemRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorizeMember($trip);
        $data = $request->validated();

        ChecklistItem::create([
            'trip_id'     => $trip->id,
            'content'     => $data['content'],
            'category'    => $data['category'],
            'assigned_to' => $trip->isOwner(Auth::user()) ? ($data['assigned_to'] ?? null) : null,
            'created_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Đã đề xuất mục.');
    }

    public function update(UpdateItemRequest $request, Trip $trip, ChecklistItem $item): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($item->trip_id === $trip->id, 404);
        abort_unless($item->created_by === Auth::id() || $trip->isOwner(Auth::user()), 403);

        $data = $request->validated();
        $updateData = [
            'content'  => $data['content'],
            'category' => $data['category'],
        ];

        if ($trip->isOwner(Auth::user())) {
            $updateData['assigned_to'] = $data['assigned_to'] ?? null;
        }

        $item->update($updateData);
        return back()->with('success', 'Đã cập nhật.');
    }

    public function assign(Request $request, Trip $trip, ChecklistItem $item): RedirectResponse
    {
        abort_unless($trip->isOwner(Auth::user()), 403, 'Chỉ trưởng nhóm mới có quyền phân công.');
        abort_unless($item->trip_id === $trip->id, 404);

        $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $item->update(['assigned_to' => $request->assigned_to ?: null]);

        return back()->with('success', 'Đã phân công.');
    }

    public function destroy(Trip $trip, ChecklistItem $item): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($item->trip_id === $trip->id, 404);
        abort_unless($this->canManageItem($trip, $item), 403);

        $item->delete();
        return back()->with('success', 'Đã xoá mục.');
    }

    public function toggle(Trip $trip, ChecklistItem $item): JsonResponse
    {
        $this->authorizeMember($trip);
        abort_unless($item->trip_id === $trip->id, 404);
        abort_unless($this->canToggleItem($trip, $item), 403);

        $item->update(['is_done' => !$item->is_done]);

        $total = $trip->checklistItems()->count();
        $done  = $trip->checklistItems()->where('is_done', true)->count();

        return response()->json([
            'is_done' => $item->is_done,
            'done'    => $done,
            'total'   => $total,
            'percent' => $total > 0 ? round($done / $total * 100) : 0,
        ]);
    }

    private function canToggleItem(Trip $trip, ChecklistItem $item): bool
    {
        if ($trip->isOwner(Auth::user())) return true;
        // Assigned → only assignee can tick
        if ($item->assigned_to) return $item->assigned_to === Auth::id();
        // Not yet assigned → creator can tick
        return $item->created_by === Auth::id();
    }

    private function canManageItem(Trip $trip, ChecklistItem $item): bool
    {
        return $item->created_by === Auth::id()
            || $item->assigned_to === Auth::id()
            || $trip->isOwner(Auth::user());
    }

    private function authorizeMember(Trip $trip): void
    {
        abort_unless($trip->isMember(Auth::user()), 403, 'Bạn không có quyền truy cập.');
    }
}
