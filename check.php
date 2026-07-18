<?php
try {
    $output = "=== Start ===\n";
    $output .= "PHP Version: " . PHP_VERSION . "\n";
    
    // Check if we can run shell commands
    $output .= "Shell exec status: " . (function_exists('shell_exec') ? 'enabled' : 'disabled') . "\n";
    
    // List files in resources/views/components/
    $components = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(__DIR__ . '/resources/views/components'),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $components[$file->getPathname()] = filemtime($file->getPathname());
        }
    }
    arsort($components);
    foreach ($components as $path => $mtime) {
        $output .= date('Y-m-d H:i:s', $mtime) . " - " . str_replace(__DIR__ . '/', '', $path) . "\n";
    }
} catch (Throwable $e) {
    $output .= "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}

file_put_contents(__DIR__ . '/git_output.txt', $output);
echo "Done\n";
