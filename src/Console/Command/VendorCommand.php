<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use TomasVotruba\Lines\Analyser;
use TomasVotruba\Lines\Finder\PhpFilesFinder;
use TomasVotruba\Lines\Helpers\NumberFormat;
use Webmozart\Assert\Assert;

final class VendorCommand extends Command
{
    public function __construct(
        private readonly PhpFilesFinder $phpFilesFinder,
        private readonly Analyser $analyser,
        private readonly SymfonyStyle $symfonyStyle,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('vendor');
        $this->setDescription('Measure current project /vendor size with and without dev dependencies');
    }

    /**
     * @return self::FAILURE|self::SUCCESS
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currentVendorDirectory = getcwd() . '/vendor';
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
        $padLeftTableStyle->setPadType(STR_PAD_LEFT);

        $linesDifferenceRelative = 100 * (1 - ($fullVendorMeasurement->getLines() - $noDevVendorMeasurement->getLines()) / $fullVendorMeasurement->getLines());

        $nonCommentLinesDifferenceRelative = 100 * (1 - ($fullVendorMeasurement->getNonCommentLines() - $noDevVendorMeasurement->getNonCommentLines()) / $fullVendorMeasurement->getNonCommentLines());

        $this->symfonyStyle->createTable()
            ->setHeaders(['Metric', 'All dependencies', 'Without dev', 'Change'])
            ->setColumnWidth(0, 20)
            ->setRows([
                [
                    'All lines',
                    NumberFormat::pretty($fullVendorMeasurement->getLines()),
                    NumberFormat::pretty($noDevVendorMeasurement->getLines()),
                    NumberFormat::percent(-1 * (100 - $linesDifferenceRelative)),
                ],
                [
                    'Lines of code',
                    NumberFormat::pretty($fullVendorMeasurement->getNonCommentLines()),
                    NumberFormat::pretty($noDevVendorMeasurement->getNonCommentLines()),
                    NumberFormat::percent(-1 * (100 - $nonCommentLinesDifferenceRelative)),
                ],
            ])
            ->setColumnStyle(1, $padLeftTableStyle)
            ->setColumnStyle(2, $padLeftTableStyle)
            ->setColumnStyle(3, $padLeftTableStyle)
            ->setColumnStyle(4, $padLeftTableStyle)
            ->render();

        $this->symfonyStyle->newLine(2);

        return Command::SUCCESS;
    }

    private function uninstallDevPackages(): void
    {
        $process = new Process(['composer', 'install', '--no-dev']);
        $process->mustRun();
    }

    private function installOriginalDependencies(): void
    {
        $process = new Process(['composer', 'install']);
        $process->mustRun();
    }
}
