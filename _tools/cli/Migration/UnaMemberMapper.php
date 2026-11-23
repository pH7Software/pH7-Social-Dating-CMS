<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Cli / Migration
 */

declare(strict_types=1);

namespace PH7\Cli\Migration;

final class UnaMemberMapper
{
    public const CSV_HEADERS = [
        'source_platform',
        'source_version',
        'source_id',
        'email',
        'username',
        'first_name',
        'last_name',
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

    public const SOURCE_PLATFORM = 'una';
    public const SOURCE_VERSION = '14.0.0';

    /**
     * Map one UNA person without carrying over the source password hash.
     * Imported members use pH7Builder's password-recovery flow.
     *
     * @return array<string, string>
     */
    public function map(array $aSource): array
    {
        $sEmail = strtolower(trim((string)($aSource['email'] ?? '')));
        [$sFirstName, $sLastName] = $this->splitName(
            trim((string)($aSource['fullname'] ?? '')),
            trim((string)($aSource['last_name'] ?? ''))
        );

        return [
            'source_platform' => self::SOURCE_PLATFORM,
            'source_version' => self::SOURCE_VERSION,
            'source_id' => (string)($aSource['source_id'] ?? ''),
            'email' => $sEmail,
            'username' => $this->resolveUsername((string)($aSource['name'] ?? ''), $sEmail),
            'first_name' => $sFirstName,
            'last_name' => $sLastName,
            'password' => '',
            'sex' => $this->mapGender((string)($aSource['gender'] ?? '')),
            'match_sex' => '',
            'birth_date' => $this->mapBirthDate((string)($aSource['birthday'] ?? '')),
            'description' => trim((string)($aSource['description'] ?? '')),
            'country' => '',
            'city' => '',
            'state' => '',
            'zip_code' => '',
            'ip' => $this->mapIp((string)($aSource['ip'] ?? '')),
            'is_active' => $this->mapActivation($aSource)
        ];
    }

    /**
     * @return array{string, string}
     */
    private function splitName(string $sFullName, string $sLastName): array
    {
        if ($sFullName === '') {
            return ['Imported', $sLastName !== '' ? $sLastName : 'Member'];
        }

        if ($sLastName !== '') {
            $sQuotedLastName = preg_quote($sLastName, '/');
            $sFirstName = trim((string)preg_replace('/\s+' . $sQuotedLastName . '$/iu', '', $sFullName));

            return [$sFirstName !== '' ? $sFirstName : $sFullName, $sLastName];
        }

        $aParts = preg_split('/\s+/u', $sFullName) ?: [];
        if (count($aParts) === 1) {
            return [$sFullName, 'Member'];
        }

        $sResolvedLastName = (string)array_pop($aParts);

        return [implode(' ', $aParts), $sResolvedLastName];
    }

    private function resolveUsername(string $sName, string $sEmail): string
    {
        $sName = trim($sName);
        if ($sName !== '') {
            return $sName;
        }

        return strstr($sEmail, '@', true) ?: 'ImportedMember';
    }

    private function mapGender(string $sGender): string
    {
        return match (strtolower(trim($sGender))) {
            'female', 'woman', 'f', '2' => 'female',
            'male', 'man', 'm', '1' => 'male',
            'couple', 'c' => 'couple',
            default => ''
        };
    }

    private function mapBirthDate(string $sBirthDate): string
    {
        $oBirthDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $sBirthDate);

        return $oBirthDate instanceof \DateTimeImmutable && $oBirthDate->format('Y-m-d') === $sBirthDate
            ? $sBirthDate
            : '';
    }

    private function mapIp(string $sIp): string
    {
        return filter_var($sIp, FILTER_VALIDATE_IP) !== false ? $sIp : '0.0.0.0';
    }

    private function mapActivation(array $aSource): string
    {
        $bAccountActive = (int)($aSource['account_active'] ?? 0) > 0;
        $bEmailConfirmed = (int)($aSource['email_confirmed'] ?? 0) === 1;
        $bProfileActive = (string)($aSource['profile_status'] ?? '') === 'active';

        return $bAccountActive && $bEmailConfirmed && $bProfileActive ? '1' : '3';
    }
}
