<?php

declare(strict_types=1);

namespace TomasVotruba\Lines\Console;

use TomasVotruba\Lines\ValueObject\TableView;
use function Termwind\render;

final class ViewRenderer
{
    public function renderTableVIew(TableView $tableView): void
    {
        ob_start();
        require_once $tableView->getTemplateFilePath();
        $viewContent = (string) ob_get_contents();
        ob_end_clean();

        render($viewContent);
    }
}
