<?php
namespace Tasar\Theme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Theme
 * @package Tasar\Theme\Facades
 */
class Theme extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tasar-theme';
    }
}
