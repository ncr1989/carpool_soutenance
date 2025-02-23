<?php

namespace App\Service;


class HaversineService {

 public function calculateKms(float $latitude1,float  $longitude1,float  $latitude2,float $longitude2):float
 {
$radiusOfEarth = 6371; // Earth's radius in kilometers

// Addresse 1
$latitudeOne = deg2rad($latitude1);
$longitudeOne = deg2rad($longitude1);
// Addresse 2
$latitudeTwo = deg2rad($latitude2);
$longitudeTwo = deg2rad($longitude2);

$distanceLongitude = $longitudeTwo - $longitudeOne;
$distanceLatitude = $latitudeTwo - $latitudeOne;

// Haversine Formula
$a = sin($distanceLatitude / 2) * sin($distanceLatitude / 2) + 
     cos($latitudeOne) * cos($latitudeTwo) * 
     sin($distanceLongitude / 2) * sin($distanceLongitude / 2);

$c = 2 * asin(sqrt($a));
$distance = $radiusOfEarth * $c;
return round($distance, 2);
 }
}