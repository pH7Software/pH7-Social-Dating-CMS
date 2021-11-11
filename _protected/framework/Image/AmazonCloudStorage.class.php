<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Image
 */

namespace PH7\Framework\Image;

use Aws\S3\S3Client;
use PH7\Framework\Config\Config;

class AmazonCloudStorage implements Storageable
{
    const ACL_PUBLIC_READ = 'public-read';

    /** @var S3Client */
    private $oS3Client;

    /** @var string */
    private $sTempFileLocation;

    /** @var string */
    private $sBucket;

    /**
     * @param string $sTempFileLocation The source file.
     * @param string $sBucket S3 bucket.
     */
    public function __construct($sTempFileLocation, $sBucket)
    {
        $this->sTempFileLocation = $sTempFileLocation;
        $this->sBucket = $sBucket;

        $this->oS3Client = new S3Client($this->getConfiguration());
    }

    /**
     * {@inheritdoc}
     */
    public function save($sFile)
    {
        $this->oS3Client->putObject([
            'Bucket' => $this->sBucket,
            'Key' => $sFile,
            'SourceFile' => $this->sTempFileLocation,
            'ACL' => self::ACL_PUBLIC_READ
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($sFile)
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
    public function getSignedUrl($sFile)
    {
        return $this->oS3Client->getObjectUrl($this->sBucket, $sFile);
    }

    /**
     * @return array
     */
    private function getConfiguration()
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
