<?php
// Interfaces

interface DateValueGeneratorContract {
    /**
     * Will write the cell
     */
    public function getValue(): string;

    /**
     * Will set the working year and month.
     * @param int $year
     * @param int $month
     * @return self
     */
    public function setYearAndMonth(int $year, int $month): self;
}

interface ColumnContract
{
    /**
     * The title of this column, as header.
     *
     * @return string
     */
    public function getHeader(): string;

    /**
     * The value generator
     *
     * @return DateValueGeneratorContract
     */
    public function getValueGenerator(): DateValueGeneratorContract;
}

interface FileGeneratorContract {

    /**
     * Add a column to the file.
     * Will put the Header on top and will calculate the value for every date.
     *
     */
    public function addColumn(ColumnContract $column): self;

    /**
     * Actual writing the content to a file, given its name.
     *
     * @param string $filename
     * @return void
     */
    public function writeFile(string $filename): void;
}

// Implementation

/**
 * @property-read int $year The year for generation.
 * @property-read int $startMonth The month (1–12) when generation starts.
 */
class DateFileGenerator implements FileGeneratorContract {
    /**
     * @var ColumnContract[]
     */
    private array $columns = [];
    public function __construct(
        private readonly int $year,
        private readonly int $startMonth = 1,
    ) {}

    public function addColumn(ColumnContract $column): self
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @param string[] $values
     * @param SplFileObject $csvFile
     * @return void
     */
    private function writeRowData(SplFileObject $csvFile, array $values): void
    {
        // future: maybe check for safety? (No additional ',' and such)
        $row = implode(",", $values);
        $csvFile->fwrite("{$row}\r\n");
    }

    public function writeFile(string $filename): void
    {
        $csvFile = $this->getCsvFile($filename);

        // Headers
        $headers = array_map(fn ($c) => $c->getHeader(), $this->columns);
        $this->writeRowData($csvFile, $headers);

        // Rows
        $lastMonthOfYear = 12;

        for ($month = $this->startMonth; $month <= $lastMonthOfYear; $month++) {
            // Single row, every column
            $rowValues = array_map(
                fn ($c) => $c->getValueGenerator()->setYearAndMonth($this->year, $month)->getValue(),
                $this->columns
            );
            $this->writeRowData($csvFile, $rowValues);
        }
    }

    /**
     * @param string $filename
     * @return SplFileObject
     */
    private function getCsvFile(string $filename): SplFileObject
    {
        // Todo: check if filename is OK and not in use.
        $csvFile = new SplFileObject($filename, 'w');
        return $csvFile;
    }
}

// Concrete Date Classes

/**
 * The columns for the file.
 */
readonly class Column implements ColumnContract
{
    public function __construct(
        private string                     $header,
        private DateValueGeneratorContract $valueGenerator
    )
    {}

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getValueGenerator(): DateValueGeneratorContract
    {
        return $this->valueGenerator;
    }
}

abstract class DateValueGenerator implements DateValueGeneratorContract {
    protected int $year = 2025;
    protected int $month = 1;

    /**
     * The generated DateTime object.
     *
     * @return DateTime
     */
    abstract protected function getDate(): DateTime;

    /**
     * The format of the outputted date string.
     *
     * @return string
     * @see https://www.php.net/manual/en/datetime.format.php
     */
    abstract protected function getFormat(): string;

    public function getValue(): string
    {
        return $this->getDate()->format($this->getFormat());
    }

    public function setYearAndMonth(int $year, int $month): self
    {
        $this->year = $year;
        $this->month = $month;

        return $this;
    }
}

/**
 * Modifies a date from the given $dateValueGenerator, when this is not on a working day.
 */
class ShouldBeOnWorkingDay extends DateValueGenerator
{
    /**
     * @param DateValueGeneratorContract $dateValueGenerator The (generated) date to check.
     * @param string $ifNotOnWorkingDay The modification on the date.
     * @see https://www.php.net/manual/en/datetime.modify.php
     */
    public function __construct(private DateValueGeneratorContract $dateValueGenerator, private readonly string $ifNotOnWorkingDay)
    {
    }

    protected function getDate(): DateTime
    {
       $proposedDate = $this->dateValueGenerator->setYearAndMonth($this->year, $this->month)->getDate();

        return $this->checkForWorkingDay($proposedDate);
    }

    protected function getFormat(): string
    {
        return $this->dateValueGenerator->getFormat();
    }

    protected function checkForWorkingDay(DateTime $proposedDate): DateTime
    {
        // Check if we need to adjust this day.
        if ($this->isWorkingDay($proposedDate)) {
            return $proposedDate;
        }

        return $proposedDate->modify($this->ifNotOnWorkingDay);
    }

    protected function isWorkingDay(DateTime $date): bool
    {
        $dayOfWeek = (int)$date->format('N'); // 1 = monday, 7 = sunday
        return $dayOfWeek <= 5;
    }
}

/**
 * A Class which will get the name of the month only.
 */
class MonthNameGenerator extends DateValueGenerator {

    protected function getDate(): DateTime
    {
        return new DateTime("{$this->year}-{$this->month}-1");
    }

    protected function getFormat(): string
    {
        return 'F'; // Full name of this month
    }
}

// idea: DateFormatter also as Middleware?
trait FormatAsNormalDate {
    protected function getFormat(): string
    {
        return 'Y-m-d';
    }
}

/**
 * Generates a date for a specific day.
 */
class FixedDateGenerator extends DateValueGenerator {

    use FormatAsNormalDate;

    /**
     * @param int $day The day of the month.
     */
    public function __construct(private readonly int $day)
    {}

    protected function getDate(): DateTime
    {
        // Todo: check if date exists (e.g. February the 30th or April the 31th or August the 50th)
        return new DateTime("{$this->year}-{$this->month}-{$this->day}");
    }
}

/**
 * Get a relative date
 */
class RelativeDateGenerator extends DateValueGenerator {
    use FormatAsNormalDate;

    /**
     * @param string $relativeDay The modification of the DateTime Object.
     * @see https://www.php.net/manual/en/datetime.modify.php
     */
    public function __construct(private readonly string $relativeDay)
    {}

    protected function getDate(): DateTime
    {
        // todo: escape it when $relativeDay is not compatible. Provide own Exception here?
        return (new DateTime("{$this->year}-{$this->month}-1"))->modify($this->relativeDay);
    }
}