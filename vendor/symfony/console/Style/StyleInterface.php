<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Style;

/**
 * Output style helpers.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface StyleInterface
{
    /**
     * Formats a command title.
     */
    public function title(string $message) : void;
    /**
     * Formats a section title.
     */
    public function section(string $message) : void;
    /**
     * Formats a list.
     */
    public function listing(array $elements) : void;
    /**
     * Formats informational text.
     * @param string|mixed[] $message
     */
    public function text($message) : void;
    /**
     * Formats a success result bar.
     * @param string|mixed[] $message
     */
    public function success($message) : void;
    /**
     * Formats an error result bar.
     * @param string|mixed[] $message
     */
    public function error($message) : void;
    /**
     * Formats an warning result bar.
     * @param string|mixed[] $message
     */
    public function warning($message) : void;
    /**
     * Formats a note admonition.
     * @param string|mixed[] $message
     */
    public function note($message) : void;
    /**
     * Formats a caution admonition.
     * @param string|mixed[] $message
     */
    public function caution($message) : void;
    /**
     * Formats a table.
     */
    public function table(array $headers, array $rows) : void;
    /**
     * Asks a question.
     *
     * @param (callable(mixed):mixed)|null $validator
     * @return mixed
     */
    public function ask(string $question, ?string $default = null, ?callable $validator = null);
    /**
     * Asks a question with the user input hidden.
     *
     * @param (callable(mixed):mixed)|null $validator
     * @return mixed
     */
    public function askHidden(string $question, ?callable $validator = null);
    /**
     * Asks for confirmation.
     */
    public function confirm(string $question, bool $default = \true) : bool;
    /**
     * Asks a choice question.
     * @param mixed $default
     * @return mixed
     */
    public function choice(string $question, array $choices, $default = null);
    /**
     * Add newline(s).
     */
    public function newLine(int $count = 1) : void;
    /**
     * Starts the progress output.
     */
    public function progressStart(int $max = 0) : void;
    /**
     * Advances the progress output X steps.
     */
    public function progressAdvance(int $step = 1) : void;
    /**
     * Finishes the progress output.
     */
    public function progressFinish() : void;
}
