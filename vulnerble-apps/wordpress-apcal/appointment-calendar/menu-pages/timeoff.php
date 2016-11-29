<div class="bs-docs-example tooltip-demo" style="background-color: #FFFFFF;">
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
      <h3><?php _e('Time Off','appointzilla','appointzilla'); ?></h3>
    </div>
    <form name="timeoff" id="timeoff" method="post" action="" >
        <table width="100%" border="0" class="table">
            <tr>
            <th scope="col"><?php _e('No.','appointzilla'); ?></th>
            <th scope="col"><?php _e('Name','appointzilla'); ?></th>
            <th scope="col"><?php _e('Date','appointzilla'); ?></th>
            <th scope="col"><?php _e('Time','appointzilla'); ?></th>
            <th scope="col"><?php _e('Repeat','appointzilla'); ?></th>
            <th scope="col"><?php _e('Status','appointzilla'); ?></th>
            <th scope="col"><?php _e('Action','appointzilla'); ?></th>
            <th scope="col" style="text-align: center;"><a rel="tooltip" title="<?php _e('Select All','appointzilla'); ?>"><input type="checkbox" id="checkbox" name="checkbox[]" value="checkbox" /></a></th>
            </tr>
                <?php
                global $wpdb;
                $EventTable = $wpdb->prefix."ap_events";
                $FindAllEvents = "SELECT * FROM `$EventTable` ORDER BY `start_date` DESC";
                $AllEvents = $wpdb->get_results($FindAllEvents, OBJECT);
                $no = 1;
                if($AllEvents) {
                    foreach($AllEvents as $Event) { ?>
            <tr>
                <td><em><?php echo $no.".";  ?></em></td>
                <td><em><?php echo ucwords($Event->name); ?></em></td>
                <td><em><?php if($Event->repeat == 'N') echo date("F jS Y", strtotime($Event->start_date)) ; else echo date("M. jS", strtotime($Event->start_date))." To ".date("M. jS Y", strtotime($Event->end_date)); ?></em></td>
                <td><em><?php echo date("g:ia", strtotime($Event->start_time))." To ".date("g:ia", strtotime($Event->end_time)); ?></em></td>
                <td>
                    <em>
                    <?php
                        if($Event->repeat == 'N' && !$Event->allday) {
                            echo _e("None", "appointzilla");
                        }
                        if($Event->repeat == 'PD') {
                            echo _e("Particular Date(s)", "appointzilla");
                        }
                        if($Event->repeat == 'D') {
                            echo _e("Daily", "appointzilla");
                        }
                        if($Event->repeat == 'W') {
                            echo _e("Weekly", "appointzilla");
                        }
                        if($Event->repeat == 'BW') {
                            echo _e("Bi-Weekly", "appointzilla");
                        }
                        if($Event->repeat == 'M') {
                            $diff = ( strtotime($Event->end_date) - strtotime($Event->start_date)  ) /60/60/24/31;
                            echo _e("Monthly", "appointzilla");
                        }
                        if($Event->allday) {
                            echo " ";
                            echo _e("All Day", "appointzilla");
                        }
                    ?>
                    </em>
                </td>
                <td>
                    <em>
                    <?php
                        if($Event->repeat != 'N') {
                            if(strtotime("$Event->end_date") < strtotime(date('Y-m-d'))) {
                                echo _e("Gone", "appointzilla");
                            }
                            if( strtotime("$Event->start_date") <= strtotime(date('Y-m-d')) && strtotime("$Event->end_date") >= strtotime(date('Y-m-d'))) {
                                echo _e("Running", "appointzilla");
                            }
                            if(strtotime("$Event->start_date") > strtotime(date('Y-m-d')))
                            {
                                echo _e("Up Coming", "appointzilla");
                            }
                        } else if($Event->repeat == 'N') {
                            if(strtotime("$Event->end_date") < strtotime(date('Y-m-d'))) {
                                echo _e("Gone", "appointzilla");
                            }
                            if( strtotime("$Event->start_date") <= strtotime(date('Y-m-d')) && strtotime("$Event->end_date") >= strtotime(date('Y-m-d'))) {
                                    echo _e("Running", "appointzilla");
                            }
                            if(strtotime("$Event->start_date") > strtotime(date('Y-m-d'))) {
                                echo _e("Up Coming", "appointzilla");
                            }
                        }
                    ?>
                    </em>
                </td>
                <td>
                    <a href="?page=update-time-off&update-timeoff=<?php echo $Event->id; ?>" title="<?php _e('Update','appointzilla'); ?>" rel="tooltip"><i class="icon-pencil"></i></a>&nbsp;
                    <a href="?page=timeoff&delete-timeoff=<?php echo $Event->id; ?>" title="<?php _e('Delete','appointzilla'); ?>" onclick="return confirm('<?php _e('Do you want to delete this time off?','appointzilla'); ?>')" rel="tooltip"><i class="icon-remove"></i>
                </td>
                <td style="text-align: center;"><a rel="tooltip" title="<?php _e('Select','appointzilla'); ?>"><input type="checkbox" id="checkbox[]" name="checkbox[]" value="<?php echo $Event->id; ?>" /></a></td>
            </tr>
                <?php
                    $no++;
                    } // foreach
                } // if
                ?>
            <tr>
                <td colspan="2"><button name="addnewtimeoff" id="addnewtimeoff" class="btn btn-info" type="button" ><i class="icon-plus icon-white"></i> <?php _e('Add New Time Off','appointzilla'); ?></button></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td style="text-align: center;"><button name="deleteall" class="btn btn-danger" type="submit" id="deleteall" onclick="return confirm('<?php _e('Do you want to delete selected Time Off?','appointzilla'); ?>')" ><i class="icon-trash icon-white"></i> <?php _e('Delete','appointzilla'); ?></button></td>
            </tr>
        </table>
    </form>

    <style type='text/css'>

    .error{
        color:#FF0000;
    }

    .modal{
        top: 40%;
    }
    .modal-body {
        max-height: 535px;
    }
    </style>

    <!---TimeOff For Add New TimeOff--->
    <div id="TimeOffModal" style="display:none;">
        <div class="modal" id="myModal">
            <form action="" method="post" name="AddNewTimeOff-From" id="AddNewTimeOff-From">
                <div class="modal-info">
                    <div class="alert alert-info"><h4><?php _e('Add New Time Off','appointzilla'); ?></h4></div>
                </div>
                <div class="modal-body">
                    <table width="100%" class="table">
                        <tr>
                            <th scope="row"><?php _e('All Day Time Off','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="allday" id="allday" type="checkbox" value="1" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Name','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="name" id="name" type="text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Start Time','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="start_time" id="start_time" type="text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('End Time','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="end_time" id="end_time" type="text" /></td>
                        </tr>
                        <tr id="event_date_tr">
                            <th scope="row"><?php _e('Date','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="event_date" id="event_date" type="text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Repeat','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td>
                                <select name="repeat" id="repeat" onchange="repeatday()">
                                    <option onclick="hideAll()" value="N"><?php _e('No','appointzilla'); ?></option>
                                    <option onclick="showPD()" value="PD"><?php _e('Particular Date(s)','appointzilla'); ?></option>
                                    <option onclick="showDaily()" value="D"><?php _e('Daily','appointzilla'); ?></option>
                                    <option onclick="showWeekly()" value="W"><?php _e('Weekly','appointzilla'); ?></option>
                                    <option onclick="showBiWeekly()" value="BW"><?php _e('Bi-Weekly','appointzilla'); ?></option>
                                    <option onclick="showMonthly()" value="M"><?php _e('Monthly','appointzilla'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr id="re_days_tr" style="display:none;">
                            <th scope="row"><?php _e('Repeat Day(s)','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="re_days" id="re_days" type="text"  maxlength="2"/></td>
                        </tr>
                        <tr id="re_weeks_tr" style="display:none;">
                            <th scope="row"><?php _e('Repeat Week(s)','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="re_weeks" id="re_weeks" type="text"  maxlength="2"/></td>
                        </tr>
                        <tr id="re_biweeks_tr" style="display:none;">
                            <th scope="row"><?php _e('Repeat Bi-Week(s)','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="re_biweeks" id="re_biweeks" type="text"  maxlength="2"/></td>
                        </tr>
                        <tr id="re_months_tr" style="display:none;">
                            <th scope="row"><?php _e('Repeat Month(s)','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="re_months" id="re_months" type="text"  maxlength="2"/></td>
                        </tr>
                        <tr id="start_date_tr" style="display:none;">
                            <th scope="row"><?php _e('Start Date','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="start_date" id="start_date" type="text" maxlength="2"/></td>
                        </tr>
                        <tr id="end_date_tr" style="display:none;">
                            <th scope="row"><?php _e('End Date','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="end_date" id="end_date" type="text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Note','appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><textarea name="note" id="note"></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row">&nbsp;</th>
                            <td>&nbsp;</td>
                            <td>

                                <button name="create" id="create" class="btn btn-success" type="submit"><i class="icon-ok icon-white"></i> <?php _e('Create Time Off','appointzilla'); ?></button>
                                <a href="#"class="btn btn-danger" id="close"><i class="icon-remove icon-white"></i> <?php _e('Close','appointzilla'); ?></a>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>


<!---Saving Time-off--->
<?php 
    if(isset($_POST['create'])) {
        // all day event
        if(isset($_POST['allday'])) {
            $allday = 1;
            $start_time = '12:00 AM';
            $end_time = '11:59 PM';
        } else {
            $allday = 0;
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
        }
        $name = $_POST['name'];
        $repeat = $_POST['repeat'];
        $start_date = $_POST['event_date'];
        $start_date = date("Y-m-d", strtotime($start_date)); //convert format

        //not repeat
        if($repeat == 'N') {
            $end_date =  date("Y-m-d", strtotime($start_date)); //convert format
        }

        //particular day
        if($repeat == 'PD') {
            $end_date = $_POST['end_date'];
            $end_date =  date("Y-m-d", strtotime($end_date)); //convert format
        }

        //daily event will be  90 days
        if($repeat == 'D') {
            $repeat_days = $_POST['re_days'];
            $repeat_days = $repeat_days - 1;
            $end_date = strtotime($start_date);
            $end_date = date("Y-m-d", strtotime("+$repeat_days days", $end_date));          //add entered days
        }
        //weekly event add 1 week
        if($repeat == 'W') {
            $repeat_weeks = $_POST['re_weeks'];
            $end_date = strtotime($start_date);
            $end_date = date("Y-m-d", strtotime("+$repeat_weeks week", $end_date));         //add 1 week
        }
        //bi-weekly event add 1 week
        if($repeat == 'BW') {
            $repeat_weeks = $_POST['re_biweeks'];
            $end_date = strtotime($start_date);
            $repeat_weeks = $repeat_weeks * 2;
            $end_date = date("Y-m-d", strtotime("+$repeat_weeks week", $end_date));         //add 1 week
        }
        //monthly event add 1 month
        if($repeat == 'M') {
            $repeat_months = $_POST['re_months'] + 1;
            $end_date = strtotime($start_date);
            $end_date = date("Y-m-d", strtotime("+$repeat_months months", $end_date));      //add 1 month
        }
        $note = $_POST['note'];
        $status = "";

        global $wpdb;
        $EventTable = $wpdb->prefix."ap_events";
        $EventInsertSql = "INSERT INTO `$EventTable` ( `id` , `name` , `allday` , `start_time` , `end_time` , `repeat` ,
            `start_date` , `end_date` , `note` , `status` ) VALUES (
            NULL , '$name', '$allday', '$start_time', '$end_time', '$repeat', '$start_date', '$end_date', '$note', '$status');";

        if($wpdb->query($EventInsertSql)) {
            echo "<script>alert('".__('Time-off successfully added.','appointzilla')."')</script>";
            echo "<script>location.href='?page=timeoff';</script>";
        }
    }
?>


<!---Delete time off single row--->
<?php 
    if(isset($_GET['delete-timeoff'])) {
        global $wpdb;
        $EventTable = $wpdb->prefix."ap_events";
        $del_id = $_GET['delete-timeoff'];
        $TimeOffDeleteSQL = "delete from `$EventTable` where `id` = '$del_id'";
        if($wpdb->query($TimeOffDeleteSQL)) {
            echo "<script>alert('".__('Time-off successfully deleted.','appointzilla')."')</script>";
            echo "<script>location.href='?page=timeoff';</script>";
        } else {
            echo "<script>location.href='?page=timeoff';</script>";
        }
    }
?>

<?php
// Delete Multiple time-off
if(isset($_POST['deleteall'])) {
    $table_name = $wpdb->prefix . "ap_events";
    for($i=0; $i<=count($_POST['checkbox'])-1; $i++) {
        $res=$_POST['checkbox'][$i];
        $deleteid= $res;
        $delete_app_query="DELETE FROM `$table_name` WHERE `id` = '$deleteid';";
        $wpdb->query($delete_app_query);
    }

    if(count($_POST['checkbox'])) {
        echo "<script>alert('".__('Selected Time-off successfully deleted.','appointzilla')."')</script>";
    } else {
        echo "<script>alert('".__('No time-off selected to delete.','appointzilla')."')</script>";
    }
    echo "<script>location.href='?page=timeoff';</script>";
}
?>

<!--validation js lib-->

<script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>

<script type="text/javascript">
jQuery(document).ready(function () {

    //select all checkbox for multiple delete
    jQuery('#checkbox').click(function(){
        if(jQuery('#checkbox').is(':checked')) {
            jQuery(":checkbox").prop("checked", true);
        } else {
            jQuery(":checkbox").prop("checked", false);
        }
    });

    //Launch Modal Form
    //hide modal
    jQuery('#close').click(function(){

        jQuery('#TimeOffModal').hide();
    });

    //show modal
    jQuery('#addnewtimeoff').click(function(){
        jQuery('#TimeOffModal').show();
    });


    //Validation
    //when load for update time-off
    if (jQuery('#allday').is(':checked'))
    {
        jQuery('#start_time').attr("disabled", true);
        jQuery('#end_time').attr("disabled", true);
    }

    //repeat
    var repeat = jQuery("#repeat").val();

    if (repeat == 'N') {
        jQuery('#start_date_tr').hide();
        jQuery('#end_date_tr').hide();
        jQuery('#re_days_tr').hide();
        jQuery('#re_weeks_tr').hide();
        jQuery('#re_biweeks_tr').hide();
        jQuery('#re_months_tr').hide();
        jQuery('#event_date_tr').show();
    }

    if (repeat == 'PD') {
        jQuery('#start_date_tr').show();
        jQuery('#end_date_tr').show();
        jQuery('#re_days_tr').hide();
        jQuery('#re_weeks_tr').hide();
        jQuery('#re_biweeks_tr').hide();
        jQuery('#re_months_tr').hide();
        jQuery('#event_date_tr').hide();
    }

    if (repeat == 'D') {
        jQuery('#start_date_tr').hide();
        jQuery('#end_date_tr').hide();
        jQuery('#re_days_tr').show();
        jQuery('#re_weeks_tr').hide();
        jQuery('#re_biweeks_tr').hide();
        jQuery('#re_months_tr').hide();
        jQuery('#event_date_tr').hide();
    }

    if (repeat == 'W') {
        jQuery('#start_date_tr').hide();
        jQuery('#end_date_tr').hide();
        jQuery('#re_days_tr').hide();
        jQuery('#re_weeks_tr').show();
        jQuery('#re_biweeks_tr').hide();
        jQuery('#re_months_tr').hide();
        jQuery('#event_date_tr').hide();
    }

    if (repeat == 'BW') {
        jQuery('#start_date_tr').hide();
        jQuery('#end_date_tr').hide();
        jQuery('#re_days_tr').hide();
        jQuery('#re_weeks_tr').hide();
        jQuery('#re_biweeks_tr').show();
        jQuery('#re_months_tr').hide();
        jQuery('#event_date_tr').hide();
    }

    if (repeat == 'M') {
        jQuery('#start_date_tr').hide();
        jQuery('#end_date_tr').hide();
        jQuery('#re_days_tr').hide();
        jQuery('#re_weeks_tr').hide();
        jQuery('#re_months_tr').show();
        jQuery('#event_date_tr').hide();
    }


    // all day event check
    jQuery('#allday').click(function(){
        if (jQuery(this).is(':checked'))
        {
            //jQuery('#name').attr("disabled", true);
            // hasName = 1;
            jQuery('#start_time').attr("disabled", true);
             hasST = 1;
            jQuery('#end_time').attr("disabled", true);
             hasET = 1;
        } else {
            jQuery('#start_time').attr("disabled", false);
             hasST = 0;
            jQuery('#end_time').attr("disabled", false);
             hasET = 0;
        }
    });


    //ON-FORM SUBMIT

    // start/end times and dates
    jQuery('#create').click(function() {

        jQuery(".error").hide();
        //name
        var nameVal = jQuery("#name").val();
        if(nameVal == '') {
            jQuery("#name").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
            return false;
        }

        //if all-day is not checked then check start/ent time required
        if (!jQuery('#allday').is(':checked')) {
            //start time
            var start_time = jQuery("#start_time").val();
            if(start_time == '') {
                jQuery("#start_time").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }

            //end time
            var end_time = jQuery("#end_time").val();
            if(end_time == ''){
                jQuery("#end_time").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }

            if(start_time == end_time) {
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
                jQuery("#end_time").after('<span class="error"><br><strong><?php _e("End-time must be bigger then Start-time.",'appointzilla'); ?></strong></span>');
                return false;
            }
        }

        //repeat
        var repeat = jQuery("#repeat").val();

        // PARTICULAR DATES
        if(repeat == 'PD'){
            var start_date = jQuery("#start_date").val();
            if(start_date == '') {
                jQuery("#start_date").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }
            var end_date = jQuery("#end_date").val();
            if(end_date == '') {
                jQuery("#end_date").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }
        }

        //DAILY
        if(repeat == 'D'){

            var re_days = jQuery("#re_days").val();
            if(re_days == '')
            {
                jQuery("#re_days").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }
        }

        //WEEKLY
        if(repeat == 'W'){

            var re_weeks = jQuery("#re_weeks").val();
            if(re_weeks == '')
            {
                jQuery("#re_weeks").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }
        }

        //Bi-WEEKLY
        if(repeat == 'BW'){

            var re_biweeks = jQuery("#re_biweeks").val();
            if(re_biweeks == '')
            {
                jQuery("#re_biweeks").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }
        }

        //MONTHLY
        if(repeat == 'M'){

            var re_months = jQuery("#re_months").val();
            if(re_months == '')
            {
                jQuery("#re_months").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }
        }

        //date
        var event_date = jQuery("#event_date").val();
        if(repeat != 'PD'){

            if(event_date == ''){
                jQuery("#event_date").after('<span class="error"><br><strong><?php _e('This field required.','appointzilla'); ?></strong></span>');
                return false;
            }
        }
    });
});


jQuery(function(){
    //load date and time picker
    jQuery('#start_time').timepicker({
        ampm: true,
        timeFormat: 'hh:mm TT',
        stepMinute: 5
    });

    jQuery('#end_time').timepicker({
        ampm: true,
        timeFormat: 'hh:mm TT',
        stepMinute: 5
    });

    jQuery('#start_date').datepicker({
        minDate: 0,
        dateFormat: 'dd-mm-yy'
    });

    jQuery('#end_date').datepicker({
        dateFormat: 'dd-mm-yy',
        minDate: 0
    });

    jQuery('#event_date').datepicker({
        dateFormat: 'dd-mm-yy',
        minDate: 0
    });
});

function repeatday() {
    var repete = jQuery("#repeat").val();
    if(repete=="PD"){ showPD();	}
    if(repete=="N")	{ hideAll(); }
    if(repete=="D")	{ showDaily();}
    if(repete=="W")	{ showWeekly();	}
    if(repete=="BW"){ showBiWeekly();	}
    if(repete=="M")	{ showMonthly(); }
}

function hideAll() {
    jQuery('#start_date_tr').hide();
    jQuery('#end_date_tr').hide();
    jQuery('#re_days_tr').hide();
    jQuery('#re_weeks_tr').hide();
    jQuery('#re_biweeks_tr').hide();
    jQuery('#re_months_tr').hide();
    jQuery('#event_date_tr').show();
}

function showPD() {
    jQuery('#start_date_tr').show();
    jQuery('#end_date_tr').show();
    jQuery('#re_days_tr').hide();
    jQuery('#re_weeks_tr').hide();
    jQuery('#re_biweeks_tr').hide();
    jQuery('#re_months_tr').hide();
    jQuery('#event_date_tr').hide();
}

function showWeekly() {
    jQuery('#start_date_tr').hide();
    jQuery('#end_date_tr').hide();
    jQuery('#re_days_tr').hide();
    jQuery('#re_weeks_tr').show();
    jQuery('#re_biweeks_tr').hide();
    jQuery('#re_months_tr').hide();
    jQuery('#event_date_tr').show();
}

function showBiWeekly() {
    jQuery('#start_date_tr').hide();
    jQuery('#end_date_tr').hide();
    jQuery('#re_days_tr').hide();
    jQuery('#re_weeks_tr').hide();
    jQuery('#re_biweeks_tr').show();//
    jQuery('#re_months_tr').hide();
    jQuery('#event_date_tr').show();
}

function showMonthly() {
    jQuery('#start_date_tr').hide();
    jQuery('#end_date_tr').hide();
    jQuery('#re_days_tr').hide();
    jQuery('#re_weeks_tr').hide();
    jQuery('#re_biweeks_tr').hide();
    jQuery('#re_months_tr').show();
    jQuery('#event_date_tr').show();
}

function showDaily() {
    jQuery('#start_date_tr').hide();
    jQuery('#end_date_tr').hide();
    jQuery('#re_days_tr').show();
    jQuery('#re_weeks_tr').hide();
    jQuery('#re_biweeks_tr').hide();
    jQuery('#re_months_tr').hide();
    jQuery('#event_date_tr').show();
}
</script>

<!--time-picker js -->
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-1.7.2.min.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui.min.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui-timepicker-addon.js', __FILE__); ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo plugins_url('js/date.js', __FILE__); ?>"></script>
</div>