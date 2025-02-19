<?php
include 'config.php'; // Load project folder settings

// Set base XAMPP htdocs directory
$basePath = "C:/xampp/htdocs";

// Build the folder path
$folderPath = $basePath;
if (!empty($projectfolder)) {
    $folderPath .= "/$projectfolder";
}
if (!empty($secondprojectfolder)) {
    $folderPath .= "/$secondprojectfolder";
}

// Normalize and convert to Windows format
$folderPath = realpath(str_replace("/", "\\", $folderPath));

// Ensure the folder exists before opening
if ($folderPath && file_exists($folderPath)) {
    shell_exec('explorer ' . escapeshellarg($folderPath));
} else {
    die("Error: Folder '$folderPath' does not exist.");
}

// Redirect back to localhost
header("Location: http://localhost/");
exit;
