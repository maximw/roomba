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
     * @return CommandsQueue
     */
    public function add(RobotCommand $command): CommandsQueue
    {
        array_push($this->queue, $command);
        return $this;
    }

    public function addQueue(CommandsQueue $queue): CommandsQueue
    {
        $this->queue = array_merge($queue->getQueue(), $this->queue);
        return $this;
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
        $command = end($this->queue);
        return $command;
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
    public function isEmpty(): bool
    {
        return empty($this->queue);
    }

    public function getQueue(): array
    {
        return $this->queue;
    }
}