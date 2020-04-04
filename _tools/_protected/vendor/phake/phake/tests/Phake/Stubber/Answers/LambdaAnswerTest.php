<?php

class Phake_Stubber_Answers_LambdaAnswerTest extends PHPUnit_Framework_TestCase
{
    public function testLambdaAnswerAcceptsOldschoolLambda()
    {
        $func   = function ($arg1) { return $arg1; };
        $answer = new Phake_Stubber_Answers_LambdaAnswer($func);
        $result = call_user_func($answer->getAnswerCallback('someObject', 'testMethod'), 'bar');
        $this->assertSame("bar", $result);
    }
}
