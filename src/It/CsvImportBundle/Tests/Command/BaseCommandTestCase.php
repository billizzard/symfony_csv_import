<?php

namespace It\CsvImportBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use It\CsvImportBundle\Command\CsvImportCommand;
use It\CsvImportBundle\Models\Parser\CsvParser;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class BaseCommandTestCase extends KernelTestCase
{
    const COMMAND = 'import:csv';

    /** @var EntityManagerInterface */
    protected static $entityManager;

    /** @var CommandTester */
    protected static $commandTester;

    public static function setUpBeforeClass()
    {
        self::bootKernel();

        self::$entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $application = new Application(static::$kernel);
        $application->add(new CsvImportCommand(static::$entityManager, new CsvParser()));
        $command = $application->find(self::COMMAND);
        static::$commandTester = new CommandTester($command);

        static::loadFixtures();
        parent::setUpBeforeClass();
    }

    /**
     * Load fixtures in children classes
     */
    protected static function loadFixtures()
    {

    }

    /**
     * Get dir for save test csv files
     * @return string
     */
    protected static function getFileDir()
    {
        $filePath = static::$kernel->getRootDir() . '/csvFiles/';
        self::createDirIfNotExist($filePath);
        $filePath .= 'test/';
        self::createDirIfNotExist($filePath);
        return $filePath;
    }

    /**
     * Create csv file form array
     * @param $fileName
     * @param $array
     * @param string $delimiter
     */
    protected static function createCsvFileFromArray($fileName, $array, $delimiter = ',')
    {
        $filePath = self::getFileDir() . $fileName;
        $fp = fopen($filePath, 'w+');

        foreach ($array as $fields) {
            fputcsv($fp, $fields, $delimiter);
        }

        fclose($fp);
    }

    public function assertSuccessRows($num, $output)
    {
        $this->assertContains('Success rows: ' . $num, $output);
    }

    public function assertSkippedRows($num, $output)
    {
        $this->assertContains('Skipped rows: ' . $num, $output);
    }

    public function assertTotalRows($num, $output)
    {
        $this->assertContains('Total rows: ' . $num, $output);
    }

    /**
     * Creates a string of a certain length
     * @param $len
     * @return string
     */
    protected static function createStringLen($len)
    {
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= 'a';
        }
        return $str;
    }

    public static function tearDownAfterClass()
    {
        self::$entityManager = null;
        self::$commandTester = null;
        self::removeTestFiles();
    }

    /**
     * Execute import:scv command for file $fileName
     * @param $fileName
     */
    protected static function executeCommand($fileName)
    {
        static::$commandTester->execute(array(
            'command'  => self::COMMAND,
            '--mode' => 'test',
            '--delimiter' => ',',
            '--name' => 'test/' . $fileName
        ));
    }

    private static function removeTestFiles()
    {
        array_map('unlink', glob(self::getFileDir() ."*.*"));
        rmdir(self::getFileDir());
    }

    private static function createDirIfNotExist($dir)
    {
        if (!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir);
        }
    }
}
