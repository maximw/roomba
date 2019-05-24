<?php


namespace App\Service\Runners;


use App\Exceptions\JsonRunnerException;
use App\Service\Commands\CommandsQueue;
use App\Service\Robot\Robot;
use App\Service\Room\Room;

class JsonRunner
{

    public function run(string $input): Report
    {
        $data = $this->parse($input);
        $room = new Room($data['map']);
        $robot = new Robot($data['start']['X'], $data['start']['Y'], $data['start']['facing'], $data['battery']);
        $robot->setRoom($room);
        $commands = new CommandsQueue($data['commands']);
        $robot->run($commands);

        return $robot->getReport();
    }

    protected function parse(string $input): array
    {
        $data = json_decode($input, true);
        if (json_last_error()) {
            throw new JsonRunnerException('Bad JSON format ' . json_last_error_msg());
        }
        if (empty($data['map'])) {
            throw new JsonRunnerException('Map is empty');
        }
        if (!isset($data['start']['X'])
            || !isset($data['start']['Y'])
            || !isset($data['start']['facing'])
        ) {
            throw new JsonRunnerException('Invalid start position');
        }
        if (!isset($data['battery'])) {
            throw new JsonRunnerException('Battery level is not set');
        }
        return $data;
    }

    protected function outputToFile($outputFile, Report $report)
    {
    }

    protected function serializeReport(Report $report): array
    {

    }

}