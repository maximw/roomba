<?php


namespace App\Service\Robot;


use App\Exceptions\InvalidCommand;
use App\Exceptions\InvalidMap;
use App\Exceptions\RobotStopped;
use App\Service\Commands\CommandsQueue;
use App\Service\Commands\RobotCommand;
use App\Service\Room\Room;
use App\Service\Runners\Report;

class Robot
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
     *
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
     *
     *
     * @var CommandsQueue[]
     */
    protected $backOffStrategies;

    /**
     * @var Report
     */
    protected $report;

    public function __construct(int $x, int $y, string $facing, int $batteryLevel)
    {
        $this->position = new RobotPosition($x, $y, $facing);
        $this->batteryLevel = $batteryLevel;
        $this->report = new Report();
        $this->prepareBackOffStrategies();
    }

    public function setRoom(Room $room)
    {
        $this->room = $room;
    }

    public function run(CommandsQueue $commands)
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
    }

    public function getReport(): Report
    {
        if (!$this->report instanceof Report) {
            throw new \Exception('Report is not initialized');
        }
        return $this->report;
    }

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
                    $this->runNewBackOffStrategy();
                } else {
                    $this->resetBackOffStrategy();
                    $this->position->move();
                    $this->report->addVisit($this->position);
                }
                break;
            case RobotCommand::BACK:
                $this->position->moveBack();
                $this->report->addVisit($this->position);
                break;
            default:
                throw new \Exception('Unsupported command ' . $command->getCode());
        }

        $this->batteryLevel = $this->batteryLevel - $this->getBatteryConsume($command);
    }

    protected function getBatteryConsume(RobotCommand $command)
    {
        return static::COMMANDS_DRAIN[$command->getCode()];
    }

    protected function runNewBackOffStrategy()
    {
        $this->strategyState = null === $this->strategyState ? 0 : $this->strategyState + 1;
        if ($this->strategyState >= count(static::BACK_OFF_STRATEGIES)) {
            throw new RobotStopped('Has no back off strategies');
        }
        $this->commands->addQueue($this->backOffStrategies[$this->strategyState]);
    }

    protected function resetBackOffStrategy()
    {
        $this->strategyState = null;
    }

    /**
     * Check before run
     *
     * @throws \Exception
     */
    protected function selfCheck()
    {
        if (!$this->room instanceof Room) {
            throw new InvalidMap('Room map is not loaded');
        }
        $this->checkCommandsQueue();
    }

    protected function checkCommandsQueue()
    {
        $commands = $this->commands->getQueue();
        foreach ($commands as $command) {
            if (!in_array($command->getCode(), array_keys(static::COMMANDS_DRAIN))) {
                throw new InvalidCommand('Unsupported command ' . $command->getCode());
            }
            if (in_array($command->getCode(), array_keys(static::FORBIDDEN_COMMANDS))) {
                throw new InvalidCommand('Unsupported command ' . $command->getCode());
            }
        }
    }


    /**
     * Prepare command queues for back off strategies
     */
    protected function prepareBackOffStrategies()
    {
        foreach (static::BACK_OFF_STRATEGIES as $commands) {
            $this->backOffStrategies[] = new CommandsQueue($commands);
        }
    }

}