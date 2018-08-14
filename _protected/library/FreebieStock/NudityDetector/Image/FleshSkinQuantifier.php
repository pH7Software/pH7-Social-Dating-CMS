<?php

/**
 * @author FreebieVectors.com
 *
 * Modified by Pierre-Henry Soria <hello@ph7cms.com>
 *
 * Image nudity detertor based on flesh color quantity.
 * Source: http://www.naun.org/multimedia/NAUN/computers/20-462.pdf
 * J. Marcial-Basilio (2011), Detection of Pornographic Digital Images, International Journal of Computers
 */
class Image_FleshSkinQuantifier extends Image
{
    /**
     * Threshold of flesh color in image to consider in pornographic,
     * see page 302.
     *
     * @var float
     */
    private $threshold = .5;

    /**
     * Pixel count to iterate over. Too increase speed, set it higher and it will
     * skip some pixels.
     *
     * @var int
     */
    private $iteratorIncrement = 1;

    /**
     * Cb and Cr value bounds. See page 300
     *
     * @var array
     */
    private $boundsCbCr = [80, 120, 133, 173];

    /**
     * Exclude white colors above this RGB color intensity
     *
     * @var int
     */
    private $excludeWhite = 250;

    /**
     * Exclude dark and black colors below this value
     *
     * @var int
     */
    private $excludeBlack = 5;

    /**
     * Quantify flesh color amount using YCbCr color model
     *
     * @return float
     */
    public function quantifyYCbCr()
    {
        // Init some vars
        $inc = $this->iteratorIncrement;
        $width = $this->width();
        $height = $this->height();
        list($Cb1, $Cb2, $Cr1, $Cr2) = $this->boundsCbCr;
        $white = $this->excludeWhite;
        $black = $this->excludeBlack;
        $total = $count = 0;

        for ($x = 0; $x < $width; $x += $inc)
            for ($y = 0; $y < $height; $y += $inc) {
                list($r, $g, $b) = $this->rgbXY($x, $y);

                // Exclude white/black colors from calculation, presumably background
                if ((($r > $white) && ($g > $white) && ($b > $white)) ||
                    (($r < $black) && ($g < $black) && ($b < $black))) continue;

                // Converg pixel RGB color to YCbCr, coefficients already divided by 255
                $Cb = 128 + (-0.1482 * $r) + (-0.291 * $g) + (0.4392 * $b);
                $Cr = 128 + (0.4392 * $r) + (-0.3678 * $g) + (-0.0714 * $b);

                // Increase counter, if necessary
                if (($Cb >= $Cb1) && ($Cb <= $Cb2) && ($Cr >= $Cr1) && ($Cr <= $Cr2))
                    $count++;
                $total++;
            }

        return $count / $total;
    }

    /**
     * Check if image is of pornographic content
     *
     * @param float $threshold
     */
    public function isPorn($threshold = false)
    {
        return $threshold === false
            ? $this->quantifyYCbCr() >= $this->threshold
            : $this->quantifyYCbCr() >= $threshold;
    }
}
