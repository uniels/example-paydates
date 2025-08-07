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

/**
 * Determine if the current DateTime is on a working day (monday-friday)
 *
 * @param DateTime $date
 * @return bool
 */
function isWorkingDay(DateTime $date):bool {
    $dayOfWeek = (int)$date->format('w');
    $transposedDayOfWeek = (6 + $dayOfWeek) % 7; // 0=monday, 7=sunday
    return $transposedDayOfWeek <= 5;
}

/**
 * The bonus paid date is on the 15th, except this is in the weekend.
 * Then it will be paid next wednesday.
 *
 * @param int $month
 * @param int $year
 * @return DateTime
 */
function getBonusPayDate(int $month, int $year): DateTime
{
    $proposedDate = new DateTime("{$year}-{$month}-15");

    // Check if this is a working day.
    if (isWorkingDay($proposedDate)) {
        return $proposedDate;
    }

    return $proposedDate->modify('next wednesday');
}

/**
 * The Base Salary date is on the last working day of the month.
 *
 * @param int $month
 * @param int $year
 * @return DateTime
 */
function getBaseSalaryDate(int $month, int $year): DateTime
{
    $proposedDate = (new DateTime("{$year}-{$month}-01"))->modify('last day of this month');
    if (isWorkingDay($proposedDate)) {
        return $proposedDate;
    }
    return $proposedDate->modify('last friday');
}