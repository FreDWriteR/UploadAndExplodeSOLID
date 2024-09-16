<?php
namespace App;

use Exception;

class Validator
{
    private $errorCodes = [
        UPLOAD_ERR_OK => 'Файл загружен успешно.',
        UPLOAD_ERR_INI_SIZE => 'Размер файла превышает максимальный размер,<br>предусмотренный сервером (php.ini).',
        UPLOAD_ERR_FORM_SIZE => 'Размер файла превышает значение, указанное<br>в атрибуте MAX_FILE_SIZE формы.',
        UPLOAD_ERR_PARTIAL => 'Файл был загружен только частично.',
        UPLOAD_ERR_NO_FILE => 'Файл не был загружен.',
        UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка.',
        UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
        UPLOAD_ERR_EXTENSION => 'Загрузка файла прервана расширением PHP.'
    ];

    public function validate($file)
    {
        // Проверка кода ошибки загрузки
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = $this->errorCodes[$file['error']] ?? 'Неизвестная ошибка загрузки.';
            throw new Exception($errorMessage);
        }

        $uploadMaxFilesizeBytes = $this->parseSize(ini_get('upload_max_filesize')); //Максимальный размер файла ограничиваемый php.ini
        $postMaxSizeBytes = $this->parseSize(ini_get('post_max_size')); //Максимальный размер POST ограничиваемый php.ini

        if ($file['size'] > $uploadMaxFilesizeBytes) {
            throw new Exception('Размер файла превышает максимальный размер, предусмотренный сервером (php.ini).');
        }

        if ($file['size'] > $postMaxSizeBytes) {
            throw new Exception('Размер файла превышает максимальный размер данных POST-запроса (php.ini).');
        }

        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($fileType !== 'txt') {
            throw new Exception('Файл должен быть .txt');
        }


    }

    private function parseSize($size)
    {
        $unit = strtolower(substr($size, -1));
        $size = (int) $size;
        switch ($unit) {
            case 'g': $size *= 1024 * 1024 * 1024; break; // Гигабайты
            case 'm': $size *= 1024 * 1024; break; // Мегабайты
            case 'k': $size *= 1024; break; // Килобайты
        }
        return $size;
    }
}
