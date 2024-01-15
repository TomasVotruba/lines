<?php

declare (strict_types=1);
namespace Lines202401\Termwind;

use ReflectionClass;
use Lines202401\Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Lines202401\Symfony\Component\Console\Input\ArgvInput;
use Lines202401\Symfony\Component\Console\Input\StreamableInputInterface;
use Lines202401\Symfony\Component\Console\Question\Question as SymfonyQuestion;
use Lines202401\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202401\Termwind\Helpers\QuestionHelper;
/**
 * @internal
 */
final class Question
{
    /**
     * The streamable input to receive the input from the user.
     * @var \Symfony\Component\Console\Input\StreamableInputInterface|null
     */
    private static $streamableInput;
    /**
     * An instance of Symfony's question helper.
     * @var \Symfony\Component\Console\Helper\SymfonyQuestionHelper
     */
    private $helper;
    public function __construct(SymfonyQuestionHelper $helper = null)
    {
        $this->helper = $helper ?? new QuestionHelper();
    }
    /**
     * Sets the streamable input implementation.
     */
    public static function setStreamableInput(?\Lines202401\Symfony\Component\Console\Input\StreamableInputInterface $streamableInput) : void
    {
        self::$streamableInput = $streamableInput ?? new ArgvInput();
    }
    /**
     * Gets the streamable input implementation.
     */
    public static function getStreamableInput() : StreamableInputInterface
    {
        return self::$streamableInput = self::$streamableInput ?? new ArgvInput();
    }
    /**
     * Renders a prompt to the user.
     *
     * @param  iterable<array-key, string>|null  $autocomplete
     * @return mixed
     */
    public function ask(string $question, iterable $autocomplete = null)
    {
        $html = (new HtmlRenderer())->parse($question)->toString();
        $question = new SymfonyQuestion($html);
        if ($autocomplete !== null) {
            $question->setAutocompleterValues($autocomplete);
        }
        $output = Termwind::getRenderer();
        if ($output instanceof SymfonyStyle) {
            $property = (new ReflectionClass(SymfonyStyle::class))->getProperty('questionHelper');
            $property->setAccessible(\true);
            $currentHelper = $property->isInitialized($output) ? $property->getValue($output) : new SymfonyQuestionHelper();
            $property->setValue($output, new QuestionHelper());
            try {
                return $output->askQuestion($question);
            } finally {
                $property->setValue($output, $currentHelper);
            }
        }
        return $this->helper->ask(self::getStreamableInput(), Termwind::getRenderer(), $question);
    }
}
