<?php
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