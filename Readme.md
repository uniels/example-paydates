# Pay dates example

## Assignment

You are required to create a small command-line utility to help a fictional company determine the dates they need to pay
salaries to their sales department.

This company is handling their sales payroll in the following way:

* Sales staff gets a monthly fixed base salary and a monthly bonus.
* The base salaries are paid on the last day of the month unless that day is a Saturday or a Sunday (weekend).
* On the 15th of every month bonuses are paid for the previous month, unless that day is a weekend. In that case, they
  are paid the first Wednesday after the 15th.
* The output of the utility should be a CSV file, containing the payment dates for the remainder of this year. The CSV
  file should contain a column for the month name, a column that contains the salary payment date for that month, and a
  column that contains the bonus payment date.

## Implementation

### Requirements

php 8 installed locally and available using your favorite console.

### Runing the script

To run this script, simply call it in your console:
```shell
php getdates.php
```

It will generate the file `result.csv` in the same directory.

If you want to have another filename, you'll be able to provide one as first argument.
For example, if you want the filename to be `dates.csv`, you'll run:

```shell
php getdates.php dates.csv
```

## Considerations

I've chosen for an as simple as possible solution here. Requirements like this won´t often change,
and when they change, they will likely be small. 
The design of this script allows even someone with little programming knowledge to implement the changes.

You need to share only the `getdates.php` to have it working.

## More flavor

### Testing the functions
To used functions are tested inside `/tests.php`
Read the [`Readme.md`](./tests/Readme.md) for test instructions.

### Another implementation using classes

In `/overkill` you'll find a class-based implementation.
You'll be able to construct your own file using the classed available.
