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

use Lines202606\Symfony\Component\Console\Command\Command;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Console\Output\OutputInterface;
final class ConsoleAlarmEvent extends ConsoleEvent
{
    /**
     * @var int|false
     */
    private $exitCode = 0;
    /**
     * @param int|false $exitCode
     */
    public function __construct(Command $command, InputInterface $input, OutputInterface $output, $exitCode = 0)
    {
        $this->exitCode = $exitCode;
        parent::__construct($command, $input, $output);
    }
    public function setExitCode(int $exitCode) : void
    {
        if ($exitCode < 0 || $exitCode > 255) {
            throw new \InvalidArgumentException('Exit code must be between 0 and 255.');
        }
        $this->exitCode = $exitCode;
    }
    public function abortExit() : void
    {
        $this->exitCode = \false;
    }
    /**
     * @return int|false
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }
}
