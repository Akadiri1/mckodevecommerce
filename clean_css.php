<?php
$file = 'www/assets/css/custom.css';
$content = file_get_contents($file);
$pos = strpos($content, "\x00");
if ($pos !== false) {
    $newContent = substr($content, 0, $pos);
    file_put_contents($file, $newContent);
    echo "File cleaned and truncated at position $pos.\n";
} else {
    echo "No null bytes found.\n";
}
