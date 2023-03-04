<?php
/**
 * @title            Acl Resource Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Acl
 * @version          0.9
 */

declare(strict_types=1);

namespace PH7\Framework\Acl;

defined('PH7') or exit('Restricted access');

class AclResource
{
    /**
     * @throws Exception
     */
    public function __get(string $sName): string
    {
        switch ($sName) {
            case 'sName':
            case 'aAllowed':
                return $this->$sName;

            default:
                throw new Exception(
                    sprintf('Unable to get "%s"', $sName)
                );
        }
    }

    /**
     * @throws Exception
     */
    public function __set(string $sName, string $sValue): void
    {
        switch ($sName) {
            case 'sName':
            case 'aAllowed':
                $this->$sName = $sValue;
                break;

            default:
                throw new Exception(
                    sprintf('Unable to set "%s"', $sName)
                );
        }
    }

    public function __isset(string $sName): bool
    {
        return isset($this->$sName);
    }
}
