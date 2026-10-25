<?php
$files = glob('C:/Users/syari/AppData/Local/Temp/opencode/pdfs/*.pdf');
foreach ($files as $file) {
    $raw = file_get_contents($file);
    preg_match_all('/MediaBox\s*\[\s*([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)\s*\]/', $raw, $matches, PREG_SET_ORDER);
    $box = $matches ? implode(',', array_slice($matches[0], 1)) : '?';
    preg_match_all('/\/Type\s*\/Page[^s]/', $raw, $pages);
    echo basename($file).' box=['.$box.'] pages~'.count($pages[0]).PHP_EOL;
}
