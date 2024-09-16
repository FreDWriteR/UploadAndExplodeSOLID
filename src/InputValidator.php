<?php

namespace App;

use Exception;

class InputValidator
{
    public function validateDelimiter($delimiter)
    {
        if (strlen($delimiter) !== 1) {
            throw new Exception('Разделитель должен быть одним символом.');
        }
    }
}
