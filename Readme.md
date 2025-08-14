# Pay dates example

## Requirements

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
An assignment like this is what I would like to call a 'beer-script'. 
A simple script which does not need a full-blown implementation and I normally create while sipping my beer. 🍺

Like the one created in [getdates.php](./getdates.php).

To run this script, simply call it in your console:

```shell
php getdates.php
```

> Make sure you've installed php locally

### More flavor

To add more flavor to it, more files are added, like
* files in `./overkill`. A class based implementation.
* files in `/tests`. Included a test class which tests the functions inside `functions.inc.php`.

