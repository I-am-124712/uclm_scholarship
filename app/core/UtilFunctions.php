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
    $dutyTime = strtotime(date_format($dutyTime,'H:m'));
    $scheduleTime = strtotime(date_format($scheduleTime,'H:m'));

	$computedLate = 0;
	$computedLateInt = 0;
	$totalLate = 0;

	$computedLate = round($scheduleTime - $dutyTime,2);
	$computedLateInt = round($computedLate);

	if ($computedLate > 0)
		if( $computedLate < $expectedHours)
			if (($computedLate - $computedLateInt) < ($computedLateInt + 0.5)){
				if (($computedLate - $computedLateInt) > ($computedLateInt + 0.25))
					$totalLate = ($computedLateInt + 0.5);
				else
                    $totalLate = $computedLateInt;
            }
			else
				$totalLate = ceil($computedLate);
		else
			$totalLate = $expectedHours;
	else
		$totalLate = 0.0;

	return $totalLate;
}
