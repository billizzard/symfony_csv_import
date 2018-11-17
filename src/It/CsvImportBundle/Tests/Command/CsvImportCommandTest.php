<?php

namespace It\CsvImportBundle\Tests\Command;

class CsvImportCommandTest extends BaseCommandTestCase
{
    protected static function loadFixtures()
    {
        $correct = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array('P0001','TV','32” Tv','10','399.99'),
            array('P0002','TV','32” Tv','10','399.99', 'yes')
        );

        $incorrectFewStockAndPrice = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array('aaa','aaa','aaa','9','4.5'),
        );

        $incorrectPrice = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array('aaa','aaa','aaa','11','aaa'),
        );
        
        $incorrectDiscontinued = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array('aaa','aaa','aaa','11','45.5', 'aaa'),
        );

        $incorrectStock = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array('aaa','aaa','aaa','aaa','45.5'),
        );

        $requiredFields = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array('aaa','aaa','aaa','11'),
        );

        $maxPrice = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array('aaa','aaa','aaa','11','1001'),
        );
        
        $incorrectLength = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array(
                self::createStringLen(11),
                self::createStringLen(51),
                self::createStringLen(256),
                11,
                45.5225
            ),
        );

        $empty = array();

        self::createCsvFileFromArray('correct.csv', $correct);
        self::createCsvFileFromArray('incorrectFewStockAndPrice.csv', $incorrectFewStockAndPrice);
        self::createCsvFileFromArray('incorrectPrice.csv', $incorrectPrice);
        self::createCsvFileFromArray('incorrectDiscontinued.csv', $incorrectDiscontinued);
        self::createCsvFileFromArray('incorrectStock.csv', $incorrectStock);
        self::createCsvFileFromArray('requiredFields.csv', $requiredFields);
        self::createCsvFileFromArray('maxPrice.csv', $maxPrice);
        self::createCsvFileFromArray('incorrectLength.csv', $incorrectLength);
        self::createCsvFileFromArray('empty.csv', $empty);
    }

    /**
     * Testing the correct file
     */
    public function testCorrectFile()
    {
        static::executeCommand('correct.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertSuccessRows(2, $output);
    }

    /**
     * Testing file where stock < 5 and price < 10
     */
    public function testIncorrectFewStockAndPrice()
    {
        static::executeCommand('incorrectFewStockAndPrice.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertContains('Stock < 10 and price < 5', $output);
        $this->assertSuccessRows(0, $output);        
    }

    /**
     * Testing file where price is not numeric
     */
    public function testIncorrectPrice()
    {
        static::executeCommand('incorrectPrice.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertContains('must be a number', $output);
        $this->assertSuccessRows(0, $output);
    }

    /**
     * Testing file where discounted filed is incorrect
     */
    public function testIncorrectDiscontinued()
    {
        static::executeCommand('incorrectDiscontinued.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertContains('must be "yes" or empty', $output);
        $this->assertSuccessRows(0, $output);
    }

    /**
     * Testing file where stock is is not numeric
     */
    public function testIncorrectStock()
    {
        static::executeCommand('incorrectStock.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertContains('must be a number', $output);
        $this->assertSuccessRows(0, $output);
    }

    /**
     * Test file without price
     */
    public function testEmptyPrice()
    {
        static::executeCommand('requiredFields.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertContains('must contain at least 5 values', $output);
        $this->assertSuccessRows(0, $output);
    }

    /**
     * Test file where price more than 1000
     */
    public function testMaxPrice()
    {
        static::executeCommand('maxPrice.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertContains('can not be more than 1000', $output);
        $this->assertSuccessRows(0, $output);
    }

    /**
     * Testing file where length code > 10, name > 50, description > 255
     */
    public function testIncorrectLength()
    {
        static::executeCommand('incorrectLength.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertContains('must not exceed 10 characters', $output);
        $this->assertContains('must not exceed 50 characters', $output);
        $this->assertContains('must not exceed 255 characters', $output);
        $this->assertSuccessRows(0, $output);
    }

    /**
     * Test empty file
     */
    public function testEmpty()
    {
        static::executeCommand('empty.csv');

        $output = static::$commandTester->getDisplay();
        $this->assertContains('File is empty', $output);
    }
}
