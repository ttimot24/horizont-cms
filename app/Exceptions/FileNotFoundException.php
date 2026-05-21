<?php

namespace App\Exceptions;

class FileNotFoundException extends \Exception
{
    public function __construct($path)
    {
        parent::__construct('File not found at path: '.$path);
    }
}
