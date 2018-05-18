<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.05.18
 * Time: 10:44
 */

namespace It\CsvImportBundle\Models\MessageWriter;


interface MessageWriter
{
    public function error($message);

    public function info($message);

    public function success($message);
}