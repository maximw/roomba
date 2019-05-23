<?php


namespace App\Service\Robot;


use App\Exceptions\CommandsQueueEmpty;
use App\Service\Commands\RobotCommand;

class Robot
{
    public const COMMANDS_DRAIN = [
        RobotCommand::TURN_LEFT => 1,
        RobotCommand::TURN_RIGHT => 1,
        RobotCommand::ADVANCE => 2,
        RobotCommand::BACK => 3,
        RobotCommand::CLEAN => 5,
    ];

    protected const BACK_STRATEGIES = [
        [
            RobotCommand::TURN_RIGHT,
            RobotCommand::ADVANCE,
        ],
        [
            RobotCommand::TURN_LEFT,
            RobotCommand::BACK,
            RobotCommand::TURN_RIGHT,
            RobotCommand::ADVANCE,
        ],
        [
            
        ]
    ];

    /**
     * Robot's current line
     *
     * @var int
     */
    protected $x;

    /**
     * Robot's current row
     *
     * @var int
     */
    protected $y;

    /**
     * Robot's current direction
     *
     * @var string
     */
    protected $facing;

    /**
     * Robot's battery level
     *
     * @var int
     */
    protected $batteryLevel;


    public function __construct(int $x, int $y, string $facing, int $batteryLevel)
    {
        $this->x = $x;
        $this->y = $y;
        $this->facing = $facing;
        $this->batteryLevel = $batteryLevel;
    }

    public function doCommand(RobotCommand $command)
    {
        $this->isCommandSupported($command);




    }

    public function getStrategies(): array
    {

    }

    protected function isCommandSupported(RobotCommand $command)
    {
        if (!in_array($command->getCode(), array_keys(static::COMMANDS_DRAIN))) {
            throw new CommandsQueueEmpty('Unsupported command ' . $command->getCode());
        }
    }


}