<?php
/**
 * Copyright (c) Pierre-Henry Soria <hi@ph7.me>
 * MIT License - https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace PH7\Cli\Misc;

class Helper
{
    public static function getFfmpegPath(): string
    {
        $isWindowsOs = 0 === stripos(PHP_OS, 'WIN');

        if ($isWindowsOs) {
            return is_file('C:\ffmpeg\bin\ffmpeg.exe') ? 'C:\ffmpeg\bin\ffmpeg.exe' : 'C:\ffmpeg\ffmpeg.exe';
        }

        return is_file('/usr/local/bin/ffmpeg') ? '/usr/local/bin/ffmpeg' : '/usr/bin/ffmpeg';
    }

    public static function generateHash(int $length): string
    {
        $sPrefix = (string)mt_rand();

        return substr(
            hash(
                'whirlpool',
                time() . hash('sha512', getenv('REMOTE_ADDR') . uniqid($sPrefix, true) . microtime(true) * 999999999999)
            ),
            0,
            $length
        );
    }

    public static function cleanString(string $value): string
    {
        return str_replace('"', '\"', $value);
    }
}
