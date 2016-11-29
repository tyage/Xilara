<style type='text/css'>
.error{ 
    color:#FF0000;
}
</style>
<?php 
global $wpdb;
if(isset($_GET['updateid'])) {
    $AppointmentId = $_GET['updateid'];
    $AppointmentTable = $wpdb->prefix . "ap_appointments";
    $Appointment = $wpdb->get_row("SELECT * FROM `$AppointmentTable` WHERE `id` ='$AppointmentId'");
?>
<div class="bs-docs-example tooltip-demo" style="background-color: #FFFFFF;">
<div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><?php _e("Update Appointment", "appointzilla"); ?></h3></div>
    <!---update appointment form--->
    <form action="" method="post" name="manageservice">
        <input name="app_key" type="hidden" id="app_key"  value="<?php echo $Appointment->appointment_key; ?>" />
        <table width="100%" class="table" >
            <tr>
                <th width="16%" scope="row"><?php _e("Name", "appointzilla"); ?></th>
                <td width="5%"><strong>:</strong></td>
                <td width="79%"><input name="appname" type="text" id="appname" value="<?php echo $Appointment->name; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Client Name", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e("Email", "appointzilla"); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="appemail" type="text" id="appemail" value="<?php echo $Appointment->email; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Client Email", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
            <th scope="row"><strong><?php _e("Service", "appointzilla"); ?></strong></th>
            <td><strong>:</strong></td>
            <td>
                <select id="serviceid" name="serviceid">
                    <?php //get all service list
                        global $wpdb;
                        $ServiceTable = $wpdb->prefix . "ap_services";
                        $service_list = $wpdb->get_results("SELECT * FROM `$ServiceTable`");
                        foreach($service_list as $service) { ?>
                        <option value="<?php echo $service->id; ?>" <?php if($Appointment->service_id == $service->id) echo "selected"; ?>><?php echo $service->name; ?></option>
                    <?php } ?>
                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Service Name", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
            </tr>
                <tr>
                <th scope="row"><strong><?php _e("Phone", "appointzilla"); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="appphone" type="text" id="appphone"  value="<?php echo $Appointment->phone; ?>" class="inputheight"/ maxlength="12">&nbsp;<a href="#" rel="tooltip" title="<?php _e("Client Phone Number", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e("Start Time", "appointzilla"); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="start_time" type="text" id="start_time"  value="<?php echo $Appointment->start_time; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Appointment Start Time", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e("End Time", "appointzilla"); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="end_time" type="text" id="end_time"  value="<?php echo $Appointment->end_time; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Appointment End Time", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e("Date", "appointzilla"); ?></strong></th>
                <td><strong>:</strong></td>
                <td><input name="start_date" type="text" id="start_date" value="<?php echo $Appointment->date; ?>" class="inputheight"/>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Appointment Date", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
                <tr>
                <th scope="row"><strong><?php _e("Description", "appointzilla"); ?></strong></th>
                <td><strong>:</strong></td>
                <td><textarea name="app_desc" id="app_desc"><?php echo $Appointment->note; ?></textarea>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Appointment Description", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a></td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e("Status", "appointzilla"); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="app_status" name="app_status">
                        <option value="pending" <?php if($Appointment->status == 'pending') echo "selected"; ?> ><?php _e("Pending", "appointzilla"); ?></option>
                        <option value="approved" <?php if($Appointment->status == 'approved') echo "selected"; ?> ><?php _e("Approved", "appointzilla"); ?></option>
                        <option value="cancelled" <?php if($Appointment->status == 'cancelled') echo "selected"; ?> ><?php _e("Cancelled", "appointzilla"); ?></option>
                        <option value="done" <?php if($Appointment->status == 'done') echo "selected"; ?> ><?php _e("Completed", "appointzilla"); ?></option>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Appointment Status", "appointzilla"); ?>Appointment Status" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>
            <tr>
                <th scope="row"><strong><?php _e("Appointment By", "appointzilla"); ?></strong></th>
                <td><strong>:</strong></td>
                <td>
                    <select id="app_appointment_by" name="app_appointment_by">
                        <option value="admin" <?php if($Appointment->appointment_by == 'admin') echo "selected"; ?> ><?php _e("Admin", "appointzilla"); ?></option>
                        <option value="user" <?php if($Appointment->appointment_by == 'user') echo "selected"; ?> ><?php _e("User", "appointzilla"); ?></option>
                    </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Appointment Booked By", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                </td>
            </tr>

            <tr>
                <th scope="row">&nbsp;</th>
                <td>&nbsp;</td>
                <td>
                    <?php if(isset($_GET['updateid'])) { ?>
                        <button id="updateppointments" type="submit" class="btn btn-success" name="updateppointments" value="<?php echo $Appointment->id; ?>"><i class="icon-pencil icon-white"></i> <?php _e("Update", "appointzilla"); ?></button>
                    <?php } ?>
                    <?php if(isset($_GET['from'])) { ?>
                        <a href="?page=appointment-calendar" class="btn btn-danger"><i class="icon-remove icon-white"></i> <?php _e("Cancel", "appointzilla"); ?></a></td>
                    <?php } else { ?>
                        <a href="?page=manage-appointments" class="btn btn-danger"><i class="icon-remove icon-white"></i> <?php _e("Cancel", "appointzilla"); ?></a></td>
                    <?php } ?>
                </tr>
        </table>
    </form>
<?php } ?>

