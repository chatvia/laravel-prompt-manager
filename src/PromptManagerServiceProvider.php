<?php

namespace Chatvia\PromptManager;

use Chatvia\PromptManager\Commands\PromptManagerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PromptManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-prompt-manager')
            ->hasConfigFile()
            ->hasCommand(PromptManagerCommand::class);
    }
}
