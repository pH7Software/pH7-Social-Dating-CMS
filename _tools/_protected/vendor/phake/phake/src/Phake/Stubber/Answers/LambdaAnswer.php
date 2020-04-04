<?php

/**
 * Allows providing a static answer to a stubbed method call.
 *
 * @author Mathieu Kooiman <mathieuk@gmail.com>
 */
class Phake_Stubber_Answers_LambdaAnswer implements Phake_Stubber_IAnswer
{
    /**
     * @var mixed
     */
    private $answerLambda;

    /**
     * @param mixed $answerLambda
     */
    public function __construct($answerLambda)
    {
        $this->answerLambda = $answerLambda;
    }

    /**
     * Nothing to process
     *
     * @param mixed $answer
     *
     * @return null
     */
    public function processAnswer($answer)
    {
    }

    public function getAnswerCallback($context, $method)
    {
        return $this->answerLambda;
    }
}


