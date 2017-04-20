<?php

/**
 * Extracts the min price from a series of DEP element
 *
 * The returned value should have 2 decimals.
 * It is assumed that at least one DEP element is passed.
 *
 * @param \SimpleXMLElement $deps
 * @return float
 */
function extractMinPrice(\SimpleXMLElement $deps)
{
    foreach($deps as $dep) {
        $discount = (empty($dep['DISCOUNT']))? 0 : str_replace('%', '', $dep['DISCOUNT']) / 100;
        $prices[] = $dep['EUR'] * (1- $discount);
    }

    return number_format(min($prices), 2);
}

/**
 * Remove html tags and multiple spaces from a string
 *
 * @param string $html
 * @return string
 */
function fixString($html)
{
    $noTags = html_entity_decode($html);
    $singleSpace = preg_replace('/[\xA0 ]+/u', " ", $noTags);

    return $singleSpace;
}

/**
 * Build the text output for a single TOUR element
 *
 * @param \SimpleXMLElement $tour
 * @return string
 */
function getTourOutput(\SimpleXMLElement $tour)
{
    // Get the main fields
    $title = (string) $tour->Title;
    $code = (string) $tour->Code;
    $duration = (int) $tour->Duration;
    $inclusions = (string) $tour->Inclusions;

    $title = fixString($title);
    $inclusions = fixString($inclusions);

    // Extract the text from CDATA
    $doc = new DOMDocument();
    $doc->loadHTML($inclusions);
    $inclusionsText = $doc->textContent;

    // Calculate the min price
    $minPrice = extractMinPrice($tour->DEP);

    // Build the output string
    $tourOutput = "Title|Code|Duration|Inclusions|MinPrice<br>";
    $tourOutput .= "$title|$code|$duration|$inclusionsText|$minPrice<br><br>";

    return $tourOutput;
}

/**
 * Parses XML tours and produces a text output
 *
 * We assume that the XML input has already validated through an XML Schema.
 *
 * @param string $xmlText
 * @return string
 */
function xmlToCSV($xmlText)
{
    $xml = new SimpleXMLElement($xmlText);

    // Get the TOUR elements
    $tourList = $xml->TOUR;

    // Build the text output
    $output = '';
    foreach($tourList as $tour) {
        $output .= getTourOutput($tour);
    }

    return $output;
}

/*
$xmlText = file_get_contents('xmltour.xml');
echo xmlToCSV($xmlText);
*/