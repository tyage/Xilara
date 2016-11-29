<?php
add_shortcode( 'APCAL_MOBILE', 'appointment_calendar_short_code_mobile' );
function appointment_calendar_short_code_mobile() {
    $AllCalendarSettings = unserialize(get_option('apcal_calendar_settings'));
    if(get_locale()) {
        $language = get_locale();
        if($language) { define('L_LANG',$language); }
    }?>
    <style type='text/css'>
    .apcal_error{
        color: #FF0000;
    }
    </style>
    <p align='center'>
    <script>
    function gofornext() {
        jQuery(".apcal-error").hide();
        var ServiceId = jQuery('#serviceid').val();
        var ServiceId = jQuery('#serviceid').val();
        var AppDate = jQuery('#appointmentdate').val();
        var  ServiceDuration =  jQuery('#serviceduration').val();
        var StartTime = jQuery('input[name=start_time]:radio:checked').val();
        if(!StartTime) {
            jQuery("#selecttimediv").after("<br><span class='apcal-error'><strong><?php _e("Select any time.", "appointzilla"); ?></strong></span>");
            return false;
        }
        document.getElementById('loading3').style.display = '';
        document.getElementById('user_info_button').style.display = 'none';
        var SecondData = "ServiceId=" + ServiceId + "&AppDate=" + AppDate + "&ServiceDuration=" + ServiceDuration + "&StartTime=" +StartTime  ;
        jQuery.ajax({
            dataType : 'html',
            type: 'GET',
            url : location.href,
            cache: false,
            data : SecondData,
            complete : function() {  },
            success: function(data) {
                data = jQuery(data).find('div#user_info');
                jQuery('#timesloatbox').hide();
                jQuery('#user_info_page').show();
                jQuery('#user_info_page').html(data);
            }
        });
    }

</script>
    <?php //save appointment and email admin & client/customer
    if( isset($_POST['Client_Name']) && isset($_POST['Client_Email']) ) {
        global $wpdb;
        $ClientName = $_POST['Client_Name'];
        $ClientEmail = $_POST['Client_Email'];
        $ClientPhone = $_POST['Client_Phone'];
        $ClientNote = $_POST['Client_Note'];
        $AppointmentDate = date("Y-m-d", strtotime($_POST['AppDate']));
        $ServiceId = $_POST['ServiceId'];
        $ServiceDuration = $_POST['Service_Duration'];
        $StartTime = $_POST['StartTime'];
        //calculate end time according to service duration
        $EndTime =  date('h:i A', strtotime("+$ServiceDuration minutes", strtotime($StartTime)) );
        $AppointmentKey = md5(date("F j, Y, g:i a"));
        $Status = __("pending", "appointzilla");
        $AppointmentBy = __("user", "appointzilla");
        $AppointmentsTable = $wpdb->prefix . "ap_appointments";
        $AddAppointment_sql ="INSERT INTO `$AppointmentsTable` ( `id` , `name` , `email` , `service_id` , `phone` , `start_time` , `end_time` , `date` , `note` , `appointment_key` , `status` , `appointment_by` ) VALUES (NULL , '$ClientName', '$ClientEmail', '$ServiceId', '$ClientPhone', '$StartTime', '$EndTime', '$AppointmentDate', '$ClientNote', '$AppointmentKey', '$Status', '$AppointmentBy');";
            if($wpdb->query($AddAppointment_sql)) {

                //$MangeAppointmentUrl = site_url().'/wp-admin/admin.php?page=manage-appointments';
                //$BlogUrl = site_url().'/wp-admin';
                $BlogName = get_bloginfo();

                //get service details
                $ServiceTable = $wpdb->prefix."ap_services";
                $ServiceData = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` = '$ServiceId'", OBJECT);
                $ServiceName = $ServiceData->name;

                //check notification is enabled
                $NotificationStatus = get_option('emailstatus');
                if($NotificationStatus == "on") {
                    $Attachments = "";
                    $AppointmentTime = $StartTime." - ".$EndTime;
                    $AdminSubject = get_option('new_appointment_admin_subject');
                    $AdminSubject = str_replace("[blog-name]", ucwords($BlogName), $AdminSubject);
                    $AdminSubject = str_replace("[client-name]", ucwords($ClientName), $AdminSubject);
                    $AdminSubject = str_replace("[client-email]", ucwords($ClientEmail), $AdminSubject);
                    $AdminSubject = str_replace("[client-phone]", ucwords($ClientPhone), $AdminSubject);
                    $AdminSubject = str_replace("[client-si]", ucwords($ClientNote), $AdminSubject);
                    $AdminSubject = str_replace("[service-name]", ucwords($ServiceName), $AdminSubject);
                    $AdminSubject = str_replace("[app-date]", $AppointmentDate, $AdminSubject);
                    $AdminSubject = str_replace("[app-status]", ucwords($Status), $AdminSubject);
                    $AdminSubject = str_replace("[app-time]", $AppointmentTime, $AdminSubject);
                    $AdminSubject = str_replace("[app-key]", $AppointmentKey, $AdminSubject);
                    $AdminSubject = str_replace("[app-note]", ucfirst($ClientNote), $AdminSubject);

                    $AdminBody = get_option('new_appointment_admin_body');
                    $AdminBody = str_replace("[blog-name]", ucwords($BlogName), $AdminBody);
                    $AdminBody = str_replace("[client-name]", ucwords($ClientName), $AdminBody);
                    $AdminBody = str_replace("[client-email]", ucwords($ClientEmail), $AdminBody);
                    $AdminBody = str_replace("[client-phone]", ucwords($ClientPhone), $AdminBody);
                    $AdminBody = str_replace("[client-si]", ucwords($ClientNote), $AdminBody);
                    $AdminBody = str_replace("[service-name]", ucwords($ServiceName), $AdminBody);
                    $AdminBody = str_replace("[app-date]", $AppointmentDate, $AdminBody);
                    $AdminBody = str_replace("[app-status]", ucwords($Status), $AdminBody);
                    $AdminBody = str_replace("[app-time]", $AppointmentTime, $AdminBody);
                    $AdminBody = str_replace("[app-key]", $AppointmentKey, $AdminBody);
                    $AdminBody = str_replace("[app-note]", ucfirst($ClientNote), $AdminBody);


                    $ClientSubject = get_option('new_appointment_client_subject');
                    $ClientSubject = str_replace("[blog-name]", ucwords($BlogName), $ClientSubject);
                    $ClientSubject = str_replace("[client-name]", ucwords($ClientName), $ClientSubject);
                    $ClientSubject = str_replace("[client-email]", ucwords($ClientEmail), $ClientSubject);
                    $ClientSubject = str_replace("[client-phone]", ucwords($ClientPhone), $ClientSubject);
                    $ClientSubject = str_replace("[client-si]", ucwords($ClientNote), $ClientSubject);
                    $ClientSubject = str_replace("[service-name]", ucwords($ServiceName), $ClientSubject);
                    $ClientSubject = str_replace("[app-date]", $AppointmentDate, $ClientSubject);
                    $ClientSubject = str_replace("[app-status]", ucwords($Status), $ClientSubject);
                    $ClientSubject = str_replace("[app-time]", $AppointmentTime, $ClientSubject);
                    $ClientSubject = str_replace("[app-key]", $AppointmentKey, $ClientSubject);
                    $ClientSubject = str_replace("[app-note]", ucfirst($ClientNote), $ClientSubject);

                    $ClientBody = get_option('new_appointment_client_body');
                    $ClientBody = str_replace("[blog-name]", ucwords($BlogName), $ClientBody);
                    $ClientBody = str_replace("[client-name]", ucwords($ClientName), $ClientBody);
                    $ClientBody = str_replace("[client-email]", ucwords($ClientEmail), $ClientBody);
                    $ClientBody = str_replace("[client-phone]", ucwords($ClientPhone), $ClientBody);
                    $ClientBody = str_replace("[client-si]", ucwords($ClientNote), $ClientBody);
                    $ClientBody = str_replace("[service-name]", ucwords($ServiceName), $ClientBody);
                    $ClientBody = str_replace("[app-date]", $AppointmentDate, $ClientBody);
                    $ClientBody = str_replace("[app-status]", ucwords($Status), $ClientBody);
                    $ClientBody = str_replace("[app-time]", $AppointmentTime, $ClientBody);
                    $ClientBody = str_replace("[app-key]", $AppointmentKey, $ClientBody);
                    $ClientBody = str_replace("[app-note]", ucfirst($ClientNote), $ClientBody);

                    //check email type
                    $EmailType = get_option('emailtype');
                    $EmailDetails = unserialize(get_option( 'emaildetails'));
                    //wp-email
                    if($EmailType == "wpmail") {
                        $AdminEmail = $EmailDetails['wpemail'];
                        $Headers[] = "From: Admin <".$AdminEmail.">";
                        //send wp email to client
                        wp_mail( $ClientEmail, $ClientSubject, $ClientBody, $Headers, $Attachments);
                        //send wp email to admin
                        wp_mail( $AdminEmail, $AdminSubject, $AdminBody, $Headers, $Attachments);
                    }

                    //php-email
                    if($EmailType == "phpmail") {
                        $AdminEmail = $EmailDetails['phpemail'];
                        $Headers[] = "From: Admin <".$AdminEmail.">";
                        ///send php email to client
                        mail($ClientEmail, $ClientSubject, $ClientBody, $Headers);
                        //send php email to admin
                        mail( $AdminEmail, $AdminSubject, $AdminBody, $Headers);
                    }

                    //wp-email
                    if($EmailType == "smtp") {
                        require_once('menu-pages/notification/Email.php');
                        $AdminEmail     = $EmailDetails['smtpemail'];
                        $HostName       = $EmailDetails['hostname'];
                        $PortNo         = $EmailDetails['portno'];
                        $SMTPEmail      = $EmailDetails['smtpemail'];
                        $Password       = $EmailDetails['password'];
                        $Headers[] = "From: Admin <".$AdminEmail.">";
                        $Email = new SendEmail();
                        //send smtp email to client
                        $Email->NotifyClient($HostName, $PortNo, $SMTPEmail, $Password, $AdminEmail, $ClientEmail, $ClientSubject, $ClientBody, $BlogName);
                        //send smtp email to admin
                        $Email->NotifyAdmin($HostName, $PortNo, $SMTPEmail, $Password, $AdminEmail, $AdminSubject, $AdminBody, $BlogName);
                    }
                } //end of notification enable check if
                echo "<div class='alert alert-success'><strong>".__('Thank you for scheduling appointment with us.<br>A confirmation mail will be forward to you soon after admin approval.', 'appointzilla')."</strong></div>";
            }
    }
?>
    <script type='text/javascript'>
    jQuery(document).ready(function() {
        //Modal Form Works - show first modal
        jQuery('#addappointment').click(function() {
            jQuery('#AppFirstModal').show();
            jQuery('#calendar').hide();
            jQuery('#addappointment').hide();
        });
        //hide modal
        jQuery('#close').click(function(){
            jQuery('#AppFirstModal').hide();
        });

        //load date picekr on modal for
        //document.addnewappointment.appdate.value = jQuery.datepicker.formatDate('dd-mm-yy', new Date());

        //AppFirstModal Validation
        jQuery('#next1').click(function(){
            jQuery(".apcal_error").hide();
            if(jQuery('#service').val() == 0) {
                jQuery("#service").after("<span class='apcal_error'><br><strong><?php _e("Select any service.", "appointzilla"); ?></strong></span>");
                return false;
            }
            var ServiceId =  jQuery('#service').val();
            var AppDate =  jQuery('#appdate').val();
            var SecondData = "ServiceId=" + ServiceId + "&AppDate=" + AppDate;
            var currenturl = jQuery(location).attr('href');
            var url = currenturl;
            jQuery('#loading1').show();     // loading button onclick next1 at first modal
            jQuery('#next1').hide();        //hide next button
            jQuery.ajax({
                dataType : 'html',
                type: 'GET',
                url : url,
                cache: false,
                data : SecondData,
                complete : function() {  },
                success: function(data) {
                    data = jQuery(data).find('div#AppSecondModal');
                    jQuery('#loading1').hide();
                    jQuery('#AppFirstModal').hide();
                    jQuery('#AppSecondModalDiv').show();
                    jQuery('#AppSecondModalDiv').html(data);
                }
            });
        });

        //back button show first modal
        jQuery('#back').click(function(){
            jQuery('#AppFirstModal').show();
            jQuery('#AppSecondModal').hide();
        });
    });

    //Modal Form Works
    function Backbutton() {
        jQuery('#AppFirstModal').show();
        jQuery('#AppSecondModalDiv').hide();
        jQuery('#next1').show();
        jQuery('#loading1').hide();
    }

    function checkvalidation() {
        jQuery(".apcal_error").hide();
        var start_time = jQuery('input[name=start_time]:radio:checked').val();
        if(!start_time) {
            jQuery("#selecttimediv").after("<br><p style='width:350px; padding:2px;' ><strong><?php _e("Select any time.", "appointzilla"); ?></strong></p>");
            return false;
        }

        if( !jQuery('#clientname').val() ) {
            jQuery("#clientname").after("<span class='apcal_error'><br><strong><?php _e("Name required.", "appointzilla"); ?></strong></span>");
            return false;
        }
        else if(!isNaN( jQuery('#clientname').val() )) {
            jQuery("#clientname").after("<span class='apcal_error'><br><strong><?php _e("Invalid name.", "appointzilla"); ?></strong></span>");
            return false;
        }

        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if( !jQuery('#clientemail').val() ) {
            jQuery("#clientemail").after("<span class='apcal_error'><br><strong><?php _e("Email required.", "appointzilla"); ?></strong></span>");
            return false;
        } else {
            if(regex.test(jQuery('#clientemail').val()) == false ) {
                jQuery("#clientemail").after("<span class='apcal_error'><br><strong><?php _e("Invalid Email.", "appointzilla"); ?></strong></span>");
                return false;
            }
        }

        if( !jQuery('#clientphone').val() ) {
            jQuery("#clientphone").after("<span class='apcal_error'><br><strong><?php _e("Phone required.", "appointzilla"); ?></strong></span>");
            return false;
        } else if(isNaN( jQuery('#clientphone').val() )) {
            jQuery("#clientphone").after("<span class='apcal_error'><br><strong><?php _e("Invalid phone number.", "appointzilla"); ?></strong></span>");
            return false;
        }

        var ServiceId = jQuery('#serviceid').val();
        var AppDate = jQuery('#appointmentdate').val();
        var ServiceDuration =  jQuery('#serviceduration').val();
        var StartTime = jQuery('input[name=start_time]:radio:checked').val();
        var Client_Name =  jQuery('#clientname').val();
        var Client_Email =  jQuery('#clientemail').val();
        var Client_Phone =  jQuery('#clientphone').val();
        var Client_Note =  jQuery('#clientnote').val();
        var SecondData = "ServiceId=" + ServiceId + "&AppDate=" + AppDate + "&StartTime=" + StartTime + '&Client_Name=' + Client_Name +'&Client_Email=' + Client_Email +'&Client_Phone=' + Client_Phone +'&Client_Note=' + Client_Note+'&Service_Duration=' + ServiceDuration;
        jQuery('#loading2').show();     // loading button onclick next1 at first modal
        jQuery('#buttonbox').hide();    // loading button onclick book now at first modal
        jQuery.ajax({
            dataType : 'html',
            type: 'POST',
            url : location.href,
            cache: false,
            data : SecondData,
            complete : function() {  },
            success: function() {
                jQuery('#AppSecondModalDiv').hide();
                jQuery('#AppThirdModalDiv').show();
            }
        });

    }
    </script>
    <!---Display Booking Instruction--->
    <?php if($AllCalendarSettings['apcal_booking_instructions']) { ?>
    <div id="bookinginstructions" align="center">
        <?php echo $AllCalendarSettings['apcal_booking_instructions']; ?>
    </div>
    <?php } ?>

    <!---Add New Appointment Button--->
    <div id="bkbtndiv" align="center" style="padding:5px;">
        <button name="addappointment" class="apcal_btn apcal_btn-primary apcal_btn-large" type="submit" id="addappointment">
            <strong></strong><i class="icon-calendar icon-white"></i> <?php
            if($AllCalendarSettings['booking_button_text'])
                echo $AllCalendarSettings['booking_button_text'];
            else echo _e("Schedule New Appointment", "appointzilla");
            ?>
            </strong>
        </button>
    </div>

    <!---Show appointment calendar--->
    <div id='calendar' style="width:100%;display:none;">
            <div align="right">Appointment Calendar Powered By: <a href="http://appointzilla.com/" title="Appointment Scheduling plugin for Wordpress" target="_blank">AppointZilla</a></div>
    </div>

    <div id="AppSecondModalDiv" style="display:none;"></div>
    <div id="AppThirdModalDiv" style="display:none;">
    <div class="apcal_alert apcal_alert-info">
        <strong><?php _e("Thank you for scheduling appointment with us. A confirmation mail will be sent to you soon after admin approval.", "appointzilla"); ?></strong><br/>
        <br/><strong><a href="" onclick="javascript: location.href;"><?php _e("Click Here", "appointzilla"); ?></a></strong> <?php _e("to book another appointment.", "appointzilla"); ?><br/>
        <br/><div align="right">Appointment Calendar Powered By: <a href="http://appointzilla.com/" title="Appointment Scheduling plugin for Wordpress" target="_blank">AppointZilla</a></div>
    </div>
    </div>

    <!---AppFirstModal For Schedule New Appointment--->
    <div id="AppFirstModal" style="display:none">
        <div id="myModal" style="z-index:99999;">
            <form action="" method="post" name="addnewappointment" id="addnewappointment">
                <div >
                    <div class="apcal_alert apcal_alert-info">
                        <p><strong><?php _e("Schedule New Appointment", "appointzilla"); ?></strong></p>
                        <div><?php _e("Select Date & Service", "appointzilla"); ?></div>
                    </div>
                </div>
                <div class="apcal_modal-body">
                    <div id="firdiv" style="float:left;">
                        <!--<div id="datepicker"></div> -->
                        <!--PHP DatePicker-->
                         <form id="form1" name="form1" method="post" action="">
                         <?php	include_once('calendar/tc_calendar.php');
                            $curr_date = date("Y-m-d", time());
                            $datepicker2=plugins_url('calendar/', __FILE__);
                            $myCalendar = new tc_calendar("date1");
                            $myCalendar->setIcon($datepicker2."images/iconCalendar.gif");
                            $myCalendar->setDate(date("d"), date("m"), date("Y"));
                            $myCalendar->setPath($datepicker2);
                            $myCalendar->setYearInterval(2035,date('Y'));
                            $myCalendar->dateAllow($curr_date, "2035-03-01", false);
                            $myCalendar->setOnChange("myChanged()");
                            $myCalendar->writeScript();	?>
                        </form>
                        <script language="javascript">
                        function myChanged() {
                            var x = document.getElementById('date1').value;
                            x = moment(x).format('DD-MM-YYYY');
                            document.getElementById('appdate').value = x;
                        }
                        </script>
                    </div>

                    <div id="secdiv" style="float:right;" >
                        <strong><?php _e("Your Appointment Date", "appointzilla"); ?>:</strong><br>
                        <input name="appdate" id="appdate" type="text" readonly="" height="30px;" value="<?php echo date('d-m-Y');?>" style="height:30px;" />
                        <?php global $wpdb;
                        $ServiceTable = $wpdb->prefix."ap_services";
                        $ServiceSql = "SELECT * FROM `$ServiceTable` WHERE `availability` = 'yes'";
                        $AllService = $wpdb->get_results($ServiceSql, OBJECT); ?><br /><br />
                            <strong><?php _e("Select Service", "appointzilla"); ?>:</strong><br>
                            <select name="service" id="service">
                                <option value="0"><?php _e("Select Service", "appointzilla"); ?></option>
                                <?php foreach($AllService as $Service) { ?>
                                    <?php if($AllCalendarSettings['show_service_cost'] == 'yes') $ShowCost = 1; else  $ShowCost = 0; ?>
                                    <?php if($AllCalendarSettings['show_service_duration'] == 'yes') $ShowDuration = 1; else  $ShowDuration = 0; ?>
                                    <option value="<?php echo $Service->id?>"><?php
                                        echo ucwords($Service->name);
                                        if($ShowDuration || $ShowCost) echo " (";
                                        if($ShowDuration) { echo $Service->duration."min"; } if($ShowDuration && $ShowCost) echo "/";
                                        if($ShowCost) { echo "$". $Service->cost; }
                                        if($ShowDuration || $ShowCost) echo ")"; ?>
                                    </option>
                                <?php }?>
                            </select>
                        <br />
                        <button name="next1" class="apcal_btn" type="button" id="next1" value="next1"><?php _e("Next", "appointzilla"); ?>  <i class="icon-arrow-right"></i></button>
                        <div id="loading1" style="display:none;"><?php _e("Loading...", "appointzilla"); ?><img src="<?php echo plugins_url()."/appointment-calendar/images/loading.gif"; ?>" /></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!---AppSecondModal For Schedule New Appointment--->

    <!--date-picker js -->
    <script src="<?php echo plugins_url('/menu-pages/datepicker-assets/js/jquery.ui.datepicker.js', __FILE__); ?>" type="text/javascript"></script>
    <?php if( isset($_GET['ServiceId']) && isset($_GET['AppDate'])) { ?>
    <div id="AppSecondModal">
        <form method="post" name="appointment-form2" id="appointment-form2" action="" onsubmit="checkvalidation()">
            <div class="apcal_modal-info">
                <div class="apcal_alert apcal_alert-info">
                    <p><strong><?php _e("Schedule New Appointment", "appointzilla"); ?></strong></p>
                    <div><?php _e("Select Time & Fill Out Form", "appointzilla"); ?></div>
                </div>
            </div>

            <div id="timesloatbox" class="apcal_alert apcal_alert-block" style="float:left; height:auto; width:94.5%;">
                <?php // time-slots calculation
                global $wpdb;
                $ServiceId =  $_GET['ServiceId'];
                $ServiceTableName = $wpdb->prefix."ap_services";
                $FindService_sql = "SELECT `name`, `duration` FROM `$ServiceTableName` WHERE `id` = '$ServiceId'";
                $ServiceData = $wpdb->get_row($FindService_sql, OBJECT);
                $ServiceDuration = $ServiceData->duration;

                $AppointmentDate = date("Y-m-d", strtotime($_GET['AppDate'])); //assign selected date by user
                $AllCalendarSettings = unserialize(get_option('apcal_calendar_settings'));
                $Biz_start_time = $AllCalendarSettings['day_start_time'];
                $Biz_end_time = $AllCalendarSettings['day_end_time'];
                if(isset($AllCalendarSettings['booking_time_slot'])) {
                    $UserDefineTimeSlot = $AllCalendarSettings['booking_time_slot'];
                } else {
                    $UserDefineTimeSlot = $ServiceDuration;
                }

                $AllSlotTimesList = array();
                $Enable = array();
                $AppPreviousTimes = array();
                $AppNextTimes = array();
                $AppBetweenTimes = array();
                $EventPreviousTimes = array();
                $EventBetweenTimes = array();
                $DisableSlotsTimes = array();
                $BusinessEndCheck =array();
                $AllSlotTimesList_User = array();
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
                }//end of event fetching foreach


                if($TodaysAllDayEvent) { ?>
                    <div class='apcal_alert apcal_alert-error'><?php _e("Sorry! No time available today.", "appointzilla"); ?></div>
                    <a class="apcal_btn" id="back" onclick="return Backbutton()"><i class="icon-arrow-left"></i> <?php _e("Back", "appointzilla"); ?></a><?php
                } else {
                    echo "<div class='apcal_alert apcal_alert-info' align='center'>". __("Available Time For", "appointzilla") ." <strong>'$ServiceData->name'</strong> ". __("On", "appointzilla"). " <strong>'".date("d-m-Y", strtotime($AppointmentDate))."'</strong></div>";

                    //Calculate all time slots according to today's biz hours
                    $start = strtotime($Biz_start_time);
                    $end = strtotime($Biz_end_time);

                    if($UserDefineTimeSlot) {
                        $UserTimeSlot = $UserDefineTimeSlot;
                    }else {
                        $UserTimeSlot = 30;
                    }
                    for( $i = $start; $i < $end; $i += (60*$UserTimeSlot)) {
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
                    if($AllAppointmentsData){
                        foreach($AllAppointmentsData as $Appointment){

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
                                $event_length = $ServiceDuration-5;     // Service duration time -  slot time
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
                                $event_length1 = $ServiceDuration-5;    // 60min Service duration time - 15 slot time
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

                    //Fetch All today's time-off and calculate disable slots
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
                                //making 5min diffrance slot
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
                                for( $i = $start_et; $i < $end_et; $i += (60*(5))) {
                                    $EventBetweenTimes[] = date('h:i A', $i);
                                }
                            }
                        }
                    }

                    //Fetch All 'MONTHLY' time-off and calculate disable slots
                    $EventTableName = $wpdb->prefix."ap_events";
                    $AllEventsSQL = "SELECT `start_time`, `end_time`, `start_date`, `end_date` FROM `$EventTableName` WHERE date('$AppointmentDate') between `start_date` AND `end_date` AND `allday` = '0' AND `repeat` = 'M'";
                    $AllEventsData = $wpdb->get_results($AllEventsSQL, OBJECT);
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

                                //
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
                        if(in_array($Single, $DisableSlotsTimes)) { $Disable[] = $Single; }
                        else { $Enable[] = $Single;	}
                    }// end foreach ?>

                    <div style="margin:30px; float: left;">
                        <?php // Show All Enable Time Slot
                        foreach($AllSlotTimesList_User as $Single) {
                            if(isset($Enable)) {
                                if(in_array($Single, $Enable)) { ?>
                                    <!--enable slots-->
                                    <div style="width:90px; float:left; padding:1px; display:inline-block;">
                                        <input name="start_time" id="start_time" type="radio"   value="<?php echo $Single; ?>"/>&nbsp;<?php echo $Single; ?>
                                    </div>
                                    <?php
                                } else { ?>
                                    <!--disable slots-->
                                    <div style="width:90px; float:left; padding:1px; display:inline-block;">
                                    <input name="start_time" id="start_time"  disabled="disabled" type="radio"  value="<?php echo $Single; ?>"/>&nbsp;<del><?php echo $Single; ?></del>
                                    </div><?php
                                }
                            }// end of enable isset
                        }// end foreach ?>
                    </div>
                    <?php  unset($DisableSlotsTimes); } ?><br />
                    <div id="selecttimediv" ><!--display select time error --></div>
                    <?php if(!$Enable && !$TodaysAllDayEvent ) {
                        echo "<br><p align=center class='apcal_alert apcal_alert-error' style='width:auto;'><strong>Sorry! Today's all appointments has been booked.</strong>  <a class='apcal_btn btn-small' id='back' onclick='Backbutton()'><i class='icon-arrow-left''></i> Back</a>";
                    } else if(!$TodaysAllDayEvent && $Enable) { ?>
                    <input type="hidden" name="serviceid" id="serviceid" value="<?php if(isset($_GET['ServiceId'])) echo $_GET['ServiceId']; else echo '1'; ?>" />
                    <input type="hidden" name="appointmentdate" id="appointmentdate"  value="<?php if(isset($_GET['AppDate'])) echo $_GET['AppDate']; else echo date("d-m-Y");  ?>"/>
                    <input type="hidden" name="serviceduration" id="serviceduration"  value="<?php echo $ServiceDuration; ?>"/><br />

                    <div id="user_info_button">
                        <div id="user1_info_button" style="float:left; width:100%"><br/>
                            <div style="float:left;">
                                &nbsp;&nbsp;&nbsp;<a class="apcal_btn" id="back" onclick="Backbutton()"><i class="icon-arrow-left"></i> <?php _e("Back", "appointzilla"); ?></a>
                            </div>
                            <div style="float:right;">
                                &nbsp;&nbsp;&nbsp;<button name="booknowapp1" class="apcal_btn" type="button" id="booknowapp1" onclick="gofornext()"><?php _e("Next", "appointzilla"); ?> <i class="icon-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>

                    <div id="user_info_button" align="center">
                        <div id="loading3" style="display:none"><p>
                            <?php _e("Loading...", "appointzilla"); ?><img src="<?php echo plugins_url()."/appointment-calendar/images/loading.gif"; ?>" /></p>
                        </div>
                    </div><?php
                } ?>
            </div>
        </form>

        <div id="user_info_page" style="display:none"></div>
            <?php if(isset($_GET['StartTime'])) { ?>
            <div id="user_info">
                <input type="hidden" name="serviceId" id="serviceid" value="<?php echo $_GET['ServiceId'];?>" />
                <input type="hidden" name="appointmentdate" id="appointmentdate" value="<?php echo $_GET['AppDate'];?>" />
                <input type="hidden" name="StartTime" id="StartTime" value="<?php echo $_GET['StartTime'];?>"/>
                <input type="hidden" name="ServiceDuration" id="ServiceDuration" value="<?php echo $_GET['ServiceDuration']; ?>" />
                <table width="100%" id="bordercssremove" class="table table-hover">
                    <tr >
                        <td width="30%" align="left" scope="row"><strong><?php _e("Name", "appointzilla"); ?></strong></td>
                        <td width="5%" align="center" valign="top"><strong>:</strong></td>
                        <td width="65%"><input type="text" name="clientname" id="clientname" height="30px;" style="height:30px;" /></td>
                    </tr>
                    <tr>
                        <td align="left" scope="row"><strong><?php _e("Email", "appointzilla"); ?></strong></td>
                        <td align="center" valign="top"><strong>:</strong></td>
                        <td><input type="text" name="clientemail" id="clientemail" height="30px;" style="height:30px;" ></td>
                    </tr>
                    <tr>
                        <td align="left" scope="row"><strong><?php _e("Phone", "appointzilla"); ?></strong></td>
                        <td align="center" valign="top"><strong>:</strong></td>
                        <td><input name="clientphone" type="text" id="clientphone" maxlength="12" height="30px;" style="height:30px;" />
                        <br/>
                        <label><?php _e("Eg: 1234567890", "appointzilla"); ?></label></td>
                    </tr>
                    <tr>
                        <td align="left" valign="middle" scope="row"><strong><?php _e("Special Instruction", "appointzilla"); ?></strong></td>
                        <td align="center" valign="top"><strong>:</strong></td>
                        <td valign="top"><textarea name="clientnote" id="clientnote"></textarea></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td id="buttonbox">
                            <a class="apcal_btn" id="back" onclick="Backbutton()"><i class="icon-arrow-left"></i> <?php _e("Back", "appointzilla"); ?></a>
                            <button name="booknowapp" class="apcal_btn apcal_btn-success" type="button" id="booknowapp" onclick="checkvalidation()"><i class="icon-ok"></i> <?php _e("Book Now", "appointzilla"); ?></button>
                        </td>
                    </tr>
                </table>
                <div id="loading2" style="display:none; color:#1FCB4A;"><?php _e('Scheduling your appointment please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url()."/appointment-calendar/images/loading.gif"; ?>" /></div>
                <style type="text/css">
                    #bordercssremove tr td  {
                        border-top: 0 solid #DDDDDD;
                    }
                </style>
            </div>
            <?php } ?>
        </div>
<?php
    }// end of isset next1 service-Id and AppDate
}//end of short code function ?>