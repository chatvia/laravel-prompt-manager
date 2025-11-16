<?php

namespace Chatvia\PromptManager\Tests;

use Chatvia\PromptManager\PromptManagerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            PromptManagerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('prompt-manager.path', dirname(__DIR__).'/tests/prompts');
    }
}
