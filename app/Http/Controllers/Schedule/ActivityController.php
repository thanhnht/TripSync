<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedule\StoreActivityRequest;
use App\Http\Requests\Schedule\UpdateActivityRequest;
use App\Models\ActivityComment;
use App\Models\ActivityVote;
use App\Models\Trip;
use App\Models\TripActivity;
use App\Models\TripDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function store(StoreActivityRequest $request, Trip $trip, TripDay $day): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($day->trip_id === $trip->id, 404);

        $activity = $day->activities()->create([
            ...$request->validated(),
            'created_by' => Auth::id(),
            'status'     => 'suggested',
            'sort_order' => $day->activities()->max('sort_order') + 1,
        ]);

        return back()->with('success', "Đã thêm hoạt động \"{$activity->title}\"!");
    }

    public function update(UpdateActivityRequest $request, Trip $trip, TripActivity $activity): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($activity->day->trip_id === $trip->id, 404);

        if ($activity->created_by !== Auth::id() && !$trip->isOwner(Auth::user())) {
            abort(403, 'Bạn không có quyền chỉnh sửa hoạt động này.');
        }

        $activity->update($request->validated());

        return back()->with('success', 'Đã cập nhật hoạt động.');
    }

    public function destroy(Trip $trip, TripActivity $activity): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($activity->day->trip_id === $trip->id, 404);

        if ($activity->created_by !== Auth::id() && !$trip->isOwner(Auth::user())) {
            abort(403, 'Bạn không có quyền xoá hoạt động này.');
        }

        $title = $activity->title;
        $activity->delete();

        return back()->with('success', "Đã xoá hoạt động \"{$title}\".");
    }

    public function approve(Trip $trip, TripActivity $activity): RedirectResponse
    {
        $this->authorizeOwner($trip);
        abort_unless($activity->day->trip_id === $trip->id, 404);

        $activity->update(['status' => 'approved']);

        return back()->with('success', "Đã duyệt hoạt động \"{$activity->title}\".");
    }

    public function reject(Trip $trip, TripActivity $activity): RedirectResponse
    {
        $this->authorizeOwner($trip);
        abort_unless($activity->day->trip_id === $trip->id, 404);

        $activity->update(['status' => 'rejected']);

        return back()->with('success', "Đã từ chối hoạt động \"{$activity->title}\".");
    }

    public function vote(Request $request, Trip $trip, TripActivity $activity): JsonResponse
    {
        $this->authorizeMember($trip);
        abort_unless($activity->day->trip_id === $trip->id, 404);

        $request->validate(['vote' => 'required|in:up,down']);

        $existing = ActivityVote::where('activity_id', $activity->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            if ($existing->vote === $request->vote) {
                $existing->delete();
                $userVote = null;
            } else {
                $existing->update(['vote' => $request->vote]);
                $userVote = $request->vote;
            }
        } else {
            ActivityVote::create([
                'activity_id' => $activity->id,
                'user_id'     => Auth::id(),
                'vote'        => $request->vote,
            ]);
            $userVote = $request->vote;
        }

        $activity->load('votes');

        // Auto-approve when every member has voted "up"
        $autoApproved = false;
        if ($activity->status === 'suggested') {
            $memberCount = $trip->members()->count();
            if ($memberCount > 0 && $activity->up_votes_count >= $memberCount) {
                $activity->update(['status' => 'approved']);
                $autoApproved = true;
            }
        }

        return response()->json([
            'up_count'     => $activity->up_votes_count,
            'down_count'   => $activity->down_votes_count,
            'user_vote'    => $userVote,
            'auto_approved'=> $autoApproved,
            'status'       => $activity->status,
        ]);
    }

    public function comment(Request $request, Trip $trip, TripActivity $activity): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($activity->day->trip_id === $trip->id, 404);

        $request->validate(['content' => 'required|string|max:1000']);

        ActivityComment::create([
            'activity_id' => $activity->id,
            'user_id'     => Auth::id(),
            'content'     => $request->content,
        ]);

        return back()->with('success', 'Đã thêm bình luận.');
    }

    public function destroyComment(Trip $trip, ActivityComment $comment): RedirectResponse
    {
        $this->authorizeMember($trip);

        if ($comment->user_id !== Auth::id() && !$trip->isOwner(Auth::user())) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Đã xoá bình luận.');
    }

    private function authorizeMember(Trip $trip): void
    {
        abort_unless($trip->isMember(Auth::user()), 403);
    }

    private function authorizeOwner(Trip $trip): void
    {
        abort_unless($trip->isOwner(Auth::user()), 403, 'Chỉ trưởng nhóm mới có quyền duyệt hoạt động.');
    }
}
