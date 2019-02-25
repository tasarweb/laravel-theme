<?php

namespace Tasar\Theme;

use File;
use Illuminate\Support\ServiceProvider;
use Tasar\Theme\Models\TasarTheme;

class themeServiceProvider extends ServiceProvider
{

    private $themePath;
    private $activeTheme = 'Default';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('tasar_themes')) {
            if (TasarTheme::all()->count() === 0)
                \Tasar\Theme\Facades\Theme::insertThemes();

            $this->activeTheme = \Tasar\Theme\Facades\Theme::getActiveTheme();
        }

        $this->themePath = public_path('themes/') . $this->activeTheme;

        // View //
        $this->loadViewsFrom("{$this->themePath}/Views", 'Theme');

        // Migration //
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        $this->load(['Actions', 'Filters']);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function register()
    {
        // Facade //
        $this->app->bind('tasar-theme', function () {
            return new Theme;
        });

        /*--------------------------------------------------------------------------
        | Register helpers.php functions
        |--------------------------------------------------------------------------*/
        require_once 'Helpers/helpers.php';
    }

    /**
     * @param array $folders
     */
    private function load(array $folders): void
    {
        foreach ($folders as $folder) {
            if (File::exists("{$this->themePath}/$folder")) {
                $files = File::files("{$this->themePath}/$folder");
                foreach ($files as $file) {
                    if ($file->getExtension() === 'php') {
                        require_once $file->getPathname();
                    }
                }
            }
        }
    }
}
