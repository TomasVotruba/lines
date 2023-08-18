<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Console\ViewRenderer;
use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Helpers\NumberFormat;
use TomasVotruba\Lines\Measurements;
use TomasVotruba\Lines\ValueObject\TableView;

final class TextOutputFormatter implements OutputFormatterInterface
{
    public function __construct(
        private readonly ViewRenderer $viewRenderer,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
    }

    public function printMeasurement(Measurements $measurements, bool $isShort): void
    {
        $this->symfonyStyle->newLine();

        $this->printFilesAndDirectories($measurements);
        $this->printLinesOfCode($measurements);

        if ($isShort) {
            $this->symfonyStyle->newLine();

            return;
        }

        $this->printStructure($measurements);
        $this->printMethods($measurements);

        $this->symfonyStyle->newLine();
    }

    private function printFilesAndDirectories(Measurements $measurements): void
    {
        $rows = $this->formatRows([
            ['Directories', $measurements->getDirectoryCount()],
            ['Files', $measurements->getFileCount()],
        ]);

        $tableView = new TableView('Metric', 'Count', $rows);
        $this->viewRenderer->renderTableVIew($tableView);
    }

    private function printLinesOfCode(Measurements $measurements): void
    {
        $rows = $this->formatRows([
            ['Code', $measurements->getNonCommentLines(), $measurements->getNonCommentLinesRelative()],
            ['Comments', $measurements->getCommentLines(), $measurements->getCommentLinesRelative()],
            ['Total', $measurements->getLines(), 100.0],
        ]);

        $tableView = new TableView('Lines of code', 'Count', $rows, true);
        $this->viewRenderer->renderTableVIew($tableView);
    }

    private function printMethods(Measurements $measurements): void
    {
        if ($measurements->getMethodCount() === 0) {
            return;
        }

        $rows = $this->formatRows([
            ['Non-static', $measurements->getNonStaticMethods(), $measurements->getNonStaticMethodsRelative()],
            ['Static', $measurements->getStaticMethods(), $measurements->getStaticMethodsRelative()],
            [],
            ['Public', $measurements->getPublicMethods(), $measurements->getPublicMethodsRelative()],
            ['Protected', $measurements->getProtectedMethods(), $measurements->getProtectedMethodsRelative()],
            ['Private', $measurements->getPrivateMethods(), $measurements->getPrivateMethodsRelative()],
        ]);

        $tableView = new TableView('Methods', 'Count', $rows, true);
        $this->viewRenderer->renderTableVIew($tableView);
    }

    private function printStructure(Measurements $measurements): void
    {
        $rows = $this->formatRows([
            ['Namespaces', $measurements->getNamespaceCount()],
            ['Classes', $measurements->getClassCount()],
            ['* Constants', $measurements->getClassConstantCount(), null, true],
            ['* Methods', $measurements->getMethodCount(), null, true],
            ['Interfaces', $measurements->getInterfaceCount()],
            ['Traits', $measurements->getTraitCount()],
            ['Enums', $measurements->getEnumCount()],
            ['Functions', $measurements->getFunctionCount()],
            ['Global constants', $measurements->getGlobalConstantCount()],
        ]);

        $tableView = new TableView('Structure', 'Count', $rows);
        $this->viewRenderer->renderTableVIew($tableView);
    }

    /**
     * @param array<array{0?: string, 1?: int|float, 2?: float|null, 3?: bool}> $rows
     * @return array<array{}|array{name: string, count: int|float|string, percent: float|string|null, isChild: bool}>>
     */
    private function formatRows(array $rows): array
    {
        return array_map(static function (array $row): array {
            if ($row === []) {
                return [];
            }

            return [
                'name' => $row[0],
                // make big numbers separated with space, e.g. "1 234"
                'count' => NumberFormat::pretty($row[1]),
                // optional float relatives
                'percent' => isset($row[2]) ? NumberFormat::percent($row[2]) : null,
                'isChild' => $row[3] ?? false,
            ];
        }, $rows);
    }
}
