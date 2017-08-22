<?php

namespace App\Helpers;

class Assets {

    public static function path($filename) {
        $pathinfo = pathinfo($filename);

        if (\App::environment() == 'production') {
            $hash = file_get_contents(public_path() . '/build/hash.txt');
            return "/build/{$pathinfo['filename']}-$hash.{$pathinfo['extension']}";
        }
        return "/build/{$pathinfo['filename']}.{$pathinfo['extension']}";
    }
}
