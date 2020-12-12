<?php

function time_difference($startdate,$enddate){
    $difference = time_difference_abs($startdate, $enddate);
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


/**
 * Code retrieved from https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
 * Author - Glavić
 * 
 * @param string $datetime DateTime string. (Format: "Y-m-d H:i:s")
 * @param bool $full Full time interval string representation.
 */
function time_elapsed_string($datetime, $full = false) {
    date_default_timezone_set('Asia/Manila');
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}