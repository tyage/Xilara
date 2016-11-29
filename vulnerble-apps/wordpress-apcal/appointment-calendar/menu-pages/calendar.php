<!---render full-calendar----->
<script type='text/javascript' xmlns="http://www.w3.org/1999/html">
jQuery(document).ready(function() {
    jQuery('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        editable: false,
        weekends: true,
        timeFormat: 'h:mmtt{-h:mmtt }',
        <?php $AllCalendarSettings = unserialize(get_option('apcal_calendar_settings')); ?>
        firstDay: <?php if($AllCalendarSettings['calendar_start_day']) echo $AllCalendarSettings['calendar_start_day']; else echo "1"; ?>,
        slotMinutes: <?php if($AllCalendarSettings['calendar_slot_time']) echo $AllCalendarSettings['calendar_slot_time']; else echo "15"; ?>,
        minTime: 0,
        defaultView: '<?php if($AllCalendarSettings['calendar_view']) echo $AllCalendarSettings['calendar_view']; else echo "month"; ?>',
        maxTime: 24,
        monthNames: ["<?php _e("January", "appointzilla"); ?>","<?php _e("February", "appointzilla"); ?>","<?php _e("March", "appointzilla"); ?>","<?php _e("April", "appointzilla"); ?>","<?php _e("May", "appointzilla"); ?>","<?php _e("June", "appointzilla"); ?>","<?php _e("July", "appointzilla"); ?>", "<?php _e("August", "appointzilla"); ?>", "<?php _e("September", "appointzilla"); ?>", "<?php _e("October", "appointzilla"); ?>", "<?php _e("November", "appointzilla"); ?>", "<?php _e("December", "appointzilla"); ?>" ],
        monthNamesShort: ["<?php _e("Jan", "appointzilla"); ?>","<?php _e("Feb", "appointzilla"); ?>","<?php _e("Mar", "appointzilla"); ?>","<?php _e("Apr", "appointzilla"); ?>","<?php _e("May", "appointzilla"); ?>","<?php _e("Jun", "appointzilla"); ?>","<?php _e("Jul", "appointzilla"); ?>","<?php _e("Aug", "appointzilla"); ?>","<?php _e("Sept", "appointzilla"); ?>","<?php _e("Oct", "appointzilla"); ?>","<?php _e("Nov", "appointzilla"); ?>","<?php _e("Dec", "appointzilla"); ?>"],
        dayNames: ["<?php _e("Sunday", "appointzilla"); ?>","<?php _e("Monday", "appointzilla"); ?>","<?php _e("Tuesday", "appointzilla"); ?>","<?php _e("Wednesday", "appointzilla"); ?>","<?php _e("Thursday", "appointzilla"); ?>","<?php _e("Friday", "appointzilla"); ?>","<?php _e("Saturday", "appointzilla"); ?>"],
        dayNamesShort: ["<?php _e("Sun", "appointzilla"); ?>","<?php _e("Mon", "appointzilla"); ?>", "<?php _e("Tue", "appointzilla"); ?>", "<?php _e("Wed", "appointzilla"); ?>", "<?php _e("Thus", "appointzilla"); ?>", "<?php _e("Fri", "appointzilla"); ?>", "<?php _e("Sat", "appointzilla"); ?>"],
        buttonText: {
            today: "<?php _e("Today", "appointzilla"); ?>",
            day: "<?php _e("Day", "appointzilla"); ?>",
            week:"<?php _e("Week", "appointzilla"); ?>",
            month:"<?php _e("Month", "appointzilla"); ?>"
        },
        selectable: true,
        selectHelper: false,
            select: function(start, end, allDay) {
                        jQuery('#AppFirstModal').show();
            },
        events: [
            <?php //Loading Appointments On Calendar Start
            global $wpdb;
            $AppointmentTableName = $wpdb->prefix."ap_appointments";
            $FetchAllApps_sql = "select `id`, `name`, `start_time`, `end_time`, `date` FROM `$AppointmentTableName`";
            $AllAppointments = $wpdb->get_results($FetchAllApps_sql, OBJECT);
            if($AllAppointments) {
                foreach($AllAppointments as $single) {
                    $title = $single->name;
                    $start = date("H, i", strtotime($single->start_time));
                    $end= date("H, i", strtotime($single->end_time));
                    // subtract 1 from month digit coz calendar work on month 0-11
                    $y = date ( 'Y' , strtotime( $single->date ) );
                    $m = date ( 'n' , strtotime( $single->date ) ) - 1;
                    $d = date ( 'd' , strtotime( $single->date ) );
                    $date = "$y-$m-$d";
                    $date = str_replace("-",", ", $date);
                    $url = "?page=update-appointment&updateid=".$single->id."&&from=calendar"; ?>
                    {
                        title: "<?php _e('Booked By', 'appointzilla'); echo ': '; echo $title; ?>",
                        start: new Date(<?php echo "$date, $start"; ?>),
                        end: new Date(<?php echo "$date, $end"; ?>),
                        url: "<?php echo $url; ?>",
                        allDay: false,
                        backgroundColor : "#1FCB4A",
                        textColor: "black",
                    },<?php
                }
            }

            //Loading Events On Calendar Start
            global $wpdb;
            $EventTableName = $wpdb->prefix."ap_events";
            $FetchAllEvent_sql = "select `id`, `name`, `start_time`, `end_time`, `start_date`, `end_date`, `repeat` FROM `$EventTableName` where `repeat` = 'N'";
            $AllEvents = $wpdb->get_results($FetchAllEvent_sql, OBJECT);
            if($AllEvents) {
                foreach($AllEvents as $Event) {
                    //convert time foramt H:i:s
                    $starttime = date("H:i", strtotime($Event->start_time));
                    $endtime = date("H:i", strtotime($Event->end_time));
                    //change time format according to calendar
                    $starttime = str_replace(":",", ", $starttime);
                    $endtime = str_replace(":", ", ", $endtime);

                    $startdate = $Event->start_date;
                    // subtract 1 from $startdate month digit coz calendar work on month 0-11
                    $y = date ( 'Y' , strtotime( $startdate ) );
                    $m = date ( 'n' , strtotime( $startdate ) ) - 1;
                    $d = date ( 'd' , strtotime( $startdate ) );
                    $startdate = "$y-$m-$d";
                    $startdate = str_replace("-",", ", $startdate);     //changing date format

                    $enddate = $Event->end_date;
                    // subtract 1 from $startdate month digit coz calendar work on month 0-11
                    $y2 = date ( 'Y' , strtotime( $enddate ) );
                    $m2 = date ( 'n' , strtotime( $enddate ) ) - 1;
                    $d2 = date ( 'd' , strtotime( $enddate ) );
                    $enddate = "$y2-$m2-$d2";

                    $enddate = str_replace("-",", ", $enddate);         //changing date format
                    $url = "?page=update-time-off&update-timeoff=".$Event->id."&from=calendar"; ?>
                    {
                        title: "<?php echo $Event->name; ?>",
                        start: new Date(<?php echo "$startdate, $starttime"; ?>),
                        end: new Date(<?php echo "$enddate, $endtime"; ?>),
                        url: "<?php echo $url; ?>",
                        allDay: false,
                        backgroundColor : "#FF7575",
                        textColor: "black",
                    },<?php
                }
            }

            //Loading Recurring Events On Calendar Start
            $FetchAllREvent_sql = "select `id`, `name`, `start_time`, `end_time`, `start_date`, `end_date`, `repeat` FROM `$EventTableName` where `repeat` != 'N'";
            $AllREvents = $wpdb->get_results($FetchAllREvent_sql, OBJECT);
            //dont show event on filtering
            if($AllREvents) {
                foreach($AllREvents as $Event) {
                    //convert time foramt H:i:s
                    $starttime = date("H:i", strtotime($Event->start_time));
                    $endtime = date("H:i", strtotime($Event->end_time));
                    //change time format according to calendar
                    $starttime = str_replace(":",", ", $starttime);
                    $endtime = str_replace(":", ", ", $endtime);

                    $startdate = $Event->start_date;
                    $enddate = $Event->end_date;

                    if($Event->repeat != 'M') {
                        //if appointment type then calculate RTC(recurring date calculation)
                        if($Event->repeat == 'PD')
                        $RDC = 1;
                        if($Event->repeat == 'D')
                        $RDC = 1;
                        if($Event->repeat == 'W')
                        $RDC = 7;
                        if($Event->repeat == 'BW')
                        $RDC = 14;

                        $Alldates = array();
                        $st_dateTS = strtotime($startdate);
                        $ed_dateTS = strtotime($enddate);
                        for ($currentDateTS = $st_dateTS; $currentDateTS <= $ed_dateTS; $currentDateTS += (60 * 60 * 24 * $RDC)) {
                            $currentDateStr = date("Y-m-d",$currentDateTS);
                            $AlldatesArr[] = $currentDateStr;

                            // subtract 1 from $startdate month digit coz calendar work on month 0-11
                            $y = date ( 'Y' , strtotime( $currentDateStr ) );
                            $m = date ( 'n' , strtotime( $currentDateStr ) ) - 1;
                            $d = date ( 'd' , strtotime( $currentDateStr ) );
                            $startdate = "$y-$m-$d";
                            $startdate = str_replace("-",", ", $startdate);     //changing date format

                            // subtract 1 from $startdate month digit coz calendar work on month 0-11
                            $y2 = date ( 'Y' , strtotime( $currentDateStr ) );
                            $m2 = date ( 'n' , strtotime( $currentDateStr ) ) - 1;
                            $d2 = date ( 'd' , strtotime( $currentDateStr ) );
                            $enddate = "$y2-$m2-$d2";

                            $enddate = str_replace("-",", ", $enddate);         //changing date format
                            $url = "?page=update-time-off&update-timeoff=".$Event->id."&from=calendar"; ?>
                            {
                                title: "<?php echo ucwords($Event->name); ?>",
                                start: new Date(<?php echo "$startdate, $starttime"; ?>),
                                end: new Date(<?php echo "$enddate, $endtime"; ?>),
                                url: "<?php echo $url; ?>",
                                allDay: false,
                                backgroundColor : "#FF7575",
                                textColor: "black",
                            },<?php
                        }// end of for
                    } else {
                        $i = 0;
                        do {
                            $NextDate = date("Y-m-d", strtotime("+$i months", strtotime($startdate)));
                            // subtract 1 from $startdate month digit coz calendar work on month 0-11
                            $y = date ( 'Y' , strtotime( $NextDate ) );
                            $m = date ( 'n' , strtotime( $NextDate ) ) - 1;
                            $d = date ( 'd' , strtotime( $NextDate ) );
                            $startdate2 = "$y-$m-$d";
                            $startdate2 = str_replace("-",", ", $startdate2);       //changing date format
                            $enddate2 = str_replace("-",", ", $startdate2);
                            $url = "?page=update-time-off&update-timeoff=".$Event->id."&from=calendar"; ?>
                            {
                                title: "<?php echo ucwords($Event->name); ?>",
                                start: new Date(<?php echo "$startdate2, $starttime"; ?>),
                                end: new Date(<?php echo "$enddate2, $endtime"; ?>),
                                url: "<?php echo $url; ?>",
                                allDay: false,
                                backgroundColor : "#FF7575",
                                textColor: "black",
                            },<?php
                            $i = $i+1;
                        } while(strtotime($enddate) != strtotime($NextDate));
                    }//end of else
                }//end of foreach
            }// end of allevents ?>
        ]
    });


    //Launch Modal Form - show frist modal
    jQuery('#addappointment').click(function(){
        jQuery('#AppFirstModal').show();
    });
    //hide modal
    jQuery('#close').click(function(){
        jQuery('#AppFirstModal').hide();
    });

    //load date picekr on modal for
    document.addnewappointment.appdate.value = jQuery.datepicker.formatDate('dd-mm-yy', new Date());
    jQuery(function(){
        jQuery("#datepicker").datepicker({
            inline: true,
            minDate: 0,
            altField: '#alternate',
            firstDay: <?php if($AllCalendarSettings['calendar_start_day']) echo $AllCalendarSettings['calendar_start_day']; else echo "0";  ?>,
            onSelect: function(dateText, inst) {
                var dateAsString = dateText;
                var seleteddate = jQuery.datepicker.formatDate('dd-mm-yy', new Date(dateAsString));
                document.addnewappointment.appdate.value = seleteddate;
            }
        });
    });

    //AppFirstModal Validation
    jQuery('#next1').click(function(){
        jQuery(".error").hide();
        if(jQuery('#service').val() == 0) {
            jQuery("#service").after("<span class='error'><p><strong><?php echo __('Select any service.', 'appointzilla'); ?></strong></p></span>");
            return false;
        }
        // get date and service and send request to client form
        var bookdate = jQuery("input#appdate").val();
        var service = jQuery("select#service").val();
        var PostData = "bookdate=" + bookdate + "&service=" + service;
        var url = "?page=time_slot";
        jQuery('#loading1').show();     // loading button onclick next1 at first modal
        jQuery('#next1').hide();        //hide next button
        jQuery.ajax({
            dataType : 'html',
            type: 'GET',
            url : url,
            cache: false,
            data : PostData,
            complete : function() { },
            success: function(data) {
                jQuery('#time_sloat').show();
                jQuery('#myfristmodel').hide();
                data=jQuery(data).find('div#myModalsecond');
                jQuery('#loading1').hide();
                jQuery('#time_sloat').html(data);
            }
        });
    });
});

