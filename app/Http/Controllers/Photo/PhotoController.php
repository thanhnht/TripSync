<?php

namespace App\Http\Controllers\Photo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Photo\StorePhotoRequest;
use App\Models\Trip;
use App\Models\TripPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class PhotoController extends Controller
{
    // Disk used for storing photos. Change to 's3' when ready to migrate.
    private const DISK = 'public';

    public function index(Trip $trip): View
    {
        $this->authorizeMember($trip);

        $photos = $trip->photos()->with('uploader')->latest()->get();

        return view('photo.index', compact('trip', 'photos'));
    }

    public function store(StorePhotoRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorizeMember($trip);

        $description = $request->input('description');

        foreach ($request->file('photos') as $file) {
            $path = $file->store("trips/{$trip->id}/photos", self::DISK);

            TripPhoto::create([
                'trip_id'       => $trip->id,
                'uploaded_by'   => Auth::id(),
                'path'          => $path,
                'disk'          => self::DISK,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'description'   => $description,
            ]);
        }

        $count = count($request->file('photos'));
        return back()->with('success', "Đã tải lên {$count} ảnh.");
    }

    public function destroy(Trip $trip, TripPhoto $photo): RedirectResponse
    {
        $this->authorizeMember($trip);
        abort_unless($photo->trip_id === $trip->id, 404);
        abort_unless(
            $photo->uploaded_by === Auth::id() || $trip->isOwner(Auth::user()),
            403, 'Bạn không có quyền xoá ảnh này.'
        );

        $photo->deleteFile();
        $photo->delete();

        return back()->with('success', 'Đã xoá ảnh.');
    }

    public function download(Trip $trip, TripPhoto $photo): StreamedResponse
    {
        $this->authorizeMember($trip);
        abort_unless($photo->trip_id === $trip->id, 404);

        return Storage::disk($photo->disk)->download($photo->path, $photo->original_name);
    }

    public function downloadBulk(Request $request, Trip $trip): BinaryFileResponse
    {
        $this->authorizeMember($trip);

        $ids = $request->validate([
            'ids'   => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer'],
        ])['ids'];

        $photos = TripPhoto::whereIn('id', $ids)
            ->where('trip_id', $trip->id)
            ->get();

        abort_if($photos->isEmpty(), 404);

        $zipPath = tempnam(sys_get_temp_dir(), 'trync_') . '.zip';
        $zip     = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $names = [];
        foreach ($photos as $photo) {
            $content = Storage::disk($photo->disk)->get($photo->path);
            if ($content === null) continue;

            // Avoid duplicate filenames inside the ZIP
            $name = $photo->original_name;
            if (isset($names[$name])) {
                $names[$name]++;
                $ext   = pathinfo($name, PATHINFO_EXTENSION);
                $base  = pathinfo($name, PATHINFO_FILENAME);
                $name  = "{$base}_{$names[$photo->original_name]}.{$ext}";
            } else {
                $names[$name] = 0;
            }

            $zip->addFromString($name, $content);
        }

        $zip->close();

        $filename = Str::slug($trip->name) . '_photos_' . now()->format('Ymd') . '.zip';

        return response()->download($zipPath, $filename)->deleteFileAfterSend(true);
    }

    private function authorizeMember(Trip $trip): void
    {
        abort_unless($trip->isMember(Auth::user()), 403, 'Bạn không có quyền truy cập.');
    }
}
