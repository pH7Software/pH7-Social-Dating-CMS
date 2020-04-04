<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Delivery;


use Skeerel\Util\Enum;

/**
 * Class Color
 * @package Skeerel\Data\Delivery
 *
 * @method static Color GREEN()
 * @method static Color ORANGE()
 * @method static Color RED()
 */
class Color extends Enum
{
    const GREEN = "green";

    const ORANGE = "orange";

    const RED = "red";
}