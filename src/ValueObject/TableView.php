<?php

declare (strict_types=1);
namespace Lines202412\TomasVotruba\Lines\ValueObject;

use Lines202412\Webmozart\Assert\Assert;
/**
 * @api used in templates
 */
final class TableView
{
    /**
     * @readonly
     * @var string
     */
    private $title;
    /**
     * @readonly
     * @var string
     */
    private $label;
    /**
     * @var TableRow[]
     * @readonly
     */
    private $tableRows;
    /**
     * @readonly
     * @var bool
     */
    private $shouldIncludeRelative = \false;
    /**
     * @param TableRow[] $tableRows
     */
    public function __construct(string $title, string $label, array $tableRows, bool $shouldIncludeRelative = \false)
    {
        $this->title = $title;
        $this->label = $label;
        $this->tableRows = $tableRows;
        $this->shouldIncludeRelative = $shouldIncludeRelative;
        Assert::allIsInstanceOf($tableRows, TableRow::class);
    }
    public function getTitle() : string
    {
        return $this->title;
    }
    public function getLabel() : string
    {
        return $this->label;
    }
    public function isShouldIncludeRelative() : bool
    {
        return $this->shouldIncludeRelative;
    }
    /**
     * @return TableRow[]
     */
    public function getRows() : array
    {
        return $this->tableRows;
    }
    public function getTemplateFilePath() : string
    {
        return __DIR__ . '/../../views/table.php';
    }
}
