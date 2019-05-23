<?php


namespace App\Service\Runners;


use App\Service\Commands\CommandsQueue;
use App\Service\Robot\Robot;
use App\Service\Room\Room;

class RobotRunner
{
    /**
     * @var Room
     */
    protected $room;


    /**
     * @var Robot
     */
    protected $robot;

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    public function runRobot(Robot $robot, CommandsQueue $commandsQueue)
    {

    }



}