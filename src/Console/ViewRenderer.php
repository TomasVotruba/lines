<?php

declare (strict_types=1);
namespace Lines202412\TomasVotruba\Lines\Console;

use Lines202412\TomasVotruba\Lines\ValueObject\TableView;
use function Lines202412\Termwind\render;
final class ViewRenderer
{
    public function renderTableView(TableView $tableView) : void
    {
        $viewContent = $this->getFileContents($tableView);
        render($viewContent);
    }
    private function getFileContents(TableView $tableView) : string
    {
        \ob_start();
        require $tableView->getTemplateFilePath();
        $viewContent = (string) \ob_get_contents();
        \ob_end_clean();
        return $viewContent;
    }
}
