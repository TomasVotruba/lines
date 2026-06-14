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

use Lines202606\Symfony\Component\Console\Exception\InvalidArgumentException;
use Lines202606\Symfony\Component\Console\Exception\RuntimeException;
use Lines202606\Symfony\Component\Console\Formatter\OutputFormatter;
use Lines202606\Symfony\Component\Console\Helper\Helper;
use Lines202606\Symfony\Component\Console\Helper\OutputWrapper;
use Lines202606\Symfony\Component\Console\Helper\ProgressBar;
use Lines202606\Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Lines202606\Symfony\Component\Console\Helper\Table;
use Lines202606\Symfony\Component\Console\Helper\TableCell;
use Lines202606\Symfony\Component\Console\Helper\TableSeparator;
use Lines202606\Symfony\Component\Console\Helper\TreeHelper;
use Lines202606\Symfony\Component\Console\Helper\TreeNode;
use Lines202606\Symfony\Component\Console\Helper\TreeStyle;
use Lines202606\Symfony\Component\Console\Input\File\InputFile;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Console\Output\ConsoleOutputInterface;
use Lines202606\Symfony\Component\Console\Output\ConsoleSectionOutput;
use Lines202606\Symfony\Component\Console\Output\OutputInterface;
use Lines202606\Symfony\Component\Console\Output\TrimmedBufferOutput;
use Lines202606\Symfony\Component\Console\Question\ChoiceQuestion;
use Lines202606\Symfony\Component\Console\Question\ConfirmationQuestion;
use Lines202606\Symfony\Component\Console\Question\FileQuestion;
use Lines202606\Symfony\Component\Console\Question\Question;
use Lines202606\Symfony\Component\Console\Terminal;
use Lines202606\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
/**
 * Output decorator helpers for the Symfony Style Guide.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class SymfonyStyle extends OutputStyle
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;
    /**
     * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface|null
     */
    private $dispatcher;
    public const MAX_LINE_LENGTH = 120;
    /**
     * @var \Symfony\Component\Console\Helper\SymfonyQuestionHelper
     */
    private $questionHelper;
    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    private $progressBar;
    /**
     * @var int
     */
    private $lineLength;
    /**
     * @var \Symfony\Component\Console\Output\TrimmedBufferOutput
     */
    private $bufferedOutput;
    public function __construct(InputInterface $input, OutputInterface $output, ?EventDispatcherInterface $dispatcher = null)
    {
        $this->input = $input;
        $this->output = $output;
        $this->dispatcher = $dispatcher;
        $this->bufferedOutput = new TrimmedBufferOutput(\DIRECTORY_SEPARATOR === '\\' ? 4 : 2, $output->getVerbosity(), \false, clone $output->getFormatter());
        // Windows cmd wraps lines as soon as the terminal width is reached, whether there are following chars or not.
        $width = (new Terminal())->getWidth() ?: self::MAX_LINE_LENGTH;
        $this->lineLength = \min($width - (int) (\DIRECTORY_SEPARATOR === '\\'), self::MAX_LINE_LENGTH);
        parent::__construct($output);
    }
    /**
     * Formats a message as a block of text.
     * @param string|mixed[] $messages
     */
    public function block($messages, ?string $type = null, ?string $style = null, string $prefix = ' ', bool $padding = \false, bool $escape = \true) : void
    {
        $messages = \is_array($messages) ? \array_values($messages) : [$messages];
        $this->autoPrependBlock();
        $this->writeln($this->createBlock($messages, $type, $style, $prefix, $padding, $escape));
        $this->newLine();
    }
    public function title(string $message) : void
    {
        $this->autoPrependBlock();
        $this->writeln([\sprintf('<comment>%s</>', OutputFormatter::escapeTrailingBackslash($message)), \sprintf('<comment>%s</>', \str_repeat('=', Helper::width(Helper::removeDecoration($this->getFormatter(), $message))))]);
        $this->newLine();
    }
    public function section(string $message) : void
    {
        $this->autoPrependBlock();
        $this->writeln([\sprintf('<comment>%s</>', OutputFormatter::escapeTrailingBackslash($message)), \sprintf('<comment>%s</>', \str_repeat('-', Helper::width(Helper::removeDecoration($this->getFormatter(), $message))))]);
        $this->newLine();
    }
    public function listing(array $elements) : void
    {
        $this->autoPrependText();
        $elements = \array_map(static function ($element) {
            return \sprintf(' * %s', $element);
        }, $elements);
        $this->writeln($elements);
        $this->newLine();
    }
    /**
     * @param string|mixed[] $message
     */
    public function text($message) : void
    {
        $this->autoPrependText();
        $messages = \is_array($message) ? \array_values($message) : [$message];
        foreach ($messages as $message) {
            $this->writeln(\sprintf(' %s', $message));
        }
    }
    /**
     * Formats a command comment.
     * @param string|mixed[] $message
     */
    public function comment($message) : void
    {
        $this->block($message, null, null, '<fg=default;bg=default> // </>', \false, \false);
    }
    /**
     * @param string|mixed[] $message
     */
    public function success($message) : void
    {
        $this->block($message, 'OK', 'fg=black;bg=green', ' ', \true);
    }
    /**
     * @param string|mixed[] $message
     */
    public function error($message) : void
    {
        $this->block($message, 'ERROR', 'fg=white;bg=red', ' ', \true);
    }
    /**
     * @param string|mixed[] $message
     */
    public function warning($message) : void
    {
        $this->block($message, 'WARNING', 'fg=black;bg=yellow', ' ', \true);
    }
    /**
     * @param string|mixed[] $message
     */
    public function note($message) : void
    {
        $this->block($message, 'NOTE', 'fg=yellow', ' ! ');
    }
    /**
     * Formats an info message.
     * @param string|mixed[] $message
     */
    public function info($message) : void
    {
        $this->block($message, 'INFO', 'fg=green', ' ', \true);
    }
    /**
     * @param string|mixed[] $message
     */
    public function caution($message) : void
    {
        $this->block($message, 'CAUTION', 'fg=white;bg=red', ' ! ', \true);
    }
    /**
     * Formats a message as an outlined block of text.
     *
     * Unlike block(), this renders colored borders instead of colored backgrounds,
     * improving readability across terminal color schemes.
     * @param string|mixed[] $messages
     */
    public function outlineBlock($messages, ?string $type = null, ?string $style = null, bool $padding = \true, bool $escape = \true) : void
    {
        $messages = \is_array($messages) ? \array_values($messages) : [$messages];
        $this->autoPrependBlock();
        $this->writeln($this->createOutlineBlock($messages, $type, $style, $padding, $escape));
        $this->newLine();
    }
    /**
     * @param string|mixed[] $message
     */
    public function outlineSuccess($message) : void
    {
        $this->outlineBlock($message, '✅ Success', 'fg=green');
    }
    /**
     * @param string|mixed[] $message
     */
    public function outlineError($message) : void
    {
        $this->outlineBlock($message, '❌ Error', 'fg=red');
    }
    /**
     * @param string|mixed[] $message
     */
    public function outlineWarning($message) : void
    {
        $this->outlineBlock($message, '⚠️ Warning', 'fg=yellow');
    }
    /**
     * @param string|mixed[] $message
     */
    public function outlineNote($message) : void
    {
        $this->outlineBlock($message, '📝 Note', 'fg=blue');
    }
    /**
     * @param string|mixed[] $message
     */
    public function outlineInfo($message) : void
    {
        $this->outlineBlock($message, 'ℹ️ Info', 'fg=green');
    }
    /**
     * @param string|mixed[] $message
     */
    public function outlineCaution($message) : void
    {
        $this->outlineBlock($message, '🚨 Caution', 'fg=red');
    }
    public function table(array $headers, array $rows) : void
    {
        $this->createTable()->setHeaders($headers)->setRows($rows)->render();
        $this->newLine();
    }
    /**
     * Formats a horizontal table.
     */
    public function horizontalTable(array $headers, array $rows) : void
    {
        $this->createTable()->setHorizontal(\true)->setHeaders($headers)->setRows($rows)->render();
        $this->newLine();
    }
    /**
     * Formats a list of key/value horizontally.
     *
     * Each row can be one of:
     * * 'A title'
     * * ['key' => 'value']
     * * new TableSeparator()
     * @param string|mixed[]|\Symfony\Component\Console\Helper\TableSeparator ...$list
     */
    public function definitionList(...$list) : void
    {
        $headers = [];
        $row = [];
        foreach ($list as $value) {
            if ($value instanceof TableSeparator) {
                $headers[] = $value;
                $row[] = $value;
                continue;
            }
            if (\is_string($value)) {
                $headers[] = new TableCell($value, ['colspan' => 2]);
                $row[] = null;
                continue;
            }
            if (!\is_array($value)) {
                throw new InvalidArgumentException('Value should be an array, string, or an instance of TableSeparator.');
            }
            $headers[] = \key($value);
            $row[] = \current($value);
        }
        $this->horizontalTable($headers, [$row]);
    }
    /**
     * @return mixed
     */
    public function ask(string $question, ?string $default = null, ?callable $validator = null)
    {
        $question = new Question($question, $default);
        $question->setValidator($validator);
        return $this->askQuestion($question);
    }
    /**
     * @return mixed
     */
    public function askHidden(string $question, ?callable $validator = null)
    {
        $question = new Question($question);
        $question->setHidden(\true);
        $question->setValidator($validator);
        return $this->askQuestion($question);
    }
    public function confirm(string $question, bool $default = \true) : bool
    {
        return $this->askQuestion(new ConfirmationQuestion($question, $default));
    }
    /**
     * @param mixed $default
     * @return mixed
     */
    public function choice(string $question, array $choices, $default = null, bool $multiSelect = \false)
    {
        if (null !== $default) {
            $values = \array_flip($choices);
            $default = $values[$default] ?? $default;
        }
        $questionChoice = new ChoiceQuestion($question, $choices, $default);
        $questionChoice->setMultiselect($multiSelect);
        return $this->askQuestion($questionChoice);
    }
    public function askFile(string $question) : ?InputFile
    {
        return $this->askQuestion(new FileQuestion($question));
    }
    /**
     * @param string|null $format
     */
    public function progressStart(int $max = 0) : void
    {
        $format = 2 <= \func_num_args() ? \func_get_arg(1) : null;
        $this->progressBar = $this->createProgressBar($max, $format);
        $this->progressBar->start();
    }
    public function progressAdvance(int $step = 1) : void
    {
        $this->getProgressBar()->advance($step);
    }
    public function progressFinish() : void
    {
        $this->getProgressBar()->finish();
        $this->newLine(2);
        unset($this->progressBar);
    }
    /**
     * @param string|null $format
     */
    public function createProgressBar(int $max = 0) : ProgressBar
    {
        $format = 2 <= \func_num_args() ? \func_get_arg(1) : null;
        $progressBar = parent::createProgressBar($max);
        if ('\\' !== \DIRECTORY_SEPARATOR || 'Hyper' === \getenv('TERM_PROGRAM')) {
            $progressBar->setEmptyBarCharacter('░');
            // light shade character \u2591
            $progressBar->setProgressCharacter('');
            $progressBar->setBarCharacter('▓');
            // dark shade character \u2593
        }
        if (null !== $format) {
            $progressBar->setFormat($format);
        }
        return $progressBar;
    }
    /**
     * @see ProgressBar::iterate()
     *
     * @template TKey
     * @template TValue
     *
     * @param iterable<TKey, TValue> $iterable
     * @param int|null               $max      Number of steps to complete the bar (0 if indeterminate), if null it will be inferred from $iterable
     * @param string|null            $format   A ProgressBar format string (e.g. ' %current%/%max% [%bar%] %memory:6s%'); null uses the default format
     *
     * @return iterable<TKey, TValue>
     */
    public function progressIterate(iterable $iterable, ?int $max = null) : iterable
    {
        $format = 3 <= \func_num_args() ? \func_get_arg(2) : null;
        yield from $this->createProgressBar(0, $format)->iterate($iterable, $max);
        $this->newLine(2);
    }
    /**
     * @return mixed
     */
    public function askQuestion(Question $question)
    {
        if ($this->input->isInteractive()) {
            $this->autoPrependBlock();
        }
        $this->questionHelper = $this->questionHelper ?? new SymfonyQuestionHelper($this->dispatcher);
        $answer = $this->questionHelper->ask($this->input, $this, $question);
        if ($this->input->isInteractive()) {
            if ($this->output instanceof ConsoleSectionOutput) {
                // add the new line of the `return` to submit the input to ConsoleSectionOutput, because ConsoleSectionOutput is holding all it's lines.
                // this is relevant when a `ConsoleSectionOutput::clear` is called.
                $this->output->addNewLineOfInputSubmit();
            }
            $this->newLine();
            $this->bufferedOutput->write("\n");
        }
        return $answer;
    }
    /**
     * @param string|iterable $messages
     */
    public function writeln($messages, int $type = self::OUTPUT_NORMAL) : void
    {
        if (!\is_iterable($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            parent::writeln($message, $type);
            $this->writeBuffer($message, \true, $type);
        }
    }
    /**
     * @param string|iterable $messages
     */
    public function write($messages, bool $newline = \false, int $type = self::OUTPUT_NORMAL) : void
    {
        if (!\is_iterable($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            parent::write($message, $newline, $type);
            $this->writeBuffer($message, $newline, $type);
        }
    }
    public function newLine(int $count = 1) : void
    {
        parent::newLine($count);
        $this->bufferedOutput->write(\str_repeat("\n", $count));
    }
    /**
     * Returns a new instance which makes use of stderr if available.
     */
    public function getErrorStyle() : self
    {
        return new self($this->input, $this->getErrorOutput());
    }
    public function createTable() : Table
    {
        $output = $this->output instanceof ConsoleOutputInterface ? $this->output->section() : $this->output;
        $style = clone Table::getStyleDefinition('symfony-style-guide');
        $style->setCellHeaderFormat('<info>%s</info>');
        return (new Table($output))->setStyle($style);
    }
    private function getProgressBar() : ProgressBar
    {
        if (!isset($this->progressBar)) {
            throw new RuntimeException('The ProgressBar is not started.');
        }
        return $this->progressBar;
    }
    /**
     * @param iterable<string, iterable|string|TreeNode> $nodes
     */
    public function tree(iterable $nodes, string $root = '') : void
    {
        $this->createTree($nodes, $root)->render();
    }
    /**
     * @param iterable<string, iterable|string|TreeNode> $nodes
     */
    public function createTree(iterable $nodes, string $root = '') : TreeHelper
    {
        $output = $this->output instanceof ConsoleOutputInterface ? $this->output->section() : $this->output;
        return TreeHelper::createTree($output, $root, $nodes, TreeStyle::default());
    }
    private function autoPrependBlock() : void
    {
        $chars = \substr(\str_replace(\PHP_EOL, "\n", $this->bufferedOutput->fetch()), -2);
        if (!isset($chars[0])) {
            $this->newLine();
            // empty history, so we should start with a new line.
            return;
        }
        // Prepend new line for each non LF chars (This means no blank line was output before)
        $this->newLine(2 - \substr_count($chars, "\n"));
    }
    private function autoPrependText() : void
    {
        $fetched = $this->bufferedOutput->fetch();
        // Prepend new line if last char isn't EOL:
        if ($fetched && \substr_compare($fetched, "\n", -\strlen("\n")) !== 0) {
            $this->newLine();
        }
    }
    private function writeBuffer(string $message, bool $newLine, int $type) : void
    {
        // We need to know if the last chars are PHP_EOL
        $this->bufferedOutput->write($message, $newLine, $type);
    }
    private function createBlock(iterable $messages, ?string $type = null, ?string $style = null, string $prefix = ' ', bool $padding = \false, bool $escape = \false) : array
    {
        $indentLength = 0;
        $prefixLength = Helper::width(Helper::removeDecoration($this->getFormatter(), $prefix));
        $lines = [];
        if (null !== $type) {
            $type = \sprintf('[%s] ', $type);
            $indentLength = Helper::width($type);
            $lineIndentation = \str_repeat(' ', $indentLength);
        }
        // wrap and add newlines for each element
        $outputWrapper = new OutputWrapper();
        foreach ($messages as $key => $message) {
            if ($escape) {
                $message = OutputFormatter::escape($message);
            }
            $message = \str_replace("\r\n", "\n", $message);
            $lines = \array_merge($lines, \explode("\n", $outputWrapper->wrap($message, $this->lineLength - $prefixLength - $indentLength, "\n")));
            if (\count($messages) > 1 && $key < \count($messages) - 1) {
                $lines[] = '';
            }
        }
        $firstLineIndex = 0;
        if ($padding && $this->isDecorated()) {
            $firstLineIndex = 1;
            \array_unshift($lines, '');
            $lines[] = '';
        }
        foreach ($lines as $i => &$line) {
            if (null !== $type) {
                $line = $firstLineIndex === $i ? $type . $line : $lineIndentation . $line;
            }
            $line = $prefix . $line;
            $paddingLength = \max($this->lineLength - Helper::width(Helper::removeDecoration($this->getFormatter(), $line)), 0);
            // ECH paints the trailing cells with the current background and CUF moves the cursor past
            // them without writing characters, so most terminals trim them when copying the selection.
            $line .= $style && $this->isDecorated() && 0 < $paddingLength ? \sprintf("\x1b[%1\$dX\x1b[%1\$dC", $paddingLength) : \str_repeat(' ', $paddingLength);
            if ($style) {
                $line = \sprintf('<%s>%s</>', $style, $line);
            }
        }
        return $lines;
    }
    private function createOutlineBlock(array $messages, ?string $type = null, ?string $style = null, bool $padding = \false, bool $escape = \false) : array
    {
        // Line format: ' │ '(3) + content($contentWidth) + ' │'(2) = lineLength
        $contentWidth = $this->lineLength - 5;
        $lines = [];
        $outputWrapper = new OutputWrapper();
        foreach ($messages as $key => $message) {
            if ($escape) {
                $message = OutputFormatter::escape($message);
            }
            $message = \str_replace("\r\n", "\n", $message);
            $lines = \array_merge($lines, \explode("\n", $outputWrapper->wrap($message, $contentWidth, "\n")));
            if (\count($messages) > 1 && $key < \count($messages) - 1) {
                $lines[] = '';
            }
        }
        if ($padding) {
            \array_unshift($lines, '');
            $lines[] = '';
        }
        $result = [];
        // Top border: ' ┌─ Type ────┐' or ' ┌────┐' when no type
        if (null !== $type) {
            $line = ' ┌─ ' . $type . ' ' . \str_repeat('─', \max(0, $this->lineLength - 6 - Helper::width($type))) . '┐';
        } else {
            $line = ' ┌' . \str_repeat('─', \max(0, $this->lineLength - 3)) . '┐';
        }
        $result[] = $style ? \sprintf('<%s>%s</>', $style, $line) : $line;
        foreach ($lines as $line) {
            $lineContentWidth = Helper::width(Helper::removeDecoration($this->getFormatter(), $line));
            $padded = $line . \str_repeat(' ', \max($contentWidth - $lineContentWidth, 0));
            $result[] = $style ? \sprintf('<%s> │ </>%s<%1$s> │</>', $style, $padded) : ' │ ' . $padded . ' │';
        }
        $borderDashes = \str_repeat('─', \max(0, $this->lineLength - 3));
        $line = ' └' . $borderDashes . '┘';
        $result[] = $style ? \sprintf('<%s>%s</>', $style, $line) : $line;
        return $result;
    }
}
