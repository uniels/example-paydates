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

if (__FILE__ == realpath($_SERVER['SCRIPT_FILENAME'])):
    // Running this script directly

    // The first argument will contain the filename.
    $filename = $argv[1] ?? 'result.csv';

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

endif;

/**
 * Determine if the current DateTime is on a working day (monday-friday)
 *
 * @param DateTime $date
 * @return bool
 */
function isWorkingDay(DateTime $date):bool {
    $dayOfWeek = (int)$date->format('N'); // 1 = monday, 7 = sunday
    return $dayOfWeek <= 5;
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
    $day = '15'; // The 15th day of this month.
    $proposedDate = new DateTime("{$year}-{$month}-{$day}");

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
    // The proposed date will be set on the last day of the month.
    // For more formatting, see: https://www.php.net/manual/en/datetime.formats.php#datetime.formats.relative
    $proposedDate = (new DateTime("{$year}-{$month}-01"))->modify('last day of this month');

    if (isWorkingDay($proposedDate)) {
        return $proposedDate;
    }
    return $proposedDate->modify('last friday');
}
