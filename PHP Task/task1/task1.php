<?php

$directory = './datafiles';

// pattern for matching file name and extention
$pattern = '/^[A-Za-z0-9]+\.ixt$/';

$matchingFiles = [];

if (is_dir($directory)) {
    if ($handle = opendir($directory)) {
        // here push matching files in null array $matchingfiles
        while (false !== ($file = readdir($handle))) {
            if (preg_match($pattern, $file)) {
                $matchingFiles[] = $file;
            }
        }
        closedir($handle);
    }

    // here i am shorting file name
    asort($matchingFiles);

    echo "List of all files in $directory <br/>";
    foreach ($matchingFiles as $file) {
        echo "<br/>";
        echo "$file";
    }
} else {
    echo "Directory $directory does not exist.";
}
