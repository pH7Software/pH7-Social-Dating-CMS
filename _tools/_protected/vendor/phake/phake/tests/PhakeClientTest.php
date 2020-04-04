<?php

class PhakeClientTest extends PHPUnit_Framework_TestCase
{
    protected function setup()
    {
        // unset the $client in Phake
        $refClass = new ReflectionClass('Phake');
        $clientProperty = $refClass->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue(null);
    }

    public function testAutoDetectsPHPUnitClient()
    {
        $client = Phake::getClient();
        $this->assertInstanceOf('Phake_Client_PHPUnit', $client);
    }
}
