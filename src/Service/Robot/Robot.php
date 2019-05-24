<?php


namespace App\Service\Robot;


use App\Exceptions\InvalidCommand;
use App\Exceptions\InvalidMap;
use App\Exceptions\RobotException;
use App\Exceptions\RobotStopped;
use App\Service\Commands\CommandsQueue;
use App\Service\Commands\RobotCommand;
use App\Service\Room\Room;
use App\Service\Runners\Report;

class Robot implements IRobot
{
    /**
     * Amount of units of battery consumed by Command
     * If Command is not in list Robot does not support it
     */
    protected const COMMANDS_DRAIN = [
        RobotCommand::TURN_LEFT => 1,
        RobotCommand::TURN_RIGHT => 1,
        RobotCommand::ADVANCE => 2,
        RobotCommand::BACK => 3,
        RobotCommand::CLEAN => 5,
    ];

    /**
     * It could be loaded to Robot from outside, but according to KISS principle let it be firmware
     */
    protected const BACK_OFF_STRATEGIES = [
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
            RobotCommand::TURN_LEFT,
            RobotCommand::TURN_LEFT,
            RobotCommand::ADVANCE,
        ],
        [
            RobotCommand::TURN_RIGHT,
            RobotCommand::BACK,
            RobotCommand::TURN_RIGHT,
            RobotCommand::ADVANCE,
        ],
        [
            RobotCommand::TURN_LEFT,
            RobotCommand::TURN_LEFT,
            RobotCommand::ADVANCE,
        ],
    ];

    /**
     * Commands forbidden in general, except back off strategies
     */
    protected const FORBIDDEN_COMMANDS = [
        RobotCommand::BACK,
    ];

    /**
     * Robot's current position
     *
     * @var RobotPosition
     */
    protected $position;

    /**
     * Robot's battery level
     *
     * @var int
     */
    protected $batteryLevel = 0;

    /**
     * @var Room
     */
    protected $room;

    /**
     * Commands loaded to robot
     *
     * @var CommandsQueue
     */
    protected $commands;

    /**
     * Current robot moving strategy
     * null = means follows command's queue
     * int = means number of current using back off strategy
     *
     * @var null|int
     */
    protected $strategyState;

    /**
     * Prepared back off strategies cache
     *
     * @var CommandsQueue[]
     */
    protected $backOffStrategies;

    /**
     * @var Report
     */
    protected $report;

    public function __construct(int $x, int $y, string $facing, int $batteryLevel, Room $room)
    {
        $this->setPosition($x, $y, $facing);
        $this->setBatteryLevel($batteryLevel);
        $this->setRoom($room);
        $this->report = new Report();
        $this->prepareBackOffStrategies();
    }

    public function setPosition(int $x, int $y, string $facing): IRobot
    {
        $this->position = new RobotPosition($x, $y, $facing);
        return $this;
    }

    public function setBatteryLevel(int $batteryLevel): IRobot
    {
        $this->batteryLevel = $batteryLevel;
        return $this;
    }

    public function setRoom(Room $room): IRobot
    {
        $this->room = $room;
        return $this;
    }

    public function run(CommandsQueue $commands): IRobot
    {
        $this->report = new Report();
        $this->commands = $commands;
        $this->selfCheck();
        $this->report->addVisit($this->position);
        try {
            while (!$commands->isEmpty()) {
                $this->doCommand($this->commands->next());
            }
        } catch (RobotStopped $e) {
        }
        $this->report->setFinalPosition($this->position, $this->batteryLevel);
        return $this;
    }

    /**
     * @return Report
     * @throws RobotException
     */
    public function getReport(): Report
    {
        if (!$this->report instanceof Report) {
            throw new RobotException('Report is not initialized');
        }
        return $this->report;
    }

    /**
     * Execute one command
     *
     * @param RobotCommand $command
     * @throws RobotException
     * @throws RobotStopped
     */
    protected function doCommand(RobotCommand $command)
    {
        if ($this->batteryLevel < $this->getBatteryConsume($command)) {
            throw new RobotStopped('Not enough energy');
        }

        switch ($command->getCode()) {
            case RobotCommand::TURN_LEFT:
                $this->position->turnLeft();
                break;
            case RobotCommand::TURN_RIGHT:
                $this->position->turnRight();
                break;
            case RobotCommand::CLEAN:
                $this->report->addClean($this->position);
                break;
            case RobotCommand::ADVANCE:
                if ($this->room->isObstacle($this->position->getNextPosition())) {
                    $this->runNextBackOffStrategy();
                } else {
                    $this->resetBackOffStrategy();
                    $this->position->move();
                    $this->report->addVisit($this->position);
                }
                break;
            case RobotCommand::BACK:
                if ($this->room->isObstacle($this->position->getBackPosition())) {
                    // Obstacle should not happen during moving back
                    throw new RobotStopped();
                } else {
                    $this->position->moveBack();
                    $this->report->addVisit($this->position);
                }
                break;
            default:
                throw new RobotException('Unsupported command ' . $command->getCode());
        }

        $this->batteryLevel = $this->batteryLevel - $this->getBatteryConsume($command);
    }

    protected function getBatteryConsume(RobotCommand $command)
    {
        return static::COMMANDS_DRAIN[$command->getCode()];
    }

    /**
     * Choose and runs the next back off strategy
     *
     * @throws RobotStopped
     */
    protected function runNextBackOffStrategy()
    {
        $this->strategyState = null === $this->strategyState ? 0 : $this->strategyState + 1;
        if ($this->strategyState >= count(static::BACK_OFF_STRATEGIES)) {
            throw new RobotStopped('Has no back off strategies');
        }
        $this->commands->prependQueue($this->backOffStrategies[$this->strategyState]);
    }

    /**
     * Returns to normal mode from back off strategy
     */
    protected function resetBackOffStrategy()
    {
        $this->strategyState = null;
    }

    /**
     * Check before run
     *
     * @return Robot
     * @throws InvalidMap
     * @throws InvalidCommand
     * @throws RobotException
     */
    protected function selfCheck(): Robot
    {
        if (!$this->room instanceof Room) {
            throw new InvalidMap('Room map is not loaded');
        }
        if (!$this->report instanceof Report) {
            throw new InvalidMap('Report is not initialized');
        }
        $this->checkCommandsQueue();
        if ($this->room->isObstacle($this->position)) {
            throw new RobotException('Invalid initial position');
        }
        return $this;
    }

    /**
     * Check if all loaded commands could be executed
     *
     * @return Robot
     * @throws InvalidCommand
     */
    protected function checkCommandsQueue(): Robot
    {
        $commands = $this->commands->getQueue();
        foreach ($commands as $command) {
            if (!in_array($command->getCode(), array_keys(static::COMMANDS_DRAIN))) {
                throw new InvalidCommand('Unsupported command ' . $command->getCode());
            }
            if (in_array($command->getCode(), static::FORBIDDEN_COMMANDS)) {
                throw new InvalidCommand('Unsupported command ' . $command->getCode());
            }
        }
        return $this;
    }

    /**
     * Prepare command queues for back off strategies
     */
    protected function prepareBackOffStrategies(): Robot
    {
        foreach (static::BACK_OFF_STRATEGIES as $commands) {
            $this->backOffStrategies[] = new CommandsQueue($commands);
        }
        return $this;
    }

}