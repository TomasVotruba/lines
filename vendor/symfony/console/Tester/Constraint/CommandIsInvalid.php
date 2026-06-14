<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Tester\Constraint;

use Lines202606\PHPUnit\Framework\Constraint\Constraint;
use Lines202606\Symfony\Component\Console\Command\Command;
final class CommandIsInvalid extends Constraint
{
    public function toString() : string
    {
        return 'is invalid';
    }
    protected function matches($other) : bool
    {
        return Command::INVALID === $other;
    }
    protected function failureDescription($other) : string
    {
        return 'the command ' . $this->toString();
    }
    protected function additionalFailureDescription($other) : string
    {
        $mapping = [Command::SUCCESS => 'Command was successful.', Command::FAILURE => 'Command failed.'];
        return $mapping[$other] ?? \sprintf('Command returned exit status %d.', $other);
    }
}
