<?php

namespace It\CsvImportBundle\Models\Checker;


/**
 * Class ProductChecker
 *
 * Verifies the product for valid data
 *
 * @package It\CsvImportBundle\Models\Checker
 */
class ProductChecker extends Checker
{
    private $errors = array();
    const MIN_LENGTH = 5;

    public function check(array $row)
    {
        $this->errors = array();

        $this->checkLength($row);
        $this->checkFields($row);

        return $this;
    }

    /**
     * Return all errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $row - one row of the file
     */
    private function checkLength($row)
    {
        if (count($row) < self::MIN_LENGTH) {
            $this->addError(count($row), 'The line must contain at least ' . self::MIN_LENGTH . ' values');
        }
    }

    /**
     * Checking all fields of product in row
     * @param $row
     */
    private function checkFields($row)
    {
        if ($row && is_array($row)) {
            $this->checkByCostAndStock($row);
            foreach ($row as $column => $val) {
                switch($column) {
                    case 0: $this->checkCode($column, $val); break;
                    case 1: $this->checkName($column, $val); break;
                    case 2: $this->checkDesc($column, $val); break;
                    case 3: $this->checkStock($column, $val); break;
                    case 4: $this->checkPrice($column, $val); break;
                    case 5: $this->checkDiscountinued($column, $val); break;
                }
            }
        }
    }

    /**
     * Rule for cost and stock
     * @param $row
     */
    private function checkByCostAndStock($row)
    {
        $stock = isset($row[3]) ? (float)$row[3] : 0;
        $price = isset($row[4]) ? (float)$row[4] : 0;
        if ($stock < 10 && $price < 5) {
            $this->addError(3, 'Stock < 10 and price < 5');
        }
    }

    /**
     * Rule for discountinued field
     * @param $column
     * @param $val
     */
    private function checkDiscountinued($column, $val)
    {
        $val = trim($val);
        if (!empty($val) && $val != 'yes') {
            $this->addError($column, 'Field value must be "yes" or empty');
        }
    }

    /**
     * Rules for the price
     * @param $column
     * @param $val
     */
    private function checkPrice($column, $val)
    {
        if (!is_numeric($val)) {
            $this->addError($column, 'Field must be a number');
        }

        if ((float)$val < 0) {
            $this->addError($column, 'Field must not be negative');
        }

        if ((float)$val > 1000) {
            $this->addError($column, 'Price can not be more than 1000');
        }
    }

    /**
     * Rules for the stock
     * @param $column
     * @param $val
     */
    private function checkStock($column, $val)
    {
        if (!is_numeric($val)) {
            $this->addError($column, 'Field must be a number');
        }

        if ((float)$val < 0) {
            $this->addError($column, 'Field must not be negative');
        }

        if ((float)$val > 99999999) {
            $this->addError($column, 'Number in the field is too large');
        }
    }

    /**
     * Rules for the description
     * @param $column
     * @param $val
     */
    private function checkDesc($column, $val)
    {
        if (mb_strlen($val) > 255) {
            $this->addError($column, 'Field must not exceed 255 characters');
        }
    }

    /**
     * Rules for the name
     * @param $column
     * @param $val
     */
    private function checkName($column, $val)
    {
        if (mb_strlen($val) > 50) {
            $this->addError($column, 'Field must not exceed 50 characters');
        }
    }

    /**
     * Rules for the code
     * @param $column
     * @param $val
     */
    private function checkCode($column, $val)
    {
        if (mb_strlen($val) > 10) {
            $this->addError($column, 'Field must not exceed 10 characters');
        }
    }

    /**
     * Add error information
     * @param $column
     * @param $message
     */
    private function addError($column, $message)
    {
        $this->errors[] = array(
            'column' => $column + 1,
            'message' => $message
        );
    }
}
