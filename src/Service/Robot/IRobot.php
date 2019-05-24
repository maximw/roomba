<?php


namespace App\Service\Robot;


use App\Service\Commands\CommandsQueue;
use App\Service\Room\Room;
use App\Service\Runners\Report;

interface IRobot
{
    public function setPosition(int $x, int $y, string $facing): IRobot;

    public function setBatteryLevel(int $batteryLevel): IRobot;

    public function setRoom(Room $room): IRobot;

    public function run(CommandsQueue $commands): IRobot;

    public function getReport(): Report;

}