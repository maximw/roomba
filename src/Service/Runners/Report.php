<?php


namespace App\Service\Runners;


use App\Service\Robot\RobotPosition;

class Report
{
    /**
     * @var RobotPosition[]
     */
    protected $visited = [];

    /**
     * @var RobotPosition[]
     */
    protected $cleaned = [];

    /**
     * @var RobotPosition
     */
    protected $final;

    /**
     * @var int
     */
    protected $battery = 0;

    /**
     * Add visited cell to report
     *
     * @param RobotPosition $position
     * @return Report
     */
    public function addVisit(RobotPosition $position): Report
    {
        // Do not write already visited cells twice
        foreach ($this->visited as $visitedPosition) {
            if ($visitedPosition->isEqual($position)) {
                return $this;
            }
        }
        $this->visited[] = clone $position;
        return $this;
    }

    /**
     * Add cleaned cell to report
     *
     * @param RobotPosition $position
     * @return Report
     */
    public function addClean(RobotPosition $position): Report
    {
        // Do not write already cleaned cells twice
        foreach ($this->cleaned as $cleanedPosition) {
            if ($cleanedPosition->isEqual($position)) {
                return $this;
            }
        }
        $this->cleaned[] = clone $position;
        return $this;
    }

    /**
     * Set final cell and battery level
     *
     * @param RobotPosition $position
     * @param int $battery
     * @return Report
     */
    public function setFinalPosition(RobotPosition $position, int $battery): Report
    {
        $this->final = clone $position;
        $this->battery = $battery;
        return $this;
    }

    /**
     * @return RobotPosition[]
     */
    public function getVisited(): array
    {
        return $this->visited;
    }

    /**
     * @return RobotPosition[]
     */
    public function getCleaned(): array
    {
        return $this->cleaned;
    }

    /**
     * @return RobotPosition
     */
    public function getFinal(): RobotPosition
    {
        return $this->final;
    }

    /**
     * @return int
     */
    public function getBattery(): int
    {
        return $this->battery;
    }

}