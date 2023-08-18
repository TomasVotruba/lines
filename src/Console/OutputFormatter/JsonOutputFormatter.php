<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Measurements;
use Webmozart\Assert\Assert;

final class JsonOutputFormatter implements OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort): void
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
                'global_constants' => $measurements->getGlobalConstantCount(),
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
        }

        $jsonString = json_encode($arrayData, JSON_PRETTY_PRINT);
        Assert::string($jsonString);

        echo $jsonString;
    }
}
