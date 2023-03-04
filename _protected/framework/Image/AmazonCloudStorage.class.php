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
     * @param string $sFile
     *
     * @return string The signed URL where the image is hosted on AWS S3.
     */
    public function getSignedUrl(string $sFile): string
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
