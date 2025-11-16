<?php

namespace Chatvia\PromptManager;

use InvalidArgumentException;

class PromptManager
{
    public function load(string $file, $placeholders = []): string
    {
        if (! file_exists($file)) {
            throw new InvalidArgumentException("Prompt file does not exist: {$file}");
        }

        $storagePath = config('prompt-manager.path');

        if (! str_starts_with($file, $storagePath)) {
            $file = config('prompt-manager.path').DIRECTORY_SEPARATOR.$file;
        }

        $content = file_get_contents($file);

        return $this->replacePlaceholders($content, $placeholders);
    }

    private function replacePlaceholders(string $content, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            // Only replace {{ key }} patterns, avoiding blade syntax like {{-- comments --}}
            $pattern = '/\{\{\s*'.preg_quote($key, '/').'\s*}}(?!\s*--)/';
            $content = preg_replace($pattern, (string) $value, $content);
        }

        return $content;
    }
}
