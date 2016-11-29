<?php
/**
 * Time Slots Calculation
 */
global $wpdb;
$ServiceId =  $_GET['service'];
$ServiceTableName = $wpdb->prefix."ap_services";
$FindService_sql = "SELECT `name`, `duration` FROM `$ServiceTableName` WHERE `id` = '$ServiceId'";
$ServiceData = $wpdb->get_row($FindService_sql, OBJECT);
$ServiceDuration = $ServiceData->duration;

$AppointmentDate = date("Y-m-d", strtotime($_GET['bookdate'])); //assign selected date by user
$AllCalendarSettings = unserialize(get_option('apcal_calendar_settings'));
$Biz_start_time = $AllCalendarSettings['day_start_time'];
$Biz_end_time = $AllCalendarSettings['day_end_time'];

if(isset($AllCalendarSettings['booking_time_slot'])) {
    $UserDefineTimeSlot = $AllCalendarSettings['booking_time_slot'];
} else {
    $UserDefineTimeSlot = $ServiceDuration;
}

$AllSlotTimesList = array();
$AppPreviousTimes = array();
$AppNextTimes = array();
$AppBetweenTimes = array();
$EventPreviousTimes = array();
$EventBetweenTimes = array();
$DisableSlotsTimes = array();
$BusinessEndCheck =array();
$AllSlotTimesList_User = array();
$Enable = array();
$TodaysAllDayEvent = 0;

$TimeOffTableName = $wpdb->prefix."ap_events";
//if today is any all-day time-off then show msg no time available today
$TodaysAllDayFetchEvents_sql = "SELECT `start_time`, `end_time`, `repeat`, `start_date`, `end_date` FROM `$TimeOffTableName` WHERE date('$AppointmentDate') between `start_date` AND `end_date` AND `allday` = '1'";

$TodaysAllDayEventData = $wpdb->get_results($TodaysAllDayFetchEvents_sql, OBJECT);

//check if appointment date in any recurring time-off date
foreach($TodaysAllDayEventData as $SingleTimeOff) {
    // none check
    if($SingleTimeOff->repeat == 'N') {
        $TodaysAllDayEvent = 1;
    }

    // daily check
    if($SingleTimeOff->repeat == 'D') {
        $TodaysAllDayEvent = 1;
    }

    // weekly check
    if($SingleTimeOff->repeat == 'W') {
        $EventStartDate = $SingleTimeOff->start_date;
        $diff = ( strtotime($EventStartDate) - strtotime($AppointmentDate)  )/60/60/24;
        if(($diff % 7) == 0) {
            $TodaysAllDayEvent = 1;
        }
    }

    //bi-weekly check
    if($SingleTimeOff->repeat == 'BW') {
        $EventStartDate = $SingleTimeOff->start_date;
        $diff = ( strtotime($EventStartDate) - strtotime($AppointmentDate)  )/60/60/24;
        if(($diff % 14) == 0) {
            $TodaysAllDayEvent = 1;
        }
    }

    //monthly check
    if($SingleTimeOff->repeat == 'M') {
        // calculate all monthly dates
        $EventStartDate = $SingleTimeOff->start_date;
        $EventEndDate = $SingleTimeOff->end_date;
        $i = 0;
        do {
                $NextDate = date("Y-m-d", strtotime("+$i months", strtotime($EventStartDate)));
                $AllEventMonthlyDates[] = $NextDate;
                $i = $i+1;
        } while(strtotime($EventEndDate) != strtotime($NextDate));

        //check appointment-date in $AllEventMonthlyDates
        if(in_array($AppointmentDate, $AllEventMonthlyDates)) {
            $TodaysAllDayEvent = 1;
        }
    }
}//end of event fetching forech


