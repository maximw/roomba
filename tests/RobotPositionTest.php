<?php


namespace App\Tests;


use App\Exceptions\RobotException;
use App\Service\Robot\RobotPosition;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class RobotPositionTest extends TestCase
{
    /**
     * @dataProvider validCoordinates
     */
    public function testValidPosition($x, $y, $facing)
    {
        $position = new RobotPosition($x, $y, $facing);

        $this->assertEquals($x, $position->getX());
        $this->assertEquals($y, $position->getY());
        $this->assertEquals($facing, $position->getFacing());
    }

    /**
     * @dataProvider invalidCoordinates
     */
    public function testInvalidPosition($x, $y, $facing)
    {
        $this->expectException(RobotException::class);

        new RobotPosition($x, $y, $facing);
    }

    /**
     * @dataProvider turnRightProvider
     */
    public function testTurnRight($facingBefore, $facingAfter)
    {
        $position = new RobotPosition(0, 0, $facingBefore);
        $position->turnRight();
        $this->assertEquals($facingAfter, $position->getFacing());
    }

    /**
     * @dataProvider turnRightProvider
     */
    public function testTurnLeft($facingAfter, $facingBefore)
    {
        $position = new RobotPosition(0, 0, $facingBefore);
        $position->turnLeft();
        $this->assertEquals($facingAfter, $position->getFacing());
    }

    public function validCoordinates()
    {
        return [
            [0, 0, RobotPosition::FACING_N],
            [1, 1, RobotPosition::FACING_E],
            [100500, 0, RobotPosition::FACING_S],
            [42, 42, RobotPosition::FACING_W],
        ];
    }

    public function invalidCoordinates()
    {
        return [
            [-1, -1, RobotPosition::FACING_N],
            [-1, 1, RobotPosition::FACING_N],
            [1, -1, RobotPosition::FACING_N],
            [0, 0, 'R'],
        ];
    }

    public function turnRightProvider()
    {
        return [
            [RobotPosition::FACING_N, RobotPosition::FACING_E],
            [RobotPosition::FACING_E, RobotPosition::FACING_S],
            [RobotPosition::FACING_S, RobotPosition::FACING_W],
            [RobotPosition::FACING_W, RobotPosition::FACING_N],
        ];

    }

}