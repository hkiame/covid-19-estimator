<?php

$data = '{
  "region": {
    "name" : "Africa",
    "avgAge": 19.7,
    "avgDailyIncomeInUSD": 4,
    "avgDailyIncomePopulation": 0.73
  },
  "periodType": "days",
  "timeToElapse": 38,
  "reportedCases": 2747,
  "population": 92931687,
  "totalHospitalBeds": 678874
}';

function covid19ImpactEstimator($data)
{
  $myData = json_decode($data, true);
  $output = array(
    'data' => $myData,
    'impact' => [],
    'severeImpact' => []
  );

  $reportedCases = $myData['reportedCases'];
  $impactCurrentlyInfected = $reportedCases * 10;
  $severeCurrentlyInfected = $reportedCases * 50;
  $periodType = $myData['periodType'];
  $timeToElapse = $myData['timeToElapse'];
  $impactInfectByRequestedTime = calcInfectByRequestedTime($impactCurrentlyInfected, getdays($periodType, $timeToElapse));
  $severeInfectByRequestedTime = calcInfectByRequestedTime($severeCurrentlyInfected, getdays($periodType, $timeToElapse));


  $output['impact']['currentlyInfected'] = $impactCurrentlyInfected;
  $output['impact']['infectionsByRequestedTime'] = $impactInfectByRequestedTime;
  $output['severeImpact']['currentlyInfected'] = $severeCurrentlyInfected;
  $output['severeImpact']['infectionsByRequestedTime'] = $severeInfectByRequestedTime;
  
  return json_encode($output);
}

function calcInfectByRequestedTime($num, $days){
  return $num * pow(2, floor($days/3));
}

function getdays($input, $value){
  switch($input){
    case 'months':
      return $value * 30;
    case 'weeks':
      return $value * 7;
    default:
      return $value;
  }
}

$result = covid19ImpactEstimator($data);

echo "<pre>";
print_r($result);
echo "</pre>";