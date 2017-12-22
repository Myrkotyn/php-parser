<?php

namespace App;

class FileGenerator
{
    private $fileName;
    const FILE_PATH = 'files/';

    public function __construct($fileName, $url)
    {
        $this->fileName = $fileName;
        $this->createFile($url);
    }

    private function createFile($url)
    {
        $file = fopen($this->getFullPath(), 'a+');
        fputcsv($file, ['Resource', $url]);
        fclose($file);
    }

    private function getFullPath()
    {
        return self::FILE_PATH . $this->fileName . '.csv';
    }

    public function writeToFile($data)
    {
        $file = fopen($this->getFullPath(), 'a+');
        fputcsv($file, $data);
        fclose($file);
    }
}