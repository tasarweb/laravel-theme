<?php
if (!function_exists('themes_path')) {
    function themes_path($filename = null)
    {
        return public_path('themes/' . \Tasar\Theme\Facades\Theme::getActiveTheme() . "/$filename");
    }
}

if (!function_exists('theme_url')) {
    function theme_url($url)
    {
        return url('/themes/' . \Tasar\Theme\Facades\Theme::getActiveTheme() . "/$url");
    }
}

if (!function_exists('addAction')) {
    function addAction($hook, $callback, $priority = 20, $arguments = 1)
    {
        return \Eventy::addAction($hook, $callback, $priority, $arguments);
    }
}

if (!function_exists('removeAction')) {
    function removeAction($hook, $callback, $priority = 20)
    {
        return \Eventy::removeAction($hook, $callback, $priority);
    }
}

if (!function_exists('addFilter')) {
    function addFilter($hook, $callback, $priority = 20, $arguments = 1)
    {
        return \Eventy::addFilter($hook, $callback, $priority, $arguments);
    }
}

if (!function_exists('removeFilter')) {
    function removeFilter($hook, $callback, $priority = 20)
    {
        return \Eventy::removeFilter($hook, $callback, $priority);
    }
}