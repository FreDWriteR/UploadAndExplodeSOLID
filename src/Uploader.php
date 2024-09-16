<?php
namespace App;

use Exception;

class Uploader
{
    private $uploadDir;

    public function __construct($uploadDir)
    {
        $this->uploadDir = $uploadDir;
        $this->isUploadDirExists();
    }

    private function isUploadDirExists()
    {
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception('Не удалось создать папку для загрузки.');
            }
        }
    }

    public function upload($file)
    {
        $fileName = basename($file['name']);
        $targetFilePath = $this->uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            throw new Exception('Ошибка при сохранении файла. Возможно,<br>директория недоступна/не существует<br>или у вас нет разрешения на запись.');
        }

        return ['path' => $targetFilePath, 'size' => $file['size'], 'error' => $file['error']];
    }
}