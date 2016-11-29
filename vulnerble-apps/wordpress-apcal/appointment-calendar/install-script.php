<?php
/**
 * Default Calendar Settings
 */
$CalendarSettingsArray = array(
    'calendar_slot_time' => '30',       // 30 min slots
    'day_start_time' => '10:00 AM',     // 10:00 AM
    'day_end_time' => '5:00 PM',        // 5:00 PM
    'calendar_view' => 'month',         // month
    'calendar_start_day' => '1',        // monday
    'booking_button_text' => 'Schedule An Appointment',     // Schedule An Appointment
    'booking_time_slot' => 30,          // booking time slot
    'show_service_cost' => 'yes',       // for service cost hide or show
    'show_service_duration' => 'yes',    // for service duration hide or show
    'apcal_booking_instructions' => 'Put your booking instructions here.<br>Or you can save It blank in case of nothing want to display.' // booking instruction befor booking button
);
add_option('apcal_calendar_settings',serialize($CalendarSettingsArray));

/**
 * Default Notification Settings
 */
add_option('emailstatus', 'on');                                        //on
add_option('emailtype', 'wpmail');                                      //wp-mail
$EmailDetails =  array ( 'wpemail' => get_settings('admin_email') );
add_option( 'emaildetails', serialize($EmailDetails));                  // current admin email

/**
 * Admin Notification Messages
 */
//new appointment admin message
$Subject = "[blog-name]: New appointment scheduled by [client-name]";
$Body = "
Hi Admin,

Appointment details are:

Client Name: [client-name]
Client Email: [client-email]
Client Phone: [client-phone]
Client Special Instruction: [client-si]

Appointment For: [service-name]
Appointment Date: [app-date]
Appointment Time: [app-time]
Appointment Status: [app-status]

View this appointment at admin dashboard.

Best Regards
[blog-name]
";
add_option("new_appointment_admin_subject", $Subject);
add_option("new_appointment_admin_body", $Body);

/**
 * Client Notification Messages
 */
//new appointment client message
$Subject = "[blog-name]: Your appointment status is [app-status]";
$Body = "
Hi [client-name],

Thank you for scheduling appointment with [blog-name].

Your appointment for [service-name] on [app-date] at [app-time].

Currently, your appointment status is [app-status].

You will get a confirmation mail once admin accepts the appointment.

Best Regards
[blog-name]
";
add_option("new_appointment_client_subject", $Subject);
add_option("new_appointment_client_body", $Body);

//approve appointment client message
$Subject = "[blog-name]: Your appointment status is [app-status]";
$Body = "
Hi [client-name],

Your appointment has been [app-status] by admin.

Now, your appointment for [service-name] on [app-date] at [app-time].

Thank you for scheduling appointment with [blog-name].

Best Regards
[blog-name]
";
add_option("approve_appointment_client_subject", $Subject);
add_option("approve_appointment_client_body", $Body);

//cancel appointment client message
$Subject = "[blog-name]: Your appointment status is [app-status]";
$Body = "
Hi [client-name],

Sorry! Due to some reason we are unable to complete your appointment request.

Now, your appointment for [service-name] on [app-date] at [app-time] has been [app-status] by admin.

Thank you for scheduling appointment with [blog-name].

Best Regards
[blog-name]
";
add_option("cancel_appointment_client_subject", $Subject);
add_option("cancel_appointment_client_body", $Body);


global $wpdb;
//create a ap_appointments table
$AppointmentsTable = $wpdb->prefix . "ap_appointments";
$AppointmentsTable_SQL = "CREATE TABLE `$AppointmentsTable` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name` VARCHAR( 30 ) NOT NULL ,
`email` VARCHAR( 256 ) NOT NULL ,
`service_id` INT( 11 ) NOT NULL ,
`phone` BIGINT( 21 ) NOT NULL ,
`start_time` VARCHAR( 10 ) NOT NULL ,
`end_time` VARCHAR( 10 ) NOT NULL ,
`date` DATE NOT NULL ,
`note` TEXT NOT NULL ,
`appointment_key` VARCHAR( 32 ) NOT NULL ,
`status` VARCHAR( 10 ) NOT NULL ,
`appointment_by` VARCHAR( 10 ) NOT NULL
)DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$wpdb->query($AppointmentsTable_SQL);


// create ap_events table
$TimeOffTable = $wpdb->prefix . "ap_events";
$TimeOffTable_SQL = "CREATE TABLE `$TimeOffTable` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name` VARCHAR( 30 ) NOT NULL ,
`allday` VARCHAR( 10 ) NOT NULL ,
`start_time` VARCHAR( 10 ) NOT NULL ,
`end_time` VARCHAR( 10 ) NOT NULL ,
`repeat` VARCHAR( 10 ) NOT NULL ,
`start_date` DATE NOT NULL ,
`end_date` DATE NOT NULL ,
`note` TEXT NOT NULL ,
`status` VARCHAR( 10 ) NOT NULL
)DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$wpdb->query($TimeOffTable_SQL);


// create ap_services table
$ServicesTable = $wpdb->prefix . "ap_services";
$ServicesTable_SQL = "CREATE TABLE `$ServicesTable` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name` VARCHAR( 50 ) NOT NULL ,
`desc` TEXT NOT NULL ,
`duration` INT( 11 ) NOT NULL ,
`unit` VARCHAR( 10 ) NOT NULL ,
`paddingtime` INT( 11 ) NOT NULL ,
`cost` FLOAT NOT NULL ,
`capacity` INT( 11 ) NOT NULL ,
`availability` VARCHAR( 10 ) NOT NULL ,
`business_id` INT( 11 ) NOT NULL ,
`category_id` INT( 11 ) NOT NULL ,
`staff_id` VARCHAR( 300 ) NOT NULL
)DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$wpdb->query($ServicesTable_SQL);


    // inserting service 'Default'
    $InsertTestService_SQL = "INSERT INTO `$ServicesTable` ( `id` , `name` , `desc` , `duration` , `unit` , `paddingtime`, `cost` , `capacity`, `availability`, `business_id`, `category_id`, `staff_id` ) VALUES ('1', 'Default', 'This is default service. You can edit this service.', '30', 'minute', '10', '100', '10', 'yes', '1', '1', '1');";
    $wpdb->query($InsertTestService_SQL);


// create a service Category
$ServiceCategoryTable = $wpdb->prefix . "ap_service_category";
$ServiceCategoryTable_SQL = "CREATE TABLE `$ServiceCategoryTable` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `name` VARCHAR( 100 ) NOT NULL )DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
$wpdb->query($ServiceCategoryTable_SQL);


    // inserting a 'Default' service category
    $InsertTestServiceCategory_SQL = "INSERT INTO `$ServiceCategoryTable` ( `id` , `name` ) VALUES ( '1', 'Default' );";
    $wpdb->query($InsertTestServiceCategory_SQL);