<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HandleAvatars
{
    /**
     * Process Base64 image, resize/compress to < 100KB, and store.
     */
    protected function processAndStoreAvatar($base64Data, $userId, $isPending = false)
    {
        try {
            // Remove data:image/...;base64, prefix
            $data = explode(',', $base64Data);
            $decoded = base64_decode(end($data));
            
            $img = imagecreatefromstring($decoded);
            if (!$img) return null;

            $filename = ($isPending ? "pending_" : "") . "{$userId}.jpg";
            $tempPath = storage_path("app/temp_{$filename}");

            // Compression logic loop
            $quality = 90;
            do {
                imagejpeg($img, $tempPath, $quality);
                $size = filesize($tempPath);
                $quality -= 5;
            } while ($size > 100 * 1024 && $quality > 10);

            imagedestroy($img);

            // Store in public disk directly to avoid symlink issues
            $finalPath = "storage/avatars/{$filename}";
            $fullPath = public_path($finalPath);
            
            // Ensure directory exists
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }

            file_put_contents($fullPath, file_get_contents($tempPath));
            
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return "/" . $finalPath;
        } catch (\Exception $e) {
            \Log::error("Avatar processing failed: " . $e->getMessage());
            return null;
        }
    }
}
