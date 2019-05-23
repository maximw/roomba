<?php


namespace App\Service\Runners;


use App\Exceptions\JsonRunnerException;

class JsonRunner
{

    public function run(string $inputFile, string $outputFile)
    {
        if (!file_exists($inputFile) || !is_readable($inputFile) || is_dir($inputFile)) {
            throw new JsonRunnerException('File is not readable ' . $inputFile);
        }

        if (file_exists($outputFile)) {
            if (!is_writable($outputFile) || is_dir($outputFile)) {
                throw new JsonRunnerException('File is not writable ' . $outputFile);
            }
        } else {
            $directory = dirname($outputFile);
            if (!is_dir($directory) || !is_writable($directory)) {
                throw new JsonRunnerException('File is not writable ' . $outputFile);
            }
        }

        $data = $this->loadFromFile($inputFile);

    }

    protected function loadFromFile(string $inputFile): array
    {
        $json = file_get_contents($inputFile);
        $data = json_decode($json, true);
        if (json_last_error()) {
            throw new JsonRunnerException('Bad JSON format ' . json_last_error_msg());
        }
        if (empty($data['map'])) {
            throw new JsonRunnerException('Map is empty');
        }
        if (empty($data['start'])) {
            throw new JsonRunnerException('Map is empty');
        }

    }

    protected function outputToFile()
    {

    }

}