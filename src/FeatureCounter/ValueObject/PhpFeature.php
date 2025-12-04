<?php

declare (strict_types=1);
namespace Lines202512\TomasVotruba\Lines\FeatureCounter\ValueObject;

final class PhpFeature
{
    /**
     * @readonly
     * @var string
     */
    private $phpVersion;
    /**
     * @readonly
     * @var string
     */
    private $name;
    /**
     * @var callable
     */
    private $nodeTrigger;
    /**
     * @var int
     */
    private $count = 0;
    /**
     * @parma PhpVersion::*
     * @param callable $nodeTrigger
     */
    public function __construct(string $phpVersion, string $name, $nodeTrigger, int $count = 0)
    {
        $this->phpVersion = $phpVersion;
        $this->name = $name;
        $this->nodeTrigger = $nodeTrigger;
        $this->count = $count;
    }
    public function getPhpVersion() : string
    {
        return $this->phpVersion;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getNodeTrigger() : callable
    {
        return $this->nodeTrigger;
    }
    public function increaseCount() : void
    {
        $this->count++;
    }
    public function getCount() : int
    {
        return $this->count;
    }
}
