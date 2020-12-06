<?php

function time_difference($startdate,$enddate){
    $starttimestamp = strtotime($startdate);
    $endtimestamp = strtotime($enddate);
    $difference = ($endtimestamp - $starttimestamp)/3600;
    return $difference <= 0? 0 : $difference;
}

function time_difference_abs($startdate,$enddate){
    $starttimestamp = strtotime($startdate);
    $endtimestamp = strtotime($enddate);
    $difference = abs($endtimestamp - $starttimestamp)/3600;
    return $difference;

}

function compute_tardiness($dutyTime, $scheduleTime, $expectedHours){
    // Convert to time first
    $dutyTime = strtotime(date_format($dutyTime,'H:i'));
    $scheduleTime = strtotime(date_format($scheduleTime,'H:i'));

	$computedTardiness = 0.0;
	$computedTardinessInt = 0;
	$totalTardiness = 0.0;

	$computedTardiness = ($scheduleTime - $dutyTime)/3600;
    $computedTardinessInt = (int)($computedTardiness);
    

	if ($computedTardiness > 0.0)
		if( $computedTardiness < $expectedHours)
			if (($computedTardiness - $computedTardinessInt) < ($computedTardinessInt + 0.5))
				if (($computedTardiness - $computedTardinessInt) > ($computedTardinessInt + 0.25))
					$totalTardiness = ($computedTardinessInt + 0.5);
				else
                    $totalTardiness = $computedTardinessInt;
			else
				$totalTardiness = ceil($computedTardiness);
		else
			$totalTardiness = $expectedHours;
	else
		$totalTardiness = 0.0;
    
	return $totalTardiness;
}


function getDateFromPartsString(int $year, int $month, int $day){
	return "DateFromParts(". $year . "," . $month . "," . $day . ")";
}