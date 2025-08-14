<?php

namespace tests;

use DateTime;
use PHPUnit\Framework\TestCase;

require_once __DIR__."/../../functions.inc.php";

class FunctionsTest extends TestCase
{
    public function testIsWorkingDayFunction(): void
    {
        // Arrange
        $workingDays = [
            new DateTime("2025-08-04"), // mo
            new DateTime("2025-08-05"), // tu
            new DateTime("2025-08-06"), // we
            new DateTime("2025-08-07"), // th
            new DateTime("2025-08-08"), // fr
        ];
        $weekendDays = [
            new DateTime("2025-08-09"), // sa
            new DateTime("2025-08-10"), // su
        ];

        // Act + Assert
        foreach ($workingDays as $workingDay) {
            $this->assertTrue(isWorkingDay($workingDay));
        }

        foreach ($weekendDays as $weekendDay) {
            $this->assertFalse(isWorkingDay($weekendDay));
        }
    }

    /**
     * The getBaseSalaryDate function should return the last day of the month.
     * When the last day of the month is on a weekend, it should return the
     * last working day, hence the friday before.
     *
     * @return void
     */
    public function testGetBaseSalaryDateFunction(): void
    {
        // Arrange
        $testcases = [
            // [Year, month, expected result]
            [2025, 1, new DateTime("2025-01-31")], // last day fri
            [2025, 3, new DateTime("2025-03-31")], // last day mon
            [2025, 4, new DateTime("2025-04-30")], // last day wed
            [2025, 5, new DateTime("2025-05-30")], // last day sat -> shift
            [2025, 7, new DateTime("2025-07-31")], // last day thu
            [2025, 8, new DateTime("2025-08-29")], // last day sun -> shift
            [2025, 9, new DateTime("2025-09-30")], // last day tue
        ];

        // Act + Assert
        foreach ($testcases as [$year, $month, $expectedResult]) {
            $this->assertEquals($expectedResult, getBaseSalaryDate($month, $year));
        }
    }

    /**
     * The getBonusPayDate function should return the 15th of a month,
     * given month and year, except when this is on a weekend day.
     * When it is on a weekend day, it should return the date on next wednesday.
     */
    public function testGetBonusDateFunction(): void
    {
        // Arrange
        $testcases = [
            [2025, 1, new DateTime("2025-01-15")], // wed
            [2025, 2, new DateTime("2025-02-19")], // sat -> 19
            [2025, 4, new DateTime("2025-04-15")], // tue
            [2025, 5, new DateTime("2025-05-15")], // thu
            [2025, 6, new DateTime("2025-06-18")], // sun -> 18
            [2025, 8, new DateTime("2025-08-15")], // fri
            [2025, 9, new DateTime("2025-09-15")], // mon
        ];

        // Act + Assert
        foreach ($testcases as [$year, $month, $expectedResult]) {
            $this->assertEquals($expectedResult, getBonusPayDate($month, $year));
        }
    }
}
