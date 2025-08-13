<?php

declare (strict_types=1);
namespace Lines202508\TomasVotruba\Lines\Console;

use Lines202508\TomasVotruba\Lines\ValueObject\TableView;
use function Lines202508\Termwind\render;
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
