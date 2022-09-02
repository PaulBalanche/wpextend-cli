<?php

namespace Wpextend\Cli\Helpers;


class Render {

    /**
     * Outputs formatted text.
     *
     * @param string $text
     * @param string $color
     * @param bool   $newLine
     */
    static function output( $text, $color = null, $newLine = true, $display = true ) {
        static $styles = [
            'success' => "\033[0;32m%s\033[0m",
            'error' => "\033[31;31m%s\033[0m",
            'info' => "\033[33m%s\033[39m",
            'warning' => "\033[33m%s\033[39m",
            'heading' => "\033[1;33m%s\033[22;39m",
        ];

        $format = '%s';

        if (isset($styles[$color])) {
            $format = $styles[$color];
        }

        if ($newLine) {
            $format .= PHP_EOL;
        }

        if( $display ) {
            printf($format, $text);
        }
        else {
            return sprintf($format, $text);
        }
    }

}