<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Delivery;


use Skeerel\Util\Enum;

/**
 * Class Type
 * @package Skeerel\Data\Delivery
 *
 * @method static Type HOME()
 * @method static Type RELAY()
 * @method static Type COLLECT()
 */
class Type extends Enum
{
    const HOME = "home";

    const RELAY = "relay";

    const COLLECT = "collect";
}