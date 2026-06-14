<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Output;

use Lines202606\Symfony\Component\Console\Exception\LogicException;
use Lines202606\Symfony\Component\Console\Formatter\OutputFormatterInterface;
/**
 * @internal
 */
final class CombinedOutput implements OutputInterface
{
    /**
     * @var OutputInterface[]
     */
    private $outputs;
    /**
     * @param OutputInterface[] $outputs
     */
    public function __construct(array $outputs)
    {
        $this->outputs = $outputs;
        if (!$outputs) {
            throw new LogicException('Expected at least one output.');
        }
    }
    /**
     * @param iterable|string $messages
     */
    public function write($messages, bool $newline = \false, int $options = 0) : void
    {
        foreach ($this->outputs as $output) {
            $output->write(...\func_get_args());
        }
    }
    /**
     * @param iterable|string $messages
     */
    public function writeln($messages, int $options = 0) : void
    {
        foreach ($this->outputs as $output) {
            $output->writeln(...\func_get_args());
        }
    }
    public function setVerbosity(int $level) : void
    {
        foreach ($this->outputs as $output) {
            $output->setVerbosity($level);
        }
    }
    public function getVerbosity() : int
    {
        \reset($this->outputs);
        return $this->outputs[\key($this->outputs)]->getVerbosity();
    }
    public function isSilent() : bool
    {
        \reset($this->outputs);
        return $this->outputs[\key($this->outputs)]->isSilent();
    }
    public function isQuiet() : bool
    {
        \reset($this->outputs);
        return $this->outputs[\key($this->outputs)]->isQuiet();
    }
    public function isVerbose() : bool
    {
        \reset($this->outputs);
        return $this->outputs[\key($this->outputs)]->isVerbose();
    }
    public function isVeryVerbose() : bool
    {
        \reset($this->outputs);
        return $this->outputs[\key($this->outputs)]->isVeryVerbose();
    }
    public function isDebug() : bool
    {
        \reset($this->outputs);
        return $this->outputs[\key($this->outputs)]->isDebug();
    }
    public function setDecorated(bool $decorated) : void
    {
        foreach ($this->outputs as $output) {
            $output->setDecorated($decorated);
        }
    }
    public function isDecorated() : bool
    {
        \reset($this->outputs);
        return $this->outputs[\key($this->outputs)]->isDecorated();
    }
    public function setFormatter(OutputFormatterInterface $formatter) : void
    {
        foreach ($this->outputs as $output) {
            $output->setFormatter($formatter);
        }
    }
    public function getFormatter() : OutputFormatterInterface
    {
        \reset($this->outputs);
        return $this->outputs[\key($this->outputs)]->getFormatter();
    }
}
