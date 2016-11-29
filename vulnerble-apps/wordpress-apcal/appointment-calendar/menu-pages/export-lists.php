<?php
if(isset($_POST["Range"]) && isset($_POST["StartDate"]) && isset($_POST["EndDate"]) && isset($_POST['FileName'])) {
    $Range = $_POST['Range'];
    $StartDate = date("Y-m-d", strtotime($_POST['StartDate']));
    $EndDate = date("Y-m-d", strtotime($_POST['EndDate']));

    global $wpdb;
    $AppointmentTable = $wpdb->prefix . "ap_appointments";
    //today list
    if($Range == "T") {
        $TodayDate = date("Y-m-d");
        $ListQuery = "SELECT * FROM `$AppointmentTable` WHERE `date` = '$TodayDate'";
    }

    //this week list
    if($Range == "W") {
        $ListQuery = "SELECT * FROM `$AppointmentTable` WHERE `date` BETWEEN '$StartDate' AND '$EndDate'";
    }

    //this month list
    if($Range == "M") {
        $ListQuery = "SELECT * FROM `$AppointmentTable` WHERE `date` BETWEEN '$StartDate' AND '$EndDate'";
    }

    //custom range list
    if($Range == "CR") {
        $ListQuery = "SELECT * FROM `$AppointmentTable` WHERE `date` BETWEEN '$StartDate' AND '$EndDate'";
    }

    //all appointment list
    if($Range == "A") {
        $ListQuery = "SELECT * FROM `$AppointmentTable`";
    }

    $FileName = $_POST['FileName'];

    $QueryResults = $wpdb->get_results($ListQuery);
    if(count($QueryResults)) {
        $DirName = "appointments-lists";
        $DirPath = "../wp-content/".$DirName;

        if(!file_exists($DirPath)) {
            mkdir($DirPath, 0777);
        }

        $FileName =  $DirPath."/".$FileName;
        $df = fopen($FileName , "x+");

        //write data into file
        $FirstRow = "#, Name, Email, Phone, Time, Date, Note, Status\n";
        fwrite($df, $FirstRow);
        $id = 1;
        foreach($QueryResults as $Data) {
            $name = $Data->name;
            $email = $Data->email;
            $phone = $Data->phone;
            $starttime = $Data->start_time;
            $endtime = $Data->end_time;
            $time = $starttime."-".$endtime;
            $date = date("jS M Y", strtotime($Data->date));
            $note = $Data->note;
            $status = $Data->status;
            $Rows = $id. "," .ucwords($name). "," .$email. "," .$phone. "," .$time. "," .$date. "," .ucfirst($note). "," .ucfirst($status)."\n";
            fwrite($df, $Rows);
            $id++;
        }
        fclose($df);
        
    } else {
        $DirName = "appointments-lists";
        $DirPath = "../wp-content/".$DirName;

        if(!file_exists($DirPath)) {
            mkdir($DirPath, 0777);
        }

        $FileName =  $DirPath."/".$FileName;
        $df = fopen($FileName , "x+");

        //write data into file
        $FirstRow = "#, Name, Email, Phone, Time, Date, Note, Status\n";
        fwrite($df, $FirstRow);
        fwrite($df, "Sorry! No Appointment(s) Found");
        fclose($df);
    }
}
?>
<div class="bs-docs-example tooltip-demo">
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><?php _e("Export Appointments", "appointzilla"); ?></h3>
    </div>
    <table width="100%" class="table" style="background-color: #FFFFFF;">
        <tr id="select-range-tr">
            <th width="18%" align="right" scope="row"><?php _e("Select Range", "appointzilla"); ?></th>
            <td width="3%" align="center"><strong>:</strong></td>
            <td width="79%">
                <select name="select_range" id="select_range">
                    <option value="0"><?php _e("Select Range", "appointzilla"); ?></option>
                    <option value="T"><?php _e("Today's Appointment", "appointzilla"); ?></option>
                    <option value="W"><?php _e("This Week Appointment", "appointzilla"); ?></option>
                    <option value="M"><?php _e("This Month Appointment", "appointzilla"); ?></option>
                    <option value="CR"><?php _e("Custom Appointment Range", "appointzilla"); ?></option>
                    <option value="A"><?php _e("All Appointment", "appointzilla"); ?></option>
                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Appointment Selection Range", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
            </td>
        </tr>
        <tr id="custom-dates1" style="display:none;">
            <th width="18%" align="right" scope="row"><?php _e("Select Start Date", "appointzilla"); ?></th>
            <td width="3%" align="center"><strong>:</strong></td>
            <td width="79%">
                <input type="text" id="start_date" name="start_date">
            </td>
        </tr>
        <tr id="custom-dates2" style="display:none;">
            <th width="18%" align="right" scope="row"><?php _e("Select End Date", "appointzilla"); ?></th>
            <td width="3%" align="center"><strong>:</strong></td>
            <td width="79%">
                <input type="text" id="end_date" name="end_date">
            </td>
        </tr>
        <tr>
            <th scope="row">&nbsp;</th>
            <td>&nbsp;</td>
            <td>
                <button name="export-lists" class="btn btn-info" type="submit" id="export-lists" data-loading-text="Saving Settings" ><i class="icon-list-alt icon-white"></i> <?php _e("Export List", "appointzilla"); ?></button>
                <br><br>
                <div id="loading-img" style="display: none;">
                    <?php _e('Generating appointments list, please wait...', 'appointzilla'); ?><img src="<?php echo plugins_url("images/loading.gif", __FILE__); ?>" />
                </div>
            </td>
        </tr>
    </table>

    <style type="text/css">
        .apcal_error{  color:#FF0000; }
    </style>
    <!--validation js lib-->
    <script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>

    <!--time-picker js -->
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-1.7.2.min.js', __FILE__); ?>" type="text/javascript"></script>
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui.min.js', __FILE__); ?>" type="text/javascript"></script>
    <script src="<?php echo plugins_url('/timepicker-assets/js/jquery-ui-timepicker-addon.js', __FILE__); ?>" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo plugins_url('js/date.js', __FILE__); ?>"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {

            jQuery('#select_range').change(function() {
                var Range = jQuery('#select_range').val();
                if(Range == "CR") {
                    jQuery("#custom-dates1").show();
                    jQuery("#custom-dates2").show();
                } else {
                    jQuery("#custom-dates1").hide();
                    jQuery("#custom-dates2").hide();
                }
            });

            jQuery('#start_date').datepicker({
                dateFormat: 'dd-mm-yy',
            });

            jQuery('#end_date').datepicker({
                dateFormat: 'dd-mm-yy',
            });

            jQuery('#export-lists').click(function(){
                jQuery(".apcal_error").hide();
                var StartDate, EndDate;
                var Range = jQuery('#select_range').val();
                var RangeName = "";
                if(Range == 0)
                {
                    jQuery("#select_range").after('<span class="apcal_error">&nbsp;<br><strong><?php _e("Select any range.", "appointzilla"); ?></strong></span>');
                    return false;
                }

                if(Range == "T") {
                   StartDate = "<?php echo date("Y-m-d"); ?>";
                   EndDate = "<?php echo date("Y-m-d"); ?>";
                   RangeName = "today";
                }
                if(Range == "W") {
                    <?php
                        $week = date("W") - 1;
                        $year = date("Y");
                        $time = strtotime("1 January $year", time());
                        $day = date('w', $time);
                        $time += ((7*$week)+1-$day)*24*3600;
                        $StartDate = date('Y-n-j', $time);
                        $time += 6*24*3600;
                        $EndDate = date('Y-n-j', $time);
                    ?>
                    StartDate = "<?php echo  date("Y-m-d", strtotime($StartDate)); ?>";
                    EndDate = "<?php echo date("Y-m-d", strtotime($EndDate)); ?>";
                    RangeName = "weekly";
                }

                if(Range == "M") {
                    StartDate = "<?php echo  date("Y-m-1"); ?>";
                    EndDate = "<?php echo  date("Y-m-t"); ?>";
                    RangeName = "monthly";
                }

                if(Range == "CR") {
                    StartDate = jQuery("#start_date").val();
                    EndDate = jQuery("#end_date").val();
                    if(StartDate == "") {
                        jQuery("#start_date").after('<span class="apcal_error">&nbsp;<br><strong><?php _e("Select any start date.", "appointzilla"); ?></strong></span>');
                        return false;
                    }
                    if(EndDate == "") {
                        jQuery("#end_date").after('<span class="apcal_error">&nbsp;<br><strong><?php _e("Select any end date.", "appointzilla"); ?></strong></span>');
                        return false;
                    }
                    RangeName = "custom-range";
                }
                if(RangeName == "") RangeName = "all";
                var Url = location.href;
                var FileName = RangeName + "-" + "<?php echo 'appointments-list_'.date('d-m-Y_h-i-s').'.csv'; ?>";
                jQuery("#loading-img").show();
                jQuery.ajax({
                    type: "POST",
                    url: Url,
                    //dataType: 'php',
                    data: "Range=" + Range + "&StartDate=" + StartDate + "&EndDate=" + EndDate + "&FileName=" + FileName,
                    success: function(ReturendData) {
                        jQuery("#download-btn-div").show();
                        location.href = location.href;
                    },
                    complete: function() {
                        jQuery("#select_range").prop('disabled', true);
                        jQuery("#custom-dates1").hide();
                        jQuery("#custom-dates2").hide();
                    },
                    error: function(error) {
                        alert(error);
                    }
                });

            });
        });
    </script>
