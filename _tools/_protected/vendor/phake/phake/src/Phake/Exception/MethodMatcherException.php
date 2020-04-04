<?php

/**
 * Thrown when a method call doesn't match an expection
 */
class Phake_Exception_MethodMatcherException extends Exception
{
    private $argument;

    /**
     * @param string $message
     * @param Exception $previous
     */
    public function __construct($message = "", Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->argument = 0;
    }

    /**
     * Updates the argument position (used in the argument chain)
     */
    public function incrementArgumentPosition()
    {
        $this->argument++;
    }

    /**
     * Returns the argument's position (0 indexed)
     * @return int
     */
    public function getArgumentPosition()
    {
        return $this->argument;
    }
}