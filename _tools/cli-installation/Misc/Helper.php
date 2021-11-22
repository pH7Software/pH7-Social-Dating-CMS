<?php

declare(strict_types=1);

namespace PH7\Cli\Installer\Misc;

class Helper {
    public static function getFfmpegPath()
    {
        $isWindowsOs = 0 === stripos(PHP_OS, 'WIN');

        if ($isWindowsOs) {
            return is_file('C:\ffmpeg\bin\ffmpeg.exe') ? 'C:\ffmpeg\bin\ffmpeg.exe' : 'C:\ffmpeg\ffmpeg.exe';
        }

        return is_file('/usr/local/bin/ffmpeg') ? '/usr/local/bin/ffmpeg' : '/usr/bin/ffmpeg';
    }
}
