<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Question;

use Lines202606\Symfony\Component\Console\Exception\InvalidArgumentException;
/**
 * Represents a question that accepts file input (paste or path).
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class FileQuestion extends Question
{
    /**
     * @var bool
     */
    private $allowPaste = \true;
    /**
     * @var bool
     */
    private $allowPath = \true;
    public function __construct(string $question, bool $allowPaste = \true, bool $allowPath = \true)
    {
        $this->allowPaste = $allowPaste;
        $this->allowPath = $allowPath;
        parent::__construct($question);
        if (!$allowPaste && !$allowPath) {
            throw new InvalidArgumentException('At least one of allowPaste or allowPath must be true.');
        }
        $this->setTrimmable(\false);
    }
    public function isPasteAllowed() : bool
    {
        return $this->allowPaste;
    }
    public function isPathAllowed() : bool
    {
        return $this->allowPath;
    }
}
