<?php
/**
 * @title            Acl Resource Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Acl
 * @version          0.9
 */

declare(strict_types=1);

namespace PH7\Framework\Acl;

defined('PH7') or exit('Restricted access');

class AclResource
{
    private string $sName;
    private array $aAllowed;

    /**
     * @throws Exception
     */
    public function __get(string $sName): string|array
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
    public function __set(string $sName, mixed $mValue): void
    {
        switch ($sName) {
            case 'sName':
                if (!is_string($mValue)) {
                    throw new Exception('ACL resource names must be strings.');
                }
                $this->sName = $mValue;
                break;

            case 'aAllowed':
                if (!is_array($mValue)) {
                    throw new Exception('ACL allowed-role lists must be arrays.');
                }
                $this->aAllowed = $mValue;
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