function backtodate() {
    jQuery('#myfristmodel').show();
    jQuery('#next1').show();
    jQuery('#time_sloat').hide();
}

function checkvalidation() {
    jQuery(".error").hide();
    var start_time = jQuery('input[name=start_time]:radio:checked').val();
    if(!start_time) {
        jQuery("#clientboxform").before("<span class='error'><p align=center><strong><?php echo __('Select any time.', 'appointzilla'); ?></strong></p></span>");
        return false;
    }

    var name = jQuery("#name").val();
    if(name == '') {
        jQuery("#name").after("<span class='error'><p><strong><?php echo __('Name required.', 'appointzilla'); ?></strong></p></span>");
        return false;
    } else {
        var name = isNaN(name);
        if(name == false) {
            jQuery("#name").after("<span class='error'><p><strong><?php echo __('Invalid name.', 'appointzilla'); ?></strong></p></span>");
            return false;
        }
    }

    var email = jQuery("#email").val();
    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(email == '') {
        jQuery("#email").after("<span class='error'><p><strong><?php echo __('Email required.', 'appointzilla'); ?></strong></p></span>");
        return false;
    } else {
        if(regex.test(email) == false ) {
            jQuery("#email").after("<span class='error'><p><strong><?php echo __('Invalid email.', 'appointzilla'); ?></strong></p></span>");
            return false;
        }
    }

    var phone = jQuery("#phone").val();
    if(phone == '') {
        jQuery("#phone").after("<span class='error'><p><strong><?php echo __('Phone required.', 'appointzilla'); ?></strong></p></span>");
        return false;
    } else {
        var phone = isNaN(phone);
        if(phone == true) {
            jQuery("#phone").after("<span class='error'><p><strong><?php echo __('Invalid phone number.', 'appointzilla'); ?></strong></p></span>");
            return false;
        }
    }

    var start_time = jQuery('input[name=start_time]:radio:checked').val();
    var name = jQuery("#name").val();
    var email = jQuery("#email").val();
    var phone = jQuery("#phone").val();
    var desc = jQuery("#desc").val();
    var bookdate = jQuery("#appointmentdate").val();
    var serviceduration = jQuery("#serviceduration").val();
    var serviceid = jQuery("#serviceid").val();
    jQuery('#loading2').show();     // display scheduling icon  after click on book button
    jQuery('#booknowapp').hide();   // hide book now button
    jQuery('#back').hide();         // hide back button after click on book button

    var PostData = 'bookdate='+ bookdate + '&serviceid=' + serviceid + '&name='+ name +'&email=' + email +'&phone='  + phone + '&desc=' +desc+ '&start_time=' + start_time + '&serviceduration=' + serviceduration;
    var url = "?page=data-save";
    jQuery.ajax({
        dataType : 'html',
        type: 'GET',
        url : url,
        cache: false,
        data : PostData,
        complete : function() {  },
        success: function(data) {
            data=jQuery(data).find('div#saving-appointment-div');
            jQuery('#saving-div').html(data);
            alert("<?php echo _e("Thank you, appointment has been scheduled. Confirmation mail sent to the client and admin.", "appointzilla"); ?>");
            window.location = '?page=appointment-calendar';
        }
    });
}
</script>

