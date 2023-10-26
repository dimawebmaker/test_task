<?php

$folder = 'datafiles';

if ( !file_exists($folder) ) {
    return print("Error. Folder '{$folder}' does not exists.");
}

$files = scandir($folder);

if ( count($files) === 2 ) {
    return print("Error. Folder '{$folder}' is empty.");
}

$file_names = [];
foreach ($files as $file) {
    if ($file == '.' || $file == '..') continue;
    if ( preg_match('/^[a-zA-Z0-9]+\.ixt$/', $file) ) {
        $file_names[] = $file;
    }
}

if ( count($file_names) === 0 ) {
    return print("Error. No files found.");
}

sort($file_names);
print(implode("\n", $file_names));