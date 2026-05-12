<?php

namespace App\Http\Controllers\Checklist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checklist\StoreItemRequest;
use App\Http\Requests\Checklist\UpdateItemRequest;
use App\Models\ChecklistItem;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

        $grouped  = $items->groupBy('category');
        $total    = $items->count();
        $done     = $items->where('is_done', true)->count();
        $members  = $trip->members;

        return view('checklist.index', compact('trip', 'grouped', 'total', 'done', 'members'));
    }

    public function store(StoreItemRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorizeMember($trip);
        $data = $request->validated();

        ChecklistItem::create([
            'trip_id'     => $trip->id,
            'content'     => $data['content'],
            'category'    => $data['category'],
            'assigned_to' => $data['assigned_to'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Đã thêm mục vào danh sách.');
    }

    public function update(UpdateItemRequest $request, Trip $trip, ChecklistItem $item): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($item->trip_id === $trip->id, 404);

        $data = $request->validated();
        $item->update([
            'content'     => $data['content'],
            'category'    => $data['category'],
            'assigned_to' => $data['assigned_to'] ?? null,
        ]);

        return back()->with('success', 'Đã cập nhật.');
    }

    public function destroy(Trip $trip, ChecklistItem $item): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($item->trip_id === $trip->id, 404);
        abort_unless(
            $item->created_by === Auth::id() || $trip->isOwner(Auth::user()),
            403
        );

        $item->delete();
        return back()->with('success', 'Đã xoá mục.');
    }

    public function toggle(Trip $trip, ChecklistItem $item): JsonResponse
    {
        $this->authorizeMember($trip);
        abort_unless($item->trip_id === $trip->id, 404);

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

    private function authorizeMember(Trip $trip): void
    {
        abort_unless($trip->isMember(Auth::user()), 403, 'Bạn không có quyền truy cập.');
    }
}
