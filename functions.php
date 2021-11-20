<?php


/**
 * Function to sluggify text thus making it url friendly.
 * Borrowed from laravel
 */
function slug($text, $separator = '-')
{

    // Convert all dashes/underscores into separator
    $flip = $separator === '-' ? '_' : '-';

    $text = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $text);

    // Replace @ with the word 'at'
    $text = str_replace('@', $separator . 'at' . $separator, $text);

    // Remove all characters that are not the separator, letters, numbers, or whitespace.
    $text = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', strtolower($text));

    // Replace all separator characters and whitespace by a single separator
    $text = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $text);

    return trim($text, $separator);
}

?>
