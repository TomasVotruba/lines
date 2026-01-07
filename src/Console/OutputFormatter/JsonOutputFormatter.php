<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\FeatureCounter\ValueObject\FeatureCollector;
use TomasVotruba\Lines\Measurements;
use Webmozart\Assert\Assert;

final class JsonOutputFormatter implements OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort, bool $showLongestFiles): void
    {
        $arrayData = [
            'filesystem' => [
                'directories' => $measurements->getDirectoryCount(),
                'files' => $measurements->getFileCount(),
            ],

            'lines_of_code' => [
                'code' => $measurements->getNonCommentLines(),
                'code_relative' => $measurements->getNonCommentLinesRelative(),
                'comments' => $measurements->getCommentLines(),
                'comments_relative' => $measurements->getCommentLinesRelative(),
                'total' => $measurements->getLines(),
            ],
        ];

        if ($isShort === false) {
            $arrayData['structure'] = [
                'namespaces' => $measurements->getNamespaceCount(),
                'classes' => $measurements->getClassCount(),
                'class_methods' => $measurements->getMethodCount(),
                'class_constants' => $measurements->getClassConstantCount(),
                'interfaces' => $measurements->getInterfaceCount(),
                'traits' => $measurements->getTraitCount(),
                'enums' => $measurements->getEnumCount(),
                'functions' => $measurements->getFunctionCount(),
                'closures' => $measurements->getClosureCount(),
                'global_constants' => $measurements->getGlobalConstantCount(),
            ];

            $arrayData['methods_access'] = [
                'non_static' => $measurements->getNonStaticMethods(),
                'non_static_relative' => $measurements->getNonStaticMethodsRelative(),
                'static' => $measurements->getStaticMethods(),
                'static_relative' => $measurements->getStaticMethodsRelative(),
            ];

            $arrayData['methods_visibility'] = [
                'public' => $measurements->getPublicMethods(),
                'public_relative' => $measurements->getPublicMethodsRelative(),
                'protected' => $measurements->getProtectedMethods(),
                'protected_relative' => $measurements->getProtectedMethodsRelative(),
                'private' => $measurements->getPrivateMethods(),
                'private_relative' => $measurements->getPrivateMethodsRelative(),
            ];
        }

        if ($showLongestFiles) {
            $arrayData['longest_files'] = $measurements->getLongestFiles();
        }

        $jsonString = json_encode($arrayData, JSON_PRETTY_PRINT);
        Assert::string($jsonString);

        echo $jsonString . PHP_EOL;
    }

    public function printFeatures(FeatureCollector $featureCollector): void
    {
        $arrayData = [];

        $previousPhpVersion = null;

        foreach ($featureCollector->getPhpFeatures() as $phpFeature) {
            if ($previousPhpVersion !== $phpFeature->getPhpVersion()) {
                $arrayData[$phpFeature->getPhpVersion()] = [];
                $previousPhpVersion = $phpFeature->getPhpVersion();
            }

            $arrayData[$phpFeature->getPhpVersion()][] = [
                'name' => $phpFeature->getName(),
                'count' => $phpFeature->getCount(),
            ];
        }

        $jsonString = json_encode($arrayData, JSON_PRETTY_PRINT);
        Assert::string($jsonString);

        echo $jsonString . PHP_EOL;
    }
}
