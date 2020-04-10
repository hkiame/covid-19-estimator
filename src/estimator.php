<?php
/*
$data = [
  "region" => [
    "name" => "Africa",
    "avgAge" => 19.7,
    "avgDailyIncomeInUSD" => 4,
    "avgDailyIncomePopulation" => 0.73
  ],
  "periodType" => "days",
  "timeToElapse" => 38,
  "reportedCases" => 2747,
  "population" => 92931687,
  "totalHospitalBeds" => 678874
];
*/


function covid19ImpactEstimator($data)
{
  $output = array(
    'data' => $data,
    'impact' => [],
    'severeImpact' => []
  );

  $reportedCases = $data['reportedCases'];
  $impactCurrentlyInfected = $reportedCases * 10;
  $severeCurrentlyInfected = $reportedCases * 50;
  $periodType = $data['periodType'];
  $timeToElapse = $data['timeToElapse'];
  $impactInfectByRequestedTime = calcInfectByRequestedTime($impactCurrentlyInfected, getdays($periodType, $timeToElapse));
  $severeInfectByRequestedTime = calcInfectByRequestedTime($severeCurrentlyInfected, getdays($periodType, $timeToElapse));
  $impactSevereCasesByReqTime = severeCasesByRequestedTime($impactInfectByRequestedTime);
  $severeSevereCasesByReqTime = severeCasesByRequestedTime($severeInfectByRequestedTime);
  $impactBedsByRequestedTime = availableBeds($impactSevereCasesByReqTime, $data['totalHospitalBeds']);
  $severeBedsByRequestedTime = availableBeds($severeSevereCasesByReqTime, $data['totalHospitalBeds']);


  $output['impact']['currentlyInfected'] = $impactCurrentlyInfected;
  $output['impact']['infectionsByRequestedTime'] = $impactInfectByRequestedTime;
  $output['impact']['severeCasesByRequestedTime'] = $impactSevereCasesByReqTime;
  $output['impact']['hospitalBedsByRequestedTime'] = $impactBedsByRequestedTime;
  $output['severeImpact']['currentlyInfected'] = $severeCurrentlyInfected;
  $output['severeImpact']['infectionsByRequestedTime'] = $severeInfectByRequestedTime;
  $output['severeImpact']['severeCasesByRequestedTime'] = $severeSevereCasesByReqTime;
  $output['severeImpact']['hospitalBedsByRequestedTime'] = $severeBedsByRequestedTime;
  
  return $output;
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

function severeCasesByRequestedTime($cases){
  return round((15/100) * $cases);
}

function availableBeds($cases, $beds){
  $requiredBeds = round(0.35 * $beds);
  return round($requiredBeds - $cases);
}