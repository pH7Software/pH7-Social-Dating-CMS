<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2013-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine
 */

declare(strict_types=1);

namespace PH7\Framework\Mvc\Model\Engine;

defined('PH7') or exit('Restricted access');

abstract class Entity
{
    private int $iId;

    /**
     * Get the primary key.
     */
    public function getKeyId(): int
    {
        $this->checkKeyId();

        return $this->iId;
    }

    /**
     * Set the primary key.
     */
    public function setKeyId(string $sId): void
    {
        $this->iId = (int)$sId;
    }

    /**
     * Check if the self::$iId attribute is not empty, otherwise we set the last insert ID.
     *
     * @see Db::lastInsertId()
     */
    protected function checkKeyId(): void
    {
        if (empty($this->iId)) {
            $this->setKeyId(Db::getInstance()->lastInsertId());
        }
    }
}
