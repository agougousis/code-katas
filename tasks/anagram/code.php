<?php

// Convert a multibyte string to array of characters
// -> Credits to the PHP manual! <-
function mbStringToArray ($string) {
    $strlen = mb_strlen($string);

	$array = [];	// in case of an empty string

    while ($strlen) {
        $array[] = mb_substr($string,0,1,"UTF-8");
        $string = mb_substr($string,1,$strlen,"UTF-8");
        $strlen = mb_strlen($string);
    }

    return $array;
}

// Checks if one string is anagram of another
// (empty strings allowed)
function isAnagram($string1, $string2) {
	// VALIDATE INPUT (one check for both strings)
	// Search for anything that is not a unicode letter or space
	if (preg_match('/^\p{L} /', $string1.$string2) === 1) {
		return false;
	}

	// Remove spaces
	$noSpaces1 = str_replace(' ', '', $string1);
	$noSpaces2 = str_replace(' ', '', $string2);

	// Transform to arrays for sorting
	$characters1 = mbStringToArray($noSpaces1);
	$characters2 = mbStringToArray($noSpaces2);

	// Sort the character arrays for comparison
	sort($characters1);
	sort($characters2);

	// Compare the two lists of characters after removing the spaces
	return ($characters1 === $characters2);
}

/*
echo "<pre>";
var_dump(isAnagram('', ''));
*/