<?php
$folderPath = 'C:\xampp\htdocs';

// Open the detected folder in Windows Explorer
shell_exec('explorer ' . escapeshellarg($folderPath));

// Redirect back to localhost
header("Location: http://localhost/");
exit;
