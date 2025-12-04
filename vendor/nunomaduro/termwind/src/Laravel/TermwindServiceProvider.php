<?php

declare (strict_types=1);
namespace Lines202512\Termwind\Laravel;

use Lines202512\Illuminate\Console\OutputStyle;
use Lines202512\Illuminate\Support\ServiceProvider;
use Lines202512\Termwind\Termwind;
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
