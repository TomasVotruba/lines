<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Tests\Console;

use Entropy\Console\Output\OutputColorizer;
use Entropy\Console\Output\OutputPrinter;
use PHPUnit\Framework\TestCase;
use TomasVotruba\Lines\Console\ConsoleTable;

final class ConsoleTableTest extends TestCase
{
    private ConsoleTable $consoleTable;

    protected function setUp(): void
    {
        $this->consoleTable = new ConsoleTable(new OutputPrinter(new OutputColorizer()));
    }

    public function testAlignsColumnsToWidestCell(): void
    {
        $lines = $this->consoleTable->createTableLines(
            ['Name', 'Count'],
            [['Directories', '12'], ['Files', '345']],
        );

        $this->assertSame([
            ' ------------- -------',
            '  <fg=green>Name</>          <fg=green>Count</>',
            ' ------------- -------',
            '  Directories   12',
            '  Files         345',
            ' ------------- -------',
        ], $lines);
    }

    public function testRendersSeparatorRow(): void
    {
        $lines = $this->consoleTable->createTableLines(
            ['A', 'B'],
            [['1', '2'], ConsoleTable::SEPARATOR, ['3', '4']],
        );

        $this->assertSame([
            ' --- ---',
            '  <fg=green>A</>   <fg=green>B</>',
            ' --- ---',
            '  1   2',
            ' --- ---',
            '  3   4',
            ' --- ---',
        ], $lines);
    }

    public function testIgnoresColorTagsWhenMeasuringWidth(): void
    {
        $lines = $this->consoleTable->createTableLines(['Version'], [['<fg=yellow>8.4</>'], ['7.4']]);

        // both value rows pad to the "Version" header width (7), regardless of color tags
        $this->assertSame([
            ' ---------',
            '  <fg=green>Version</>',
            ' ---------',
            '  <fg=yellow>8.4</>',
            '  7.4',
            ' ---------',
        ], $lines);
    }
}
