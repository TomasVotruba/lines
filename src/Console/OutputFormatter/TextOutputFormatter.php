<?php

declare (strict_types=1);
namespace Lines202307\TomasVotruba\Lines\Console\OutputFormatter;

use Lines202307\Symfony\Component\Console\Helper\TableSeparator;
use Lines202307\Symfony\Component\Console\Output\OutputInterface;
use Lines202307\TomasVotruba\Lines\Console\TablePrinter;
use Lines202307\TomasVotruba\Lines\Contract\OutputFormatterInterface;
use Lines202307\TomasVotruba\Lines\Measurements;
final class TextOutputFormatter implements OutputFormatterInterface
{
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Console\TablePrinter
     */
    private $tablePrinter;
    public function __construct(TablePrinter $tablePrinter)
    {
        $this->tablePrinter = $tablePrinter;
    }
    public function printMeasurement(Measurements $measurements, OutputInterface $output) : void
    {
        // newline
        $output->writeln('');
        $this->printFilesAndDirectories($measurements);
        $this->printLinesOfCode($measurements);
        $this->tablePrinter->printItemValueTable([['Class max', $measurements->getMaxClassLength()], ['Class average', $measurements->getAverageClassLength()], ['Method max', $measurements->getMaxMethodLength()], ['Method average', $measurements->getAverageMethodLength()]], 'Length Stats', 'Lines');
        $this->tablePrinter->printItemValueTable([['Classes', $measurements->getClassLines(), $measurements->getClassLinesRelative()], ['Functions', $measurements->getFunctionLines(), $measurements->getFunctionLinesRelative()], ['Not in classes/functions', $measurements->getNotInClassesOrFunctions(), $measurements->getNotInClassesOrFunctionsRelative()]], 'Classes vs functions vs rest', 'Lines', \true);
        $this->tablePrinter->printItemValueTable([['Namespaces', $measurements->getNamespaces()], ['Classes', $measurements->getClassCount()], ['Interfaces', $measurements->getInterfaceCount()], ['Traits', $measurements->getTraitCount()], ['Enums', $measurements->getEnumCount()], ['Constants', $measurements->getConstantCount()], ['Methods', $measurements->getMethodCount()], ['Functions', $measurements->getFunctionCount()]], 'Structure', 'Count');
        if ($measurements->getMethodCount() !== 0) {
            $this->tablePrinter->printItemValueTable([['Non-static', $measurements->getNonStaticMethods(), $measurements->getNonStaticMethodsRelative()], ['Static', $measurements->getStaticMethods(), $measurements->getStaticMethodsRelative()], new TableSeparator(), ['Public', $measurements->getPublicMethods(), $measurements->getPublicMethodsRelative()], ['Protected', $measurements->getProtectedMethods(), $measurements->getProtectedMethodsRelative()], ['Private', $measurements->getPrivateMethods(), $measurements->getPrivateMethodsRelative()]], 'Methods', 'Count', \true);
        }
        if ($measurements->getConstantCount() !== 0) {
            $constantsRows = [['Global', $measurements->getGlobalConstantCount(), $measurements->getGlobalConstantCountRelative()], ['Class', $measurements->getClassConstants(), $measurements->getClassConstantCountRelative()]];
            if ($measurements->getClassConstants() !== 0) {
                $constantsRows[] = new TableSeparator();
                $constantsRows[] = ['Non-public', $measurements->getNonPublicClassConstants(), $measurements->getNonPublicClassConstantsRelative()];
            }
            $this->tablePrinter->printItemValueTable($constantsRows, 'Constants', 'Count', \true);
        }
    }
    private function printFilesAndDirectories(Measurements $measurements) : void
    {
        $tableRows = [['Directories', $measurements->getDirectories()], ['Files', $measurements->getFiles()]];
        $this->tablePrinter->printItemValueTable($tableRows, 'Metric', 'Count');
    }
    private function printLinesOfCode(Measurements $measurements) : void
    {
        $tableRows = [['Code', $measurements->getNonCommentLines(), $measurements->getNonCommentLinesRelative()], ['Comments', $measurements->getCommentLines(), $measurements->getCommentLinesRelative()], ['Total', $measurements->getLines(), 100.0]];
        $this->tablePrinter->printItemValueTable($tableRows, 'Lines of code', 'Count', \true);
    }
}
