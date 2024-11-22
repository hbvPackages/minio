<?php


namespace Hbv\Minio;

use Illuminate\Support\ServiceProvider;

class MinioProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mergeConfigSettings();
        $this->mergeEnvVariables();
    }

    /**
     * Add .env
     *
     * @return void
     */
    protected function mergeEnvVariables()
    {
        $filesystemConfigPath = config_path('filesystems.php');
        $packageConfigPath = __DIR__ . '/../config/config.php';

        if (!file_exists($filesystemConfigPath) || !file_exists($packageConfigPath)) {
            return;
        }

        $filesystemConfig = require $filesystemConfigPath;
        $packageConfig = require $packageConfigPath;

        if (!isset($filesystemConfig['disks']['minio'])) {
            $filesystemConfig['disks']['minio'] = $packageConfig['minio'];

            $newContent = "<?php\n\nreturn " . var_export($filesystemConfig, true) . ";\n";
            file_put_contents($filesystemConfigPath, $newContent);
        }
    }

    /**
     *  Add configs to config/filesystem.php
     *
     *
     * @return void
     */
    protected function mergeConfigSettings(): void
    {
        $filesystemConfigPath = config_path('filesystems.php');
        $packageConfigPath = __DIR__ . '..//../config.php';

        if (!file_exists($filesystemConfigPath) || !file_exists($packageConfigPath)) {
            return;
        }

        $filesystemConfig = require $filesystemConfigPath;
        $packageConfig = require $packageConfigPath;

        if (!isset($filesystemConfig['disks']['minio'])) {
            $filesystemConfig['disks']['minio'] = $packageConfig['minio'];
        }

        $newContent = "<?php\n\nreturn " . var_export($filesystemConfig, true) . ";\n";
        file_put_contents($filesystemConfigPath, $newContent);
    }
}
