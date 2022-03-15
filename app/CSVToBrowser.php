<?php

namespace App;
/**
 *
 */
class CSVToBrowser
{
    /**
     * @var
     */
    private $headerColumns;
    /**
     * @var
     */
    private $bodyColumns;

    /**
     * @var string
     */
    private $separator;

    /**
     * @param $headerColumns
     * @param String|null $separator
     */
    public function __construct($headerColumns, string $separator = null)
    {
        $this->headerColumns = $headerColumns;
        $this->separator = $separator ?? ';';
    }


    /**
     * @param array $associativeArray
     * @param array|null $except
     * @param string $separator
     * @return CSVToBrowser
     */
    public static function createFromAssociativeArray(array $associativeArray, array $except = null, string $separator = ';'): CSVToBrowser
    {

        $newInstance = new CSVToBrowser(array_keys($associativeArray), $separator);
        if ($except !== null) {
            foreach ($except as $removeKeyName) {
                $newInstance->removeHeaderColumn($removeKeyName);
            }
        }
        return $newInstance;
    }

    public function generateCsvFromArray(array $rows, string $outputName): void
    {
        foreach ($rows as $row) {
            $this->addRow((array)$row);
        }
        $this->generateCSV($outputName);
    }

    /**
     * @param $columnName
     * @return void
     */
    public function removeHeaderColumn($columnName): void
    {
        unset($this->headerColumns[$columnName]);
    }

    /**
     * @param $data
     * @return void
     */
    public function addRow($data): void
    {
        if (count($data) < count($this->headerColumns)) {
            $difference = count($this->headerColumns) - count($data);
            for ($i = 0; $i <= $difference; $i++) {
                $data[] = 'No data provided for this column';
            }
        }
        $this->bodyColumns[] = $data;
    }

    /**
     * @param string $outputName The filename output
     * @return void
     */
    public function generateCSV(string $outputName): void
    {
        header('Content-Encoding: UTF-8');
        header('Content-Type: application/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename=' . $outputName . '.csv');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, $this->headerColumns, $this->separator);

        foreach ($this->bodyColumns as $row) {
            fputcsv($fp, $row, $this->separator);
        }
        fclose($fp);
    }


}
