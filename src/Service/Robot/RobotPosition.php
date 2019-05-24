<?php


namespace App\Service\Robot;



class RobotPosition
{

    public const FACING_N = 'N';
    public const FACING_E = 'E';
    public const FACING_S = 'S';
    public const FACING_W = 'W';

    protected const FACINGS = [
        self::FACING_N, self::FACING_E, self::FACING_S, self::FACING_W,
    ];


    /**
     * @var int
     */
    protected $x;

    /**
     * @var int
     */
    protected $y;

    /**
     * @var string
     */
    protected $facing;

    public function __construct(int $x, int $y, string $facing)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setFacing($facing);
    }

    public function turnRight(): RobotPosition
    {
        $index = array_search($this->facing, static::FACINGS);
        $index = $index + 1;
        if ($index >= count(static::FACINGS)) {
            $index = 0;
        }
        $this->facing = static::FACINGS[$index];
        return $this;
    }

    public function turnLeft(): RobotPosition
    {
        $index = array_search($this->facing, static::FACINGS);
        $index = $index - 1;
        if ($index < 0) {
            $index = count(static::FACINGS) - 1;
        }
        $this->facing = static::FACINGS[$index];
        return $this;
    }

    public function move(): RobotPosition
    {
        switch ($this->facing) {
            case static::FACING_N:
                $this->y--;
                break;
            case static::FACING_E:
                $this->x++;
                break;
            case static::FACING_S:
                 $this->y++;
                break;
            case static::FACING_W:
                $this->x--;
                break;
            default:
                throw new \Exception('Unknown facing');
        }
        return $this;
    }

    public function moveBack(): RobotPosition
    {
        switch ($this->facing) {
            case static::FACING_N:
                $this->y++;
                break;
            case static::FACING_E:
                $this->x--;
                break;
            case static::FACING_S:
                 $this->y--;
                break;
            case static::FACING_W:
                $this->x++;
                break;
            default:
                throw new \Exception('Unknown facing');
        }
        return $this;
    }

    public function getNextPosition(): RobotPosition
    {
        $position = clone $this;
        $position->move();
        return $position;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @param int $x
     * @return RobotPosition
     * @throws \Exception
     */
    public function setX(int $x): RobotPosition
    {
        if ($x < 0) {
            throw new \Exception('Negative X coordinate');
        }
        $this->x = $x;
        return $this;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $y
     * @return RobotPosition
     * @throws \Exception
     */
    public function setY(int $y): RobotPosition
    {
        if ($y < 0) {
            throw new \Exception('Negative Y coordinate');
        }
        $this->y = $y;
        return $this;
    }

    /**
     * @return string
     */
    public function getFacing(): string
    {
        return $this->facing;
    }

    /**
     * @param string $facing
     * @return RobotPosition
     * @throws \Exception
     */
    public function setFacing(string $facing): RobotPosition
    {
        if (!in_array($facing, self::FACINGS)) {
            throw new \Exception('Invalid facing');
        }
        $this->facing = $facing;
        return $this;
    }

    public function isEqual(RobotPosition $position): bool
    {
        return $this->getX() === $position->getX()
            && $this->getY() === $position->getY();
    }

}