<?php


namespace App\Service\Room;


use App\Exceptions\InvalidMap;

class Room
{
    public const CLEANABLE_CELL = 'S';
    public const COLUMN_CELL = 'C';
    public const OUTSIDE_CELL = null;

    public const CELL_TYPES = [
        self::CLEANABLE_CELL,
        self::COLUMN_CELL,
        self::OUTSIDE_CELL,
    ];

    public const OBSTACLES = [
        self::CLEANABLE_CELL,
        self::OUTSIDE_CELL,
    ];

    /**
     * @var array
     */
    protected $map = [];

    protected $sizeX;

    protected $sizeY;

    public function __construct(array $map)
    {
        $this->loadMap($map);
    }

    public function isObstacle(int $x, int $y, string $direction)
    {

    }

    public function validatePosition(int $x, int $y): bool
    {
        if ($x < 0 || $x >= $this->sizeX) {
            return false;
        }
        if ($y < 0 || $y >= $this->sizeY) {
            return false;
        }

        if (in_array($this->map[$x][$y], static::OBSTACLES)) {
            return false;
        }

        return true;
    }

    protected function loadMap(array $map): void
    {
        if (empty($map)) {
            throw new InvalidMap('Room has no cells');
        }

        $rowsCount = null;

        foreach ($map as $line) {
            if (!is_array($line)) {
                throw new InvalidMap();
            }
            if (null === $rowsCount) {
                $rowsCount = count($line);
            }
            if ($rowsCount !== count($line)) {
                throw new InvalidMap('Room array is not rectangle');
            }
            $mapLine = [];
            foreach ($line as $cell) {
                $cell = $this->normalizeCell($cell);
                if (!in_array($cell, static::CELL_TYPES)) {
                    throw new InvalidMap();
                }
                $mapLine[] = $cell;
            }
            $this->map[] = $mapLine;
        }

        $this->sizeX = $rowsCount;
        $this->sizeY = count($map);
    }

    protected function normalizeCell(string $cell = null)
    {
        return static::OUTSIDE_CELL == $cell ? static::OUTSIDE_CELL : mb_strtoupper($cell, 'UTF-8');
    }

}