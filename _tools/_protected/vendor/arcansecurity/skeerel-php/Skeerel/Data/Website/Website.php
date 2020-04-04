<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Website;


use Skeerel\Exception\IllegalArgumentException;
use Skeerel\Util\UUID;

class Website
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $url;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var string[]
     */
    private $domains;

    /**
     * User constructor.
     * @param array $data
     * @throws IllegalArgumentException
     */
    function __construct($data) {
        if (!is_array($data)) {
            throw new IllegalArgumentException("User cannot be parsed due to incorrect data");
        }

        if (isset($data['id']) && is_string($data['id']) && UUID::isValid($data['id'])) {
            $this->id = $data['id'];
        }

        if (isset($data['name']) && is_string($data['name'])) {
            $this->name = $data['name'];
        }

        if (isset($data['image']) && is_string($data['image'])) {
            $this->image = $data['image'];
        }

        if (isset($data['url']) && is_string($data['url'])) {
            $this->url = $data['url'];
        }

        if (isset($data['status']) && is_string($data['status'])) {
            $this->status = Status::fromStrValue($data['status'], true);
        }

        if (isset($data['domains']) && is_array($data['domains'])) {
            $this->domains = [];
            foreach($data['domains'] as $domain) {
                if (is_string($domain)) {
                    $this->domains[] = $domain;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string[]
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * @param int $level
     * @return string
     */
    public function toString($level = 1) {
        $tab = str_repeat("\t", $level);
        $tab2 = str_repeat("\t", $level-1);
        return
            "{\n" .
                $tab . "id => $this->id,\n" .
                $tab . "name => $this->name,\n" .
                $tab . "image => $this->image,\n" .
                $tab . "url => $this->url,\n" .
                $tab . "status => $this->status,\n" .
                $tab . "domains => $this->domains,\n" .
            $tab2 . "}";
    }
}