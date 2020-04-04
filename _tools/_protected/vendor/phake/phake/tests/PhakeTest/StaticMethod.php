<?php

class PhakeTest_StaticMethod
{
    public $className = 'PhakeTest_ClassWithStaticMethod';
    
    public function askSomething()
    {
        $className = $this->className;
        return $className::ask();
    }
}
