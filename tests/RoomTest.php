<?php


namespace App\Tests;


use App\Exceptions\InvalidMap;
use App\Service\Robot\RobotPosition;
use App\Service\Room\Room;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class RoomTest extends TestCase
{
    public function testValidMap()
    {
        $map = [
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", null, null, "S"],
          ];

        $room = new Room($map);

        $this->assertAttributeEquals($map, 'map', $room);
    }

    public function testValidMapNormalize()
    {
        $map = [
            ["S", "s", "S", "S"],
            ["S", "s", "C", "S"],
            ["S", "s", "S", "S"],
            ["S", "null", null, "S"],
          ];

        $normalizedMap = [
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", null, null, "S"],
          ];

        $room = new Room($map);

        $this->assertAttributeEquals($normalizedMap, 'map', $room);
    }

    public function testInvalidMapCharacter()
    {
        $this->expectException(InvalidMap::class);

        $map = [
            ["S", "Z", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", "null", "S", "S"],
          ];

        $room = new Room($map);
    }

    public function testInvalidMapNotSquare()
    {
        $this->expectException(InvalidMap::class);

        $map = [
            ["S", "Z", "S", "S"],
            ["S", "S", "C", "S", "S"],
            ["S", "S", "S", "S"],
            ["S", "null", "S", "S"],
          ];

        $room = new Room($map);
    }

    public function testInvalidMapEmpty()
    {
        $this->expectException(InvalidMap::class);

        $room = new Room([]);
    }


    public function testInvalidMapNotArray()
    {
        $this->expectException(\TypeError::class);

        $room = new Room(42);
    }

    public function testInvalidMapNotSubArray()
    {
        $this->expectException(InvalidMap::class);

        $map = [
            ["S", "Z", "S", "S"],
            42,
            ["S", "S", "S", "S"],
            ["S", "null", "S", "S"],
          ];

        $room = new Room($map);
    }

    /**
     * @dataProvider obstacleCoordinates
     * @param $x
     * @param $y
     */
    public function testIsObstacle($x, $y)
    {
        $map = [
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", null, null, "S"],
          ];

        $room = new Room($map);
        $position = new RobotPosition($x, $y, RobotPosition::FACING_N);

        $this->assertTrue($room->isObstacle($position));
    }

    /**
     * @dataProvider spaceCoordinates
     * @param $x
     * @param $y
     */
    public function testIsNotObstacle($x, $y)
    {
        $map = [
            ["S", "S", "S", "S"],
            ["S", "S", "C", "S"],
            ["S", "S", "S", "S"],
            ["S", null, null, "S"],
          ];

        $room = new Room($map);
        $position = new RobotPosition($x, $y, RobotPosition::FACING_N);

        $this->assertFalse($room->isObstacle($position));
    }

    public function obstacleCoordinates()
    {
        return [
            [0, 5],
            [5, 0],
            [1, 3],
            [2, 1],
        ];
    }

    public function spaceCoordinates()
    {
        return [
            [1, 1],
            [3, 3],
        ];
    }
}