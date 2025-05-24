<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesImages
{
    /**
     * Upload an image to public storage
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string|null $oldPath
     * @return string|null
     */
    public function uploadImage($file, string $directory, string $oldPath = null)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Delete old image if exists
        // if ($oldPath) {
        //     $this->deleteImage($oldPath);
        // }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(10).'_'.time().'.'.$extension;

        // Store file in public disk
        $path = $file->storeAs(
            ""

            // $directory,
            // $filename,
            // 'public'
        );

        return $path;
    }

    /**
     * Delete an image from public storage
     *
     * @param string|null $path
     * @return void
     */
    public function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Get full public URL for a stored image
     *
     * @param string|null $path
     * @return string|null
     */
    public function getImageUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}