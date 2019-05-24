<?php


namespace App\Service\Commands;


use App\Exceptions\CommandsQueueEmpty;

/**
 * Class CommandsQueue
 * @package App\Service\Commands
 */
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

    /**
     * Prepend new queue to current
     *
     * @param CommandsQueue $queue
     * @return CommandsQueue
     */
    public function prependQueue(CommandsQueue $queue): CommandsQueue
    {
        $this->queue = array_merge($queue->getQueue(), $this->queue);
        return $this;
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

    /**
     * Get queue as array
     *
     * @return array
     */
    public function getQueue(): array
    {
        return $this->queue;
    }
}