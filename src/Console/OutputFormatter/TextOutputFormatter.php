<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Helpers\NumberFormat;
use TomasVotruba\Lines\Measurements;

final class TextOutputFormatter implements OutputFormatterInterface
{
    private TableStyle $padLeftTableStyle;

    public function __construct(
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        $padLeftTableStyle = new TableStyle();
        $padLeftTableStyle->setPadType(STR_PAD_LEFT);

        $this->padLeftTableStyle = $padLeftTableStyle;
    }

    public function printResult(Measurements $measurements, OutputInterface $output): void
    {
        $this->printFilesAndDirectories($measurements);
        $this->printLinesOfCode($measurements);

        $rows = [];
        $rows[] = ['Min', $measurements->getMinClassLength()];
        $rows[] = ['Max ', $measurements->getMaxClassLength()];
        $rows[] = ['Average ', $measurements->getAverageClassLength()];
        $this->printItemValueTable($rows, 'Class length', 'Lines');

        $rows = [];
        $rows[] = ['Min', $measurements->getMinMethodLength()];
        $rows[] = ['Max', $measurements->getMaxMethodLength()];
        $rows[] = ['Average', $measurements->getAverageMethodLength()];
        $this->printItemValueTable($rows, 'Method length', 'Lines');

        $format = <<<'END'
Size
    Classes
        Lines                       %10d (%.2f%%)
    Functions                               %10d (%.2f%%)
        Average Length                      %10d
    Not in classes or functions             %10d (%.2f%%)

Structure
    Namespaces                              %10d
    Interfaces                              %10d
    Traits                                  %10d
    Classes                                 %10d
        Methods                             %10d
            Scope
                Non-Static                  %10d (%.2f%%)
                Static                      %10d (%.2f%%)
            Visibility
                Public                      %10d (%.2f%%)
                Protected                   %10d (%.2f%%)
                Private                     %10d (%.2f%%)
            Functions                       %10d
                Named                       %10d (%.2f%%)
                Anonymous                   %10d (%.2f%%)
            Constants                       %10d
                Global                      %10d (%.2f%%)
                Class                       %10d (%.2f%%)
                Public                      %10d (%.2f%%)
                Non-Public                  %10d (%.2f%%)
END;

        $result = sprintf(
            $format,
            $measurements->getClassLines(),
            $measurements->getClassLinesRelative(),

            // functions
            $measurements->getFunctionLines(),
            $measurements->getFunctionLinesRelative(),
            $measurements->getAverageFunctionLength(),

            // non-class & non-function code
            $measurements->getNotInClassesOrFunctions(),
            $measurements->getNotInClassesOrFunctionsRelative(),

            // elements
            $measurements->getNamespaces(),
            $measurements->getInterfaces(),
            $measurements->getTraits(),
            $measurements->getClasses(),

            // methods
            $methods = $measurements->getMethods(),
            $nonStaticMethods = $measurements->getNonStaticMethods(),
            $methods > 0 ? ($nonStaticMethods / $methods) * 100 : 0,
            $staticMethods = $measurements->getStaticMethods(),
            $methods > 0 ? ($staticMethods / $methods) * 100 : 0,
            $publicMethods = $measurements->getPublicMethods(),
            $methods > 0 ? ($publicMethods / $methods) * 100 : 0,
            $protectedMethods = $measurements->getProtectedMethods(),
            $methods > 0 ? ($protectedMethods / $methods) * 100 : 0,
            $privateMethods = $measurements->getPrivateMethods(),
            $methods > 0 ? ($privateMethods / $methods) * 100 : 0,

            // functions
            $functions = $measurements->getFunctionCount(),
            $namedFunctions = $measurements->getNamedFunctionCount(),
            $functions > 0 ? ($namedFunctions / $functions) * 100 : 0,
            $anonymousFunctions = $measurements->getAnonymousFunctionCount(),
            $functions > 0 ? ($anonymousFunctions / $functions) * 100 : 0,

            // constants
            $constants = $measurements->getConstantCount(),
            $globalConstants = $measurements->getGlobalConstantCount(),
            $constants > 0 ? ($globalConstants / $constants) * 100 : 0,
            $classConstants = $measurements->getClassConstants(),
            $constants > 0 ? ($classConstants / $constants) * 100 : 0,
            $publicClassConstants = $measurements->getPublicClassConstants(),
            $classConstants > 0 ? ($publicClassConstants / $classConstants) * 100 : 0,
            $nonPublicClassConstants = $measurements->getNonPublicClassConstants(),
            $classConstants > 0 ? ($nonPublicClassConstants / $classConstants) * 100 : 0
        );

        //        $output->writeln($result);
    }

    private function printFilesAndDirectories(Measurements $measurements): void
    {
        $rows = [['Directories', $measurements->getDirectories()], ['Files', $measurements->getFiles()]];

        $this->printItemValueTable($rows, 'Metric', 'Count');
    }

    private function printLinesOfCode(Measurements $measurements): void
    {
        $tableRows = [
            [
                'Comments',
                NumberFormat::pretty($measurements->getCommentLines()),
                $measurements->getCommentLinesRelative() . ' %',
            ],

            [
                'Code',
                NumberFormat::pretty($measurements->getNonCommentLines()),
                $measurements->getNonCommentLinesRelative() . ' %',
            ],

            [new TableSeparator(), new TableSeparator(), new TableSeparator()],

            [
                '<options=bold>Total</>',
                '<options=bold>' . NumberFormat::pretty($measurements->getLines()) . '</>',
                '<options=bold>100.0 %</>',
            ],
        ];

        $this->symfonyStyle->createTable()
            ->setColumnWidth(0, 30)
            ->setColumnWidth(1, 8)
            ->setColumnWidth(2, 7)
            ->setHeaders(['Lines of code', 'Count', 'Relative'])
            ->setRows($tableRows)
            ->setColumnStyle(1, $this->padLeftTableStyle)
            ->setColumnStyle(2, $this->padLeftTableStyle)
            ->render();

        $this->symfonyStyle->newLine(2);
    }

    /**
     * @param mixed[] $rows
     */
    private function printItemValueTable(array $rows, string $titleHeader, string $countHeader): void
    {
        $this->symfonyStyle->createTable()
            ->setHeaders([$titleHeader, $countHeader])
            ->setColumnWidth(0, 30)
            ->setRows($rows)
            ->setColumnStyle(1, $this->padLeftTableStyle)
            ->render();

        $this->symfonyStyle->newLine();
    }
}
