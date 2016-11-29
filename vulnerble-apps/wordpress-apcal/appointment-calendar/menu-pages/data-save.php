<div id="saving-appointment-div">
    <?php
        global $wpdb;
        $AppointmentDate = date("Y-m-d", strtotime($_GET['bookdate']));
        $ServiceId = $_GET['serviceid'];
        $ServiceDuration = $_GET['serviceduration'];
        $ClientName = $_GET['name'];
        $ClientEmail = $_GET['email'];
        $ClientPhone = $_GET['phone'];
        $ClientNote = $_GET['desc'];
        $StartTime = $_GET['start_time'];
        //calculate end time according to service duration
        $EndTime =  date('h:i A', strtotime("+$ServiceDuration minutes", strtotime($StartTime)));
        $AppointmentKey = md5(date("F j, Y, g:i a"));
        $Status = "pending";
        $AppointmentBy = "admin";

        $AppointmentTable = $wpdb->prefix . "ap_appointments";
        $NewAppointmentSQL = "INSERT INTO `$AppointmentTable` (  `id` , `name` , `email` , `service_id` , `phone` , `start_time` , `end_time` , `date` , `note` , `appointment_key` , `status` , `appointment_by` ) VALUES (NULL , '$ClientName', '$ClientEmail', '$ServiceId', '$ClientPhone', '$StartTime', '$EndTime', '$AppointmentDate', '$ClientNote', '$AppointmentKey', '$Status', '$AppointmentBy');";
        if($wpdb->query($NewAppointmentSQL)) {

            //$MangeAppointmentUrl = site_url().'/wp-admin/admin.php?page=manage-appointments';
            //$BlogUrl = site_url().'/wp-admin';
            $BlogName = get_bloginfo();

            $ServiceTable = $wpdb->prefix."ap_services";
            $Service = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` = '$ServiceId'", OBJECT);
            $ServiceName = $Service->name;

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
                    //send smtp email to admin
                    $Email->NotifyAdmin($HostName, $PortNo, $SMTPEmail, $Password, $AdminEmail, $AdminSubject, $AdminBody, $BlogName);
                }
            } //end of notification enable check if
        }
    ?>
</div>