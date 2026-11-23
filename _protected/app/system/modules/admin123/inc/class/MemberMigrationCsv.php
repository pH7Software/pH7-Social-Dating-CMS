<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

final class MemberMigrationCsv
{
    public const FIELDS = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'sex',
        'match_sex',
        'birth_date',
        'description',
        'country',
        'city',
        'state',
        'zip_code',
        'ip',
        'is_active'
    ];

    private const HEADER_ALIASES = [
        'source_platform' => ['sourceplatform'],
        'source_version' => ['sourceversion'],
        'source_id' => ['sourceid'],
        'username' => ['username', 'login', 'user', 'nickname'],
        'first_name' => ['name', 'firstname', 'givenname', 'forename'],
        'last_name' => ['lastname', 'surname', 'familyname'],
        'match_sex' => ['matchsex', 'looking', 'lookingfor'],
        'sex' => ['sex', 'gender'],
        'email' => ['email', 'mail', 'emailid'],
        'password' => ['password', 'passwd'],
        'description' => [
            'desc',
            'description',
            'descriptionme',
            'generaldescription',
            'about',
            'aboutme',
            'bio',
            'biography',
            'comment'
        ],
        'country' => ['country', 'countryid'],
        'city' => ['city', 'town'],
        'state' => ['state', 'district', 'province', 'region'],
        'zip_code' => [
            'zip',
            'zipcode',
            'postal',
            'postcode',
            'postalcode',
            'pin',
            'pincode',
            'eircode'
        ],
        'birth_date' => ['birthday', 'birthdate', 'dateofbirth', 'dob'],
        'ip' => ['ip', 'ipaddress'],
        'is_active' => ['isactive', 'activationstatus']
    ];

    /**
     * @return array<string, int>
     */
    public function mapHeaders(array $aHeader): array
    {
        $aColumns = [];

        foreach ($aHeader as $iPosition => $mHeader) {
            $sHeader = $this->normalizeHeader((string)$mHeader);

            foreach (self::HEADER_ALIASES as $sField => $aAliases) {
                if (in_array($sHeader, $aAliases, true)) {
                    $aColumns[$sField] = (int)$iPosition;
                    break;
                }
            }
        }

        return $aColumns;
    }

    /**
     * Return a row value without treating the string "0" as empty.
     *
     * @param array<string, int>    $aColumns
     * @param array<string, string> $aDefaults
     */
    public function getValue(
        array $aRow,
        array $aColumns,
        array $aDefaults,
        string $sField,
        bool $bPreserveEmpty = false
    ): string {
        if (isset($aColumns[$sField])) {
            $sValue = trim((string)($aRow[$aColumns[$sField]] ?? ''));

            if ($sValue !== '' || $bPreserveEmpty) {
                return $sValue;
            }
        }

        return $aDefaults[$sField] ?? '';
    }

    /**
     * Canonical files carry provenance fields that ad-hoc legacy CSV files do not.
     * Their intentional empty values must not be replaced with sample defaults.
     *
     * @param array<string, int> $aColumns
     */
    public function isCanonicalMigration(array $aColumns): bool
    {
        return isset($aColumns['source_platform'], $aColumns['source_version'], $aColumns['source_id']);
    }

    public function assertDialect(string $sDelimiter, string $sEnclosure): void
    {
        if (!in_array($sDelimiter, [',', ';', '|'], true)) {
            throw new \InvalidArgumentException('Unsupported CSV delimiter.');
        }

        if (!in_array($sEnclosure, ['"', '/'], true)) {
            throw new \InvalidArgumentException('Unsupported CSV enclosure.');
        }
    }

    private function normalizeHeader(string $sHeader): string
    {
        $sHeader = (string)preg_replace('/^\xEF\xBB\xBF/', '', $sHeader);

        return strtolower(trim(str_replace(['-', '_', ' '], '', $sHeader)));
    }
}
