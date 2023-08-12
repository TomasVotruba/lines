<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\Command;

use TomasVotruba\Lines\Measurements;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use TomasVotruba\Lines\Analyser;
use TomasVotruba\Lines\Console\View;
use TomasVotruba\Lines\Finder\PhpFilesFinder;
use TomasVotruba\Lines\Helpers\Calculator;
use TomasVotruba\Lines\Helpers\NumberFormat;
use Webmozart\Assert\Assert;

final class VendorCommand extends Command
{
    public function __construct(
        private readonly PhpFilesFinder $phpFilesFinder,
        private readonly Analyser $analyser,
        private readonly View $view,
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
        $this->view->setOutput($output);

        $currentVendorDirectory = getcwd() . '/vendor';
        Assert::directory($currentVendorDirectory);

        $fullVendorMeasurement = $this->view->loading(
            'Measuring current "/vendor" size...',
            function () use ($currentVendorDirectory): Measurements {
                $vendorFilePaths = $this->phpFilesFinder->findInDirectories([$currentVendorDirectory]);
                return $this->analyser->measureFiles($vendorFilePaths);
            }
        );

        $noDevVendorMeasurement = $this->view->loading(
            'Uninstalling dev dependencies and measuring "/vendor" again...',
            function () use ($currentVendorDirectory) {
                $this->uninstallDevPackages();
                $noDevVendorFilePaths = $this->phpFilesFinder->findInDirectories([$currentVendorDirectory]);

                $measurements = $this->analyser->measureFiles($noDevVendorFilePaths);
                $this->installOriginalDependencies();

                return $measurements;
            }
        );

        $linesDifferenceRelative = Calculator::relativeChange(
            $fullVendorMeasurement->getLines(),
            $noDevVendorMeasurement->getLines()
        );

        $nonCommentLinesDifferenceRelative = Calculator::relativeChange(
            $fullVendorMeasurement->getNonCommentLines(),
            $noDevVendorMeasurement->getNonCommentLines()
        );

        $this->view->render('vendor', [
            'all' => [
                'full' => NumberFormat::pretty($fullVendorMeasurement->getLines()),
                'noDev' => NumberFormat::pretty($noDevVendorMeasurement->getLines()),
                'percent' => NumberFormat::percent($linesDifferenceRelative),
            ],
            'lines' => [
                'full' => NumberFormat::pretty($fullVendorMeasurement->getNonCommentLines()),
                'noDev' => NumberFormat::pretty($noDevVendorMeasurement->getNonCommentLines()),
                'percent' => NumberFormat::percent($nonCommentLinesDifferenceRelative),
            ],
        ]);

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
