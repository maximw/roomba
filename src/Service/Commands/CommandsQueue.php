<?php


namespace App\Service\Commands;



use App\Exceptions\CommandsQueueEmpty;

class CommandsQueue
{
    /** @var RobotCommand[]  */
    protected $queue = [];

    public function __construct(array $commands)
    {
        foreach ($commands as $commandCode) {
            $this->queue[] = new RobotCommand($commandCode);
        }
    }

    /**
     * Add new Command to queue
     *
     * @param RobotCommand $command
     */
    public function add(RobotCommand $command)
    {
        array_push($this->queue, $command);
    }
    /**
     * Return the first element
     *
     * @return RobotCommand
     * @throws CommandsQueueEmpty
     */
    public function head(): RobotCommand
    {
        if ($this->isEmpty()) {
            throw new CommandsQueueEmpty();
        }
        $token = end($this->queue);
        return $token;
    }

    /**
     * Retrieve Command from queue
     *
     * @return RobotCommand
     * @throws CommandsQueueEmpty
     */
    public function next(): RobotCommand
    {
        if ($this->isEmpty()) {
            throw new CommandsQueueEmpty();
        }
        $command = array_shift($this->queue);
        return $command;
    }
    /**
     * Check if queue is empty
     *
     * @return bool
     */
    public function isEmpty():bool
    {
        return empty($this->queue);
    }

}