<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.05.18
 * Time: 10:41
 */

namespace It\CsvImportBundle\Models\Parser;


interface Parser
{
    public function open($filePath);
    public function close();
    public function getRow();
}