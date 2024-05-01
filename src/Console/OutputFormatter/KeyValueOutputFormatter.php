<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console\OutputFormatter;

use TomasVotruba\Lines\Contract\OutputFormatterInterface;
use TomasVotruba\Lines\Measurements;

final class KeyValueOutputFormatter implements OutputFormatterInterface
{
    public function printMeasurement(Measurements $measurements, bool $isShort): void
    {
        $arrayData = [
            'directories' => $measurements->getDirectoryCount(),
            'files' => $measurements->getFileCount(),
            'code' => $measurements->getNonCommentLines(),
            'code_relative' => $measurements->getNonCommentLinesRelative(),
            'comments' => $measurements->getCommentLines(),
            'comments_relative' => $measurements->getCommentLinesRelative(),
            'total' => $measurements->getLines(),
        ];

        if ($isShort === false) {
            $extraData = [
                'namespaces' => $measurements->getNamespaceCount(),
                'classes' => $measurements->getClassCount(),
                'class_methods' => $measurements->getMethodCount(),
                'class_constants' => $measurements->getClassConstantCount(),
                'interfaces' => $measurements->getInterfaceCount(),
                'traits' => $measurements->getTraitCount(),
                'enums' => $measurements->getEnumCount(),
                'functions' => $measurements->getFunctionCount(),
                'global_constants' => $measurements->getGlobalConstantCount(),
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

            $arrayData = array_merge($arrayData, $extraData);
        }

        $keyValuePairs = [];
        foreach ($arrayData as $key => $value) {
            $keyValuePairs[] = $key . '=' . $value;
        }

        $keyValueString = implode(',', $keyValuePairs);

        echo $keyValueString . PHP_EOL;
    }
}
