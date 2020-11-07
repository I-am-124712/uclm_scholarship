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
    // $dutyTime = var_export(0.0 + strtotime(date_format($dutyTime,'H:m')),true);
    // $scheduleTime = var_export(0.0 + strtotime(date_format($scheduleTime,'H:m')),true);
    $dutyTime = strtotime(date_format($dutyTime,'H:i'));
    $scheduleTime = strtotime(date_format($scheduleTime,'H:i'));

	$computedLate = 0.0;
	$computedLateInt = 0;
	$totalLate = 0.0;

	$computedLate = ($scheduleTime - $dutyTime)/3600;
    $computedLateInt = (int)($computedLate);
    
    // echo "computedLate: ".var_export($computedLate,true)."\n";
    // echo "computedLateInt: ".var_export($computedLateInt,true)."\n";

	if ($computedLate > 0.0)
		if( $computedLate < $expectedHours)
			if (($computedLate - $computedLateInt) < ($computedLateInt + 0.5))
				if (($computedLate - $computedLateInt) > ($computedLateInt + 0.25))
					$totalLate = ($computedLateInt + 0.5);
				else
                    $totalLate = $computedLateInt;
			else
				$totalLate = ceil($computedLate);
		else
			$totalLate = $expectedHours;
	else
		$totalLate = 0.0;
    
    // echo "totalLate: ".var_export($totalLate,true)."\n\n";
	return $totalLate;
}
