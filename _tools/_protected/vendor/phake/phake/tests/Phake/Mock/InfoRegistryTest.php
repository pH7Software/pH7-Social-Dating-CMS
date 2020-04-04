<?php
/**
 * Created by PhpStorm.
 * User: mlively
 * Date: 3/30/14
 * Time: 3:29 PM
 */

class Phake_Mock_InfoRegistryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phake_Mock_InfoRegistry
     */
    private $registry;

    /**
     * @Mock
     * @var Phake_Mock_Info
     */
    private $info1;

    /**
     * @Mock
     * @var Phake_Mock_Info
     */
    private $info2;

    /**
     * @Mock
     * @var Phake_Mock_Info
     */
    private $info3;

    public function setUp()
    {
        Phake::initAnnotations($this);
        $this->registry = new Phake_Mock_InfoRegistry();
        $this->registry->addInfo($this->info1);
        $this->registry->addInfo($this->info2);
        $this->registry->addInfo($this->info3);
    }

    public function testReset()
    {
        $this->registry->resetAll();

        Phake::verify($this->info1)->resetInfo();
        Phake::verify($this->info2)->resetInfo();
        Phake::verify($this->info3)->resetInfo();
    }
}
 