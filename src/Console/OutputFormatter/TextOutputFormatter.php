<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;
use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Helpers\NumberFormat;
use TomasVotruba\Lines\Measurements;
use function Termwind\render;
use function Termwind\renderUsing;

final class TextOutputFormatter implements OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, OutputInterface $output, bool $isShort): void
    {
        renderUsing($output);

        $output->writeln('');

        $this->printFilesAndDirectories($measurements);
        $this->printLinesOfCode($measurements);

        if ($isShort) {
            $output->writeln('');

            return;
        }

        $this->printStructure($measurements);
        $this->printMethods($measurements);

        $output->writeln('');
    }

    private function printFilesAndDirectories(Measurements $measurements): void
    {
        $this->renderView('table', [
            'title' => 'Metric',
            'label' => 'Count',
            'rows' => $this->formatRows([
                ['Directories', $measurements->getDirectoryCount()],
                ['Files', $measurements->getFileCount()],
            ]),
        ]);
    }

    private function printLinesOfCode(Measurements $measurements): void
    {
        $this->renderView('table', [
            'title' => 'Lines of code',
            'label' => 'Count',
            'includeRelative' => true,
            'rows' => $this->formatRows([
                ['Code', $measurements->getNonCommentLines(), $measurements->getNonCommentLinesRelative()],
                ['Comments', $measurements->getCommentLines(), $measurements->getCommentLinesRelative()],
                ['Total', $measurements->getLines(), 100.0],
            ]),
        ]);
    }

    private function printMethods(Measurements $measurements): void
    {
        if ($measurements->getMethodCount() === 0) {
            return;
        }

        $this->renderView('table', [
            'title' => 'Methods',
            'label' => 'Count',
            'includeRelative' => true,
            'rows' => $this->formatRows([
                ['Non-static', $measurements->getNonStaticMethods(), $measurements->getNonStaticMethodsRelative()],
                ['Static', $measurements->getStaticMethods(), $measurements->getStaticMethodsRelative()],
                [],
                ['Public', $measurements->getPublicMethods(), $measurements->getPublicMethodsRelative()],
                ['Protected', $measurements->getProtectedMethods(), $measurements->getProtectedMethodsRelative()],
                ['Private', $measurements->getPrivateMethods(), $measurements->getPrivateMethodsRelative()],
            ]),
        ]);
    }

    private function printStructure(Measurements $measurements): void
    {
        $this->renderView('table', [
            'title' => 'Structure',
            'label' => 'Count',
            'rows' => $this->formatRows([
                ['Namespaces', $measurements->getNamespaceCount()],
                ['Classes', $measurements->getClassCount()],
                ['* Constants', $measurements->getClassConstantCount(), null, true],
                ['* Methods', $measurements->getMethodCount(), null, true],
                ['Interfaces', $measurements->getInterfaceCount()],
                ['Traits', $measurements->getTraitCount()],
                ['Enums', $measurements->getEnumCount()],
                ['Functions', $measurements->getFunctionCount()],
                ['Global constants', $measurements->getGlobalConstantCount()],
            ]),
        ]);
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

    /**
     * @param array<string, mixed> $data
     */
    private function renderView(string $view, array $data): void
    {
        extract($data);

        ob_start();

        include __DIR__ . sprintf('/../views/%s.php', $view);

        render((string) ob_get_contents());

        ob_end_clean();
    }
}
