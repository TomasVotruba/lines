<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\EventListener;

use Lines202606\Symfony\Component\Console\ConsoleEvents;
use Lines202606\Symfony\Component\Console\Event\QuestionAnsweredEvent;
use Lines202606\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lines202606\Symfony\Component\Validator\Validator\ValidatorInterface;
/**
 * Validates Question answers (user input) using the Validator component.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class ValidateQuestionInputListener implements EventSubscriberInterface
{
    /**
     * @readonly
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }
    public function onQuestionAnswered(QuestionAnsweredEvent $event) : void
    {
        $violations = $this->validator->validate($event->value, $event->constraints);
        foreach ($violations as $violation) {
            $event->addViolation($violation->getMessage());
        }
    }
    public static function getSubscribedEvents() : array
    {
        return [ConsoleEvents::QUESTION_ANSWERED => 'onQuestionAnswered'];
    }
}
