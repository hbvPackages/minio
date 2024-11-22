<?php

namespace Hbv\Minio\Controllers;

use App\Models\Files;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MinioControl
{
    public static function upload($file, $path='public'): string
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $fileName = sha1(md5(time() . $file->getClientOriginalName()));
            $path = sprintf('%s/%s.jpg', $path, $fileName);
            Storage::disk('minio')->put($path, $content, 'public');
            return sprintf('%s/%s/%s', env('APP_URL'), 'minio/public',  $path);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public function multiUpload($files, $path): array
    {
        $data = [];
        foreach ($files as $file) {
            $content = file_get_contents($file->getRealPath());
            $fileName = sha1(md5(time() . $file->getClientOriginalName()));
            $path = sprintf('%s/%s.jpg', $path, $fileName);
            $data[] = Storage::disk('minio')->put($path, $content, 'public');
        }
        return $data;
    }

    public static function getTemporaryLink($path): string
    {
        $disk = Storage::disk('minio');
        $expiration = Carbon::now()->addMinutes(30);
        return $disk->temporaryUrl($path, $expiration);
    }

}
