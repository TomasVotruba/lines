<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Event;

use Lines202606\Symfony\Contracts\EventDispatcher\Event;
/**
 * Event dispatched when constraint validation is needed for a question.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class QuestionAnsweredEvent extends Event
{
    /**
     * @readonly
     * @var mixed
     */
    public $value;
    /**
     * @readonly
     * @var mixed[]
     */
    public $constraints;
    /**
     * @var mixed[]
     */
    private $violations = [];
    /**
     * @param mixed $value
     */
    public function __construct($value, array $constraints)
    {
        $this->value = $value;
        $this->constraints = $constraints;
    }
    public function addViolation(string $message) : void
    {
        $this->violations[] = $message;
    }
    public function getViolations() : array
    {
        return $this->violations;
    }
    public function hasViolations() : bool
    {
        return (bool) $this->violations;
    }
}
