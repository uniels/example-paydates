<?php
require('./classes.php');

// The first argument will contain the filename.
$filename = $argv[1];

// Set date values to work with
$currentDate = new DateTime();
$currentMonth = (int)$currentDate->format('n');
$currentMonth = 1;
$currentYear = $currentDate->format('Y');

// Solution
$csv = new DateFileGenerator($currentYear, $currentMonth);

$csv->addColumn(new Column("Month", new MonthNameGenerator()));
$csv->addColumn(
    new Column(
        "BonusPayDate",
        new ShouldBeOnWorkingDay(
            new FixedDateGenerator(15),
            'next wednesday'
        )
    )
);
$csv->addColumn(
    new Column(
        "BaseSalaryDate",
        new ShouldBeOnWorkingDay(
            new RelativeDateGenerator('last day of this month'),
            'last friday'
        )
    )
);

$csv->writeFile($filename);

die();

