<?php

namespace Tasar\Theme;

use Symfony\Component\Finder\Finder;
use Tasar\Theme\Models\TasarTheme;

/**
 * Class Theme
 * @package Tasar\Theme
 * @method String getThemeName($themeName = null)
 * @method String getThemeDescription($themeName = null)
 * @method String getThemeVersion($themeName = null)
 * @method String getThemeUrl($themeName = null)
 * @method String getThemeAuthor($themeName = null)
 * @method String getThemeAuthorUrl($themeName = null)
 */
class Theme
{
    private $activeTheme;

    public function __construct()
    {
        $this->setActiveTheme();
    }

    public function __call($method, $arg)
    {
        if (!in_array($method, ['getThemeName', 'getThemeDescription', 'getThemeVersion', 'getThemeUrl', 'getThemeAuthor', 'getThemeAuthorUrl']))
            throw new \BadMethodCallException();

        return $this->getThemeInfo(count($arg) === 0 ? null : $arg[0], substr($method, 8));
    }

    private function setActiveTheme()
    {
        $active = TasarTheme::where('is_active', 1);
        if ($active->exists() && $this->themeExists($active->first()->dir))
            $this->activeTheme = $active->first()->dir;
        else {
            $default = TasarTheme::where('dir', 'Default')->get()->first();
            if ($default->exists() && $this->themeExists($default->dir)) {
                $default->is_active = true;
                $default->save();
                $this->activeTheme = $default->dir;
            }
        }
    }

    public function getActiveTheme()
    {
        return $this->activeTheme;
    }

    /**
     * Return theme information
     *
     * @param String|null $themeName
     * @param String|null $value
     * @return array|bool|mixed
     */
    public function getThemeInfo(String $themeDir = null, String $value = null)
    {
        if ($themeDir === null)
            $themeDir = $this->activeTheme;
        $file = $this->themeExists($themeDir);
        if ($file) {
            $info = file_get_contents(public_path("themes/$themeDir/theme.json"));
            $themeInfo = json_decode($info, true);
            if ($value === null || $value === '')
                return $themeInfo;

            return array_key_exists($value, $themeInfo) ? $themeInfo[$value] : false;
        }
        return false;
    }

    public function themeExists(String $themeName)
    {
        return file_exists(public_path("themes/$themeName/theme.json"));
    }

    public function getThemeList()
    {
        $this->insertThemes();

        return TasarTheme::all();
    }

    /**
     * Get all of the directories within a given directory.
     *
     * @param string $directory
     * @return array
     */
    private function directories(String $directory)
    {
        $directories = [];

        foreach (Finder::create()->in($directory)->directories()->depth(0)->sortByName() as $dir) {
            $directories[] = $dir->getBasename();
        }

        return $directories;
    }

    public function insertThemes()
    {
        $themes = $this->directories(public_path('themes'));
        foreach ($themes as $theme) {
            if ($this->themeExists($theme)) {
                TasarTheme::updateOrCreate(
                    ['dir' => $theme],
                    [
                        'name' => $this->getThemeName($theme),
                        'description' => $this->getThemeDescription($theme),
                        'version' => $this->getThemeVersion($theme),
                        'author' => $this->getThemeAuthor($theme),
                        'url' => $this->getThemeUrl($theme),
                        'authorUrl' => $this->getThemeAuthorUrl($theme)
                    ]
                );
            }
        }

        $this->databaseClean($themes);

        return true;
    }

    private function databaseClean(array $themeFolders)
    {
        $themes = TasarTheme::select('id', 'dir')->get();
        foreach ($themes as $theme) {
            if (!in_array($theme->dir, $themeFolders))
                $theme->delete($theme->id);
        }
    }
}
