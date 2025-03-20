<?php
$uploads_dir = __DIR__ . "/uploads/";

echo "<h2>Uploaded Files:</h2>";
echo "<ul>";

foreach (scandir($uploads_dir) as $file) {
    if ($file !== "." && $file !== "..") {
        echo "<li><a href='uploads/$file'>$file</a></li>";
    }
}

echo "</ul>";
?>
