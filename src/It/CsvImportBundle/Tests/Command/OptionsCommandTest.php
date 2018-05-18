<?php

namespace It\CsvImportBundle\Tests\Command;

use It\CsvImportBundle\Command\CsvImportCommand;
use It\CsvImportBundle\Models\Parser\CsvParser;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class OptionsCommandTest extends BaseCommandTestCase
{
    protected static function loadFixtures()
    {
        $correct = array(
            array('Product Code','Product Name','Product Description','Stock','Cost in GBP','Discontinued'),
            array('P0001','TV','32” Tv','10','399.99'),
            array('P0002','TV','32” Tv','10','399.99')
        );
        self::createCsvFileFromArray('changedDelimiter.csv', $correct, '|');
        self::createCsvFileFromArray('customName.csv', $correct);
    }

    /**
     * Test file with not default delimiter, and check delimiter option
     */
    public function testDelimiterOption()
    {
        self::$commandTester->execute(array(
            'command'  => self::COMMAND,
            '--mode' => 'test',
            '--name' => 'test/changedDelimiter.csv',
            '--delimiter' => '|'
        ));

        $output = self::$commandTester->getDisplay();
        $this->assertSuccessRows(2, $output);
    }

    /**
     * Test option: name
     */
    public function testNameOption()
    {
        static::executeCommand('customName.csv');

        $output = self::$commandTester->getDisplay();
        $this->assertSuccessRows(2, $output);
    }
}
