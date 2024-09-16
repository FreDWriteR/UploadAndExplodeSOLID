<?php
namespace App;

use Exception;

class Processor
{
    private $delimiter;

    public function __construct($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function processWithExplodeAndPreg($fileContent)
    {
        $lines = explode($this->delimiter, $fileContent);
        $result = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line) {
                //Использование preg_match_all
                $digitsCount = preg_match_all('/\d/', $line);

                //Использование preg_replace + strlen
//                $onlyDigits = preg_replace('/\D/', '', $line);
//                $digitsCount = strlen($onlyDigits);

                //Использование preg_replace_callback
//                $digitsCount = 0;
//                preg_replace_callback('/\d/', function() use (&$digitsCount) {
//                    $digitsCount++;
//                }, $line);

                $result[] = $line . ' = ' . $digitsCount;
            }
        }

        return ['lines' => $result];
    }

    public function processWithPregSplitAndPreg($fileContent)
    {
        $lines = preg_split('/\s*' . preg_quote($this->delimiter, '/') . '\s*/', $fileContent, -1, PREG_SPLIT_NO_EMPTY);
        $result = [];

        foreach ($lines as $line) {
            $digitsCount = preg_match_all('/\d/', $line);

            //Использование preg_replace + strlen
//                $onlyDigits = preg_replace('/\D/', '', $line);
//                $digitsCount = strlen($onlyDigits);

            //Использование preg_replace_callback
//                $digitsCount = 0;
//                preg_replace_callback('/\d/', function() use (&$digitsCount) {
//                    $digitsCount++;
//                }, $line);

            $result[] = $line . ' = ' . $digitsCount;
        }

        return ['lines' => $result];
    }

    public function processOneLineAtATimeAndPreg($filePath)
    {
        $fileHandle = fopen($filePath, 'r');
        if (!$fileHandle) {
            throw new Exception('Не удалось открыть файл.');
        }

        $result = [];
        $line = '';
        while (($fileLine = fgets($fileHandle)) !== false) {
            $fileLine = trim($fileLine, "\n\r");

            if ($fileLine === '') {
                continue;
            }
            if (!str_contains($fileLine, $this->delimiter)) {
                $line .= $fileLine;
            } else {
                $lines = explode($this->delimiter, $line . $fileLine);
                $line = '';

                foreach ($lines as $currentLine) {
                    if ($currentLine) {
                        $digitsCount = preg_match_all('/\d/', $currentLine);

                        //Использование preg_replace + strlen
//                      $onlyDigits = preg_replace('/\D/', '', $line);
//                      $digitsCount = strlen($onlyDigits);

                        //Использование preg_replace_callback
//                      $digitsCount = 0;
//                      preg_replace_callback('/\d/', function() use (&$digitsCount) {
//                          $digitsCount++;
//                      }, $line);

                        $result[] = $currentLine . ' = ' . $digitsCount;
                    }
                }
            }
        }

        if ($line) {
            $digitsCount = preg_match_all('/\d/', $line);
            $result[] = $line . ' = ' . $digitsCount;
        }

        fclose($fileHandle);

        return ['lines' => $result];
    }

    public function processOneLineAtATimeWithFgetcsvAndPreg($filePath)
    {
        $fileHandle = fopen($filePath, 'r');
        if (!$fileHandle) {
            throw new Exception('Не удалось открыть файл.');
        }

        $result = [];
        $line = '';
        while (($lines = fgetcsv($fileHandle, 0, $this->delimiter)) !== false) {
            if ($lines && count($lines) == 1) {
                $line .= $lines[0];
            }
            else if ($lines) {
                $lines[0] = $line . $lines[0];
                $line = '';

                foreach ($lines as $currentLine) {
                    if ($currentLine) {
                        $digitsCount = preg_match_all('/\d/', $currentLine);

                        //Использование preg_replace + strlen
    //                      $onlyDigits = preg_replace('/\D/', '', $line);
    //                      $digitsCount = strlen($onlyDigits);

                        //Использование preg_replace_callback
    //                      $digitsCount = 0;
    //                      preg_replace_callback('/\d/', function() use (&$digitsCount) {
    //                          $digitsCount++;
    //                      }, $line);

                        $result[] = $currentLine . ' = ' . $digitsCount;
                    }
                }
            }
        }

        if ($line) {
            $digitsCount = preg_match_all('/\d/', $line);
            $result[] = $line . ' = ' . $digitsCount;
        }

        fclose($fileHandle);

        return ['lines' => $result];
    }

    public function processWithStreamGetLineAndPreg($filePath)
    {
        $fileHandle = fopen($filePath, 'r');
        if (!$fileHandle) {
            throw new Exception('Не удалось открыть файл.');
        }

        $result = [];
        while (($line = stream_get_line($fileHandle, 1024, $this->delimiter)) !== false) {
            $line = trim($line);
            if ($line !== '') {
                $digitsCount = preg_match_all('/\d/', $line);
                $result[] = $line . ' = ' . $digitsCount;
            }
        }

        fclose($fileHandle);

        return ['lines' => $result];
    }
}

