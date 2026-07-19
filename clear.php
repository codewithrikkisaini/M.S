<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Artisan;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = "=== Clearing Cache ===\n";

try {
    Artisan::call('view:clear');
    $output .= "View Cache: " . Artisan::output() . "\n";
} catch (Throwable $e) {
    $output .= "View Cache Error: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('route:clear');
    $output .= "Route Cache: " . Artisan::output() . "\n";
} catch (Throwable $e) {
    $output .= "Route Cache Error: " . $e->getMessage() . "\n";
}

try {
    Artisan::call('config:clear');
    $output .= "Config Cache: " . Artisan::output() . "\n";
} catch (Throwable $e) {
    $output .= "Config Cache Error: " . $e->getMessage() . "\n";
}

file_put_contents(__DIR__ . '/clear_output.txt', $output);
echo "Done\n";
