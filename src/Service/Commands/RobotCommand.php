<?php


namespace App\Service\Commands;


use App\Exceptions\InvalidCommand;

class RobotCommand
{
    // List of all available commands for all Robot models
    public const TURN_LEFT = 'TL';
    public const TURN_RIGHT = 'TR';
    public const ADVANCE = 'A';
    public const CLEAN = 'C';
    public const BACK = 'B';

    public const ALL_COMMANDS = [
        self::TURN_LEFT,
        self::TURN_RIGHT,
        self::ADVANCE,
        self::CLEAN,
        self::BACK,
    ];

    /**
     * @var string
     */
    protected $code;

    public function __construct(string $code)
    {
        $code = $this->normalizeCommand($code);
        if (!in_array($code, static::ALL_COMMANDS)) {
            throw new InvalidCommand();
        }
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * It could be omitted, depends of expected input format
     *
     * @param string $code
     * @return false|mixed|string|string[]|null
     */
    protected function normalizeCommand(string $code)
    {
        return mb_strtoupper($code, 'UTF-8');
    }


}