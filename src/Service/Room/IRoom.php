<?php


namespace App\Service\Room;


use App\Service\Robot\RobotPosition;

interface IRoom
{
    public const CLEANABLE_CELL = 'S';
    public const COLUMN_CELL = 'C';
    public const OUTSIDE_CELL = null;

    /**
     * All available cell types
     */
    public const CELL_TYPES = [
        self::CLEANABLE_CELL,
        self::COLUMN_CELL,
        self::OUTSIDE_CELL,
    ];

    /**
     * Robot can't move here
     */
    public const OBSTACLES = [
        self::COLUMN_CELL,
        self::OUTSIDE_CELL,
    ];

    public function isObstacle(RobotPosition $position): bool;
}