<style type='text/css'>
.error {
    color:#FF0000;
}
</style>


<!----Add New Appointment Button--->
<div id="bkbtndiv" align="center" style="padding:5px;">
    <button name="addappointment" class="btn btn-primary" type="submit" id="addappointment">
        <strong><i class="icon-calendar icon-white"></i>
            <?php if(isset($AllCalendarSettings['booking_button_text'])) {
                echo $AllCalendarSettings['booking_button_text'];
            } else  {
                echo _e("Schedule New Appointment", "appointzilla");
            } ?>
        </strong>
    </button>
</div>

<!---show full-calendar--->
<div id="calendar" style="background-color: #ffffff;"></div>

<!---AppFirstModal For Schedule New Appointment--->
<div id="AppFirstModal" style="display:none;">
    <div class="modal" id="myModal" >
        <div id="myfristmodel">
            <form action="" method="post" name="addnewappointment" id="addnewappointment">
                <div class="modal-info">
                    <div style="float:right; margin-top:5px; margin-right:10px;">
                        <a id="close" href="#" style="float:right; margin-right:4px; margin-top:12px;" ><i class="icon-remove"></i></a>
                    </div>
                    <div class="alert alert-info">
                        <h4><?php _e("Schedule New Appointment", "appointzilla"); ?></h4><?php _e("Select Date & Service", "appointzilla"); ?>
                    </div>
                </div>

                <div class="modal-body">
                    <div id="firdiv" style="float:left;">
                        <div id="datepicker"></div>
                    </div>

                    <div id="secdiv" style="float:right;" >
                        <p><strong><?php echo _e("Your Appointment Date", "appointzilla"); ?>:</strong></p>
                        <input name="appdate" id="appdate" type="text" disabled="disabled" />
                            <?php global $wpdb;
                            $ServiceTable = $wpdb->prefix."ap_services";
                            $findservice_sql = "SELECT * FROM `$ServiceTable` WHERE `availability` = 'yes'";
                            $AllService = $wpdb->get_results($findservice_sql, OBJECT); ?>
                        <p><strong><?php _e("Select Service", "appointzilla"); ?>:</strong></p>
                        <select name="service" id="service">
                            <option value="0"><?php _e("Select any service", "appointzilla"); ?></option>
                            <?php foreach($AllService as $Service) echo "<option value='$Service->id'>".ucwords($Service->name)." (".$Service->duration."min/$$Service->cost)</option>"; ?>
                        </select>
                        <br>
                        <button name="next1" class="btn" type="button" id="next1"><?php echo _e("Next", "appointzilla"); ?> <i class="icon-arrow-right"></i> </button>
                        <div id="loading1" style="display:none;"><?php _e('Loading...', 'appointzilla'); ?><img src="<?php echo plugins_url()."/appointment-calendar/images/loading.gif"; ?>" /></div>
                    </div>
                </div>
            </form>
        </div>
        <!---time_slot start  model  2--->
        <div id="time_sloat" style="display:" ></div>
    </div>
    <div id="saving-div" style="display:"></div>
</div>

<!---AppSecondModal For Schedule New Appointment--->
<div id="AppSecondModal" style="display:none;">
</div>
<!--date-picker js -->
<script src="<?php echo plugins_url('/datepicker-assets/js/jquery.ui.datepicker.js', __FILE__); ?>" type="text/javascript"></script>