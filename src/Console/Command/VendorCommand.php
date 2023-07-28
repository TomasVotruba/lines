<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use TomasVotruba\Lines\Analyser;
use TomasVotruba\Lines\Console\OutputFormatter\JsonOutputFormatter;
use TomasVotruba\Lines\Console\OutputFormatter\TextOutputFormatter;
use TomasVotruba\Lines\Exception\ShouldNotHappenException;
use TomasVotruba\Lines\Finder\PhpFilesFinder;

final class VendorCommand extends Command
{
    public function __construct(
        private readonly PhpFilesFinder $phpFilesFinder,
        private readonly Analyser $analyser,
        private readonly SymfonyStyle $symfonyStyle,
        private readonly JsonOutputFormatter $jsonOutputFormatter,
        private readonly TextOutputFormatter $textOutputFormatter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('vendor');
        $this->setDescription('Measure current project /vendor size with and without dev dependencies');

        $this->addOption('json', null, InputOption::VALUE_NONE, 'Output in JSON format');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isJson = (bool) $input->getOption('json');

        $currentVendorDirectory = getcwd() . '/vendor';
        if (! file_exists($currentVendorDirectory)) {
            throw new ShouldNotHappenException(
                'Local /vendor directory could not be found. Be sure to have this tool installed in a composer project and have run "composre install"'
            );
        }

        $this->symfonyStyle->note('Measuring current "/vendor" size...');
        $vendorFilePaths = $this->phpFilesFinder->findInDirectories([$currentVendorDirectory], ['php']);
        $fullVendorMeasurement = $this->analyser->measureFiles($vendorFilePaths);

        $this->symfonyStyle->note('Temporarily uninstalling dev packages...');

        // remove --dev dependencies
        $composerInstallNoDevProcess = new Process(['composer', 'install', '--no-dev']);
        $composerInstallNoDevProcess->mustRun();

        $this->symfonyStyle->note('Measuring "/vendor" size without dev dependencies...');
        $noDevVendorFilePaths = $this->phpFilesFinder->findInDirectories([$currentVendorDirectory], ['php']);
        $noDevVendorMeasurement = $this->analyser->measureFiles($noDevVendorFilePaths);

        $this->symfonyStyle->note('Adding back dev packages...');

        // restore original vendor
        $composerInstallProcess = new Process(['composer', 'install']);
        $composerInstallProcess->mustRun();

        // @todo add better comparisong table including relative size changes :) only lines!
        if ($isJson) {
            $this->symfonyStyle->title('Vendor Size');
            $this->jsonOutputFormatter->printMeasurement($fullVendorMeasurement, $output);

            $this->symfonyStyle->title('Vendor Size without dev packages');
            $this->jsonOutputFormatter->printMeasurement($noDevVendorMeasurement, $output);
        } else {
            $this->symfonyStyle->title('Vendor Size');
            $this->textOutputFormatter->printMeasurement($fullVendorMeasurement, $output);

            $this->symfonyStyle->title('Vendor Size without dev packages');
            $this->textOutputFormatter->printMeasurement($noDevVendorMeasurement, $output);
        }

        return Command::SUCCESS;
    }
}
