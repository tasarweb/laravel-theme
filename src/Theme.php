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
    private $activeTheme = 'Default';

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

    public function setActiveTheme()
    {
        $active = TasarTheme::where('is_active', 1);
        if ($active->exists() && $this->themeExists($active->first()->dir))
            $this->activeTheme = $active->first()->dir;
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
    public function getThemeInfo(String $themeName = null, String $value = null)
    {
        if ($themeName === null)
            $themeName = $this->activeTheme;
        $file = $this->themeExists($themeName);
        if ($file) {
            $info = file_get_contents(public_path("themes/$themeName/theme.json"));
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
     * @param  string $directory
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
        return true;
    }
}