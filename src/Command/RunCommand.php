<?php


namespace App\Command;


use App\Exceptions\InternalException;
use App\Service\Runners\JsonRunner;
use App\Service\Runners\ReportSerializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{

    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Run robot from JSON file')
            ->setHelp('Run robot from JSON file')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'Input JSON file')
            ->addArgument('outputFile', InputArgument::REQUIRED, 'Output JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = $input->getArgument('inputFile');
        $outputFile = $input->getArgument('outputFile');

        if (!file_exists($inputFile) || !is_readable($inputFile) || is_dir($inputFile)) {
            throw new InternalException('File is not readable ' . $inputFile);
        }

        if (file_exists($outputFile)) {
            if (!is_writable($outputFile) || is_dir($outputFile)) {
                throw new InternalException('File is not writable ' . $outputFile);
            }
        } else {
            $directory = dirname($outputFile);
            if (!is_dir($directory) || !is_writable($directory)) {
                throw new InternalException('File is not writable ' . $outputFile);
            }
        }

        $jsonString = file_get_contents($inputFile);
        $jsonRunner = new JsonRunner();
        $report = $jsonRunner->run($jsonString);

        $serializer = new ReportSerializer($report);
        file_put_contents($outputFile, $serializer->asJson());

        return 1;
    }
}