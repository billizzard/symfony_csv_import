<?php

namespace It\CsvImportBundle\Command;

use It\CsvImportBundle\Entity\ProductData;
use Doctrine\ORM\EntityManagerInterface;

use It\CsvImportBundle\Models\Checker\ProductChecker;
use It\CsvImportBundle\Models\MessageWriter\ConsoleWriter;
use It\CsvImportBundle\Models\Parser\Parser;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CsvImportCommand extends ContainerAwareCommand
{
    const VALID_ENCODING = ['UTF-8'];

    /** @var EntityManagerInterface  */
    private $em;

    /** @var Parser  */
    private $parser;

    public function __construct(EntityManagerInterface $em, Parser $parser, $name = null)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->parser = $parser;
    }

    protected function configure()
    {
        $this
            ->setName('import:csv')
            ->setDescription('Import csv file for Product Data')
            ->addOption(
                'mode',
                null,
                InputOption::VALUE_OPTIONAL,
                'Import can run in test mode (without change database)'
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_OPTIONAL,
                'File name in directory app/csvFiles'
            )
            ->addOption(
                'delimiter',
                null,
                InputOption::VALUE_OPTIONAL,
                'Delimiter of values'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mode = $this->getMode($input);
        $filePath = $this->getFilePath($input);
        $delimiter = $this->getDelimiter($input);
        $consoleWriter = new ConsoleWriter($output);
        $counter = array('numRow' => 0, 'successRow' => 0, 'errorRow' => 0);

        try {
            $this->parser->open($filePath);
            $this->parser->setDelimiter($delimiter);
            $this->detectEncoding($filePath);

            $consoleWriter->info('File: ' . $filePath);
            $consoleWriter->info('Delimiter: ' . $delimiter);

            if ($mode) {
                $consoleWriter->info('Mode: ' . $mode);
            }

            $productCsvChecker = new ProductChecker();

            while (($data = $this->parser->getRow()) !== false) {
                $counter['numRow']++;
                $errors = $productCsvChecker->check($data)->getErrors();

                if (!$errors) {
                    if ($mode !== 'test') {
                        $this->changeProductData($data);
                    }
                    $counter['successRow']++;
                } else {
                    $counter['errorRow']++;
                    foreach ($errors as $error) {
                        $consoleWriter->error('Error: ' . $error['message'] . '. Position: [row: ' . $counter['numRow'] . '; column: ' . $error['column'] . ']');
                    }
                }
            }

            $this->em->flush();

            if (!$counter['numRow']) {
                $consoleWriter->info('File is empty');
            } else {
                $consoleWriter->info('Total rows: ' . $counter['numRow']);
                $consoleWriter->info('Skipped rows: ' . $counter['errorRow']);
                $consoleWriter->success('Success rows: ' . $counter['successRow']);
            }
        } catch (\Exception $e) {
            $consoleWriter->error($e->getMessage());
        }
    }

    /**
     * Check valid encoding
     * @param $filePath - path to file
     * @throws \Exception
     */
    private function detectEncoding($filePath)
    {
        $encoding = mb_detect_encoding(file_get_contents($filePath), self::VALID_ENCODING);
        if (!in_array($encoding, self::VALID_ENCODING)) {
            throw new \Exception('Encoding is not supported. The encoding must be: ' . implode(', ', self::VALID_ENCODING));
        }

    }

    /**
     * If product exists - changes it. If not exist - creates it
     * @param $data - array with product data
     */
    private function changeProductData($data)
    {
        $repository = $this->em->getRepository(ProductData::class);
        $product = $repository->findOneBy(array('code' => $data[0]));

        if (!$product) {
            $product = new ProductData();
            $product->setCode($data[0]);
        }

        $product->setName($data[1]);
        $product->setDesc($data[2]);
        $product->setStockLevel((float)$data[3]);
        $product->setPrice((float)$data[4]);
        if (isset($data[5]) && $data[5] == 'yes') {
            $dt = new \DateTime();
            $dt->format('Y-m-d H:i:s');
            $product->setDiscontinued($dt);
        }
        $this->em->persist($product);
    }

    /**
     * Get mode from console
     * @param InputInterface $input
     * @return mixed
     */
    private function getMode(InputInterface $input)
    {
        $mode = $input->getOption('mode');
        if ($mode && $mode != 'test') {
            throw new RuntimeException('Mode ' . $mode . ' not existing.');
        }

        return $mode;
    }

    /**
     * Get file name from console, or from config.yml
     * @param InputInterface $input
     * @return string
     */
    private function getFilePath(InputInterface $input)
    {
        $fileName = $input->getOption('name');
        
        if (!$fileName) {
            $fileName = $this->getContainer()->getParameter('it_csv_import.name');
        }
        
        return $this->getContainer()->get('kernel')->getRootDir() . '/csvFiles/' . $fileName;
    }

    /**
     * Get delimiter from console, or from config.yml
     * @param InputInterface $input
     * @return mixed
     */
    private function getDelimiter(InputInterface $input)
    {
        $delimiter = $input->getOption('delimiter');

        if (!$delimiter) {
            $delimiter = $this->getContainer()->getParameter('it_csv_import.delimiter');
        }

        return $delimiter;
    }
}