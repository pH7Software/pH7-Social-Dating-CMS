<?php

/**
 * @author           Pierre-Henry SORIA <hello@ph7builder.com>
 * @copyright        (c) 2015-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7\Framework\Api;

defined('PH7') or exit('Restricted access');

trait Api
{
    private const SENSITIVE_FIELDS = [
        'password',
        'hashValidation',
        'twoFactorAuthSecret'
    ];

    /**
     * Encode the data to JSON.
     *
     * @return string|bool returns the data encoded to JSON or FALSE if the data is invalid
     */
    public function set($mData): string|bool
    {
        if (is_array($mData)) {
            return json_encode($this->filterSensitiveFields($mData));
        }

        return false;
    }

    private function filterSensitiveFields(mixed $mData): mixed
    {
        if (is_array($mData)) {
            foreach ($mData as $sField => $mValue) {
                if (is_string($sField) && in_array($sField, self::SENSITIVE_FIELDS, true)) {
                    unset($mData[$sField]);
                    continue;
                }

                $mData[$sField] = $this->filterSensitiveFields($mValue);
            }

            return $mData;
        }

        if (is_object($mData)) {
            $oFilteredData = clone $mData;

            foreach (get_object_vars($oFilteredData) as $sField => $mValue) {
                if (in_array($sField, self::SENSITIVE_FIELDS, true)) {
                    unset($oFilteredData->{$sField});
                    continue;
                }

                $oFilteredData->{$sField} = $this->filterSensitiveFields($mValue);
            }

            return $oFilteredData;
        }

        return $mData;
    }
}
