<?php

namespace Chatvia\PromptManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Chatvia\PromptManager\PromptManager
 *
 * @method load(string $filePath, array $placeholders = []): string
 */
class PromptManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Chatvia\PromptManager\PromptManager::class;
    }
}
