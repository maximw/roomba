<?php


namespace App\Service\Runners;


use App\Exceptions\SerializeException;

class ReportSerializer
{
    /** @var Report */
    protected $report;

    public function __construct(Report $report = null)
    {
        $this->setReport($report);
    }

    /**
     * Get Report as array
     *
     * @param Report|null $report
     * @return array
     * @throws SerializeException
     */
    public function asArray(Report $report = null): array
    {
        $this->setReport($report);
        if (!$this->report instanceof Report) {
            throw new SerializeException('Report is not set');
        }

        $serializedData = [];

        $serializedData['visited'] = [];
        foreach ($this->report->getVisited() as $position) {
            $serializedData['visited'][] = ['X' => $position->getX(), 'Y' => $position->getY()];
        }

        $serializedData['cleaned'] = [];
        foreach ($this->report->getCleaned() as $position) {
            $serializedData['cleaned'][] = ['X' => $position->getX(), 'Y' => $position->getY()];
        }


        $serializedData['final'] = [
            'X' => $this->report->getFinal()->getX(),
            'Y' => $this->report->getFinal()->getY(),
            'facing' => $this->report->getFinal()->getFacing(),
            ];

        $serializedData['battery'] = $this->report->getBattery();

        return $serializedData;
    }

    /**
     * Get Report as JSON string
     *
     * @param Report|null $report
     * @return string
     * @throws SerializeException
     */
    public function asJson(Report $report = null): string
    {
        $serializedData = $this->asArray($report);
        return json_encode($serializedData, JSON_PRETTY_PRINT);
    }

    /**
     * @return Report
     */
    public function getReport(): Report
    {
        return $this->report;
    }

    /**
     * @param Report $report
     * @return ReportSerializer
     */
    public function setReport(Report $report = null): ReportSerializer
    {
        if (null !== $report) {
            $this->report = $report;
        }
        return $this;
    }

}