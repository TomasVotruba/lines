<?php

declare (strict_types=1);
namespace Lines202606\Termwind\Laravel;

use Lines202606\Illuminate\Console\OutputStyle;
use Lines202606\Illuminate\Support\ServiceProvider;
use Lines202606\Termwind\Termwind;
final class TermwindServiceProvider extends ServiceProvider
{
    /**
     * Sets the correct renderer to be used.
     */
    public function register() : void
    {
        $this->app->resolving(OutputStyle::class, function ($style) : void {
            Termwind::renderUsing($style->getOutput());
        });
    }
}
