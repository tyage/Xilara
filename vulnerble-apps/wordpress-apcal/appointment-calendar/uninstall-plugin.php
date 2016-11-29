<?php 

if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

# Uninstall Appointment Calendar
if(isset($_POST['uninstallapcal'])) {
    global $wpdb;

	if( !wp_verify_nonce($_POST['appointment_remove_nonce_check'],'appointment_remove_nonce_check') ){
			echo '<script>alert("Sorry, your nonce did not verify.");</script>';
			return false;
		}
		
    //drop ap_appointments table
    $table_appointments = $wpdb->prefix . "ap_appointments";
    $wpdb->query("DROP TABLE `$table_appointments`");

    //drop ap_events table
    $table_events = $wpdb->prefix . "ap_events";
    $events = "DROP TABLE `$table_events`";
    $wpdb->query($events);

    //drop ap_services table
    $table_services = $wpdb->prefix . "ap_services";
    $services = "DROP TABLE `$table_services`";
    $wpdb->query($services);

    //drop a service Category
    $table_service_category = $wpdb->prefix . "ap_service_category";
    $service_category = "DROP TABLE `$table_service_category`";
    $wpdb->query($service_category);

    //delete all default calendar options & settings
    delete_option('apcal_calendar_settings');

    //delete notification details
    delete_option('emailstatus');
    delete_option('emailtype');
    delete_option('emaildetails');

    //delete notification messages
    delete_option('new_appointment_admin_subject');
    delete_option('new_appointment_admin_body');

    delete_option('new_appointment_client_subject');
    delete_option('new_appointment_client_body');

    delete_option('approve_appointment_client_subject');
    delete_option('approve_appointment_client_body');

    delete_option('cancel_appointment_client_subject');
    delete_option('cancel_appointment_client_body');


    // DEACTIVATE APPOINTMENT CALENDAR PLUGIN
    $plugin = "appointment-calendar/appointment-calendar.php";
    deactivate_plugins($plugin);
    ?>
    <div class="alert" style="width:95%; margin-top:10px;">
        <p><?php _e('Appointment Calendar Plugin has been successfully removed. It can be re-activated from the ', 'appointzilla'); ?> <strong><a href="plugins.php"><?php _e('Plugins Page', 'appointzilla'); ?></a></strong>.</p>
    </div>
    <?php
    return;
}

if(!isset($_POST['uninstallapcal'])) { ?>
<div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
  <h3><?php _e('Remove Plugin Data', 'appointzilla'); ?></h3> 
</div>
<div class="alert alert-error" style="width:95%;">
    <form method="post">
	<?php wp_nonce_field('appointment_remove_nonce_check','appointment_remove_nonce_check'); ?>
    <h3><?php _e('Remove Appointment Calendar Plugin Data', 'appointzilla'); ?></h3>
    <p><?php _e('This operation wiil delete all Appointment Calendar data & settings. If you continue, You will not be able to retrieve or restore your appointments entries.', 'appointzilla'); ?></p>
	
	<p><?php _e('The plugin will also be deactivated after this operation.', 'appointzilla'); ?></p>
	
    <p><button id="uninstallapcal" type="submit" class="btn btn-danger" name="uninstallapcal" value="" onclick="return confirm('<?php _e('Warning! Appointment Calendar data & settings, including appointment entries will be deleted. This cannot be undone. OK to delete, CANCEL to stop', 'appointzilla'); ?>')" ><i class="icon-trash icon-white"></i> <?php _e('REMOVE PLUGIN DATA', 'appointzilla'); ?></button></p>
    </form>
</div>
<?php } ?>