<?php
namespace App\Service;

use App\Entity\Trips;
use App\Repository\TripsRepository;

class TripsService
{
    /**
     * Trips repo
     */
    private  $tripsRepo;
    /**
     * Trips Service constructor
     *
     * @param  mixed $tripsRepo
     *
     * @return void
     */
    public function __construct(TripsRepository $tripsRepo)
    {
        $this->tripsRepo = $tripsRepo;
    }
    /**
     * Main calculation routine called from controller
     *
     * @return array
     */
    public function calculateTrips() : array
    {
        $trips = $this->getAllTrips();
        $rows = [];
        foreach($trips as $trip)
        {
            $highestAvg = 0;
            if(count($trip->getTripMeasures()) > 1)
            {
                $highestAvg = $this->calculateMaxAvg($this->calculatePartialAvgSpeeds($trip));
            }
            $rows[] = [
                $trip->getName(),
                $this->getTotalDistance($trip),
                $trip->getMeasureInterval(),
                $highestAvg
            ];
        }
        return $rows;
    }

    /**
     * Calculate partial average speed based on 
     * two measured speeds and interval (time)
     *
     * @param  mixed $delta
     * @param  mixed $interval
     *
     * @return float
     */
    private function calculatePartialAvg(float $delta, int $interval)
    {
        if($interval === 0)
        {
            return 0;
        }
        return (3600 * $delta)/$interval;
    }
    /**
     * Calculate max average speed in set
     * and round it down to integer
     *
     * @param  mixed $partialAvgSet
     *
     * @return int
     */
    private function calculateMaxAvg(array $partialAvgSet) : int
    {
        return floor(max($partialAvgSet));
    }
    
    /**
     * Calculate partial average speeds for given trip
     *
     * @param  mixed $trip
     *
     * @return array
     */
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
    /**
     * Return total distance based on last measure in set
     *
     * @param  mixed $trip
     *
     * @return float
     */
    private function getTotalDistance(Trips $trip)
    {
        $measures = $trip->getTripMeasures()->toArray();
        return end($measures)->getDistance();
    }
    /**
     * Return all trips from database
     *
     * @return Trips[]
     */
    private function getAllTrips()
    {
        return $this->tripsRepo->findAll();
    }
}