<!--validation js lib-->
<script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function () {

    jQuery(function(){
       //load date and time picker
        jQuery('#start_time').timepicker({
            ampm: true,
            timeFormat: 'hh:mm TT'
        });

        jQuery('#end_time').timepicker({
            ampm: true,
            timeFormat: 'hh:mm TT'
        });

        jQuery('#start_date').datepicker({
            //minDate: 0,
            dateFormat: 'dd-mm-yy'
        });
    });

    // update appointment validation
    jQuery('#updateppointments').click(function() {

        jQuery(".error").hide();
        //start-date app-name app-email service-id app-phone start_time end_time start_date
        var appname = jQuery("#appname").val();
        if(appname == '') {
            jQuery("#appname").after('<span class="error"><br><strong><?php _e("Name cannot be blank.", "appointzilla"); ?></strong></span>');
            return false;
        }
        var appemail = jQuery("input#appemail").val();
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (appemail == "") {
            jQuery("#appemail").after('<span class="error"><br><strong><?php _e("Email field cannot be blank.", "appointzilla"); ?></strong></span>');
            return false;
        } else {
            if(regex.test(appemail) == false ) {
                jQuery("#appemail").after('<span class="error"><br><strong><?php _e("Invalid email address.", "appointzilla"); ?></strong></span>');
                return false;
            }
        }

        //phone
        var appphone = jQuery("#appphone").val();
        if(appphone == '') {
            jQuery("#appphone").after('<span class="error"><br><strong><?php _e("Phone field cannot be blank.", "appointzilla"); ?></strong></span>');
            return false;
        } else {
            var appphone = isNaN(appphone);
            if(appphone == true) {
                jQuery("#appphone").after('<span class="error"><br><strong><?php _e("Invalid Phone number.", "appointzilla"); ?></strong></span>');
                return false;
            }
        }

        //start time
        var start_time = jQuery("#start_time").val();
        if(start_time == ''){
            jQuery("#start_time").after('<span class="error"><br><strong><?php _e('Start Time  cannot be blank.', 'appointzilla'); ?></strong></span>');
            return false;
        }

        //end-time
        var end_time = jQuery("#end_time").val();
        if(end_time == ''){
            jQuery("#end_time").after('<span class="error"><br><strong><?php _e('End Time cannot be blank.', 'appointzilla'); ?></strong></span>');
            return false;
        }

        // st & ed compression
        if(start_time == end_time){
            jQuery("#start_time").after('<span class="error"><br><strong><?php _e("Start-time and End-time cant be equal.",'appointzilla'); ?></strong></span>');
            jQuery("#end_time").after('<span class="error"><br><strong><?php _e("Start-time and End-time cant be equal.",'appointzilla'); ?></strong></span>');
            return false;
        }

        //convert both time into timestamp
        var stt = new Date("October 13, 2013 " + start_time);
        stt = stt.getTime();

        var endt = new Date("October 13, 2013 " + end_time);
        endt = endt.getTime();
        console.log("Time1: "+ stt + " Time2: " + endt);

        if(stt > endt) {
            jQuery("#start_time").after('<span class="error"><br><strong><?php _e("Start-time must be smaller then End-time.",'appointzilla'); ?></strong></span>');
            jQuery("#end_time").after('<span class="error"><br><strong></strong></span>');
            return false;
        }

        var start_date = jQuery("#start_date").val();
        if(start_date == ''){
            jQuery("#start_date").after('<span class="error"><br><strong><?php _e("Start Date cannot be blank.",'appointzilla'); ?></strong></span>');
            return false;
        }
    });
});
</script>

<!--time-picker js -->
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-1.7.2.min.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui.min.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui-timepicker-addon.js', __FILE__); ?>" type="text/javascript"></script>
<?php 

