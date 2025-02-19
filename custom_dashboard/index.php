<?php include 'inc/header.php'; ?>

<?php
// Set project directory for XAMPP
$dir = "C:/xampp/htdocs";
$excluded_folders = array('custom_dashboard', 'dashboard', 'img', 'webalizer', 'xampp');
$projects = array_diff(scandir($dir), array('.', '..'), $excluded_folders);

// Function to get folder size
function getFolderSize($folder)
{
    $size = 0;
    foreach (glob(rtrim($folder, '/') . '/*', GLOB_NOSORT) as $file) {
        $size += is_file($file) ? filesize($file) : getFolderSize($file);
    }
    return $size;
}

// Function to detect framework
function detectFramework($projectPath)
{
    if (file_exists("$projectPath/composer.json")) {
        return "Laravel";
    } elseif (file_exists("$projectPath/package.json")) {
        return "Node.js";
    } elseif (file_exists("$projectPath/wp-config.php")) {
        return "WordPress";
    } else {
        return "Custom PHP";
    }
}
?>

<main class="container-flex">
    <!-- Left Section - Quick Links & Search Bar -->
    <div class="left-section">
        <div class="quick-links">
            <a class="link" href="http://localhost/" target="_blank">Home</a>
            <a class="link" href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a>
            <a class="link" href="inc/open_folder.php">Open Projects Folder</a>
        </div>

        <div class="search-container">
            <input type="text" id="search-bar" placeholder="Search projects..." onkeyup="filterProjects()">
        </div>
    </div>

    <!-- Right Section - Server Info + Projects -->
    <div class="right-section">
        <div class="server-info">
            <p>PHP Version: <?php echo phpversion(); ?></p>
            <p>MySQL: <?php echo (mysqli_connect("localhost", "root", "") ? "Running" : "Not Running"); ?></p>
        </div>

        <div class="grid" id="project-grid">
            <?php foreach ($projects as $project): ?>
                <?php
                $projectPath = "$dir/$project";
                if (is_dir($projectPath)):
                    $lastModified = date("F d, Y", filemtime($projectPath));
                    $size = round(getFolderSize($projectPath) / (1024 * 1024), 2); // Convert to MB
                    $framework = detectFramework($projectPath);
                    $projectURL = "http://localhost/$project";
                ?>
                    <div class="card">
                        <a href="<?= $projectURL ?>" target="_blank"><?= ucfirst($project) ?></a>
                        <p>Last Updated: <?= $lastModified ?></p>
                        <p>Size: <?= $size ?> MB</p>
                        <p>Framework: <?= $framework ?></p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include 'inc/footer.php'; ?>