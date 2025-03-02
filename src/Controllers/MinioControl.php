<?php

namespace Hbv\Minio\Controllers;

use App\Models\Files;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MinioControl
{
    /**
     * @param $file
     * @param $path
     * @return string
     */
    public static function upload($file, $path = 'public'): string
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $fileName = sha1(md5(time() . $file->getClientOriginalName()));
            $path = sprintf('%s/%s.jpg', $path, $fileName);
            Storage::disk('minio')->put($path, $content, 'public');
            return sprintf('%s/%s/%s', env('APP_URL'), 'minio/public', $path);
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $files
     * @param $publicPath
     * @return array
     */
    public static function multiUpload($files, $publicPath = 'public'): array
    {
        $data = [];
        foreach ($files as $file) {
            $content = file_get_contents($file->getRealPath());
            $fileName = sha1(md5(time() . $file->getClientOriginalName()));
            $path = sprintf('%s/%s.jpg', $publicPath, $fileName);
            Storage::disk('minio')->put($path, $content, 'public');
            $data[] = sprintf('%s/%s/%s', env('APP_URL'), 'minio/public', $path);
            $content = '';
            $fileName = '';
        }
        return $data;
    }

    /**
     * @param $path
     * @return string
     */
    public static function getTemporaryLink($path): string
    {
        $disk = Storage::disk('minio');
        $expiration = Carbon::now()->addMinutes(30);
        return $disk->temporaryUrl($path, $expiration);
    }

    /**
     * @param $path
     * @return bool
     */
    public static function remove($path): bool
    {
        return Storage::disk('minio')->delete($path);
    }
}
