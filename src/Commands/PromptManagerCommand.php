<?php

namespace Chatvia\PromptManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PromptManagerCommand extends Command
{
    public $signature = 'make:prompt {name : The name of the prompt template} {--format=markdown : The format of the prompt template (markdown, blade, text)}';

    public $description = 'Create a new LLM prompt template';

    public function handle(): int
    {
        $name = $this->argument('name');
        $format = $this->option('format');

        if (! $this->validateFormat($format)) {
            $this->error('Invalid format specified. Valid formats are: markdown, blade, text.');

            return self::FAILURE;
        }

        $storagePath = config('prompt-manager.path');

        $filePath = $this->createDirectoryStructure($storagePath, $name, $format);

        $this->newLine();

        if (File::exists($filePath)) {
            $this->error('Prompt '.$name.' already exists.');

            return self::FAILURE;
        }

        if (File::put($filePath, 'Your prompt content goes here...') === false) {
            $this->error('Failed to create prompt template: '.$filePath);

            return self::FAILURE;
        }

        $this->info('Prompt created successfully: '.$filePath);

        return self::SUCCESS;
    }

    private function validateFormat(string $format): bool
    {
        $validFormats = ['markdown', 'blade', 'text'];

        return in_array($format, $validFormats);
    }

    private function createDirectoryStructure(string $basePath, string $name, string $format): string
    {
        // Normalize the name and handle directory separators
        $normalizedName = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name);
        $pathInfo = pathinfo($normalizedName);
        $directory = $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] : '';
        $filename = Str::of($pathInfo['filename'])->lower()->kebab();

        // Create the full directory path
        $fullPath = $basePath;
        if ($directory) {
            $fullPath .= DIRECTORY_SEPARATOR.$directory;

            // Create directories recursively if they don't exist
            if (! is_dir($fullPath)) {
                File::makeDirectory(path: $fullPath, recursive: true);
            }
        }

        // Create the final file path with proper extension
        $extension = $this->getExtensionForFormat($format);

        return $fullPath.DIRECTORY_SEPARATOR.$filename.$extension;
    }

    private function getExtensionForFormat(string $format): string
    {
        return match ($format) {
            'blade' => '.blade.php',
            'text' => '.txt',
            default => '.md'
        };
    }
}
