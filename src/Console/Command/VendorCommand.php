<?php

declare (strict_types=1);
namespace Lines202308\TomasVotruba\Lines\Console\Command;

use Lines202308\Symfony\Component\Console\Command\Command;
use Lines202308\Symfony\Component\Console\Helper\TableStyle;
use Lines202308\Symfony\Component\Console\Input\InputInterface;
use Lines202308\Symfony\Component\Console\Output\OutputInterface;
use Lines202308\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202308\Symfony\Component\Process\Process;
use Lines202308\TomasVotruba\Lines\Analyser;
use Lines202308\TomasVotruba\Lines\Finder\PhpFilesFinder;
use Lines202308\TomasVotruba\Lines\Helpers\Calculator;
use Lines202308\TomasVotruba\Lines\Helpers\NumberFormat;
use Lines202308\Webmozart\Assert\Assert;
final class VendorCommand extends Command
{
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Finder\PhpFilesFinder
     */
    private $phpFilesFinder;
    /**
     * @readonly
     * @var \TomasVotruba\Lines\Analyser
     */
    private $analyser;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(PhpFilesFinder $phpFilesFinder, Analyser $analyser, SymfonyStyle $symfonyStyle)
    {
        $this->phpFilesFinder = $phpFilesFinder;
        $this->analyser = $analyser;
        $this->symfonyStyle = $symfonyStyle;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('vendor');
        $this->setDescription('Measure current project /vendor size with and without dev dependencies');
    }
    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $currentVendorDirectory = \getcwd() . '/vendor';
        Assert::directory($currentVendorDirectory);
        $this->symfonyStyle->writeln('<fg=yellow>Measuring current "/vendor" size...</>');
        $vendorFilePaths = $this->phpFilesFinder->findInDirectories([$currentVendorDirectory]);
        $fullVendorMeasurement = $this->analyser->measureFiles($vendorFilePaths);
        $this->uninstallDevPackages();
        $this->symfonyStyle->writeln('<fg=yellow>Uninstalling dev dependencies and measuring "/vendor" again...</>');
        $noDevVendorFilePaths = $this->phpFilesFinder->findInDirectories([$currentVendorDirectory]);
        $noDevVendorMeasurement = $this->analyser->measureFiles($noDevVendorFilePaths);
        $this->installOriginalDependencies();
        $this->symfonyStyle->newLine();
        $padLeftTableStyle = new TableStyle();
        $padLeftTableStyle->setPadType(\STR_PAD_LEFT);
        $linesDifferenceRelative = Calculator::relativeChange($fullVendorMeasurement->getLines(), $noDevVendorMeasurement->getLines());
        $nonCommentLinesDifferenceRelative = Calculator::relativeChange($fullVendorMeasurement->getNonCommentLines(), $noDevVendorMeasurement->getNonCommentLines());
        $this->symfonyStyle->createTable()->setHeaders(['Metric', 'All dependencies', 'Without dev', 'Change'])->setColumnWidth(0, 20)->setRows([['All lines', NumberFormat::pretty($fullVendorMeasurement->getLines()), NumberFormat::pretty($noDevVendorMeasurement->getLines()), NumberFormat::percent($linesDifferenceRelative)], ['Lines of code', NumberFormat::pretty($fullVendorMeasurement->getNonCommentLines()), NumberFormat::pretty($noDevVendorMeasurement->getNonCommentLines()), NumberFormat::percent($nonCommentLinesDifferenceRelative)]])->setColumnStyle(1, $padLeftTableStyle)->setColumnStyle(2, $padLeftTableStyle)->setColumnStyle(3, $padLeftTableStyle)->setColumnStyle(4, $padLeftTableStyle)->render();
        $this->symfonyStyle->newLine(2);
        return Command::SUCCESS;
    }
    private function uninstallDevPackages() : void
    {
        $process = new Process(['composer', 'install', '--no-dev']);
        $process->mustRun();
    }
    private function installOriginalDependencies() : void
    {
        $process = new Process(['composer', 'install']);
        $process->mustRun();
    }
}
