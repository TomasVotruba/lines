<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TomasVotruba\Lines\Command\FeaturesCommand;
use TomasVotruba\Lines\DependencyInjection\ContainerFactory;

final class FeaturesCommandTest extends TestCase
{
    private FeaturesCommand $featuresCommand;

    protected function setUp(): void
    {
        $containerFactory = new ContainerFactory();
        $container = $containerFactory->create();

        $this->featuresCommand = $container->make(FeaturesCommand::class);
    }

    public function testDefaultDirectory(): void
    {
        $commandTester = new CommandTester($this->featuresCommand);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('PHP features', $output);
    }

    public function testMultipleDirectories(): void
    {
        $commandTester = new CommandTester($this->featuresCommand);
        $commandTester->execute([
            'project-directories' => [__DIR__ . '/../Fixture/IncludeMe', __DIR__ . '/../Fixture/ExcludeMe'],
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('PHP features', $output);
    }
}
