<?php

declare (strict_types=1);
namespace Lines202605\Entropy\Attributes;

use Attribute;
use Lines202605\PHPUnit\Framework\TestCase;
#[Attribute(Attribute::TARGET_CLASS)]
final class RelatedTest
{
    /**
     * @param class-string<TestCase> $testClass
     */
    public function __construct(string $testClass)
    {
    }
}
