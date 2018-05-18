<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 08.05.18
 * Time: 10:41
 */

namespace It\CsvImportBundle\Models\Checker;


abstract class Checker
{
    abstract public function check(array $row);

    abstract public function getErrors();
}