</div>


<div class="bs-docs-example tooltip-demo">
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;">
        <h3><?php _e("Appointments Lists", "appointzilla"); ?></h3>
    </div>
    <table class="table" style="background-color: #FFFFFF;">
        <thead>
        <tr>
            <th>#</th>
            <th><?php _e("Lists", "appointzilla"); ?></th>
            <th><?php _e("Date", "appointzilla"); ?></th>
            <th><?php _e("Time", "appointzilla"); ?></th>
            <th><?php _e("Size", "appointzilla"); ?></th>
            <th><?php _e("Action", "appointzilla"); ?></th>
        </tr>
        </thead>
        <tbody>
    <?php
        $DirName = "appointments-lists";
        $DirPath = "../wp-content/".$DirName;
        // check if appointment list dir exist and not empty
        $TotalFiles =count(glob("$DirPath/*"));
        if( file_exists($DirPath) &&  $TotalFiles > 0) {

            $AllFiles = scandir($DirPath, 1);
            $i = 1;
            foreach($AllFiles as $File) {
                if ($File != "." && $File != "..") {
        ?>
            <tr>
                <td><?php echo $i."."; ?></td>
                <td><?php echo ucfirst($File); ?></td>
                <td><?php
                    $FilePath = $DirPath."/".$File;
                    if (file_exists($FilePath)) {
                        echo date ("d-m-Y", filemtime($FilePath));
                    } ?></td>
                <td><?php if (file_exists($FilePath)) {
                        echo date ("H:i:s", filemtime($FilePath));
                    } ?></td>
                <td><?php echo round( ( filesize($FilePath)/1024 ), 2) . ' KB'; ?></td>
                <td>
                    <a class="btn btn-mini btn-success" href="<?php echo $FilePath; ?>" target="_blank"><i class="icon-download-alt icon-white"></i> <strong><?php _e("Download", "appointzilla"); ?></strong></a>
                    <a class="btn btn-mini btn-danger" href="?page=apcal-export-lists&delete-file=<?php echo $File; ?>"><i class="icon-remove icon-white"></i> <strong><?php _e("Delete", "appointzilla"); ?></strong></a>
                </td>
            </tr>
               <?php

                }//end of foreach inner if
                $i++;
            }//end of foreach
            ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><a class="btn btn-small btn-danger" href="?page=apcal-export-lists&delete-file=all"><i class="icon-trash icon-white"></i> <strong><?php _e("Delete All", "appointzilla"); ?></strong></a></td>
            </tr>
            <?php
        } else {
        ?>
            <tr>
                <td colspan="6" class="alert"><strong><?php _e("Sorry! No appointment lists are available.", "appointzilla"); ?></strong></td>
            </tr>
        <?php
        }//end of else
        ?>
        </tbody>
    </table>
