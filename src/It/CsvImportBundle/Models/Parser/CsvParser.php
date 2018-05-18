<?php

namespace It\CsvImportBundle\Models\Parser;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class CsvParser
 * Parser for csv files
 * @package It\CsvImportBundle\Models\Parser
 */
class CsvParser implements Parser
{
    private $titles = [];
    private $resource;
    private $delimiter;

    /**
     * Try to open file, and get titles from file
     * @param $filePath
     * @param array $options
     */
    public function open($filePath)
    {
        if (file_exists($filePath)) {
            $this->resource = @fopen($filePath, "r");
            if ($this->resource) {
                $titles = fgetcsv($this->resource, 4096, $this->getDelimiter());
                $this->setTitles($titles);
            } else {
                throw new FileNotFoundException('Cannot open file: ' . $filePath);
            }
        } else {
            throw new FileNotFoundException('File not found: ' . $filePath);
        }
    }

    /**
     * Return one row from file
     * @return array|false|null
     */
    public function getRow()
    {
        $row = fgetcsv($this->resource, 4096, $this->getDelimiter());
        return $row;
    }

    /**
     * Close all resources
     */
    public function close()
    {
        if ($this->resource) {
            fclose($this->resource);
        }
    }

    public function setTitles($titles)
    {
        $this->titles = $titles;
    }

    public function getTitles()
    {
        return $this->titles;
    }

    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }

    public function getDelimiter()
    {
        return $this->delimiter ? $this->delimiter : ',';
    }

}