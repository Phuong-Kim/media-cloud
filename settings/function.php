<?php 
/**
 * @param // Set file to show in relative format
 */
function formatSizeUnits($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while ($bytes >= 1000 && $i < 4) {
        $bytes /= 1000;
        $i++;
    }
    return round($bytes, 0) . ' ' . $units[$i];
}

/**
 * @param $folderPath // Get File size
 */
function getFolderSize($folderPath) {
    $totalSize = 0;
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderPath));

    foreach ($files as $file) {
        if ($file->isFile()) {
            $totalSize += $file->getSize();
        }
    }

    return $totalSize;
}