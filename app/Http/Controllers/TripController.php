<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripActivity;
use App\Models\TripMember;
use App\Http\Requests\Trip\JoinTripRequest;
use App\Http\Requests\Trip\StoreTripRequest;
use App\Http\Requests\Trip\UpdateTripRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TripController extends Controller
{
    // ── Dashboard / Index ──────────────────────────────────────

    public function dashboard(): View
    {
        $user = Auth::user();

        $myTrips = Trip::where('owner_id', $user->id)
            ->withCount('members')
            ->orderByDesc('created_at')
            ->get();

        $joinedTrips = $user->trips()
            ->where('owner_id', '!=', $user->id)
            ->withCount('members')
            ->orderByDesc('trip_members.created_at')
            ->get();

        $stats = [
            'total'     => $myTrips->count() + $joinedTrips->count(),
            'planning'  => $myTrips->where('status', 'planning')->count()
                         + $joinedTrips->where('status', 'planning')->count(),
            'ongoing'   => $myTrips->where('status', 'ongoing')->count()
                         + $joinedTrips->where('status', 'ongoing')->count(),
            'completed' => $myTrips->where('status', 'completed')->count()
                         + $joinedTrips->where('status', 'completed')->count(),
        ];

        return view('trips.dashboard', compact('myTrips', 'joinedTrips', 'stats'));
    }

    // ── CRUD ───────────────────────────────────────────────────

    public function create(): View
    {
        return view('trips.create');
    }

    public function store(StoreTripRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['owner_id'] = Auth::id();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('trips/covers', 'public');
        }

        $trip = Trip::create($data);

        // Add owner as member
        TripMember::create([
            'trip_id'   => $trip->id,
            'user_id'   => Auth::id(),
            'role'      => 'owner',
            'joined_at' => now(),
        ]);

        $trip->generateInviteCode();

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Chuyến đi "' . $trip->name . '" đã được tạo thành công! 🗺️');
    }

    public function show(Trip $trip): View
    {
        $this->authorizeTrip($trip);

        $trip->load(['owner', 'members' => fn($q) => $q->withPivot('role', 'joined_at')]);

        $dayIds = $trip->days()->pluck('id');

        $checklistTotal   = $trip->checklistItems()->count();
        $checklistDone    = $trip->checklistItems()->where('is_done', true)->count();

        $previewPhotos    = $trip->photos()->limit(6)->get();
        $photoCount       = $trip->photos()->count();

        $totalExpense     = $trip->expenses()->sum('amount');
        $expenseCount     = $trip->expenses()->count();

        $activityCount    = TripActivity::whereIn('trip_day_id', $dayIds)->count();
        $activityApproved = TripActivity::whereIn('trip_day_id', $dayIds)->where('status', 'approved')->count();

        return view('trips.show', compact(
            'trip',
            'checklistTotal', 'checklistDone',
            'previewPhotos', 'photoCount',
            'totalExpense', 'expenseCount',
            'activityCount', 'activityApproved',
        ));
    }

    public function edit(Trip $trip): View
    {
        $this->authorizeOwner($trip);

        return view('trips.edit', compact('trip'));
    }

    public function update(UpdateTripRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($trip->cover_image) {
                Storage::disk('public')->delete($trip->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')
                ->store('trips/covers', 'public');
        }

        $trip->update($data);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Cập nhật chuyến đi thành công!');
    }

    public function destroy(Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        if ($trip->cover_image) {
            Storage::disk('public')->delete($trip->cover_image);
        }

        $trip->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Đã xoá chuyến đi.');
    }

    // ── Invite / Join ──────────────────────────────────────────

    public function showJoin(): View
    {
        return view('trips.join');
    }

    public function join(JoinTripRequest $request): RedirectResponse
    {

        $trip = Trip::where('invite_code', strtoupper($request->invite_code))->first();

        if (!$trip) {
            return back()->withErrors(['invite_code' => 'Mã mời không hợp lệ.']);
        }

        $user = Auth::user();

        if ($trip->isMember($user)) {
            return redirect()->route('trips.show', $trip)
                ->with('info', 'Bạn đã là thành viên của chuyến đi này.');
        }

        TripMember::create([
            'trip_id'   => $trip->id,
            'user_id'   => $user->id,
            'role'      => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Bạn đã tham gia chuyến đi "' . $trip->name . '"! 🎉');
    }

    public function regenerateInvite(Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        $code = $trip->generateInviteCode();

        return back()->with('success', 'Đã tạo mã mời mới: ' . $code);
    }

    // ── Member Management ──────────────────────────────────────

    public function removeMember(Trip $trip, int $userId): RedirectResponse
    {
        $this->authorizeOwner($trip);

        if ($userId === $trip->owner_id) {
            return back()->withErrors(['error' => 'Không thể xoá trưởng nhóm.']);
        }

        TripMember::where('trip_id', $trip->id)
            ->where('user_id', $userId)
            ->delete();

        return back()->with('success', 'Đã xoá thành viên.');
    }

    public function leaveTrip(Trip $trip): RedirectResponse
    {
        $user = Auth::user();

        if ($trip->isOwner($user)) {
            return back()->withErrors(['error' => 'Trưởng nhóm không thể rời chuyến đi. Hãy chuyển quyền hoặc xoá chuyến đi.']);
        }

        TripMember::where('trip_id', $trip->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Bạn đã rời chuyến đi.');
    }

    public function updateStatus(Request $request, Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        $request->validate([
            'status' => 'required|in:planning,ongoing,completed,cancelled',
        ]);

        $trip->update(['status' => $request->status]);

        return back()->with('success', 'Đã cập nhật trạng thái: ' . $trip->fresh()->status_label);
    }

    // ── Private helpers ────────────────────────────────────────

    private function authorizeTrip(Trip $trip): void
    {
        if (!$trip->isMember(Auth::user())) {
            abort(403, 'Bạn không có quyền truy cập chuyến đi này.');
        }
    }

    private function authorizeOwner(Trip $trip): void
    {
        if (!$trip->isOwner(Auth::user())) {
            abort(403, 'Chỉ trưởng nhóm mới có quyền thực hiện hành động này.');
        }
    }
}
