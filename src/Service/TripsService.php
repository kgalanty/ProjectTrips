<?php
namespace App\Service;

use App\Entity\Trips;
use App\Repository\TripsRepository;
class TripsService
{
    private  $tripsRepo;
    public function __construct(TripsRepository $tripsRepo)
    {
        $this->tripsRepo = $tripsRepo;
    }
    private function calculatePartialAvg(float $delta, int $interval)
    {
        return (3600 * $delta)/$interval;
    }
    private function calculateMaxAvg(array $partialAvgSet) : int
    {
        return floor(max($partialAvgSet));
    }
    private function calculatePartialAvgSpeeds(Trips $trip) : array
    {
        $measures = $trip->getTripMeasures();
        $measuresCount = count($measures);
        $tripInterval = $trip->getMeasureInterval();
        $partialAvgSet = [];
        for($m=0;$m<$measuresCount-1;$m++)
        {
            $partialAvgSet[] = $this->calculatePartialAvg(
                $measures[$m+1]->getDistance() - $measures[$m]->getDistance(),
                $tripInterval
            );
        }
        return $partialAvgSet;
    }
    private function getTotalDistance(Trips $trip)
    {
        $measures = $trip->getTripMeasures()->toArray();
        return end($measures)->getDistance();
    }
    private function getAllTrips()
    {
        return $this->tripsRepo->findAll();
    }
    public function calculateTrips()
    {
        $trips = $this->getAllTrips();
        $rows = [];
        foreach($trips as $trip)
        {
            $rows[] = [
                $trip->getName(),
                $this->getTotalDistance($trip),
                $trip->getMeasureInterval(),
                $this->calculateMaxAvg($this->calculatePartialAvgSpeeds($trip))
            ];
        }
        dd($rows);
    }
}