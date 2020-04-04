<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Util;


class UUID
{
    /**
     * @param string $uuid
     * @return bool
     */
    public static function isValid($uuid) {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) === 1;
    }
}