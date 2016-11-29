<div class="bs-docs-example tooltip-demo">
    <div style="background:#C3D9FF; margin-bottom:10px; padding-left:10px;"><h3><?php _e("Settings", "appointzilla"); ?></h3></div>

    <div class="bs-docs-example" style="background-color: #FFFFFF;">
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#calendar-settings"><?php _e("Calendar Settings", "appointzilla"); ?></a></li>
            <li><a data-toggle="tab" href="#notification-settings"><?php _e("Notification Settings", "appointzilla"); ?></a></li>
            <li><a data-toggle="tab" href="#notification-message"><?php _e("Notification Message", "appointzilla"); ?></a></li>
        </ul>

        <!--tabs-body-->
        <div class="tab-content" id="myTabContent" style="padding-left: 15px;">

            <!--calendar settings-->
            <div id="calendar-settings" class="tab-pane fade in active">
                <?php $AllCalendarSettings = unserialize(get_option('apcal_calendar_settings')); ?>
                <fieldset>
                    <legend><?php _e("Manage Calendar Settings", "appointzilla"); ?></legend>
                    <table width="100%" class="table">
                        <tr>
                            <th width="18%" align="right" scope="row"><?php _e("Calendar Slot Time", "appointzilla"); ?></th>
                            <td width="3%" align="center"><strong>:</strong></td>
                            <td width="79%">
                                <?php $CalendarSlotTime = $AllCalendarSettings['calendar_slot_time']; ?>
                                <select name="calendar_slot_time" id="calendar_slot_time">
                                    <option value="15" <?php if($CalendarSlotTime && $CalendarSlotTime == '15') echo "selected"; ?>><?php _e("15 Minute", "appointzilla"); ?></option>
                                    <option value="30" <?php if($CalendarSlotTime && $CalendarSlotTime == '30') echo "selected"; ?>><?php _e("30 Minute", "appointzilla"); ?></option>
                                    <option value="60" <?php if($CalendarSlotTime && $CalendarSlotTime == '60') echo "selected"; ?>><?php _e("60 Minute", "appointzilla"); ?></option>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Calendar Time Slot", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th align="right" scope="row"><?php _e("Day Start Time", "appointzilla"); ?></th>
                            <td align="center"><strong>:</strong></td>
                            <td>
                                <?php $day_start_time = $AllCalendarSettings['day_start_time']; ?>
                                <select name="day_start_time" id="day_start_time">
                                    <?php
                                    $biz_start_time = strtotime("01:00 AM");
                                    $biz_end_time = strtotime("11:00 PM");
                                    //making 15min slots
                                    for( $i = $biz_start_time; $i <= $biz_end_time; $i += (60*(15))) {
                                        if( $day_start_time && $day_start_time == date('g:i A', $i) ) {
                                            $selected = 'selected';
                                        } else {
                                            $selected='';
                                        }
                                        echo "<option $selected value='". date('g:i A', $i)."'>". date('g:i A', $i) ."</option>";
                                    }
                                    ?>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Calendar Day Start Time", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th align="right" scope="row"><?php _e("Day End Time", "appointzilla"); ?></th>
                            <td align="center"><strong>:</strong></td>
                            <td>
                                <?php $day_end_time = $AllCalendarSettings['day_end_time']; ?>
                                <select name="day_end_time" id="day_end_time">
                                    <?php
                                    //making 60min slots
                                    for( $i = $biz_start_time; $i <= $biz_end_time; $i += (60*(15))) {
                                        if( $day_end_time && $day_end_time == date('g:i A', $i) ) {
                                            $selected = 'selected';
                                        } else {
                                            $selected='';
                                        }
                                        echo "<option $selected value='". date('g:i A', $i)."'>". date('g:i A', $i) ."</option>";
                                    }
                                    ?>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Calendar Day End Time", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th align="right" scope="row"><?php _e("Calendar View", "appointzilla"); ?></th>
                            <td align="center"><strong>:</strong></td>
                            <td>
                                <?php
                                $CalendarView = $AllCalendarSettings['calendar_view']; ?>
                                <select id="calendar_view" name="calendar_view">
                                    <option value="agendaDay" <?php if($CalendarView && $CalendarView == 'agendaDay') echo "selected"; ?>><?php _e("Day", "appointzilla"); ?></option>
                                    <option value="agendaWeek" <?php if($CalendarView && $CalendarView == 'agendaWeek') echo "selected"; ?>><?php _e("Week", "appointzilla"); ?></option>
                                    <option value="month" <?php if($CalendarView && $CalendarView == 'month') echo "selected"; ?>><?php _e("Month", "appointzilla"); ?></option>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Calendar View", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th align="right" scope="row"><?php _e("Calendar First Day", "appointzilla"); ?></th>
                            <td align="center"><strong>:</strong></td>
                            <td>
                                <?php $CalendarStartDay = $AllCalendarSettings['calendar_start_day']; ?>
                                <select name="calendar_start_day" id="calendar_start_day">
                                    <option value="1" <?php if($CalendarStartDay == 1) echo "selected";  ?>><?php _e("Monday", "appointzilla"); ?></option>
                                    <option value="2" <?php if($CalendarStartDay == 2) echo "selected";  ?>><?php _e("Tuesday", "appointzilla"); ?></option>
                                    <option value="3" <?php if($CalendarStartDay == 3) echo "selected";  ?>><?php _e("Wednesday", "appointzilla"); ?></option>
                                    <option value="4" <?php if($CalendarStartDay == 4) echo "selected";  ?>><?php _e("Thursday", "appointzilla"); ?></option>
                                    <option value="5" <?php if($CalendarStartDay == 5) echo "selected";  ?>><?php _e("Friday", "appointzilla"); ?></option>
                                    <option value="6" <?php if($CalendarStartDay == 6) echo "selected";  ?>><?php _e("Saturday", "appointzilla"); ?></option>
                                    <option value="0" <?php if($CalendarStartDay == 0) echo "selected";  ?>><?php _e("Sunday", "appointzilla"); ?></option>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Calendar First Day", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th align="right" scope="row"><?php _e("Booking Button Text", "appointzilla")?></th>
                            <td align="center"><strong>:</strong></td>
                            <td>
                                <input name="booking_button_text" type="text" id="booking_button_text" value="<?php echo $AllCalendarSettings['booking_button_text'];?>" />
                                &nbsp;<a href="#" rel="tooltip" title="<?php _e("Booking Button Text", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th align="right" scope="row"><?php _e("Booking Time Slot", 'appointzilla'); ?></th> <td align="center"><strong>:</strong></td>
                            <td><?php if(isset($AllCalendarSettings['booking_time_slot'])) {
                                    $BookingTimeSlot = $AllCalendarSettings['booking_time_slot'];
                                } else {
                                    $BookingTimeSlot = 30;
                                } ?>
                                <select name="booking_time_slot" id="booking_time_slot">
                                    <option <?php if($BookingTimeSlot == 5) echo "selected"; ?> value="5"><?php _e("5 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 10) echo "selected"; ?> value="10"><?php _e("10 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 15) echo "selected"; ?> value="15"><?php _e("15 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 20) echo "selected"; ?> value="20"><?php _e("20 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 25) echo "selected"; ?> value="25"><?php _e("25 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 30) echo "selected"; ?> value="30"><?php _e("30 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 35) echo "selected"; ?> value="35"><?php _e("35 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 40) echo "selected"; ?> value="40"><?php _e("40 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 45) echo "selected"; ?> value="45"><?php _e("45 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 60) echo "selected"; ?> value="60"><?php _e("60 Minutes (1 Hour)", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 75) echo "selected"; ?> value="75"><?php _e("75 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 90) echo "selected"; ?> value="90"><?php _e("90 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 120) echo "selected"; ?> value="120"><?php _e("120 Minutes (2 Hour)", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 150) echo "selected"; ?> value="150"><?php _e("150 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 180) echo "selected"; ?> value="180"><?php _e("180 Minutes (3 Hour)", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 210) echo "selected"; ?> value="210"><?php _e("210 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 240) echo "selected"; ?> value="240"><?php _e("240 Minutes (4 Hour)", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 270) echo "selected"; ?> value="270"><?php _e("270 Minutes", 'appointzilla'); ?></option>
                                    <option <?php if($BookingTimeSlot == 300) echo "selected"; ?> value="300"><?php _e("300 Minutes (5 Hour)", 'appointzilla'); ?></option>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Booking Time Slot' ,'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>

                        <tr>
                            <th align="right" scope="row"><?php _e("Display Service Cost", "appointzilla")?></th>
                            <td align="center"><strong>:</strong></td>
                            <td>
                                <select name="show_service_cost" id="show_service_cost">
                                    <option value="yes" <?php if($AllCalendarSettings['show_service_cost'] == 'yes') echo "selected"; ?>><?php echo _e('Yes' ,'appointzilla'); ?></option>
                                    <option value="no" <?php if($AllCalendarSettings['show_service_cost'] == 'no') echo "selected"; ?>><?php echo _e('No' ,'appointzilla'); ?></option>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Show or hide service cost at client booking form.", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>

                        <tr>
                            <th align="right" scope="row"><?php _e("Display Service Duration", "appointzilla")?></th>
                            <td align="center"><strong>:</strong></td>
                            <td>
                                <select name="show_service_duration" id="show_service_duration">
                                    <option value="yes" <?php if($AllCalendarSettings['show_service_duration'] == 'yes') echo "selected"; ?>><?php echo _e('Yes' ,'appointzilla'); ?></option>
                                    <option value="no" <?php if($AllCalendarSettings['show_service_duration'] == 'no') echo "selected"; ?>><?php echo _e('No' ,'appointzilla'); ?></option>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e("Show or hide service duration at client booking form.", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th align="right" scope="row"><?php _e("Booking Instructions", "appointzilla")?></th>
                            <td align="center"><strong>:</strong></td>
                            <td><b><?php _e("You can use only these HTML tags like:", "appointzilla"); ?></b><br>&lt;p&gt;&lt;/p&gt;, &lt;h1&gt;&lt;/h1&gt;, &lt;h2&gt&lt;/h2&gt;, &lt;h3&gt;&lt;/h3&gt;, &lt;h4&gt;&lt;/h4&gt;, <br>&lt;h5&gt;&lt;/h5&gt;, &lt;h6&gt;&lt;/h6&gt;, &lt;b&gt;&lt;/b&gt;, &lt;em&gt;&lt;/em&gt; &lt;br&gt;&lt;/br&gt;
                                <textarea id="apcal_booking_instructions" name="apcal_booking_instructions" style="width: 500px; height: 150px;"><?php if($AllCalendarSettings['apcal_booking_instructions']) echo $AllCalendarSettings['apcal_booking_instructions']; ?></textarea>
                                &nbsp;<a href="#" rel="tooltip" title="<?php _e("Booking instruction will be appears on client interface before boooking button.<br> You can use only these HTML tags like p, h1, h2, h3, h4, h5, h6, b, em to make more visualize instructions.", "appointzilla"); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">&nbsp;</th>
                            <td>&nbsp;</td>
                            <td>
                                <button name="save-settings" class="btn btn-success" id="save-settings" data-loading-text="Saving Settings" onclick="return SaveCalendarSettings('save-calendar-settings');" ><i class="fa fa-save"></i> <?php _e("Save", "appointzilla"); ?></button>
                                <div id="loading-img-calendar-settings" style="display: none;"><?php _e("Saving", "appointzilla"); ?>...<i class="fa fa-spinner fa-spin fa-2x"></i></div>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>

            <!--notification settings-->
            <div id="notification-settings" class="tab-pane fade">
                <fieldset>
                    <legend><?php _e("Manage Notification Settings", "appointzilla"); ?></legend>
                    <table width="100%" class="table">
                        <tr>
                            <th colspan="2" scope="row"><?php _e('Enable', 'appointzilla'); ?></th>
                            <td width="3%"><strong>:</strong></td>
                            <td width="69%"><input name="enable" type="checkbox" id="enable" <?php if(get_option('emailstatus') == 'on') echo 'checked'; ?> />&nbsp;<a href="#" rel="tooltip" title="<?php _e('ON/OFF Notification', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
                            <td width="3%">&nbsp;</td>
                            <td width="3%">&nbsp;</td>
                            <td width="3%">&nbsp;</td>
                        </tr>
                        <?php $emailtype = get_option('emailtype'); ?>
                        <tr>
                            <th colspan="2" scope="row"><?php _e('Email Type', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td>
                                <select name="emailtype" id="emailtype">
                                    <option value="0" <?php if(get_option('emailstatus') == 'off') echo "selected=selected";?>><?php _e('Select Type', 'appointzilla'); ?></option>
                                    <option value="wpmail" <?php if($emailtype == 'wpmail' && get_option('emailstatus') == 'on') echo 'selected';?>><?php _e('WP Mail', 'appointzilla'); ?></option>
                                    <option value="phpmail" <?php if($emailtype == 'phpmail' && get_option('emailstatus') == 'on') echo 'selected';?>><?php _e('PHP Mail', 'appointzilla'); ?></option>
                                    <option value="smtp" <?php if($emailtype == 'smtp' && get_option('emailstatus') == 'on') echo 'selected';?>><?php _e('SMTP Mail', 'appointzilla'); ?></option>
                                </select>&nbsp;<a href="#" rel="tooltip" title="<?php _e('Notification Type', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php
                        $EmailDetails =  get_option('emaildetails');
                        if($EmailDetails) {
                            $EmailDetails = unserialize($EmailDetails);
                        }
                        ?>
                        <!--wp mail-->
                        <tr id="wpmaildetails1" style="display:none;">
                            <th colspan="2" scope="row"><?php _e('WP Mail Details', 'appointzilla'); ?></th>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr id="wpmaildetails2" style="display:none;">
                            <th scope="row">&nbsp;</th>
                            <th scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="wpemail" type="text" id="wpemail"  value="<?php if(isset($EmailDetails['wpemail'])) { echo $EmailDetails['wpemail']; } ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Admin Email', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

                        <!--php mail-->
                        <tr id="phpmaildetails1" style="display:none;">
                            <th colspan="2" scope="row"><?php _e('PHPMail Details', 'appointzilla'); ?></th>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr id="phpmaildetails2" style="display:none;">
                            <th scope="row">&nbsp;</th>
                            <th scope="row"><?php _e('Email', 'appointzilla'); ?></th>
                            <td><strong>:</strong></td>
                            <td><input name="phpemail" type="text" id="phpemail" value="<?php if(isset($EmailDetails['phpemail'])) { echo $EmailDetails['phpemail']; } ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Admin Email', 'appointzilla'); ?>" ><i  class="icon-question-sign"></i></a></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

                        <!--smtp-->
                        <tr id="smtpdetails1" style="display:none;">
                            <th colspan="2" scope="row"><?php _e('SMTP Mail Details', 'appointzilla'); ?></th>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr id="smtpdetails2" style="display:none;">
                            <th width="9%" scope="row">&nbsp;</th>
                            <td width="10%" scope="row"><?php _e('Host Name', 'appointzilla'); ?></td>
                            <td><strong>:</strong></td>
                            <td><input name="hostname" type="text" id="hostname" class="inputhieght" value="<?php if(isset($EmailDetails['hostname'])) { echo $EmailDetails['hostname']; } ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Host Name', 'appointzilla'); ?><br>Like Eg: <br>Gmail = smtp.gmail.com, <br>Yahoo = smtp.yahoo.com" ><i class="icon-question-sign"></i></a>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr id="smtpdetails3" style="display:none;">
                            <th scope="row">&nbsp;</th>
                            <td scope="row"><?php _e('Port Number', 'appointzilla'); ?></td>
                            <td><strong>:</strong></td>
                            <td><input name="portno" type="text" id="portno" value="<?php if(isset($EmailDetails['portno'])) { echo $EmailDetails['portno']; } ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('SMTP Port Number', 'appointzilla'); ?><br>Gmail & Yahoo Port Number = 465" ><i class="icon-question-sign"></i></a></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr id="smtpdetails4" style="display:none;">
                            <th scope="row">&nbsp;</th>
                            <td scope="row"><?php _e('Email', 'appointzilla'); ?></td>
                            <td><strong>:</strong></td>
                            <td><input name="smtpemail" type="text" id="smtpemail" value="<?php if(isset($EmailDetails['smtpemail'])) { echo $EmailDetails['smtpemail']; } ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Admin SMTP Email', 'appointzilla'); ?>" ><i class="icon-question-sign"></i></a></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr id="smtpdetails5" style="display:none;">
                            <th scope="row">&nbsp;</th>
                            <td scope="row"><?php _e('Password', 'appointzilla'); ?></td>
                            <td><strong>:</strong></td>
                            <td><input name="password" type="password" id="password" value="<?php if(isset($EmailDetails['password'])) { echo $EmailDetails['password']; } ?>" />&nbsp;<a href="#" rel="tooltip" title="<?php _e('Admin SMTP Email Password', 'appointzilla'); ?>"><i class="icon-question-sign"></i></a></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

                        <tr>
                            <th colspan="2" scope="row">&nbsp;</th>
                            <td>&nbsp;</td>
                            <td>
                                <button name="save-notification-settings" class="btn btn-success" type="submit" id="save-notification-settings" onclick="return SaveNotificationSettings('save-notification-settings');"><i class="fa fa-save"></i> <?php _e('Save', 'appointzilla'); ?></button>
                                <div id="loading-img-notification-settings" style="display: none;"><?php _e("Saving", "appointzilla"); ?>...<i class="fa fa-spinner fa-spin fa-2x"></i></div>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </fieldset>
            </div>

            <!--notification message-->
            <div id="notification-message" class="tab-pane fade">
                <fieldset>
                    <legend><?php _e("Manage Notification Message", "appointzilla"); ?></legend>

                        <!--notify admin on new appointment-->
                        <p><strong><?php _e("Notify Admin On New Appointment", "appointzilla"); ?></strong></p>
                        <p><?php _e("Subject", "appointzilla"); ?></p>
                        <input type="text" id="new-appointment-admin-subject" name="new-appointment-admin-subject" value="<?php echo get_option("new_appointment_admin_subject"); ?>" style="width: 470px;">
                        <p><?php _e("Message Body", "appointzilla"); ?></p>
                        <textarea id="new-appointment-admin-body" name="new-appointment-admin-body" style="width: 470px; height: 280px;"><?php echo get_option("new_appointment_admin_body"); ?></textarea><br>
                        <button name="save-message" class="btn btn-success" id="save-message" onclick="return SaveNotificationMessage('new-appointment-admin-message');"><i class="fa fa-save"></i> <?php _e('Save', 'appointzilla'); ?></button>
                        <div id="loading-img-new-appointment-admin-message" style="display: none;"><?php _e("Saving", "appointzilla"); ?>...<i class="fa fa-spinner fa-spin fa-2x"></i></div>
                        <hr>

                        <!--notify client on new appointment-->
                        <p><strong><?php _e("Notify Client On New Appointment", "appointzilla"); ?></strong></p>
                        <p><?php _e("Subject", "appointzilla"); ?></p>
                        <input type="text" id="new-appointment-client-subject" name="new-appointment-client-subject" value="<?php echo get_option("new_appointment_client_subject"); ?>" style="width: 470px;">
                        <p><?php _e("Message Body", "appointzilla"); ?></p>
                        <textarea id="new-appointment-client-body" name="new-appointment-client-body" style="width: 470px; height: 280px;"><?php echo get_option("new_appointment_client_body"); ?></textarea><br>
                        <button name="save-message" class="btn btn-success" id="save-message" onclick="return SaveNotificationMessage('new-appointment-client-message');"><i class="fa fa-save"></i> <?php _e('Save', 'appointzilla'); ?></button>
                        <div id="loading-img-new-appointment-client-message" style="display: none;"><?php _e("Saving", "appointzilla"); ?>...<i class="fa fa-spinner fa-spin fa-2x"></i></div>
                        <hr>


                        <!--notify client on approve appointment-->
                        <p><strong><?php _e("Notify Client On Approve Appointment", "appointzilla"); ?></strong></p>
                        <p><?php _e("Subject", "appointzilla"); ?></p>
                        <input type="text" id="approve-appointment-client-subject" name="approve-appointment-client-subject" value="<?php echo get_option("approve_appointment_client_subject"); ?>" style="width: 470px;">
                        <p><?php _e("Message Body", "appointzilla"); ?></p>
                        <textarea id="approve-appointment-client-body" name="approve-appointment-client-body" style="width: 470px; height: 280px;"><?php echo get_option("approve_appointment_client_body"); ?></textarea><br>
                        <button name="save-message" class="btn btn-success" id="save-message" onclick="return SaveNotificationMessage('approve-appointment-client-message');"><i class="fa fa-save"></i> <?php _e('Save', 'appointzilla'); ?></button>
                        <div id="loading-img-approve-appointment-client-message" style="display: none;"><?php _e("Saving", "appointzilla"); ?>...<i class="fa fa-spinner fa-spin fa-2x"></i></div>
                        <hr>

                        <!--notify client on cancel appointment-->
                        <p><strong><?php _e("Notify Client On Cancel Appointment", "appointzilla"); ?></strong></p>
                        <p><?php _e("Subject", "appointzilla"); ?></p>
                        <input type="text" id="cancel-appointment-client-subject" name="cancel-appointment-client-subject" value="<?php echo get_option("cancel_appointment_client_subject"); ?>" style="width: 470px;">
                        <p><?php _e("Message Body", "appointzilla"); ?></p>
                        <textarea id="cancel-appointment-client-body" name="cancel-appointment-client-body" style="width: 470px; height: 280px;"><?php echo get_option("cancel_appointment_client_body"); ?></textarea><br>
                        <button name="save-message" class="btn btn-success" id="save-message" onclick="return SaveNotificationMessage('cancel-appointment-client-message');"><i class="fa fa-save"></i> <?php _e('Save', 'appointzilla'); ?></button>
                        <div id="loading-img-cancel-appointment-client-message" style="display: none;"><?php _e("Saving", "appointzilla"); ?>...<i class="fa fa-spinner fa-spin fa-2x"></i></div>
                        <hr>
                </fieldset>
            </div>

        </div>
        <!--tabs-body-end-->
    </div>
</div>

<!--tabs js lib-->
<script src="<?php echo plugins_url('/bootstrap-assets/js/bootstrap.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/bootstrap-assets/js/bootstrap.min.js', __FILE__); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/bootstrap-assets/js/bootstrap-tab.js', __FILE__); ?>" type="text/javascript"></script>
<!--validation js-->
<script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>

<style type="text/css">
    .error{  color:#FF0000; }
</style>

<!--validation js lib-->
<script src="<?php echo plugins_url('/js/jquery.min.js', __FILE__); ?>" type="text/javascript"></script>

<script type="text/javascript">
    /**
     * Calendar Settings Validation & Ajax PostData
     */
    function SaveCalendarSettings(Action) {
        jQuery(".error").hide();
        var CalendarSlotTime = jQuery("#calendar_slot_time").val();
        var DayStartTime = jQuery("#day_start_time").val();
        var DayEndTime = jQuery("#day_end_time").val();
        var CalendarView = jQuery("#calendar_view").val();
        var CalendarStartDay = jQuery("#calendar_start_day").val();
        var BookingButtonText = jQuery("#booking_button_text").val();
        var BookingTimeSlot = jQuery("#booking_time_slot").val();
        var ServiceCost = jQuery("#show_service_cost").val();
        var ServiceDuration = jQuery("#show_service_duration").val();
        var BookingInstructions = jQuery("#apcal_booking_instructions").val();

        var PostData1 = "Action=" + Action + "&CalendarSlotTime=" + CalendarSlotTime + "&DayStartTime=" + DayStartTime + "&DayEndTime=" + DayEndTime;
        var PostData2 = "&CalendarView=" + CalendarView + "&CalendarStartDay=" + CalendarStartDay + "&BookingButtonText=" + BookingButtonText;
        var PostData3 = "&BookingTimeSlot=" + BookingTimeSlot + "&ServiceCost=" + ServiceCost + "&ServiceDuration=" + ServiceDuration + "&BookingInstructions=" + BookingInstructions;
        var PostData = PostData1 + PostData2 + PostData3;
        jQuery("#loading-img-calendar-settings").show();
        jQuery.ajax({
            dataType : 'html',
            type: 'POST',
            url : location.href,
            cache: false,
            data : PostData,
            complete : function() { },
            success: function() {
                jQuery("#loading-img-calendar-settings").hide();
                alert("<?php _e("Calendar settings successfully saved.", "appointzilla"); ?>");
            }
        });
    }

    /**
     * Notification Settings Page On Load Settings
     */
    jQuery(document).ready(function() {
        //on-load if check enable
        var emailtype = jQuery('#emailtype').val();
        if(jQuery('#enable').is(':checked')) {
            jQuery('#emailtype').attr("disabled", false);        //enable
            if(emailtype == 'wpmail') {
                jQuery('#smtpdetails1').hide();
                jQuery('#smtpdetails2').hide();
                jQuery('#smtpdetails3').hide();
                jQuery('#smtpdetails4').hide();
                jQuery('#smtpdetails5').hide();

                jQuery('#phpmaildetails1').hide();
                jQuery('#phpmaildetails2').hide();

                jQuery('#wpmaildetails1').show();
                jQuery('#wpmaildetails2').show();
            }

            if(emailtype == 'phpmail') {
                jQuery('#smtpdetails1').hide();
                jQuery('#smtpdetails2').hide();
                jQuery('#smtpdetails3').hide();
                jQuery('#smtpdetails4').hide();
                jQuery('#smtpdetails5').hide();

                jQuery('#phpmaildetails1').show();
                jQuery('#phpmaildetails2').show();

                jQuery('#wpmaildetails1').hide();
                jQuery('#wpmaildetails2').hide();
            }
            if(emailtype == 'smtp') {
                jQuery('#smtpdetails1').show();
                jQuery('#smtpdetails2').show();
                jQuery('#smtpdetails3').show();
                jQuery('#smtpdetails4').show();
                jQuery('#smtpdetails5').show();

                jQuery('#phpmaildetails1').hide();
                jQuery('#phpmaildetails2').hide();

                jQuery('#wpmaildetails1').hide();
                jQuery('#wpmaildetails2').hide();
            }
        } else {
            jQuery('#emailtype').attr("disabled", true);
        }

        //on-click
        jQuery('#enable').click(function(){

            jQuery(".error").hide();

            if (jQuery(this).is(':checked')) {
                jQuery('#emailtype').attr("disabled", false);
            }  else {
                jQuery('#emailtype').attr("disabled", true);
            }
        });

        //onchange email type
        jQuery('#emailtype').change(function(){
            var emailtype = jQuery('#emailtype').val();
            if(jQuery('#enable').is(':checked') && emailtype)  {
                if(emailtype=='wpmail') {
                    jQuery('#smtpdetails1').hide();
                    jQuery('#smtpdetails2').hide();
                    jQuery('#smtpdetails3').hide();
                    jQuery('#smtpdetails4').hide();
                    jQuery('#smtpdetails5').hide();

                    jQuery('#phpmaildetails1').hide();
                    jQuery('#phpmaildetails2').hide();

                    jQuery('#wpmaildetails1').show();
                    jQuery('#wpmaildetails2').show();
                }

                if(emailtype == 'phpmail') {
                    jQuery('#smtpdetails1').hide();
                    jQuery('#smtpdetails2').hide();
                    jQuery('#smtpdetails3').hide();
                    jQuery('#smtpdetails4').hide();
                    jQuery('#smtpdetails5').hide();

                    jQuery('#phpmaildetails1').show();
                    jQuery('#phpmaildetails2').show();

                    jQuery('#wpmaildetails1').hide();
                    jQuery('#wpmaildetails2').hide();
                }
                if(emailtype == 'smtp') {
                    jQuery('#smtpdetails1').show();
                    jQuery('#smtpdetails2').show();
                    jQuery('#smtpdetails3').show();
                    jQuery('#smtpdetails4').show();
                    jQuery('#smtpdetails5').show();

                    jQuery('#phpmaildetails1').hide();
                    jQuery('#phpmaildetails2').hide();

                    jQuery('#wpmaildetails1').hide();
                    jQuery('#wpmaildetails2').hide();
                }
            }
        });
    });

    /**
     * Notification Settings Validation & Ajax PostData
     */
    function SaveNotificationSettings(Action) {

        jQuery(".error").hide();
        //enable
        if (jQuery('#enable').is(':checked')) {
            var enable = "on";
        } else {
            var enable = "off";
        }

        var emailtype = jQuery('#emailtype').val();
        if(emailtype == 0) {
            jQuery("#emailtype").after('<span class="error">&nbsp;<br><strong><?php _e('Select email type' ,'appointzilla'); ?></strong></span>');
            return false;
        }

        //wp-email
        if(emailtype == 'wpmail') {
            var wpemail = jQuery('#wpemail').val();
            if(wpemail == '') {
                jQuery("#wpemail").after('<span class="error">&nbsp;<br><strong><?php _e('Wp email required.' ,'appointzilla'); ?></strong></span>');
                return false;
            } else {
                var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                if(regex.test(wpemail) == false ) {
                    jQuery("#wpemail").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid wp email.' ,'appointzilla'); ?></strong></span>');
                    return false;
                }
            }
            var PostData = "Action=" + Action + "&emailtype=" + emailtype + "&wpemail=" + wpemail + "&enable=" + enable;
        }

        //php-email
        if(emailtype == 'phpmail') {
            var phpemail = jQuery('#phpemail').val();
            if(phpemail == '') {
                jQuery("#phpemail").after('<span class="error">&nbsp;<br><strong><?php _e('Php email required.' ,'appointzilla'); ?></strong></span>');
                return false;
            } else {
                var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                if(regex.test(phpemail) == false ) {
                    jQuery("#phpemail").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid php email.' ,'appointzilla'); ?></strong></span>');
                    return false;
                }
            }
            var PostData = "Action=" + Action + "&emailtype=" + emailtype + "&phpemail=" + phpemail + "&enable=" + enable;
        }

        //smtp
        if(emailtype == 'smtp') {
            var hostname = jQuery('#hostname').val();
            if(hostname == '') {
                jQuery("#hostname").after('<span class="error">&nbsp;<br><strong><?php _e('Host name required.' ,'appointzilla'); ?></strong></span>');
                return false;
            }

            var portno = jQuery('#portno').val();
            if(portno == '') {
                jQuery("#portno").after('<span class="error">&nbsp;<br><strong><?php _e('Port number required.' ,'appointzilla'); ?></strong></span>');
                return false;
            }
            var portnoRes = isNaN(portno);
            if(portnoRes == true) {
                jQuery("#portno").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid port number.' ,'appointzilla'); ?></strong></span>');
                return false;
            }

            var smtpemail = jQuery('#smtpemail').val();
            if(smtpemail == '') {
                jQuery("#smtpemail").after('<span class="error">&nbsp;<br><strong><?php _e('Email required' ,'appointzilla'); ?></strong></span>');
                return false;
            } else {
                var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                if(regex.test(smtpemail) == false ) {
                    jQuery("#smtpemail").after('<span class="error">&nbsp;<br><strong><?php _e('Invalid smtp email.' ,'appointzilla'); ?></strong></span>');
                    return false;
                }
            }

            var password = jQuery('#password').val();
            if(password == '') {
                jQuery("#password").after('<span class="error">&nbsp;<br><strong><?php _e('Password required.' ,'appointzilla'); ?></strong></span>');
                return false;
            }
            var PostData = "Action=" + Action + "&emailtype=" + emailtype + "&hostname=" + hostname + "&portno=" + portno + "&smtpemail=" + smtpemail + "&password=" + password + "&enable=" + enable;
        }

        jQuery('#enable').is(':checked')
        jQuery("#loading-img-notification-settings").show();
        jQuery.ajax({
            dataType : 'html',
            type: 'POST',
            url : location.href,
            cache: false,
            data : PostData,

            complete : function() { },
            success: function() {
                jQuery("#loading-img-notification-settings").hide();
                alert("<?php _e("Notification settings successfully saved.", "appointzilla"); ?>");
            }
        });
    }

    /***
     *  Notification Message Validation & Ajax PostData
     */
    function SaveNotificationMessage(Action) {
        //new app admin msg
        if(Action == "new-appointment-admin-message"){
            var Subject = jQuery("#new-appointment-admin-subject").val();
            var Body = jQuery("#new-appointment-admin-body").val();
            var PostData = "Action=" + Action + "&Subject=" + Subject + "&Body=" + Body;
        }

        //new app client msg
        if(Action == "new-appointment-client-message"){
            var Subject = jQuery("#new-appointment-client-subject").val();
            var Body = jQuery("#new-appointment-client-body").val();
            var PostData = "Action=" + Action + "&Subject=" + Subject + "&Body=" + Body;
        }

        //approve app client msg
        if(Action == "approve-appointment-client-message"){
            var Subject = jQuery("#approve-appointment-client-subject").val();
            var Body = jQuery("#approve-appointment-client-body").val();
            var PostData = "Action=" + Action + "&Subject=" + Subject + "&Body=" + Body;
        }

        //cancel app client msg
        if(Action == "cancel-appointment-client-message"){
            var Subject = jQuery("#cancel-appointment-client-subject").val();
            var Body = jQuery("#cancel-appointment-client-body").val();
            var PostData = "Action=" + Action + "&Subject=" + Subject + "&Body=" + Body;
        }

        jQuery("#loading-img-" + Action).show();
        jQuery.ajax({
            dataType : 'html',
            type: 'POST',
            url : location.href,
            cache: false,
            data : PostData,
            complete : function() { },
            success: function() {
                jQuery("#loading-img-" + Action).hide();
                alert("<?php _e("Notification Message successfully saved.", "appointzilla"); ?>");
            }
        });
    }
</script>

<?php //Saving Settings
if(isset($_POST['Action'])) {
    echo $Action = $_POST['Action'];
    print_r($_POST);

    /**
     * Saving Calendar Settings
     */
    if($Action == "save-calendar-settings") {
        $CalendarSettingsArray = array(
            'calendar_slot_time' => $_POST['CalendarSlotTime'],
            'day_start_time' => $_POST['DayStartTime'],
            'day_end_time' => $_POST['DayEndTime'],
            'calendar_view' => $_POST['CalendarView'],
            'calendar_start_day' => $_POST['CalendarStartDay'],
            'booking_button_text' => $_POST['BookingButtonText'],
            'booking_time_slot' => $_POST['BookingTimeSlot'],
            'show_service_cost' => $_POST['ServiceCost'],
            'show_service_duration' => $_POST['ServiceDuration'],
            'apcal_booking_instructions' => $_POST['BookingInstructions'],
        );
        update_option('apcal_calendar_settings', serialize($CalendarSettingsArray));
    }

    /**
     * Saving Notification Settings
     */
    if($Action == "save-notification-settings") {
        if(isset($_POST['enable']) == 'on') {
            //wp-mail
            if($_POST['emailtype'] == 'wpmail') {
                update_option('emailstatus', $_POST['enable']);
                update_option('emailtype', $_POST['emailtype']);

                $EmailDetails =  array ( 'wpemail' => $_POST['wpemail'] );
                update_option( 'emaildetails', serialize($EmailDetails));
            }

            //php-mail
            if($_POST['emailtype'] == 'phpmail')
            {
                update_option('emailstatus', $_POST['enable']);
                update_option('emailtype', $_POST['emailtype']);
                $EmailDetails =  array ( 'phpemail' => $_POST['phpemail']);
                update_option('emaildetails', serialize($EmailDetails));
            }

            //smtp mail
            if($_POST['emailtype'] == 'smtp') {
                update_option('emailstatus', $_POST['enable']);
                update_option('emailtype', $_POST['emailtype']);
                $EmailDetails =  array ( 'hostname' => $_POST['hostname'],
                    'portno' => $_POST['portno'],
                    'smtpemail' => $_POST['smtpemail'],
                    'password' => $_POST['password'],
                );
                update_option('emaildetails', serialize($EmailDetails));
            }
        } else {
            delete_option('emailstatus');
            delete_option('emailtype');
            delete_option('emaildetails');
        }
    }

    /**
     * Saving Notification Message
     */
    if($Action == "new-appointment-admin-message") {
        $Subject = $_POST['Subject'];
        $Body = $_POST['Body'];
        update_option("new_appointment_admin_subject", $Subject);
        update_option("new_appointment_admin_body", $Body);
    }

    if($Action == "new-appointment-client-message") {
        $Subject = $_POST['Subject'];
        $Body = $_POST['Body'];
        update_option("new_appointment_client_subject", $Subject);
        update_option("new_appointment_client_body", $Body);
    }

    if($Action == "approve-appointment-client-message") {
        $Subject = $_POST['Subject'];
        $Body = $_POST['Body'];
        update_option("approve_appointment_client_subject", $Subject);
        update_option("approve_appointment_client_body", $Body);
    }

    if($Action == "cancel-appointment-client-message") {
        $Subject = $_POST['Subject'];
        $Body = $_POST['Body'];
        update_option("cancel_appointment_client_subject", $Subject);
        update_option("cancel_appointment_client_body", $Body);
    }
}
?>