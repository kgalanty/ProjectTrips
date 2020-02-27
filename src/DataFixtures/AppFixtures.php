<?php

namespace App\DataFixtures;

use App\Entity\TripMeasures;
use App\Entity\Trips;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class AppFixtures extends Fixture
{
	private $tripsStorage = __DIR__.'/../../config/trips.yaml';
	private function provideInitialData() : array
	{
		try
		{
			if(file_exists($this->tripsStorage))
			{
				return Yaml::parseFile($this->tripsStorage);
			}
			throw new ParseException('File doesn\'t exist');
		}
		catch (ParseException $exception)
		{
			printf('Unable to parse the YAML string: %s', $exception->getMessage());
		}
	}
	private function handleSingleTrip(array $singleTrip) : Trips
	{
		$trip = new Trips();
		$trip->setName($singleTrip['name']);
		$trip->setMeasureInterval($singleTrip['measure_interval']);
		return $trip;
	}
	private function handleTripMeasure(float $distances, Trips $tripEntity) : TripMeasures
	{
		$measure = new TripMeasures();
		$measure->setDistance($distances);
		$measure->setTrip($tripEntity);
		return $measure;
	}
	public function load(ObjectManager $manager)
	{ 
		$tripsdata = $this->provideInitialData();
		if($tripsdata)
		{
			foreach($tripsdata as $singleTrip)
			{
				$tripEntity = $this->handleSingleTrip($singleTrip);
				$manager->persist($tripEntity);
				foreach($singleTrip['distances'] as $distance)
				{
					$manager->persist($this->handleTripMeasure($distance, $tripEntity));
				}
				
			}
		}
		$manager->flush();
	}
}
