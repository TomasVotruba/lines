<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use function Termwind\render;
use TomasVotruba\Lines\ValueObject\TableView;

final class ViewRenderer
{
    public function renderTableView(TableView $tableView): void
    {
        $viewContent = $this->getFileContents($tableView);

        render($viewContent);
    }

    private function getFileContents(TableView $tableView): string
    {
        ob_start();
        require $tableView->getTemplateFilePath();
        $viewContent = (string) ob_get_contents();
        ob_end_clean();

        return $viewContent;
    }
}
