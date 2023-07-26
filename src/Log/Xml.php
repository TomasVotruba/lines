<?php declare(strict_types=1);
namespace TomasVotruba\Lines\Log;

use function file_put_contents;
use DOMDocument;

final class Xml
{
    /** @noinspection UnusedFunctionResultInspection */
    public function printResult(string $filename, array $count): void
    {
        $document               = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $root = $document->createElement('phploc');

        $document->appendChild($root);

        if ($count['directories'] > 0) {
            $root->appendChild(
                $document->createElement('directories', (string) $count['directories'])
            );

            $root->appendChild(
                $document->createElement('files', (string) $count['files'])
            );
        }

        unset($count['directories'], $count['files']);

        foreach ($count as $k => $v) {
            $root->appendChild(
                $document->createElement($k, (string) $v)
            );
        }

        file_put_contents($filename, $document->saveXML());
    }
}