</div>
<?php
// delete file
if(isset($_GET["delete-file"])) {
    $DirName = "appointments-lists";
    $DirPath = "../wp-content/".$DirName;
    $File = $_GET["delete-file"];

    if($File != 'all') {
        $FilePath = $DirPath."/".$File;
        if( file_exists($FilePath) ) {
            if(unlink($FilePath)) {
                ?><script> alert("<?php _e('List successfully deleted.', 'appointzilla'); ?>"); location.href = '?page=apcal-export-lists'; </script><?php

            } else {
                ?><script> alert("<?php _e('Unable to delete list or list file not exist.', 'appointzilla'); ?>"); location.href = '?page=apcal-export-lists'; </script><?php
            }
        }
    } else {
        //delete all files from appointments-lists directory
        if( file_exists($DirPath) ) {
            $AllFiles = scandir($DirPath, 1);
            foreach($AllFiles as $File) {
                if ($File != "." && $File != "..") {
                    $FilePath = $DirPath."/".$File;
                    // delete all files
                    if( file_exists($FilePath) ) { unlink($FilePath); }
                }
            }
            ?><script> alert("<?php _e('All list file(s) successfully deleted.', 'appointzilla'); ?>"); location.href = '?page=apcal-export-lists'; </script><?php
        }
    }
}
?>