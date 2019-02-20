<?php
/**
 * @title            Stream File
 * @desc             Stream File and Standard Streams, Network Sockets.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File
 */

namespace PH7\Framework\File;

defined('PH7') or exit('Restricted access');

class Stream
{
    /**
     * @return resource I/O streams.
     */
    public static function getInput()
    {
        return @file_get_contents('php://input');
    }

    /**
     * @return resource Standard input.
     */
    public static function input()
    {
        return @fopen('php://stdin', 'r');
    }

    /**
     * @return resource Standard output.
     */
    public static function out()
    {
        return @fopen('php://stdout', 'w');
    }

    /**
     * @return resource Standard error.
     */
    public static function error()
    {
        return @fopen('php://stderr', 'w');
    }
}
