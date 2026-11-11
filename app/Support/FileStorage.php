<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * The single place every file attachment in the app goes through — stores on the default
 * filesystem disk (the "bunnycdn" disk, see config/filesystems.php and
 * App\Providers\HelperServiceProvider::boot()) instead of the old uploadImage()/uploadFile()
 * helpers, which moved files directly onto local disk and bypassed Laravel's Storage layer
 * entirely. Any file type is accepted — callers decide what to allow via their own validation
 * rules (`mimes:`/`mimetypes:`), this class just stores whatever it's given.
 */
class FileStorage
{
    public static function store(UploadedFile $file, string $folder): string
    {
        $filename = uniqid() . '_' . time() . '.' . strtolower($file->getClientOriginalExtension());

        return Storage::putFileAs($folder, $file, $filename);
    }

    public static function url(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            Storage::delete($path);
        }
    }

    /**
     * Duplicates an already-stored file under a new unique name in $folder — each resulting
     * attachment row then owns an independent file, so deleting one later never breaks the
     * other (e.g. PurchaseRequestReminder::markFulfilled() copying its attachments onto the
     * PurchaseRequest it creates, rather than having both rows point at the same stored file).
     */
    public static function copy(string $existingPath, string $folder): string
    {
        $filename = uniqid() . '_' . time() . '.' . strtolower(pathinfo($existingPath, PATHINFO_EXTENSION));
        $newPath  = trim($folder, '/') . '/' . $filename;

        Storage::copy($existingPath, $newPath);

        return $newPath;
    }
}
