<?php
/**
 * Script written on PHP 8.3, but it will probably work on older versions too.
 *
 * Notes:
 * * Not accounted for public holidays.
 * * Although not in the specification, I assume people will get paid, even the last day is a weekend day.
 *   I've chosen to set this on the last working day of that month (friday)
 * * I assume the filename is correct and the user will account for overwrites.
 */

require "functions.inc.php";

// The first argument will contain the filename.
$filename = $argv[1]; // Todo: check if filename is OK and not in use.

// Open a file to write.
$csvFile = fopen($filename, "w");
fwrite($csvFile, "Month,BonusPayDate,BaseSalaryDate\r\n");

// Set date values to work with
$currentDate = new DateTime();
$currentMonth = (int)$currentDate->format('n');
$currentYear = $currentDate->format('Y');

$lastMonthOfYear = 12;

for ($month = $currentMonth; $month <= $lastMonthOfYear; $month++) {
    // Get the dates
    $bonusPayDate = getBonusPayDate($month, $currentYear);
    $baseSalaryDate = getBaseSalaryDate($month, $currentYear);

    // Get the month
    $monthName = $bonusPayDate->format('F');

    // Write to file
    $csvLine = "{$monthName},{$bonusPayDate->format('Y-m-d')},{$baseSalaryDate->format('Y-m-d')}\r\n";
    fwrite($csvFile, $csvLine);
}

fclose($csvFile);
die("File written to {$filename}\n");
