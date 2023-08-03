<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use Symfony\Component\Console\Output\OutputInterface;
use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Measurements;
use Webmozart\Assert\Assert;

final class JsonOutputFormatter implements OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, OutputInterface $output, bool $isShort): void
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
            $arrayData['lengths'] = [
                'class_max' => $measurements->getMaxClassLength(),
                'class_average' => $measurements->getAverageClassLength(),
                'method_max' => $measurements->getMaxMethodLength(),
                'method_average' => $measurements->getAverageMethodLength(),
            ];

            $arrayData['structure'] = [
                'namespaces' => $measurements->getNamespaces(),
                'classes' => $measurements->getClassCount(),
                'interfaces' => $measurements->getInterfaceCount(),
                'traits' => $measurements->getTraitCount(),
                'enums' => $measurements->getEnumCount(),
                'constants' => $measurements->getConstantCount(),
                'methods' => $measurements->getMethodCount(),
                'functions' => $measurements->getFunctionCount(),
            ];

            $arrayData['methods'] = [
                'non_static' => $measurements->getNonStaticMethods(),
                'non_static_relative' => $measurements->getNonStaticMethodsRelative(),
                'static' => $measurements->getStaticMethods(),
                'static_relative' => $measurements->getStaticMethodsRelative(),
                'public' => $measurements->getPublicMethods(),
                'public_relative' => $measurements->getPublicMethodsRelative(),
                'protected' => $measurements->getProtectedMethods(),
                'protected_relative' => $measurements->getProtectedMethodsRelative(),
                'private' => $measurements->getPrivateMethods(),
                'private_relative' => $measurements->getPrivateMethodsRelative(),
            ];

            $arrayData['constants'] = [
                'class' => $measurements->getClassConstants(),
                'class_public' => $measurements->getPublicClassConstants(),
                'class_public_relative' => $measurements->getPublicClassConstantsRelative(),
                'class_non_public' => $measurements->getNonPublicClassConstants(),
                'class_non_public_relative' => $measurements->getNonPublicClassConstantsRelative(),
                'global' => $measurements->getGlobalConstantCount(),
                'global_relative' => $measurements->getGlobalConstantCountRelative(),
            ];
        }

        $jsonString = json_encode($arrayData, JSON_PRETTY_PRINT);
        Assert::string($jsonString);

        $output->writeln($jsonString);
    }
}
