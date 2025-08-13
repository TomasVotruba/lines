<?php

declare (strict_types=1);
namespace Lines202508\Termwind\Helpers;

use Lines202508\Symfony\Component\Console\Formatter\OutputFormatter;
use Lines202508\Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Lines202508\Symfony\Component\Console\Output\OutputInterface;
use Lines202508\Symfony\Component\Console\Question\Question;
/**
 * @internal
 */
final class QuestionHelper extends SymfonyQuestionHelper
{
    /**
     * {@inheritdoc}
     */
    protected function writePrompt(OutputInterface $output, Question $question) : void
    {
        $text = OutputFormatter::escapeTrailingBackslash($question->getQuestion());
        $output->write($text);
    }
}
