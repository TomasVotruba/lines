<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202412\Symfony\Component\Console\Helper;

use Lines202412\Symfony\Component\Console\Input\InputAwareInterface;
use Lines202412\Symfony\Component\Console\Input\InputInterface;
/**
 * An implementation of InputAwareInterface for Helpers.
 *
 * @author Wouter J <waldio.webdesign@gmail.com>
 */
abstract class InputAwareHelper extends Helper implements InputAwareInterface
{
    protected $input;
    /**
     * @return void
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }
}