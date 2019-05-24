<?php


namespace App\Service\Room;


use App\Exceptions\InvalidMap;
use App\Service\Robot\RobotPosition;

/**
 * Represents room map
 *
 * Class Room
 * @package App\Service\Room
 */
class Room implements IRoom
{
    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var int
     */
    protected $sizeX;

    /**
     * @var int
     */
    protected $sizeY;

    public function __construct(array $map)
    {
        $this->loadMap($map);
    }

    /**
     * Check if given position is not
     *
     * @param RobotPosition $position
     * @return bool
     */
    public function isObstacle(RobotPosition $position): bool
    {
        if ($position->getX() < 0 || $position->getX() >= $this->sizeX) {
            return true;
        }
        if ($position->getY() < 0 || $position->getY() >= $this->sizeY) {
            return true;
        }

        if (in_array($this->map[$position->getY()][$position->getX()], static::OBSTACLES)) {
            return true;
        }

        return false;
    }

    /**
     * Load room map from array
     *
     * @param array $map
     * @throws InvalidMap
     */
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

    /**
     * @param string|null $cell
     * @return string
     */
    protected function normalizeCell(string $cell = null): ?string
    {
        // In description of problem null value is used, but in examples - string "null"
        // It is for supporting both of them
        if ('null' === $cell) {
            $cell = null;
        }
        return static::OUTSIDE_CELL == $cell ? static::OUTSIDE_CELL : mb_strtoupper($cell, 'UTF-8');
    }

}