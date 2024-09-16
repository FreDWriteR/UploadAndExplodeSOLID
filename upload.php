<?php
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', 0);

    require 'vendor/autoload.php';

    use App\Uploader;
    use App\Validator;
    use App\Processor;
    use App\InputValidator;


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadedFile'])) {
        $uploadDir = __DIR__ . '/files/';
        $delimiter = $_POST['delimiter'];

        try {
            $inputValidator = new InputValidator();
            $inputValidator->validateDelimiter($delimiter);

            $uploader = new Uploader($uploadDir);
            $validator = new Validator();
            $processor = new Processor($delimiter);

            $uploadedFile = $_FILES['uploadedFile'];

            $validator->validate($uploadedFile);
            $uploadedFile = $uploader->upload($uploadedFile);

            $fileContent = file_get_contents($uploadedFile['path']);

            $startTime = microtime(true);
            $startMemory = memory_get_usage();

            $result = $processor->processWithExplodeAndPreg($fileContent);
//            $result = $processor->processWithPregSplitAndPreg($fileContent);
//            $result = $processor->processOneLineAtATimeAndPreg($uploadedFile['path']);
//            $result = $processor->processOneLineAtATimeWithFgetcsvAndPreg($uploadedFile['path']);
//            $result = $processor->processWithStreamGetLineAndPreg($uploadedFile['path']);

            $executionTime = microtime(true) - $startTime;
            $memoryUsage = round((memory_get_usage() - $startMemory) / 1024 / 1024, 4);

            $result['executionTime'] = round($executionTime, 4);
            $result['memoryUsage'] = $memoryUsage;

            $result['methodName'] = 'processWithExplodeAndPreg';
//            $result['methodName'] = 'processWithPregSplitAndPreg';
//            $result['methodName'] = 'processOneLineAtATimeAndPreg';
//            $result['methodName'] = 'processOneLineAtATimeWithFgetcsvAndPreg';
//            $result['methodName'] = 'processWithStreamGetLineAndPreg';

            echo json_encode(['status' => 'success', 'result' => $result]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }