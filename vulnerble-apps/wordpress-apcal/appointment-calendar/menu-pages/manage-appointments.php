<?php global $wpdb; ?>
<div class="bs-docs-example tooltip-demo" style="background-color: #FFFFFF;">
<div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
  <h3><?php _e("Manage Appointments", "appointzilla"); ?></h3>
</div>
<form action="" method="post" name="manage-appointments">
    <table width="100%" border="0" class="table">
        <tr>
            <td>
                <div style="float:left;">
                    <select name="filtername">
                        <option value="All" <?php if(isset($_POST['filtername']) == 'All') echo "selected"; ?>><?php _e("All Appointments", "appointzilla"); ?></option>
                        <option value="pending" <?php if(isset($_POST['filtername']) == 'pending') echo "selected"; ?>><?php _e("Pending Appointments", "appointzilla"); ?></option>
                        <option value="approved" <?php if(isset($_POST['filtername']) == 'approved') echo "selected"; ?>><?php _e("Apporved Appointments", "appointzilla"); ?></option>
                        <option value="cancelled" <?php if(isset($_POST['filtername']) == 'cancelled') echo "selected"; ?>><?php _e("Cancelled Appointments", "appointzilla"); ?></option>
                        <option value="done" <?php if(isset($_POST['filtername']) == 'done') echo "selected"; ?>><?php _e("Completed Appointments", "appointzilla"); ?></option>
                        <option value="today" <?php if(isset($_POST['filtername']) == 'today') echo "selected"; ?>><?php _e("Today's Appointments", "appointzilla"); ?></option>
                    </select>
                </div>&nbsp;<button id="filter" class="btn btn-small btn-info" type="submit" name="filter"><i class="icon-th-list icon-white"></i> <?php _e("Filter Appointments", "appointzilla"); ?></button>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Filter Appointments", "appointzilla"); ?>"><i class="icon-question-sign"></i></a>
            </td>
        </tr>
    </table>
</form>
<?php
require_once('ps_pagination.php');
$conn = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
if(!$conn) die("Failed to connect to database!");
$status = mysql_select_db(DB_NAME, $conn);
if(!$status) die("Failed to select database!");
$AppointmentsTable = $wpdb->prefix . "ap_appointments";
if(isset($_POST['filter'])) {
    $FilterData = $_POST['filtername'];
    if($FilterData == 'today') {
        $FilterAppointments = date('Y-m-d');
        $sql = "SELECT * FROM `$AppointmentsTable` WHERE `date` = '$FilterAppointments'";
        $pager = new PS_Pagination($conn, $sql, 10);
        $pager->setDebug(true);
        $AllAppointments = $pager->paginate();
    } else {
        $FilterAppointments =$FilterData;
        $sql = "SELECT * FROM `$AppointmentsTable` WHERE `status` = '$FilterAppointments'";
        $pager = new PS_Pagination($conn, $sql, 10);
        $pager->setDebug(true);
        $AllAppointments = $pager->paginate();
    }

    if($FilterData == 'All') {
        $sql = "SELECT * FROM `$AppointmentsTable`";
        $pager = new PS_Pagination($conn, $sql, 10);
        $pager->setDebug(true);
        $AllAppointments = $pager->paginate();
    }
} else {
    // WordPress  database user name and password
    $sql = "SELECT * FROM `$AppointmentsTable`";
    $pager = new PS_Pagination($conn, $sql, 10);
    $pager->setDebug(true);
    $AllAppointments = $pager->paginate();
}
?>
<form method="post" name="manage-appointments">
    <table width="" border="0" class="table table-hover">
        <tr>
            <th scope="col"><?php _e("No.", "appointzilla"); ?></th>
            <th scope="col"><?php _e("Name", "appointzilla"); ?></th>
            <th scope="col"><?php _e("Date", "appointzilla"); ?></th>
            <th scope="col"><?php _e("Time", "appointzilla"); ?></th>
            <th scope="col"><?php _e("Service", "appointzilla"); ?></th>
            <th scope="col"><?php _e("Status", "appointzilla"); ?></th>
            <th scope="col"><?php _e("Action", "appointzilla"); ?></th>
            <th scope="col" style="text-align: center;"><a data-placement="left" rel="tooltip" title="<?php _e("Select All", "appointzilla"); ?>" ><input type="checkbox" id="checkbox" name="checkbox[]" value="0" /></a></th>
        </tr>
        <?php
        //get all category list
        $i = 1;
        if($AllAppointments) {
            while($Appointment = mysql_fetch_assoc($AllAppointments)) { ?>
        <tr>
            <td><em><?php echo $i; ?></em></td>
            <td><em><?php echo ucwords($Appointment['name']); ?> <br> (<?php echo $Appointment['email']; ?>)</em></td>
            <td>
                <em><?php echo date("d-m-Y", strtotime($Appointment['date'])); ?></em>
            </td>
            <td><em><?php echo date("h:ia", strtotime($Appointment['start_time']))." - ".date("h:ia", strtotime($Appointment['end_time'])); ?></em></td>
            <td>
                <em>
                    <?php
                    $AppId = $Appointment['service_id'];
                    $ServiceTable = $wpdb->prefix . "ap_services";
                    $Service = $wpdb->get_row("SELECT * FROM `$ServiceTable` WHERE `id` = '$AppId'");
                    if(count($Service)) {
                        echo ucfirst($Service->name);
                    }
                    ?>
                </em>
            </td>
            <td><em><?php echo ucfirst($Appointment['status']); ?></em></td>
            <td>
                <a href="?page=update-appointment&viewid=<?php if(isset($Appointment['id'])) { echo $Appointment['id']; } ?>" title="<?php _e("View", "appointzilla"); ?>" rel="tooltip"><i class="icon-eye-open"></i></a>&nbsp;
                <a href="?page=update-appointment&updateid=<?php if(isset($Appointment['id'])) { echo $Appointment['id']; } ?>" title="<?php _e("Update", "appointzilla"); ?>" rel="tooltip"><i class="icon-pencil"></i></a>&nbsp;
                <a href="?page=manage-appointments&delete=<?php if(isset($Appointment['id'])) { echo $Appointment['id']; } ?>" rel="tooltip" title="<?php _e("Delete", "appointzilla"); ?>" onclick="return confirm('<?php _e("Do you want to delete this appointment?", "appointzilla"); ?>')"><i class="icon-remove" ></i>
            </td>
            <td style="text-align: center;"><a rel="tooltip" data-placement="left" title="<?php _e("Select", "appointzilla"); ?>"><input type="checkbox" id="checkbox" name="checkbox[]" value="<?php if(isset($Appointment['id'])) { echo $Appointment['id']; } ?>" /></a></td>
        </tr>
        <?php $i++; }   ?>
        <tr>
            <td colspan="7"><span  id="pagination-digg" ><?php echo $pager->renderFullNav(); ?> </span ></td>
            <td style="text-align: center;"><button name="deleteall" class="btn btn-danger" type="submit" id="deleteall" onclick="return confirm('<?php _e("Do you want to delete selected appointments?", "appointzilla"); ?>')"><i class="icon-trash icon-white"></i> <?php _e("Delete", "appointzilla"); ?></button></td>
        </tr>
        <?php } else { ?>
        <tr >
            <td colspan="8" class="alert"><strong><?php _e("Sorry No Appointments", "appointzilla"); ?></strong></td>
        </tr>
        <?php } ?>
    </table>
</form>
<style type="text/css">
.error{  color:#FF0000; }

#pagination-digg {
    background:#FFFFFF;
    color:#2e6ab1;
    font-weight:bold;
    padding:6px;
    width:auto;
    border: 0px solid #6699FF;
}

#pagination-digg .page_link  {
    border:solid 1px #2e6ab1;
    color:#888888;
    font-weight:bold;
    margin-right:2px;
    padding:3px 4px;
}
</style>

<!--validation js lib-->
<script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>

<script type="text/javascript">
jQuery(document).ready(function (){
    jQuery('#checkbox').click(function(){
        if(jQuery('#checkbox').is(':checked')) {
            jQuery(":checkbox").prop("checked", true);
        } else {
            jQuery(":checkbox").prop("checked", false);
        }
    });
});
</script>

<?php
//delete appointment
if(isset($_GET['delete'])) {
    $DeleteId = $_GET['delete'];
    if($DeleteId){
        $AppointmentTable = $wpdb->prefix . "ap_appointments";
        $DeleteAppointmentSQL = "DELETE FROM `$AppointmentTable` WHERE `id` = '$DeleteId';";
        $wpdb->query($DeleteAppointmentSQL);
        echo "<script>alert('". __('Appointment deleted.', 'appointzilla') ."')</script>";
        echo "<script>location.href='?page=manage-appointments';</script>";
    }
}

// delete all selected appointment with checkbox
if(isset($_POST['deleteall'])) {
    if(isset($_POST['checkbox'])) {
        $AppointmentTable = $wpdb->prefix . "ap_appointments";
        for($i=0; $i<=count($_POST['checkbox'])-1; $i++) {
            $DeleteId = $_POST['checkbox'][$i];
            $DeleteAppointmentSQL = "DELETE FROM `$AppointmentTable` WHERE `id` = '$DeleteId';";
            $wpdb->query($DeleteAppointmentSQL);
        }
        echo "<script>alert('". __('Selected appointments successfully deleted.', 'appointzilla') ."')</script>";
        echo "<script>location.href='?page=manage-appointments';</script>";
    } else {
        echo "<script>alert('". __('Please select any appointment.', 'appointzilla') ."')</script>";
    }
}
?>
</div>