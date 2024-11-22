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
        $packageEnvPath = __DIR__ . '..//../.env.example';
        $projectEnvPath = base_path('.env');

        if (!file_exists($packageEnvPath)) return;

        if (!file_exists($projectEnvPath)) {
            copy($packageEnvPath, $projectEnvPath);
        } else {
            $packageEnvContent = file($packageEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $projectEnvContent = file($projectEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            $projectEnvKeys = array_map(function ($line) {
                return explode('=', $line, 2)[0];
            }, $projectEnvContent);

            $mergedContent = $projectEnvContent;

            foreach ($packageEnvContent as $line) {
                $key = explode('=', $line, 2)[0];
                if (!in_array($key, $projectEnvKeys)) {
                    $mergedContent[] = $line;
                }
            }

            file_put_contents($projectEnvPath, implode(PHP_EOL, $mergedContent) . PHP_EOL);
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
        $packageConfigPath = __DIR__ . '/config/config.php';

        if (!file_exists($filesystemConfigPath)) {
            throw new \RuntimeException("The file filesystems.php does not exist at path: $filesystemConfigPath");
        }

        if (!file_exists($packageConfigPath)) {
            throw new \RuntimeException("The package config file does not exist at path: $packageConfigPath");
        }

        $filesystemConfig = require $filesystemConfigPath;
        $packageConfig = require $packageConfigPath;

        if (!isset($filesystemConfig['disks']['minio'])) {
            $filesystemConfig['disks']['minio'] = $packageConfig['minio'];

            $newContent = "<?php\n\nreturn " . var_export($filesystemConfig) . ";\n";

            file_put_contents($filesystemConfigPath, $newContent);
        }
    }
}