if(isset($_POST['updateppointments'])) {
    global $wpdb;
    $UpdateAppId = $_POST['updateppointments'];
    $ClientName = $_POST['appname'];
    $ClientEmail = $_POST['appemail'];
    $ClientPhone = $_POST['appphone'];
    $ClientNote = $_POST['app_desc'];
    $ServiceId = $_POST['serviceid'];
    $StartTime = $_POST['start_time'];
    $EndTime = $_POST['end_time'];
    $AppointmentKey = $_POST['app_key'];
    $AppointmentDate = date("Y-m-d", strtotime($_POST['start_date']));
    $Status =  $_POST['app_status'];
    $AppointmentBy = $_POST['app_appointment_by'];
    $AppointmentsTable = $wpdb->prefix . "ap_appointments";
    $UpdateAppointment ="UPDATE `$AppointmentsTable` SET `name` = '$ClientName',
        `email` = '$ClientEmail',
        `service_id` = '$ServiceId',
        `phone` = '$ClientPhone',
        `start_time` = '$StartTime',
        `end_time` = '$EndTime',
        `date` = '$AppointmentDate',
        `note` = '$ClientNote',
        `status` = '$Status',
        `appointment_by` = '$AppointmentBy' WHERE `id` ='$UpdateAppId';";

    if($wpdb->query($UpdateAppointment)) {
        //send notification to client if appointment approved or cancelled
        if($Status == 'approved' || $Status == 'cancelled' ) {
            //$GetAppKey = $wpdb->get_row("SELECT * FROM `$AppointmentsTable` WHERE `id` = '$UpdateAppId' ", OBJECT);
            //$MangeAppointmentUrl = site_url().'/wp-admin/admin.php?page=manage-appointments';
            //$BlogUrl = site_url().'/wp-admin';
            $BlogName = get_bloginfo();

            $ServiceTable = $wpdb->prefix."ap_services";
            $ServiceData = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` = '$ServiceId'", OBJECT);
            $ServiceName = $ServiceData->name;

            //check notification is enabled & notification type
            $NotificationStatus = get_option('emailstatus');
            if($NotificationStatus == "on") {
                $Attachments = "";
                $AppointmentTime = $StartTime." - ".$EndTime;

                if($Status == "approved") {
                    $ClientSubject = get_option('approve_appointment_client_subject');
                    $ClientBody = get_option('approve_appointment_client_body');
                }

                if($Status == "cancelled") {
                    $ClientSubject = get_option('cancel_appointment_client_subject');
                    $ClientBody = get_option('cancel_appointment_client_body');
                }

                //client subject replacement
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

                //client body replacement
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
                }

                //php-email
                if($EmailType == "phpmail") {
                    $AdminEmail = $EmailDetails['phpemail'];
                    $Headers[] = "From: Admin <".$AdminEmail.">";
                    ///send php email to client
                    mail($ClientEmail, $ClientSubject, $ClientBody, $Headers);
                }

                //wp-email
                if($EmailType == "smtp") {
                    require_once('notification/Email.php');
                    $AdminEmail     = $EmailDetails['smtpemail'];
                    $HostName       = $EmailDetails['hostname'];
                    $PortNo         = $EmailDetails['portno'];
                    $SMTPEmail      = $EmailDetails['smtpemail'];
                    $Password       = $EmailDetails['password'];
                    $Headers[] = "From: Admin <".$AdminEmail.">";
                    $Email = new SendEmail();
                    //send smtp email to client
                    $Email->NotifyClient($HostName, $PortNo, $SMTPEmail, $Password, $AdminEmail, $ClientEmail, $ClientSubject, $ClientBody, $BlogName);
                }
            }


        }// end of update check

        //redirect to updated appointment details page
        echo "<script>alert('". __('Appointment successfully updated.', 'appointzilla') ."')</script>";
        echo "<script>location.href='?page=update-appointment&viewid=$UpdateAppId';</script>";
    } else {
        //redirect to updated appointment details page
        echo "<script>location.href='?page=update-appointment&viewid=$UpdateAppId';</script>";
    }
} // end of isset ?>

<!---appointment view page--->
<?php if(isset($_GET['viewid'])) {
    $AppId = $_GET['viewid'];
    $AppointmentTable = $wpdb->prefix . "ap_appointments";
    $Appointment = $wpdb->get_row("SELECT * FROM `$AppointmentTable` WHERE `id` = '$AppId'");
?>
<div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><?php _e("View Appointment", "appointzilla"); ?>: <?php echo ucwords($Appointment->name); ?></h3></div>
    <table width="100%" class="table" >
        <tr>
            <th width="16%" scope="row"><?php _e("Name", "appointzilla"); ?></th>
            <td width="5%"><strong>:</strong></td>
            <td width="79%"><em><?php echo ucwords($Appointment->name); ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Email", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo $Appointment->email; ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Service", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td>
                <em>
                <?php
                $ServiceTable = $wpdb->prefix . "ap_services";
                $Service = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` ='$Appointment->service_id'");
                echo ucwords($Service->name);
                ?>
                </em>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Phone", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo $Appointment->phone; ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Start Time", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo date("h:ia", strtotime($Appointment->start_time)); ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("End Time", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo date("h:ia", strtotime($Appointment->end_time)); ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Date", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo $Appointment->date; ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Description", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo ucfirst($Appointment->note); ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Appointment Key", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo $Appointment->appointment_key; ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Status", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo ucfirst($Appointment->status); ?></em></td>
        </tr>
        <tr>
            <th scope="row"><?php _e("Appointment By", "appointzilla"); ?></th>
            <td><strong>:</strong></td>
            <td><em><?php echo ucfirst($Appointment->appointment_by); ?></em></td>
        </tr>
        <tr>
            <th scope="row">&nbsp;</th>
            <td>&nbsp;</td>
            <td><a href="?page=manage-appointments" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i> <?php _e("Back", "appointzilla"); ?></a></td>
        </tr>
    </table>
<?php }  ?>
<!--time-picker js -->
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-1.7.2.min.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui.min.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui-timepicker-addon.js', __FILE__); ?>" type="text/javascript"></script>