<?php


namespace App\Command;


use App\Service\Runners\JsonRunner;
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
            ->addArgument('inputFile', InputArgument::REQUIRED, 'Input json file')
            ->addArgument('outputFile', InputArgument::REQUIRED, 'Output json file');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFile = $input->getArgument('inputFile');
        $outputFile = $input->getArgument('outputFile');

        $jsonRunner = new JsonRunner($inputFile, $outputFile);
        $jsonRunner->run();
        return 1;
        /*
        try {


            Out::setOutput($output);
            In::init($input, $output, $this->getHelper('question'));
            $startFile = realpath($input->getArgument('scenario'));
            chdir(dirname($startFile));
            if ($input->getOption('config')) {
                Config::loadFromFile($input->getOption('config'), true);
            } else {
                Config::loadFromFile('./config.yaml', false);
            }
            Config::loadInput($input);
            $tester = new Tester($startFile, $input->getOption('junit-report'));
            return $tester->run();
        } catch (InternalError $e) {
            Out::printError($e);
            return 1;
        }
        */
    }
}