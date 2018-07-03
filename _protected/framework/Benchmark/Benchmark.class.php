<?php
/**
 * @title            Benchmark Class
 *
 * @package          PH7 / Framework / Benchmark
 *
 * Copyright (c) 2012 Jeremy Perret
 *
 * File Modified by Pierre-Henry Soria, Copyright (c) 2014-2018
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace PH7\Framework\Benchmark;

defined('PH7') or exit('Restricted access');

class Benchmark
{
    const SIZE_MODE = 1024;

    /** @var float */
    private $fStartTime;

    /** @var float */
    private $fEndTime;

    /** @var int */
    private $iMemoryUsage;

    /**
     * Sets start microtime
     *
     * @return void
     */
    public function start()
    {
        $this->fStartTime = microtime(true);
    }

    /**
     * Sets end microtime
     *
     * @return void
     */
    public function end()
    {
        $this->fEndTime = microtime(true);
        $this->iMemoryUsage = memory_get_usage(true);
    }

    /**
     * Returns the elapsed time, readable or not
     *
     * @param bool $raw Whether the result must be human readable
     * @param string|null $format The format to display (printf format)
     *
     * @return string|float
     */
    public function getTime($raw = false, $format = null)
    {
        $elapsed = $this->fEndTime - $this->fStartTime;

        return $raw ? $elapsed : self::readableElapsedTime($elapsed, $format);
    }

    /**
     * Returns a human readable elapsed time
     *
     * @param float $microtime
     * @param string|null $format The format to display (printf format)
     * @param int $round
     *
     * @return string
     */
    public static function readableElapsedTime($microtime, $format = null, $round = 3)
    {
        if ($format === null) {
            $format = '%.3f%s';
        }

        if ($microtime >= 1) {
            $unit = 's';
            $time = round($microtime, $round);
        } else {
            $unit = 'ms';
            $time = round($microtime * 1000);

            $format = preg_replace('/(%.[\d]+f)/', '%d', $format);
        }

        return sprintf($format, $time, $unit);
    }

    /**
     * Returns the memory usage at the end checkpoint
     *
     * @param bool $raw Whether the result must be human readable
     * @param string|null $format The format to display (printf format)
     *
     * @return string|float
     */
    public function getMemoryUsage($raw = false, $format = null)
    {
        return $raw ? $this->iMemoryUsage : self::readableSize($this->iMemoryUsage, $format);
    }

    /**
     * Returns a human readable memory size
     *
     * @param int $size
     * @param string|null $format The format to display (printf format)
     * @param int $round
     *
     * @return string
     */
    public static function readableSize($size, $format = null, $round = 3)
    {
        if ($format === null) {
            $format = '%.2f%s';
        }

        $units = explode(' ', 'B Kb Mb Gb Tb');

        for ($i = 0; $size > self::SIZE_MODE; $i++) {
            $size /= self::SIZE_MODE;
        }

        if (0 === $i) {
            $format = preg_replace('/(%.[\d]+f)/', '%d', $format);
        }

        return sprintf($format, round($size, $round), $units[$i]);
    }

    /**
     * Returns the memory peak, readable or not
     *
     * @param bool $raw Whether the result must be human readable
     * @param string|null $format The format to display (printf format)
     *
     * @return string|float
     */
    public function getMemoryPeak($raw = false, $format = null)
    {
        $memory = memory_get_peak_usage(true);

        return $raw ? $memory : self::readableSize($memory, $format);
    }
}
