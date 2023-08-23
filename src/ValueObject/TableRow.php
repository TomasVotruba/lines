<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines\ValueObject;

/**
 * @api used in templates
 */
final class TableRow
{
    /**
     * @readonly
     * @var string
     */
    private $name;
    /**
     * @readonly
     * @var string
     */
    private $count;
    /**
     * @readonly
     * @var string|null
     */
    private $percent;
    /**
     * @readonly
     * @var bool
     */
    private $isChild;
    public function __construct(string $name, string $count, ?string $percent, bool $isChild)
    {
        $this->name = $name;
        $this->count = $count;
        $this->percent = $percent;
        $this->isChild = $isChild;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getCount() : string
    {
        return $this->count;
    }
    public function getPercent() : ?string
    {
        return $this->percent;
    }
    public function isChild() : bool
    {
        return $this->isChild;
    }
}
