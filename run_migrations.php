<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Running migrations...\n";
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "Migrations exit code: " . $exitCode . "\n";
    echo "Output: \n" . \Illuminate\Support\Facades\Artisan::output() . "\n";
} catch (\Exception $e) {
    echo "Error running migrations: " . $e->getMessage() . "\n";
}
