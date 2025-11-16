<?php

use Chatvia\PromptManager\PromptManager;

afterEach(function () {
    $files = glob(config('prompt-manager.path') . DIRECTORY_SEPARATOR . '*');
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitkeep') {
            unlink($file);
        }
    }
});

it('should fail when no name is provided', function () {
    expect(fn () => $this->artisan('make:prompt')->run())
        ->toThrow(Symfony\Component\Console\Exception\RuntimeException::class);
});

it('should create prompt template with different formats', function (string $format, string $extension) {
    $filePath = config('prompt-manager.path') . DIRECTORY_SEPARATOR . "test-prompt{$extension}";

    $this
        ->artisan('make:prompt', ['name' => 'test prompt', '--format' => $format])
        ->assertSuccessful();

    expect(file_exists($filePath))->toBeTrue();
})->with([
    'markdown' => ['markdown', '.md'],
    'blade' => ['blade', '.blade.php'],
    'text' => ['text', '.txt'],
]);

it('should fail when format does not exist', function (string $invalidFormat) {
    $this
        ->artisan('make:prompt', ['name' => 'test-prompt', '--format' => $invalidFormat])
        ->assertExitCode(1)
        ->expectsOutput('Invalid format specified. Valid formats are: markdown, blade, text.');
})->with(['json', 'xml', 'yaml', '', 'foobar']);

it('should load a prompt file and replace placeholders', function () {
    $promptManager = new PromptManager();
    $filePath = config('prompt-manager.path') . DIRECTORY_SEPARATOR . 'sample-prompt.md';

    file_put_contents($filePath, "Hello, {{ name }}! Welcome to {{ place }}.");

    $placeholders = [
        'name' => 'Alice',
        'place' => 'Wonderland',
    ];

    $loadedPrompt = $promptManager->load($filePath, $placeholders);

    expect($loadedPrompt)->toBe("Hello, Alice! Welcome to Wonderland.");
});

it('should handle placeholders with various spacing', function () {
    $promptManager = new PromptManager();
    $filePath = config('prompt-manager.path') . DIRECTORY_SEPARATOR . 'spacing-test.md';

    file_put_contents($filePath, "{{name}}, {{ title }}, {{   role   }}, {{ department	}}");

    $placeholders = [
        'name' => 'John',
        'title' => 'Developer',
        'role' => 'Senior',
        'department' => 'Engineering',
    ];

    $loadedPrompt = $promptManager->load($filePath, $placeholders);

    expect($loadedPrompt)->toBe("John, Developer, Senior, Engineering");
});

it('should preserve blade syntax while replacing custom placeholders', function () {
    $promptManager = new PromptManager();
    $filePath = config('prompt-manager.path') . DIRECTORY_SEPARATOR . 'blade-test.blade.php';

    $content = "Hello {{ name }}! {{-- This is a blade comment --}} @if(true) {{ \$variable }} @endif";
    file_put_contents($filePath, $content);

    $placeholders = ['name' => 'Alice'];

    $loadedPrompt = $promptManager->load($filePath, $placeholders);

    expect($loadedPrompt)->toBe("Hello Alice! {{-- This is a blade comment --}} @if(true) {{ \$variable }} @endif");
});

it('should throw exception when file does not exist', function () {
    $promptManager = new PromptManager();

    expect(fn () => $promptManager->load('nonexistent-file.md'))
        ->toThrow(InvalidArgumentException::class);
});

it('should handle empty placeholders array', function () {
    $promptManager = new PromptManager();
    $filePath = config('prompt-manager.path') . DIRECTORY_SEPARATOR . 'no-placeholders.md';

    file_put_contents($filePath, "This is a simple prompt without placeholders.");

    $loadedPrompt = $promptManager->load($filePath);

    expect($loadedPrompt)->toBe("This is a simple prompt without placeholders.");
});

