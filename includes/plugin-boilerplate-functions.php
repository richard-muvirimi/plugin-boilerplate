<?php

/**
 * Plugin general functionality
 */

/**
 * Get temp dir
 *
 * @since 1.0.0
 * @version 1.0.0
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 * @return string
 */
function plugin_boilerplate_temp_dir()
{
    global $wp_filesystem;

    $dir = $wp_filesystem->wp_content_dir() . PLUGIN_BOILERPLATE_SLUG;
    //$dir = get_temp_dir() . PLUGIN_BOILERPLATE_SLUG;

    if (!$wp_filesystem->exists($dir)) {
        $wp_filesystem->mkdir($dir);
    }

    return $dir . plugin_boilerplate_dir_separator();
}

/**
 * Get dir seperator
 *
 * @since 1.0.0
 * @version 1.0.0
 * @return string
 */
function plugin_boilerplate_dir_separator()
{
    //Todo: try to calculate from number of occurances (phpexcel)

    return "/";
}

/**
 * The location to copy boilerplate files from
 *
 * @since 1.0.0
 * @version 1.0.0
 * @return string
 */
function plugin_boilerplate_files()
{
    return apply_filters(PLUGIN_BOILERPLATE_SLUG . "-files", plugin_dir_path(__DIR__) . "includes/" . PLUGIN_BOILERPLATE_SLUG);
}

/**
 * Walk a directory calling a function for each file
 *
 * @since 1.0.0
 * @version 1.0.0
 * @global WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
 * @param callable $callback
 * @param string $location
 * @return bool
 */
function plugin_boilerplate_walk_dir(callable $callback, $location)
{

    global $wp_filesystem;

    $files =     $wp_filesystem->dirlist($location, false);

    /**
     * Push directories up for when copying
     */
    uasort($files, function ($data1, $data2) {
        if ($data1["type"] == $data2["type"]) {
            return 0;
        } else {
            return ($data1["type"] = "d") ? -1 : 1;
        }
    });

    $done = true;
    foreach ($files as $file => $data) {

        $path = $location . plugin_boilerplate_dir_separator() . $file;

        $content = "";
        if ($wp_filesystem->is_dir($path)) {

            plugin_boilerplate_walk_dir($callback, $path);
        } elseif ($wp_filesystem->is_file($path)) {
            $content = apply_filters(PLUGIN_BOILERPLATE_SLUG . "-file-content", $wp_filesystem->get_contents($path), $path);
        }

        $target = apply_filters(PLUGIN_BOILERPLATE_SLUG . "-file-path", $path);

        $destination = str_replace(plugin_boilerplate_files(), "", $target);

        $done &= call_user_func($callback, $path, $destination, $content);
    }

    return $done;
}