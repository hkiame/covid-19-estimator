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


$data2 = [
  'region' => array(
    'name' => "Africa",
    'avgAge' => 19.7,
    'avgDailyIncomeInUSD' => 2,
    'avgDailyIncomePopulation' => 0.66
  ),
  'periodType' => "weeks",
  'timeToElapse' => 2,
  'reportedCases' => 1031,
  'population' => 3963979,
  'totalHospitalBeds' => 65704
];

$data3 =[
  'region' => array(
    'name' => "Africa",
    'avgAge' => 19.7,
    'avgDailyIncomeInUSD' => 3,
    'avgDailyIncomePopulation' => 0.74,
  ),
  'periodType' => "days",
  'timeToElapse' => 47,
  'reportedCases' => 2090,
  'population' => 2610231,
  'totalHospitalBeds' => 93199
];

*/


function covid19ImpactEstimator($data)
{
  $output = array(
    'data' => $data,
    'impact' => [],
    'severeImpact' => []
  );
  var_dump($data);
  $reportedCases = $data['reportedCases'];
  $impactCurrentlyInfected = $reportedCases * 10;
  $severeCurrentlyInfected = $reportedCases * 50;
  $periodType = $data['periodType'];
  $timeToElapse = $data['timeToElapse'];
  $days = getdays($periodType, $timeToElapse);
  $impactInfectByRequestedTime = calcInfectByRequestedTime($impactCurrentlyInfected, $days);
  $severeInfectByRequestedTime = calcInfectByRequestedTime($severeCurrentlyInfected, $days);
  $impactSevereCasesByReqTime = severeCasesByRequestedTime($impactInfectByRequestedTime);
  $severeSevereCasesByReqTime = severeCasesByRequestedTime($severeInfectByRequestedTime);
  $impactBedsByRequestedTime = availableBeds($impactSevereCasesByReqTime, $data['totalHospitalBeds']);
  $severeBedsByRequestedTime = availableBeds($severeSevereCasesByReqTime, $data['totalHospitalBeds']);
  $impactIcuCases = icuCases($impactInfectByRequestedTime);
  $severeIcuCases = icuCases($severeInfectByRequestedTime);
  $impactVentilatorCases = VentilatorCases($impactInfectByRequestedTime);
  $severeVentilatorCases = VentilatorCases($severeInfectByRequestedTime);
  $impactIncomeLost = incomeLost($impactInfectByRequestedTime, $data['region']['avgDailyIncomePopulation'], 
                                $data['region']['avgDailyIncomeInUSD'], $days);
  $severeIncomeLost = incomeLost($severeInfectByRequestedTime, $data['region']['avgDailyIncomePopulation'], 
                                $data['region']['avgDailyIncomeInUSD'], $days);
  $severeVentilatorCases = VentilatorCases($severeInfectByRequestedTime);


  $output['impact']['currentlyInfected'] = $impactCurrentlyInfected;
  $output['impact']['infectionsByRequestedTime'] = $impactInfectByRequestedTime;
  $output['impact']['severeCasesByRequestedTime'] = $impactSevereCasesByReqTime;
  $output['impact']['hospitalBedsByRequestedTime'] = $impactBedsByRequestedTime;
  $output['impact']['casesForICUByRequestedTime'] = $impactIcuCases;
  $output['impact']['casesForVentilatorsByRequestedTime'] = $impactVentilatorCases;
  $output['impact']['dollarsInFlight'] = $impactIncomeLost;
  $output['severeImpact']['currentlyInfected'] = $severeCurrentlyInfected;
  $output['severeImpact']['infectionsByRequestedTime'] = $severeInfectByRequestedTime;
  $output['severeImpact']['severeCasesByRequestedTime'] = $severeSevereCasesByReqTime;
  $output['severeImpact']['hospitalBedsByRequestedTime'] = $severeBedsByRequestedTime;
  $output['severeImpact']['casesForICUByRequestedTime'] = $severeIcuCases;
  $output['severeImpact']['casesForVentilatorsByRequestedTime'] = $severeVentilatorCases;
  $output['severeImpact']['dollarsInFlight'] = $severeIncomeLost;
  
  return $output;
}

function calcInfectByRequestedTime($num, $days){
  return $num * pow(2, trunc($days/3));
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
  return trunc((15/100) * $cases);
}

function availableBeds($cases, $beds){
  $r = 0.35 * $beds;
  return trunc($r - $cases);
}

function icuCases($cases){
  return trunc(0.05 * $cases);
}

function ventilatorCases($cases){
  return trunc(0.02 * $cases);
}

function incomeLost($infected, $avgDailyIncomePop, $avgDailyIncome, $days){
  $result = $infected * $avgDailyIncomePop * $avgDailyIncome / $days;
  return trunc($result);
}

function trunc($value){
  $r = preg_replace("/\.\d+/", "", $value);
  return (int)$r;
}


// echo "<pre>";
// print_r(covid19ImpactEstimator($data3));
// echo "</pre>";