if($TodaysAllDayEvent) {
    echo "<div class='alert alert-error'>" . __("Sorry! No time available today.", "appointzilla") . "</div>";
    echo "<a class='btn btn-primary' id='back' onclick='backtodate()'>&larr; " . __("Back", "appointzilla") . "</a>";
} else {
    echo "<div class='alert alert-info'>" . __("Available Time For", "appointzilla") . " <strong>'$ServiceData->name'</strong> " . __("On", "appointzilla") . "  <strong>'".date("l, jS M.", strtotime($AppointmentDate))."'</strong></div>";

    //Calculate all time slots according to today's biz hours
    $start = strtotime($Biz_start_time);
    $end = strtotime($Biz_end_time);

    if($UserDefineTimeSlot)
    {
        $UserTimeSlot = $UserDefineTimeSlot;
    }else
    {
        $UserTimeSlot = 30;
    }
    for( $i = $start; $i < $end; $i += (60*$UserTimeSlot))
    {
        $AllSlotTimesList_User[] = date('h:i A', $i);
    }
    // Business end check
    $Business_end = strtotime($Biz_end_time);
    $ServiceDuration_Biss= $ServiceDuration-5;
    $ServiceDuration_Biss = $ServiceDuration_Biss *60;
    $EndStartTime = $Business_end - $ServiceDuration_Biss;
    for( $i = $EndStartTime; $i < $Business_end; $i += (60*5)) {
        $BusinessEndCheck[] = date('h:i A', $i);
    }

    // Create Business Time slot for calculation
    for( $i = $start; $i < $end; $i += (60*5)) {
        $AllSlotTimesList[] = date('h:i A', $i);
    }

    //Fetch All today's appointments and calculate disable slots
    $AppointmentTableName = $wpdb->prefix."ap_appointments";
    $AllAppointments_sql = "SELECT `start_time`, `end_time` FROM `$AppointmentTableName` WHERE `date`= '$AppointmentDate'";

    $AllAppointmentsData = $wpdb->get_results($AllAppointments_sql, OBJECT);
    if($AllAppointmentsData) {
        foreach($AllAppointmentsData as $Appointment) {

            $AppStartTimes[] = date('h:i A', strtotime( $Appointment->start_time ) );
            $AppEndTimes[] = date('h:i A', strtotime( $Appointment->end_time ) );

            //now calculate 5min slots between appointment's start_time & end_time
            $start_et = strtotime($Appointment->start_time);
            $end_et = strtotime($Appointment->end_time);
            //make 15-10=5min slot
            for( $i = $start_et; $i < $end_et; $i += (60*(5))) {
                $AppBetweenTimes[] = date('h:i A', $i);
            }
        }

            //calculating  Next & Previous time of booked appointments
            foreach($AllSlotTimesList as $single) {
                if(in_array($single, $AppStartTimes)) {
                    //get next time
                    $time = $single;
                    $event_length = $ServiceDuration-5;             // Service duration time -  slot time
                    $timestamp = strtotime("$time");
                    $endtime = strtotime("+$event_length minutes", $timestamp);
                    $next_time = date('h:i A', $endtime);
                    //calculate next time
                    $start = strtotime($single);
                    $end = strtotime($next_time);
                    //making 5min difference slot
                    for( $i = $start; $i <= $end; $i += (60*(5))) {
                        $AppNextTimes[] = date('h:i A', $i);
                    }

                    //calculate previous time
                    $time1 = $single;
                    $event_length1 = $ServiceDuration-5; // 60min Service duration time - 15 slot time
                    $timestamp1 = strtotime("$time1");
                    $endtime1 = strtotime("-$event_length1 minutes", $timestamp1);
                    $next_time1 = date('h:i A', $endtime1);

                    $start1 = strtotime($next_time1);
                    $end1 = strtotime($single);
                    //making 5min diff slot
                    for( $i = $start1; $i <= $end1; $i += (60*(5))) {
                        $AppPreviousTimes[] = date('h:i A', $i);
                    }
                }
            }
            //end calculating Next & Previous time of booked appointments
    } // end if $AllAppointmentsData


        //Fetch All today's timeoff and calculate disable slots
        $EventTableName = $wpdb->prefix."ap_events";
        $AllEventsSQL = "SELECT `start_time`, `end_time` FROM `$EventTableName` WHERE date('$AppointmentDate') between `start_date` AND `end_date` AND `allday` = '0' AND `repeat` != 'W' AND `repeat` != 'BW' AND `repeat` != 'M'";
        $AllEventsData = $wpdb->get_results($AllEventsSQL, OBJECT);
        if($AllEventsData) {
            foreach($AllEventsData as $Event) {
                //calculate previous time (event start time to back service-duration-5)
                $minustime = $ServiceDuration - 5;
                $start = date('h:i A', strtotime("-$minustime minutes", strtotime($Event->start_time)));
                $start = strtotime($start);
                $end =  $Event->start_time;
                $end = strtotime($end);
                //making 5min difference slot
                for( $i = $start; $i <= $end; $i += (60*(5))) {
                    $EventPreviousTimes[] = date('h:i A', $i);
                }

                //calculating between time (start - end)
                $start_et = strtotime($Event->start_time);
                $end_et = strtotime($Event->end_time);
                //making 5min slot
                for( $i = $start_et; $i < $end_et; $i += (60*(5))) {
                    $EventBetweenTimes[] = date('h:i A', $i);
                }
            }
        }



        //Fetch All 'WEEKLY' time-off and calculate disable slots
        $EventTableName = $wpdb->prefix."ap_events";
        $AllEventsSQL = "SELECT `start_time`, `end_time`, `start_date`, `end_date` FROM `$EventTableName` WHERE date('$AppointmentDate') between `start_date` AND `end_date` AND `allday` = '0' AND `repeat` = 'W'";
        $AllEventsData = $wpdb->get_results($AllEventsSQL, OBJECT);
        if($AllEventsData) {
            foreach($AllEventsData as $Event) {
                //calculate all weekly dates between recurring_start_date - recurring_end_date
                $Current_Re_Start_Date = $Event->start_date;
                $Current_Re_End_Date = $Event->end_date;

                $Current_Re_Start_Date = strtotime($Current_Re_Start_Date);
                $Current_Re_End_Date = strtotime($Current_Re_End_Date);

                //make weekly dates
                for( $i = $Current_Re_Start_Date; $i <= $Current_Re_End_Date; $i += (60 * 60 * 24 * 7)) {
                    $AllEventWeelylyDates[] = date('Y-m-d', $i);
                }
                if(in_array($AppointmentDate, $AllEventWeelylyDates)) {
                    //calculate previous time (event start time to back service-duration-5)
                    $minustime = $ServiceDuration - 5;
                    $start = date('h:i A', strtotime("-$minustime minutes", strtotime($Event->start_time)));
                    $start = strtotime($start);
                    $end =  $Event->start_time;
                    $end = strtotime($end);
                    //making 5min difference slot
                    for( $i = $start; $i <= $end; $i += (60*(5))) {
                        $EventPreviousTimes[] = date('h:i A', $i);
                    }

                    //calculating between time (start - end)
                    $start_et = strtotime($Event->start_time);
                    $end_et = strtotime($Event->end_time);
                    //making 5min slot
                    for( $i = $start_et; $i < $end_et; $i += (60*(5))) {
                        $EventBetweenTimes[] = date('h:i A', $i);
                    }
                }
            }
        }


        //Fetch All 'BI-WEEKLY' time-off and calculate disable slots
        $EventTableName = $wpdb->prefix."ap_events";
        $AllEventsSQL = "SELECT `start_time`, `end_time`, `start_date`, `end_date` FROM `$EventTableName` WHERE date('$AppointmentDate') between `start_date` AND `end_date` AND `allday` = '0' AND `repeat` = 'BW'";
        $AllEventsData = $wpdb->get_results($AllEventsSQL, OBJECT);
        if($AllEventsData) {
            foreach($AllEventsData as $Event) {
                //calculate all weekly dates between recurring_start_date - recurring_end_date
                $Current_Re_Start_Date = $Event->start_date;
                $Current_Re_End_Date = $Event->end_date;

                $Current_Re_Start_Date = strtotime($Current_Re_Start_Date);
                $Current_Re_End_Date = strtotime($Current_Re_End_Date);
                //make bi-weekly dates
                for( $i = $Current_Re_Start_Date; $i <= $Current_Re_End_Date; $i += (60 * 60 * 24 * 14)) {
                    $AllEventBiWeelylyDates[] = date('Y-m-d', $i);
                }

                if(in_array($AppointmentDate, $AllEventBiWeelylyDates)) {
                    //calculate previous time (event start time to back service-duration-5)
                    $minustime = $ServiceDuration - 5;
                    $start = date('h:i A', strtotime("-$minustime minutes", strtotime($Event->start_time)));
                    $start = strtotime($start);
                    $end =  $Event->start_time;
                    $end = strtotime($end);
                    //making 5min difference slot
                    for( $i = $start; $i <= $end; $i += (60*(5))) {
                        $EventPreviousTimes[] = date('h:i A', $i);
                    }

                    //calculating between time (start - end)
                    $start_et = strtotime($Event->start_time);
                    $end_et = strtotime($Event->end_time);
                    //making 5min slot
                    for( $i = $start_et; $i < $end_et; $i += (60*(5)))  {
                        $EventBetweenTimes[] = date('h:i A', $i);
                    }
                }
            }
        }


        //Fetch All 'MONTHLY' time-off and calculate disable slots
        $EventTableName = $wpdb->prefix."ap_events";
        $AllEventts_sql = "SELECT `start_time`, `end_time`, `start_date`, `end_date` FROM `$EventTableName` WHERE date('$AppointmentDate') between `start_date` AND `end_date` AND `allday` = '0' AND `repeat` = 'M'";
        $AllEventsData = $wpdb->get_results($AllEventts_sql, OBJECT);
        if($AllEventsData) {
            foreach($AllEventsData as $Event) {
                //calculate all weekly dates between recurring_start_date - recurring_end_date
                $Current_Re_Start_Date = $Event->start_date;
                $Current_Re_End_Date = $Event->end_date;

                $i = 0;
                do {
                        $NextDate = date("Y-m-d", strtotime("+$i months", strtotime($Current_Re_Start_Date)));
                        $AllEventMonthlyDates[] = $NextDate;
                        $i = $i+1;
                } while(strtotime($Current_Re_End_Date) != strtotime($NextDate));

                if(in_array($AppointmentDate, $AllEventMonthlyDates)) {
                    //calculate previous time (event start time to back service-duration-5)
                    $minustime = $ServiceDuration - 5;
                    $start = date('h:i A', strtotime("-$minustime minutes", strtotime($Event->start_time)));
                    $start = strtotime($start);
                    $end =  $Event->start_time;
                    $end = strtotime($end);
                    //making 5min difference slot
                    for( $i = $start; $i <= $end; $i += (60*(5))) {
                        $EventPreviousTimes[] = date('h:i A', $i);
                    }

                    $start_et = strtotime($Event->start_time);
                    $end_et = strtotime($Event->end_time);
                    //making 5min slot
                    for( $i = $start_et; $i < $end_et; $i += (60*(5))) {
                        $EventBetweenTimes[] = date('h:i A', $i);
                    }
                }
            }
        }

        $DisableSlotsTimes = array_merge($AppBetweenTimes, $AppPreviousTimes, $EventPreviousTimes, $EventBetweenTimes, $BusinessEndCheck);
        unset($AppBetweenTimes);
        unset($AppPreviousTimes);
        unset($AppNextTimes);
        unset($EventBetweenTimes);
        unset($BusinessEndCheck);

        // compare All Business Time slot with  with DisableSlotsTimes
        foreach($AllSlotTimesList as $Single) {
            if(in_array($Single, $DisableSlotsTimes)) {
                $Disable[] = $Single;
            } else {
                $Enable[] = $Single;
            }
        }// end foreach

        // Show All Enable Time Slot
        foreach($AllSlotTimesList_User as $Single) {
            if(isset($Enable)) {
                if(in_array($Single, $Enable)) { // disable slots ?>
                    <div style="width:100px; float:left; padding:2px;">
                        <input name="start_time" id="start_time" type="radio"   value="<?php echo $Single; ?>"/>&nbsp;<?php echo $Single; ?>
                    </div>
                    <?php
                } else { // enable slots ?>
                    <div style="width:100px; float:left; padding:2px;">
                    <input name="start_time" id="start_time"  disabled="disabled" type="radio"  value="<?php echo $Single; ?>"/>&nbsp;<del><?php echo $Single; ?></del>
                    </div>
                    <?php
                }
            }// end of enable isset
        }// end foreach
    unset($DisableSlotsTimes);
} // end else
?>