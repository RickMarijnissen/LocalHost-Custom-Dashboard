<?php
$folderPath = 'C:\laragon';

// Open the folder in Windows Explorer
shell_exec('explorer ' . escapeshellarg($folderPath));
