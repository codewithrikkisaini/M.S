<?php
$output = "=== Git Status ===\n";
$output .= shell_exec("git status 2>&1");
$output .= "\n=== Git Diff ===\n";
$output .= shell_exec("git diff 2>&1");
file_put_contents(__DIR__ . '/git_output.txt', $output);
echo "Done\n";
