<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Website;

use Skeerel\Util\Enum;

/**
 * Class Currency
 * @package Skeerel\Data\Payment
 *
 * @method static Currency EUR()
 */
class Status extends Enum
{
    const NOT_VERIFIED = "NOT_VERIFIED";
    const PENDING = "PENDING";
    const REJECTED = "REJECTED";
    const VERIFIED = "VERIFIED";
}