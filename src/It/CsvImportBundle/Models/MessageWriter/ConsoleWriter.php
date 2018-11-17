<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.05.18
 * Time: 10:46
 */

namespace It\CsvImportBundle\Models\MessageWriter;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConsoleWriter
 * @package It\CsvImportBundle\Models\MessageWriter
 */
class ConsoleWriter implements MessageWriter
{
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function error($message)
    {
        $this->output->writeln('<fg=red>' . $message . '</>');
    }

    public function info($message)
    {
        $this->output->writeln('<fg=yellow>' . $message . '</>');
    }

    public function success($message)
    {
        $this->output->writeln('<fg=green>' . $message . '</>');
    }
}