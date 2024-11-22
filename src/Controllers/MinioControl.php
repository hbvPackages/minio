<?php

namespace Hbv\Minio\Controllers;

use App\Models\Files;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MinioControl
{
    public static function upload($info, $file, $path='public'): string
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $fileName = sha1(md5(time() . $file->getClientOriginalName()));
            $path = sprintf('%s/%s.jpg', $path, $fileName);
            Storage::disk('minio')->put($path, $content, 'public');
            $fileInfo = [
                'file_name' => $fileName,
                'file_path' => $path,
                'format' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'entity_id' => $info['entity_id'],
                'entity_type' => $info['entity_type'],
                'section' => $info['section'],
                'alt' => $info['alt'] ?? null,
                'description' => $info['description'] ?? null,
            ];
            Files::create($fileInfo);
            return sprintf('%s/%s/%s', env('APP_URL'), 'minio/public',  $path);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
    public static function getTemporaryLink($path): string
    {
        $disk = Storage::disk('minio');
        $expiration = Carbon::now()->addMinutes(30);
        return $disk->temporaryUrl($path, $expiration);
    }

}
