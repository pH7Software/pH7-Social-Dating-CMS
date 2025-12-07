<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Image
 */

declare(strict_types=1);

namespace PH7\Framework\Image;

use Aws\S3\S3Client;
use PH7\Framework\Config\Config;

class AmazonCloudStorage implements Storageable
{
    private const ACL_PUBLIC_READ = 'public-read';
    private const SIGNED_URL_EXPIRATION = '+20 minutes';
    private const PDF_CONTENT_TYPE = 'application/pdf';

    private S3Client $oS3Client;

    private string $sTempFileLocation;

    private string $sBucket;

    /**
     * @param string $sTempFileLocation The source file.
     * @param string $sBucket S3 bucket.
     */
    public function __construct(string $sTempFileLocation, string $sBucket)
    {
        $this->sTempFileLocation = $sTempFileLocation;
        $this->sBucket = $sBucket;

        $this->oS3Client = new S3Client($this->getConfiguration());

    }

    public function save(string $sFile): self
    {
        $this->oS3Client->putObject([
            'Bucket' => $this->sBucket,
            'Key' => $sFile,
            'SourceFile' => $this->sTempFileLocation,
            'ACL' => self::ACL_PUBLIC_READ
        ]);

        return $this;
    }

    public function remove(string $sFile): self
    {
        $this->oS3Client->deleteObject([
            'Bucket' => $this->sBucket,
            'Key' => $sFile
        ]);

        return $this;
    }

    /**
     * Get a signed URL for secure access to private files (e.g., PDFs).
     *
     * @param string $sFile
     * @param string $sExpiration Expiration time (default: +20 minutes)
     *
     * @return string The signed URL with temporary access.
     */
    public function getSignedUrl(string $sFile, string $sExpiration = self::SIGNED_URL_EXPIRATION): string
    {
        $oCommand = $this->oS3Client->getCommand('GetObject', [
            'Bucket' => $this->sBucket,
            'Key' => $sFile
        ]);

        $oRequest = $this->oS3Client->createPresignedRequest($oCommand, $sExpiration);

        return (string)$oRequest->getUri();
    }

    /**
     * Get a signed URL specifically for PDF preview with proper content type.
     *
     * @param string $sFile
     * @param string $sExpiration
     *
     * @return string The signed URL for PDF preview.
     */
    public function getSignedPdfUrl(string $sFile, string $sExpiration = self::SIGNED_URL_EXPIRATION): string
    {
        $oCommand = $this->oS3Client->getCommand('GetObject', [
            'Bucket' => $this->sBucket,
            'Key' => $sFile,
            'ResponseContentType' => self::PDF_CONTENT_TYPE,
            'ResponseContentDisposition' => 'inline'
        ]);

        $oRequest = $this->oS3Client->createPresignedRequest($oCommand, $sExpiration);

        return (string)$oRequest->getUri();
    }

    /**
     * Get the public URL for embedded images.
     *
     * @param string $sFile
     *
     * @return string The public URL.
     */
    public function getPublicUrl(string $sFile): string
    {
        return $this->oS3Client->getObjectUrl($this->sBucket, $sFile);
    }

    private function getConfiguration(): array
    {
        return [
            'region'  => Config::getInstance()->values['storage']['aws.default_region'],
            'version' => 'latest',
            'credentials' => [
                'key'    => Config::getInstance()->values['storage']['aws.access_key_id'],
                'secret' => Config::getInstance()->values['storage']['aws.secret_access_key'],
            ]
        ];
    }
}
