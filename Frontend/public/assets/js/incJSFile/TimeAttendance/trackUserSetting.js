let GLOBAL_LOCATION = 0;
let GLOBAL_NETWORK = 0;
let VISABLE;
let USER_SETTING_NAME = "";
let MONITORONOFF;
let IS_MANUAL = 0;
let IS_ATTENDANCE_OVERRIDE = 0;
let IS_USB_DISABLE = 0;
let IS_SYSTEM_LOCK = 0;
let IS_MOBILE_DATA = 0;
function CancelButton() {
    $('#SaveButton').attr('disabled', false);
}

//onready function to append the user details
$(document).ready(function () {
    settingsApplied($.parseJSON($("#UserData").attr('name')));
    getLogo();
});

// For setting the track-user setting on save
function settingsApplied(data) {
    $('#errorLatLng').html("");
    if (data.code === 200) {
        // 1-org 2-group   3-custom
        if ([1, 2, 3].includes(data.data.tracking_rule_type)) {
            var UserData = data.data.custom_tracking_rule;
            UserData.userBlock == "1" ? $("#userBlock").prop('checked', true):$("#userBlock").prop('checked', false);
            UserData.usbDisable == "1" ? $("#USB_feature").prop('checked', true):$("#USB_feature").prop('checked', false);
            if(UserData.userBlock != undefined && UserData.userBlock != null && UserData.block.email != undefined && UserData.block.email != null && UserData.block.contact != undefined && UserData.block.contact != null){
                $('#userBlockEmail').val(UserData.block.email);
                $('#userBlockContact').val(UserData.block.contact);
            }
            $("#roll_option option[id='" + UserData.system.type + "']").attr('selected', true);
            UserData.system.visibility === true ? ($("#visable").attr('checked', true), $('#BreakTime').attr('disabled', false)) : ($("#stealth").attr('checked', true), VISABLE = false, $('#BreakTime').attr('disabled', true), disableTrackScenario(1));
            UserData.screenshot.employeeAccessibility === "true" ? $("#AccessWatchingSS").attr('checked', "checked") : $("#AccessWatchingSS").attr('checked', false);
            UserData.screenshot.employeeCanDelete === "true" ? $("#deleteAccessSS").attr('checked', true) : $("#deleteAccessSS").attr('checked', false);
            // UserData.task.employeeCanCreateTask === "true" ? $("#AllowTaskEmployee").attr('checked', true) : $("#AllowTaskEmployee").attr('checked', false);
            $("#SSFrequencySelected option[id='" + UserData.screenshot.frequencyPerHour + "']").attr("selected", true);
            $("#BreakTime option[id='" + UserData.breakInMinute + "']").attr('selected', true);
            $("#IdleTime option[id='" + UserData.idleInMinute + "']").attr('selected', true);
            $("#inactiveTime option[id='" + UserData.system.inactiveTime + "']").attr('selected', true);
            $("#idleTimeForTimeSheet").val( UserData.timesheetIdleTime);
            (UserData.system.autoUpdate == "0") ? ($('#autoUpdates_id').parent().removeClass('toggle btn btn-xs btn-primary').addClass('toggle btn btn-xs btn-light off')) : ($('#autoUpdates_id').parent().removeClass('toggle btn btn-xs btn-light off').addClass('toggle btn btn-xs btn-primary')); // or 'checked'
            UserData.features.application_usage == "1" ? ($("#AppRadio0").prop('checked', false), $("#AppRadio1").prop('checked', 'checked')) : ($("#AppRadio1").prop('checked', false), $("#AppRadio0").prop('checked', 'checked'));

            UserData.features.keystrokes == "1" ? ($("#KeyStrokeRadio0").prop('checked', false), $("#KeyStrokeRadio1").prop('checked', true)) : ($("#KeyStrokeRadio1").prop('checked', false), $("#KeyStrokeRadio0").prop('checked', true));
            UserData.features.web_usage == "1" ? ($("#WU0").prop('checked', false), $("#WU1").prop('checked', true)) : ($("#WU1").prop('checked', false), $("#WU0").prop('checked', true));
            UserData.features.screenshots == "1" ? ($("#SS0").prop('checked', false), $("#SS1").prop('checked', true), $('#SSFrequencySelected').prop('disabled', false)) : ($("#SS1").prop('checked', false), $("#SS0").prop('checked', true), $('#SSFrequencySelected').prop('disabled', true));
            UserData.manual_clock_in == "1" ? ($("#manual_clock_in").prop('checked', true), IS_MANUAL = 1, $("#manual_clock_out").prop('checked', false)) : ($("#manual_clock_out").prop('checked', true), $("#manual_clock_in").prop('checked', false));
            UserData.usbDisable == "1" ? ($("#usb_enable").prop('checked', true), IS_USB_DISABLE = 1, $("#usb_disable").prop('checked', false)) : ($("#usb_disable").prop('checked', true), $("#usb_enable").prop('checked', false));
            UserData.systemLock == "1" ? ($("#system_lock_enable").prop('checked', true), IS_SYSTEM_LOCK = 1, $("#system_lock_disable").prop('checked', false)) : ($("#system_lock_disable").prop('checked', true), $("#system_lock_enable").prop('checked', false));
            UserData.isSilahMobileGeoLocation == "1" ? ($("#mobile_data_enable").prop('checked', true), IS_MOBILE_DATA = 1, $("#mobile_data_disable").prop('checked', false)) : ($("#mobile_data_disable").prop('checked', true), $("#mobile_data_enable").prop('checked', false));
            UserData.is_attendance_override == "1" ? ($("#attendance_in").prop('checked', true), IS_ATTENDANCE_OVERRIDE = 1, $("#attendance_out").prop('checked', false)) : ($("#attendance_out").prop('checked', true), $("#attendance_in").prop('checked', false));
            // Tracking features applied
            UserData.features.screencast == "1" ? ($("#ScreenCast0").prop('checked', false), $("#ScreenCast1").prop('checked', 'checked')) : ($("#ScreenCast1").prop('checked', false), $("#ScreenCast0").prop('checked', 'checked'));
            UserData.features.realTimeTrack == "1" ? ($("#real_time_track_enable").prop('checked', true), $("#real_time_track_disable").prop('checked', false)) : ($("#real_time_track_disable").prop('checked', true), $("#real_time_track_enable").prop('checked', false));
            UserData.features.location == "1" ? ($("#location_enable").prop('checked', true), $("#location_disable").prop('checked', false)) : ($("#location_disable").prop('checked', true), $("#location_enable").prop('checked', false));
            // DLP features applied
            UserData.dlpFeatures?.web_block == "1" ? ($("#webBlockRadio0").prop('checked', false), $("#webBlockRadio1").prop('checked', 'checked')) : ($("#webBlockRadio1").prop('checked', false), $("#webBlockRadio0").prop('checked', 'checked'), $(".WebBlockModalBtn").css('display', 'none'));
            UserData.dlpFeatures?.app_block == "1" ? ($("#appBlockRadio0").prop('checked', false), $("#appBlockRadio1").prop('checked', 'checked')) : ($("#appBlockRadio1").prop('checked', false), $("#appBlockRadio0").prop('checked', 'checked'), $(".AppBlockModalBtn").css('display', 'none'));
            UserData.dlpFeatures?.usb_detec == "1" ? ($("#usbDetecRadio0").prop('checked', false), $("#usbDetecRadio1").prop('checked', 'checked')) : ($("#usbDetecRadio1").prop('checked', false), $("#usbDetecRadio0").prop('checked', 'checked'), $("#usbBlock").css('display', 'none'), $(".usbDetecInfo").css('display', 'block'));
            UserData.dlpFeatures?.usb_block == "1" ? ($("#usbBlockRadio0").prop('checked', false), $("#usbBlockRadio1").prop('checked', 'checked')) : ($("#usbBlockRadio1").prop('checked', false), $("#usbBlockRadio0").prop('checked', 'checked'));
            UserData.dlpFeatures?.blue_detec == "1" ? ($("#blueDetecRadio0").prop('checked', false), $("#blueDetecRadio1").prop('checked', 'checked')) : ($("#blueDetecRadio1").prop('checked', false), $("#blueDetecRadio0").prop('checked', 'checked'), $("#bluetoothBloc").css('display', 'none'), $(".bluetoothInfo").css('display', 'block'));
            UserData.dlpFeatures?.blue_block == "1" ? ($("#blueBlockRadio0").prop('checked', false), $("#blueBlockRadio1").prop('checked', 'checked')) : ($("#blueBlockRadio1").prop('checked', false), $("#blueBlockRadio0").prop('checked', 'checked'));
            UserData.dlpFeatures?.clip_detec == "1" ? ($("#clipDetecRadio0").prop('checked', false), $("#clipDetecRadio1").prop('checked', 'checked')) : ($("#clipDetecRadio1").prop('checked', false), $("#clipDetecRadio0").prop('checked', 'checked'), $("#clipboardhBlock").css('display', 'none'), $(".clipboardInfo").css('display', 'block'));
            UserData.dlpFeatures?.clip_block == "1" ? ($("#clipBlockRadio0").prop('checked', false), $("#clipBlockRadio1").prop('checked', 'checked')) : ($("#clipBlockRadio1").prop('checked', false), $("#clipBlockRadio0").prop('checked', 'checked'));
            UserData.dlpFeatures?.system_lock == "1" ? ($("#sysLocRadio0").prop('checked', false), $("#sysLocRadio1").prop('checked', 'checked')) : ($("#sysLocRadio1").prop('checked', false), $("#sysLocRadio0").prop('checked', 'checked'));

            if (Number(ENV_SPECIAL_IDLE_ADMIN) === 1) {
            }
            switch (UserData.trackingMode) {
                case "unlimited" : {
                    setTimeout(function () {
                        $("#Scenario2").attr("checked", false);
                        $("#Scenario1").attr("checked", true);
                        $("#UnlimitedActiveTab").attr('class', 'nav-link active');
                    }, 100);
                    $("#FixedActiveTab").attr('class', 'nav-link');
                    if (UserData.system.visibility === "true") $("#ProjectActiveTab,#ManualActiveTab").attr('class', 'nav-link');
                    $("#NetworkActiveTab").attr('class', 'nav-link');
                    $("#Unlimited").attr('class', 'container tab-pane active');
                    $("#Fixed,#NetworkBased,#ManualClockedIn,#projectBased").attr('class', 'container tab-pane fade');
                    if (UserData.tracking.length != 0) {
                        $.each(UserData.tracking?.unlimited?.day?.split(","), function (days, i) {
                            $("#Unlimited" + i + "").attr('checked', 'checked');
                        });
                    }
                }
                    break;
                case "fixed" : {
                    setTimeout(function () {
                        $("#Scenario2").attr("checked", true);
                        //for active the tab icon  for UI
                        $("#UnlimitedActiveTab").attr('class', 'nav-link ');
                        $("#FixedActiveTab").attr('class', 'nav-link active');
                    }, 100);

                    if (UserData.system.visibility === "true") $("#ProjectActiveTab,#ManualActiveTab").attr('class', 'nav-link');
                    $("#NetworkActiveTab").attr('class', 'nav-link');
                    //for active the tab inputs
                    $("#Unlimited,#NetworkBased,#ManualClockedIn,#ProjectBased").attr('class', 'container tab-pane fade');
                    $("#Fixed").attr('class', 'container tab-pane active');
                    //    for append the data in selected user
                    $.each(UserData.tracking.fixed, function (index, value) {
                        switch (index) {
                            case "mon" : {
                                if (value.status.toString() === "true") {
                                    $("#FixedMonday").attr("checked", true);
                                    $("#MONDAYstartTime").val(value.time.start);
                                    $("#MONDAYendTime").val(value.time.end);
                                }
                            }
                                break;
                            case "tue" : {
                                if (value.status.toString() === "true") {
                                    $("#FixedTuesday").attr("checked", true);
                                    $("#TUESDAYstartTime").val(value.time.start);
                                    $("#TUESDAYendTime").val(value.time.end);
                                }
                            }
                                break;
                            case "wed" : {
                                if (value.status.toString() === "true") {
                                    $("#FixedWEDNESDAY").attr("checked", true);
                                    $("#WEDNESDAYstartTime").val(value.time.start);
                                    $("#WEDNESDAYendTime").val(value.time.end);
                                }
                            }
                                break;
                            case "thu": {
                                if (value.status.toString() === "true") {
                                    $("#FixedTHURSDAY").attr("checked", true);
                                    $("#THURSDAYstartTime").val(value.time.start);
                                    $("#THURSDAYendTime").val(value.time.end);
                                }
                            }
                                break;
                            case "fri": {
                                if (value.status.toString() === "true") {
                                    $("#FixedFRIDAY").attr("checked", true);
                                    $("#FRIDAYstartTime").val(value.time.start);
                                    $("#FRIDAYendTime").val(value.time.end);
                                }
                            }
                                break;
                            case "sat": {
                                if (value.status.toString() === "true") {
                                    $("#FixedSATURDAY").attr("checked", true);
                                    $("#SATURDAYstartTime").val(value.time.start);
                                    $("#SATURDAYendTime").val(value.time.end);
                                }
                            }
                                break;
                            case "sun": {
                                if (value.status.toString() === "true") {
                                    $("#FixedSUNDAY").attr("checked", true);
                                    $("#SUNDAYstartTime").val(value.time.start);
                                    $("#SUNDAYendTime").val(value.time.end);
                                }
                            }
                        }
                    })
                }
                    break;
                case "networkBased" : {
                    if (add_multiple_network_track && UserData.tracking.networkBased) {
                        $("#Scenario6").attr("checked", true);
                        //for active the tab icon  for UI
                        $("#UnlimitedActiveTab, #FixedActiveTab, #ProjectActiveTab, #ManualActiveTab").attr('class', 'nav-link');
                        $("#NetworkActiveTab").attr('class', 'nav-link active');
                        //for active the tab inputs
                        $("#Unlimited, #Fixed, #ManualClockedIn, #ProjectBased").attr('class', 'container tab-pane fade');
                        $("#NetworkBased").attr('class', 'container tab-pane active');
                        GLOBAL_NETWORK = 0;
                        removeRowNet();
                        if (typeof UserData.tracking.networkBased === "object" && UserData.tracking.networkBased.length) {
                            Object.keys(UserData.tracking.networkBased).forEach((key, i) => {
                                if (i !== 0) addGeoRowloc(1);
                                $(`#MACaddress${i}`).val(UserData.tracking.networkBased[key].ipAddress);
                                $(`#NetworkName${i}`).val(UserData.tracking.networkBased[key].networkName);
                                $(`#officeNetwork${i}`).prop("checked", UserData.tracking.networkBased[key].officeNetwork === "true");
                            });
                        } else {
                            if (UserData.tracking.networkBased !== null) {
                                $("#MACaddress0").val(UserData.tracking.networkBased.ipAddress);
                                UserData.tracking.networkBased.networkName !== undefined ? $("#NetworkName0").val(UserData.tracking.networkBased.networkName) : $("#NetworkName0").val("--");
                                UserData.tracking.networkBased.officeNetwork === "true" ? $("#officeNetwork0").attr("checked", true) : $("#officeNetwork0").attr("checked", false);
                            }
                        }

                    } else {
                        $("#Scenario6").attr("checked", true);
                        //for active the tab icon  for UI
                        $("#UnlimitedActiveTab, #FixedActiveTab, #ProjectActiveTab, #ManualActiveTab").attr('class', 'nav-link');
                        $("#NetworkActiveTab").attr('class', 'nav-link active');
                        //for active the tab inputs
                        $("#Unlimited, #Fixed, #ManualClockedIn, #ProjectBased").attr('class', 'container tab-pane fade');
                        if (UserData.tracking.networkBased !== null) {
                            $("#NetworkBased").attr('class', 'container tab-pane active');
                            $("#MACaddress0").val(UserData.tracking.networkBased.ipAddress);
                            UserData.tracking.networkBased.networkName !== undefined ? $("#NetworkName0").val(UserData.tracking.networkBased.networkName) : $("#NetworkName0").val("--");
                            UserData.tracking.networkBased.officeNetwork === "true" ? $("#officeNetwork0").attr("checked", true) : $("#officeNetwork0").attr("checked", false);
                        }
                    }
                }
                    break;
                case "manual" : {
                    $("#Scenario4").attr("checked", true);
                    //for active the tab icon  for UI
                    $("#UnlimitedActiveTab, #FixedActiveTab, #ProjectActiveTab, #NetworkActiveTab").attr('class', 'nav-link');
                    $("#ManualActiveTab").attr('class', 'nav-link active');
                    //for active the tab inputs
                    $("#Unlimited, #Fixed, #NetworkBased, #ProjectBased").attr('class', 'container tab-pane fade');
                    $("#ManualClockedIn").attr('class', 'container tab-pane active');
                }
                    break;
                case "projectBased" : {
                    $("#Scenario5").attr("checked", true);
                    //for active the tab icon  for UI
                    $("#UnlimitedActiveTab, #FixedActiveTab, #ManualActiveTab, #NetworkActiveTab").attr('class', 'nav-link');
                    $("#ProjectActiveTab").attr('class', 'nav-link active');
                    //for active the tab inputs
                    $("#Unlimited, #Fixed, #NetworkBased, #ManualClockedIn").attr('class', 'container tab-pane fade');
                    $("#ProjectBased").attr('class', 'container tab-pane active');
                    UserData.tracking.projectBased.forEach(function (projectBase) {
                        $("#" + projectBase.id).attr("checked", true)
                    });
                }
                    break;
                case "geoLocation" : {
                    $("#Scenario7").attr("checked", true);
                    //for active the tab icon  for UI
                    $("#UnlimitedActiveTab, #FixedActiveTab, #ManualActiveTab, #NetworkActiveTab", "#ProjectActiveTab").attr('class', 'nav-link');
                    $("#GeoLocation").attr('class', 'nav-link active');
                    //for active the tab inputs
                    $("#Unlimited, #Fixed, #NetworkBased, #ManualClockedIn, #ProjectBased").attr('class', 'container tab-pane fade');
                    $("#GeoLocation").attr('class', 'container tab-pane active');
                    //  Appending Geo-Location Data
                    removeRowloc();
                    GLOBAL_LOCATION = 0;
                    if (UserData.tracking.geoLocation != undefined) {
                        if (UserData.tracking.geoLocation.length > 0) {
                            for (var i = 0; i < UserData.tracking.geoLocation.length; i++) {
                                if (i != 0) addRowloc(1);
                                $("#location" + i).val(UserData.tracking.geoLocation[i]["location"]);
                                $('#longitude' + i).val(UserData.tracking.geoLocation[i]["lat"] + ", " + UserData.tracking.geoLocation[i]["lon"]);
                                $('#range' + i).val(UserData.tracking.geoLocation[i]["distance"]);
                                $('#longitude' + i).attr("disabled", false);
                                $('#range' + i).attr("disabled", false);
                            }
                        }
                    } else {
                        $('#location0, #longitude0, #range0').val("");
                        $('#longitude0').attr("disabled", true);
                        $('#range0').attr("disabled", true);
                    }
                }
            }
        }
        if ((data.data.tracking_rule_type == '2' || data.data.tracking_rule_type == '1')) {
            $('#SaveButton').attr('disabled', true);
            $('#AdvanceSaveButton').attr('disabled', true);
            data.data.tracking_rule_type == '2' ? (USER_SETTING_NAME = "" , $("#AppliedSetting option[id='" + data.data.group_id + "']").attr('selected', true)) : (USER_SETTING_NAME = "default", $("#AppliedSetting").val(data.data.tracking_rule_type));
        } else {
            $('#AdvanceSaveButton').attr('disabled', false);
            $('#SaveButton').attr('disabled', false)
        }
        const screen_record = Object.values(UserData.screenRecord).map(value => value).includes(1);
        let videoQualityID= UserData.screenRecord.ultrafast_720_21=='1' ? "vid720" :UserData.screenRecord.ultrafast_1080_21=='1'? 'vid1080': 'vid1280' ;
        $("#"+videoQualityID).attr("selected", true);
        screen_record ? ($("#vd0").prop('checked', false), $("#vd1").prop('checked', true), $('#videoQuality').prop('disabled', false)) : ($("#vd1").prop('checked', false), $("#vd0").prop('checked', true), $('#videoQuality').prop('disabled', true));

    }
}

let trackData = $.parseJSON($("#UserData").attr('name'));

function TrackUserAdvWeb() {
    $('#webMonitorOnlyError, #webSuspendOnvisitError, #webSuspendKeystrokeError, #userWebsitesAddEditInput, #userApplicationsAddEdit,#userBlockEmailError,#userBlockContactError, #userBluetoothAddEditInput').html("");
    $("#userTrackingWebsite,#userTracSuspendWebsiteVisit,#userTracSuspendKeystrokeWebsite").empty();
    if (trackData.code === 200) {
        if (trackData.data.custom_tracking_rule.tracking.domain) {
            if (trackData.data.custom_tracking_rule.tracking.domain.monitorOnly) {
                trackData.data.custom_tracking_rule.tracking.domain.monitorOnly.forEach(function (user) {
                    $('#userTrackingWebsite').append('<option selected> ' + user + ' </option>');
                })
            }
            if (trackData.data.custom_tracking_rule.tracking.domain.suspendMonitorWhenVisited) {
                trackData.data.custom_tracking_rule.tracking.domain.suspendMonitorWhenVisited.forEach(function (user) {
                    $('#userTracSuspendWebsiteVisit').append('<option selected> ' + user + ' </option>');
                })
            }
            if (trackData.data.custom_tracking_rule.tracking.domain.suspendKeystrokesWhenVisited) {
                trackData.data.custom_tracking_rule.tracking.domain.suspendKeystrokesWhenVisited.forEach(function (user) {
                    $('#userTracSuspendKeystrokeWebsite').append('<option selected> ' + user + ' </option>');
                })
            }
            if (trackData.data.custom_tracking_rule.tracking.domain.websiteBlockList) {
                trackData.data.custom_tracking_rule.tracking.domain.websiteBlockList.forEach(function (user) {
                    $('#userWebsitesAddEditInput').append('<option selected> ' + user + ' </option>');
                })
            }
            if (trackData.data.custom_tracking_rule.tracking.domain.appBlockList) {
                trackData.data.custom_tracking_rule.tracking.domain.appBlockList.split(",") .forEach(function (user) {
                    $('#userApplicationsAddEdit').append('<option selected> ' + user + ' </option>');
                })
            }
            if (trackData.data.custom_tracking_rule.tracking.bluetooth?.bluetoothAdress) {
                trackData.data.custom_tracking_rule.tracking.bluetooth.bluetoothAdress.split(",") .forEach(function (user) {
                    $('#userBluetoothAddEditInput').append('<option selected> ' + user + ' </option>');
                })
            }
            // ((trackData.data.custom_tracking_rule.tracking.domain.suspendKeystrokesPasswords == false) || (trackData.data.custom_tracking_rule.tracking.domain.suspendKeystrokesPasswords == "false")) ? ($('#userMonitorKeystrokes').parent().removeClass('toggle btn btn-xs btn-primary').addClass('toggle btn btn-xs btn-light off')) : ($('#userMonitorKeystrokes').parent().removeClass('toggle btn btn-xs btn-light off').addClass('toggle btn btn-xs btn-primary')); // or 'checked'
            // ((trackData.data.custom_tracking_rule.tracking.domain.suspendKeystrokesPasswords == false) || (trackData.data.custom_tracking_rule.tracking.domain.suspendKeystrokesPasswords == "false")) ? (MONITORONOFF = "off", changeToggle()) : (MONITORONOFF = "on", changeToggle()); // or 'checked'
            ((trackData.data.custom_tracking_rule.tracking.domain.suspendKeystrokesPasswords == false) || (trackData.data.custom_tracking_rule.tracking.domain.suspendKeystrokesPasswords == "false")) ? (MONITORONOFF = "on", changeToggle()) : (MONITORONOFF = "off", changeToggle()); // or 'checked'
            $("#login_from_other_system").prop('checked', false);
            userBlockEnableDisable();
        }
    }
}


function changeToggle() {

    if (MONITORONOFF === "off") {
        ($('#userMonitorKeystrokes').parent().removeClass('toggle btn btn-xs btn-primary').addClass('toggle btn btn-xs btn-light off'));
        MONITORONOFF = "on";
    } else if (MONITORONOFF === "on") {
        MONITORONOFF = "off";
        ($('#userMonitorKeystrokes').parent().removeClass('toggle btn btn-xs btn-light off').addClass('toggle btn btn-xs btn-primary'))// or 'checked'
    }
}

//for save all the data for track-user-setting
$(document).on('submit', "#SaveAllData", function (e) {
    $('#errorLatLng').html("");
    e.preventDefault();
    let checkAllTimes = 0;
    let form = document.getElementById('SaveAllData');
    let formData = new FormData(form);
    let systemRoll = $('#roll_option option:selected').attr('id');
    let SSFreq = $('#SSFrequencySelected option:selected').attr('id');
    let BreakTime = $('#BreakTime option:selected').attr('id');
    let IdleTime = $('#IdleTime option:selected').attr('id');
    let inactiveTime = $('#inactiveTime option:selected').attr('id');
    let AutoUpdate = $('#autoUpdates_id').parent().hasClass('toggle btn btn-xs btn-primary') ? 1 : 0 ;
    switch ($('input[name="Scenario"]:checked').val()) {
        case "unlimited" : {
            let unlimited = $.map($('input[name="Unlimited"]:checked'), function (c) {
                return c.value;
            });
            formData.append('unlimitedDays', unlimited);
        }
            break;
        case "fixed" : {
            let FixedList = $.map($('input[name="Fixed"]:checked'), function (c) {
                if ($('#' + c.value + 'startTime').val() == "" || $('#' + c.value + 'endTime').val() == "") {
                    errorSwal("Please, Provide start and end times for all selected checkboxes");
                    checkAllTimes = 1
                }
                return c.value + "#" + $('#' + c.value + 'startTime').val() + "#" + $('#' + c.value + 'endTime').val();
            });
            formData.append('FixedListTimes', FixedList);
        }
            break;
        case "networkBased" : {

            if(add_multiple_network_track)
            {
                const networkData = [];
                for (var i = 0; i <= GLOBAL_NETWORK; i++) {
                    if (($('#NetworkName' + i).val() !== undefined) && ($('#MACaddress' + i).val() !== undefined)) {
                        if ($('#NetworkName' + i).val() !== "" && $('#MACaddress' + i).val() !== "") {
                            networkData.push({
                                "NetworkName": $('#NetworkName' + i).val(),
                                "MACaddress": $('#MACaddress' + i).val(),
                                "officeNetwork": $('#officeNetwork' + i).is(":checked") ? true : false
                            });
                        }
                    }
                }
                formData.append('networkData', JSON.stringify(networkData));
            }else{
                if ($('input[name="officeNetwork"]').is(":checked") == true) formData.append("officeNetwork", true);
                else formData.append("officeNetwork", false);
                formData.append("NetworkName", $("#NetworkName0").val());
                formData.append("MACaddress", $("#MACaddress0").val());
            }

        }
            break;
        case "projectBased" : {
            let projectBasedIds = $.map($('input[name="projectBased"]:checked'), function (c) {
                return c.id
            });
            formData.append('projectBasedIds', projectBasedIds);
        }
            break;
        case "geoLocation" : {
            // if (Number(ENV_SPECIAL_IDLE_ADMIN) === 1) {
            const locate = [];
            for (var i = 0; i <= GLOBAL_LOCATION; i++) {
                if (($('#location' + i).val() != undefined) && ($('#longitude' + i).val() != undefined) && ($('#range' + i).val() != undefined)) {
                    if ($('#location' + i).val() != "") {
                        if ($('#longitude' + i).val() != "") {
                            if ($('#range' + i).val() != "") {
                                locate.push({
                                    "location": $('#location' + i).val(),
                                    "lat": $('#longitude' + i).val().split(",")[0],
                                    "lon": $('#longitude' + i).val().split(",")[1],
                                    "distance": $('#range' + i).val()
                                });
                            } else {

                                $("#errorLatLng").html(LOCATION_LOCALIZATION.range);
                                return false;
                            }
                        } else {
                            alert('hi');
                            $("#errorLatLng").html(LOCATION_LOCALIZATION.longitude);
                            return false;
                        }
                    }
                    if (($('#location' + i).val().length > 0) || ($('#longitude' + i).val().length > 0) || ($('#range' + i).val().length > 0)) {
                        if ($('#location' + i).val() == "") {
                            $("#errorLatLng").html(LOCATION_LOCALIZATION.location_req);
                            return false;
                        } else if ($('#longitude' + i).val() == "") {
                            $("#errorLatLng").html(LOCATION_LOCALIZATION.longitude);
                            return false;
                        } else if ($('#range' + i).val() == "") {
                            $("#errorLatLng").html(LOCATION_LOCALIZATION.range);
                            return false;
                        }
                    } else {
                        if ($('#location' + i).val().length === 0) $("#errorLatLng").html(LOCATION_LOCALIZATION.location_req)
                        else $('#longitude' + i).val().length === 0 ? $("#errorLatLng").html(LOCATION_LOCALIZATION.longitude) : $("#errorLatLng").html(LOCATION_LOCALIZATION.range);
                        return false;
                    }
                }
            }
            formData.append('geoLocation', JSON.stringify(locate));
            // }
        }
    }

    $('input[name="AllowEmployeeTask"]').is(":checked") ? formData.append("AllowNewTask", true) : formData.append("AllowNewTask", false);
    $('input[name="AccessOfWatchingSS"]').is(":checked") ? formData.append('AccessOfWatchingSS', true) : formData.append('AccessOfWatchingSS', false);
    $('input[name="AccessOfDeleteSS"]').is(":checked") ? formData.append('AccessOfDeleteSS', true) : formData.append('AccessOfDeleteSS', false);

    let trackingData = $("#advanseUserData").attr('name');
    trackingData = JSON.parse(trackingData);
    if (!trackingData.tracking.bluetooth) {
        trackingData.tracking.bluetooth = {};
        trackingData.tracking.bluetooth.bluetoothAdress = "";
    }
    trackingData = JSON.stringify(trackingData);

    formData.append('SystemRoll', systemRoll);
    formData.append('SSFrequency', SSFreq);
    formData.append('BreakTime', BreakTime);
    formData.append('IdleTime', IdleTime);
    formData.append('inactiveTime', inactiveTime);
    formData.append('timesheetIdleTime', $('#idleTimeForTimeSheet').val());
    formData.append('TrackingScenario', $('input[name="Scenario"]:checked').val());
    formData.append("UserId", $("#user-id").attr('name'));
    formData.append("KeyStrokeOption", $('input[name="KeyStrokeOption"]:checked').val() !== undefined ? $('input[name="KeyStrokeOption"]:checked').val() : 0);
    formData.append("ScreenshotsOption", $('input[name="ScreenshotsOption"]:checked').val());
    formData.append("videoOption", $('input[name="videoOption"]:checked').val());
    formData.append("BlockWebsiteOption", $('input[name="BlockWebsiteOption"]:checked').val());
    formData.append("WebsiteOption", $('input[name="WebsiteOption"]:checked').val());
    formData.append("Appoption", $('input[name="Appoption"]:checked').val());
    formData.append("Track_rule_type", $("#AppliedSetting").val());
    formData.append("group_id", $("#AppliedSetting").children(":selected").attr('id'));
    formData.append("video_quality", $("#videoQuality").val());
    formData.append("auto_update", AutoUpdate);
    formData.append('domainTrackData', trackingData);
    formData.append('manual_clock_in', IS_MANUAL);
    formData.append('usbDisable', IS_USB_DISABLE);
    formData.append('systemLock', IS_SYSTEM_LOCK);
    formData.append('isSilahMobileGeoLocation', IS_MOBILE_DATA);
    formData.append('is_attendance_override', IS_ATTENDANCE_OVERRIDE);
    formData.append('USB_feature', $("#USB_feature").prop('checked') ? "1" : "0");
    formData.append('userBlock', $("#userBlock").prop('checked') ? "1" : "0");
    // formData.append('userBlockLogo',userBlockLogo);
    formData.append('userBlockEmail', $('#userBlockEmail').val());
    formData.append('userBlockContact', $('#userBlockContact').val());
    // Tracking features
    formData.append("ScreenCast", $('input[name="ScreenCast"]:checked').val() !== undefined ? $('input[name="ScreenCast"]:checked').val() : 0);
    formData.append("realTimeTrack", $('input[name="realTimeTrackOption"]:checked').val() !== undefined ? $('input[name="realTimeTrackOption"]:checked').val() : 0);
    formData.append("location", $('input[name="locationOption"]:checked').val() !== undefined ? $('input[name="locationOption"]:checked').val() : 0);
    // DLP features
    formData.append("webBlockRadio", $('input[name="webBlockRadio"]:checked').val() !== undefined ? $('input[name="webBlockRadio"]:checked').val() : 0);
    formData.append("appBlockRadio", $('input[name="appBlockRadio"]:checked').val() !== undefined ? $('input[name="appBlockRadio"]:checked').val() : 0);
    formData.append("usbDetecRadio", $('input[name="usbDetecRadio"]:checked').val() !== undefined ? $('input[name="usbDetecRadio"]:checked').val() : 0);
    formData.append("usbBlockRadio", $('input[name="usbBlockRadio"]:checked').val() !== undefined ? $('input[name="usbBlockRadio"]:checked').val() : 0);
    formData.append("blueDetecRadio", $('input[name="blueDetecRadio"]:checked').val() !== undefined ? $('input[name="blueDetecRadio"]:checked').val() : 0);
    formData.append("blueBlockRadio", $('input[name="blueBlockRadio"]:checked').val() !== undefined ? $('input[name="blueBlockRadio"]:checked').val() : 0);
    formData.append("clipDetecRadio", $('input[name="clipDetecRadio"]:checked').val() !== undefined ? $('input[name="clipDetecRadio"]:checked').val() : 0);
    formData.append("clipBlockRadio", $('input[name="clipBlockRadio"]:checked').val() !== undefined ? $('input[name="clipBlockRadio"]:checked').val() : 0);
    formData.append("sysLocRadio", $('input[name="sysLocRadio"]:checked').val() !== undefined ? $('input[name="sysLocRadio"]:checked').val() : 0);

    if (checkAllTimes == 1) return false;
    $.ajax({
        type: 'post',
        url: "/" + userType + '/track-user-setting',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#SaveButton').attr('disabled', true);
            $("#errorMessage").empty();
            $("#errorMessageUnlimited").empty();
            $("#ErrorNetworkName").empty();
            $("#ErrorMACAddress").empty();
        },
        success: function (response) {
            $('#SaveButton').attr('disabled', false);
            if(response.code == 408)  return   $("#errorLatLng").html(response.errors);
            if (response.code == 205) {
                $.each(response.errors, function (index) {
                    switch (index) {
                        case "FixedListTimes" :
                            $("#errorMessage").html(Track_user_settings.select_one_day);
                            break;
                        case "Fixed" :
                            $("#errorMessage").html(MONITORING_CONTROL_LOCALIZATION.shift_start_end_time);
                            break;
                        case "unlimitedDays" :
                            $("#errorMessageUnlimited").html(Track_user_settings.choose_one_day);
                            break;
                        case "NetworkName" :
                            $("#ErrorNetworkName").html(Track_user_settings.Network_reqired);
                            break;
                        case "MACaddress" :
                            $("#ErrorMACAddress").html(response.errors.MACaddress[0]);
                        case "projectBasedIds" :
                            $("#errorMessageProjectBased").html(Track_user_settings.projectBasedError);
                            break;
                    }
                })
            } else if (response.code == 200) {
                setTimeout(function () {
                    location.reload();
                    settingsApplied(response);
                }, 100)

                successHandler(response.msg);
                // location.reload();
            } else errorSwal(response.msg, response.error);
        },
        error: function (jqXHR) {
            if (jqXHR.status == 410) {
                $("#UnaccessModal").empty();
                $("#UnaccessModal").css('display', 'block');
                $("#UnaccessModal").append('<div class="alert alert-danger text-center"><button type="button" class="close" data-dismiss="alert" >&times;</button><b  id="ErrorMsgForUnaccess"> ' + jqXHR.responseJSON.error + '</b></div>')
            } else errorHandler("Please, Try Again Later")
        }
    })
});

//To make the same start time and end time for all days in fixed input
function makeALLsameValues() {
    var FixedList = {};
    var Fixed = $.map($('input[name="Fixed"]:checked'), function (c) {
        FixedList.values = c.value + "#" + $('#' + c.value + 'startTime').val() + "#" + $('#' + c.value + 'endTime').val();
    });
    if ($('input[name="Fixed"]:checked').length === 1) {
        if (FixedList.values.split("#")[1] == "" || FixedList.values.split("#")[2] == "") $("#errorMessage").html(MONITORING_CONTROL_LOCALIZATION.select_start_end_time);
        else {
            // if (FixedList.values.split("#")[1] >= FixedList.values.split("#")[2]) {
            //     $("#errorMessage").html("Shift start time must be lessthan the shift End time");
            // } else {
            $('input[name="Fixed"]').prop('checked', true);
            $("#MONDAYstartTime").val(FixedList.values.split("#")[1]);
            $("#MONDAYendTime").val(FixedList.values.split("#")[2]);
            $("#TUESDAYstartTime").val(FixedList.values.split("#")[1]);
            $("#TUESDAYendTime").val(FixedList.values.split("#")[2]);
            $("#WEDNESDAYstartTime").val(FixedList.values.split("#")[1]);
            $("#WEDNESDAYendTime").val(FixedList.values.split("#")[2]);
            $("#THURSDAYstartTime").val(FixedList.values.split("#")[1]);
            $("#THURSDAYendTime").val(FixedList.values.split("#")[2]);
            $("#FRIDAYstartTime").val(FixedList.values.split("#")[1]);
            $("#FRIDAYendTime").val(FixedList.values.split("#")[2]);
            $("#SATURDAYstartTime").val(FixedList.values.split("#")[1]);
            $("#SATURDAYendTime").val(FixedList.values.split("#")[2]);
            $("#SUNDAYstartTime").val(FixedList.values.split("#")[1]);
            $("#SUNDAYendTime").val(FixedList.values.split("#")[2]);
            // }
        }
    } else if ($('input[name="Fixed"]:checked').length > 1) $("#errorMessage").html(MONITORING_CONTROL_LOCALIZATION.checkBox_select);
    else {
        $("#errorMessage").html(MONITORING_CONTROL_LOCALIZATION.select_oneDay);
    }
}

//to check the radio button with selected input
function radioChecked(id) {
    if (VISABLE === false) {
        $(".nav li.disabled a").click(function () {
            return false;
        });
        $(".nav a.disabled a").click(function () {
            return false;
        });
    }
    if (VISABLE === true) {
        switch (id) {
            case 4: {
                //for active the tab icon  for UI
                $("#UnlimitedActiveTab,#FixedActiveTab,#ProjectActiveTab,#NetworkActiveTab").attr('class', 'nav-link');
                $("#ManualActiveTab").attr('class', 'nav-link active');
                //for active the tab inputs
                $("#Unlimited,#Fixed,#NetworkBased,#ProjectBased").attr('class', 'container tab-pane fade');
                $("#ManualClockedIn").attr('class', 'container tab-pane active');
            }
                break;
            case 5: {
                $("#UnlimitedActiveTab,#FixedActiveTab,#ManualActiveTab,#NetworkActiveTab").attr('class', 'nav-link');
                $("#ProjectActiveTab").attr('class', 'nav-link active');
                //for active the tab inputs
                $("#Unlimited,#Fixed,#NetworkBased,#ManualClockedIn").attr('class', 'container tab-pane fade');
                $("#ProjectBased").attr('class', 'container tab-pane active');
            }

        }
    }

    setTimeout(function () {
        $('#Scenario' + id).prop("checked", true);
    }, 10);
}

//function to disable the manual clocked and project based in track-scenario when stealth mode is selcted
function disableTrackScenario(id) {
    if (id === 1) {
        $('#BreakTime').attr('disabled', true);
        VISABLE = false;
        $('#FixedNav').attr('class', 'col nav-item p-0 form-check disabled');
        $('#ManualNav').attr('class', 'col nav-item p-0 form-check disabled');
        $('#ManualActiveTab').attr('class', 'nav-link disabled');
        $('#Scenario4').attr('disabled', true);
        $('#ProjectBasedNav').attr('class', 'col nav-item p-0 form-check disabled');
        $('#ProjectActiveTab').attr('class', 'nav-link disabled');
        $('#Scenario5').attr('disabled', true);
        let scenario = $('input[name="Scenario"]:checked').attr('value');
        if (!(scenario === "unlimited" || scenario === "fixed")) {
            $('#Scenario1').prop('checked', true);
            $("#UnlimitedActiveTab").attr('class', 'nav-link active');
            $("#Unlimited").attr('class', 'container tab-pane active');
            $("#ManualClockedIn").attr('class', 'container tab-pane fade');
            $("#ProjectBased").attr('class', 'container tab-pane fade');
            // $("input[name='Unlimited']").attr('checked',false);
        }


    } else {
        $('#BreakTime').attr('disabled', false);
        VISABLE = true;
        $('#ManualNav').attr('class', 'col nav-item p-0 form-check');
        $('#ManualActiveTab').attr('class', 'nav-link');
        $('#Scenario4').attr('disabled', false);
        $('#ProjectBasedNav').attr('class', 'col nav-item p-0 form-check');
        $('#ProjectActiveTab').attr('class', 'nav-link');
        $('#Scenario5').attr('disabled', false);
    }

}

//function for enable and disable the screenshot frequency section
function screenshotactive(id,param) {
    if(param == 'ss') id == 1 ? $('#SSFrequencySelected').attr('disabled', false) : $('#SSFrequencySelected').attr('disabled', true);
    else id == 1 ? $('#videoQuality').attr('disabled', false) : $('#videoQuality').attr('disabled', true);
}

//on change of user setting
$("#AppliedSetting").on('change', function () {
    $(this).val() == '3' ? ($('#SaveButton').attr('disabled', false), $('#AdvanceSaveButton').attr('disabled', false)) : ($('#SaveButton').attr('disabled', true), $('#AdvanceSaveButton').attr('disabled', true));

    if ($(this).val() != '3') {
        Swal.fire({
            title: Track_user_settings.change_settings,
            html: '<p style="color: red">' + Track_user_settings.note + ':- <span style="color: black">' + Track_user_settings.old_settings + ' </span></p>',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: saveText,
            cancelButtonText: cancelText,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) $("#SaveAllData").submit();
        })
    }

})


// type : 1 - web, 2 - app , 3 - track advance settings, 4 - bluetooth
function userWebsitesAddEditSubmit(type = 0) {
    if(type == 0) return;
    let FormElement = type == 1 ? "userWebsitesAddEditForm" : type == 2 ? "userApplicationsAddEditForm" : type == 4 ? "userBluetoothAddEditForm" : "TrackAdvanceSettingForm";
    let ErrorElement = type == 1 ? "userWebsitesAddEditError" :  type == 2 ? "userApplicationsAddEditError" : type == 4 ? "userBluetoothAddEditError" : "TrackAdvanceSettingError";
    let BlockModal = type == 1 ? "WebBlockModal" : type == 2 ? "AppBlockModal" : "TrackAdvanceSetting";

    $('#'+ErrorElement).html("");
    let advanceTrackData = $("#advanseUserData").attr('name');
    const formData = new FormData(document.getElementById(FormElement));
    formData.append('track_data', advanceTrackData);
    formData.append('employee_id', trackData.data.id);
    formData.append('type', $("#AppliedSetting").val());
    formData.append('group_id', $("#AppliedSetting").children(":selected").attr('id'));
    formData.append('MONITORONOFF', MONITORONOFF);
    formData.append('USB_feature', $("#USB_feature").prop('checked') ? "1" : "0");
    formData.append('userBlock', $("#userBlock").prop('checked') ? "1" : "0");
    formData.append('userBlockLogo',userBlockLogo);
    formData.append("auto_update", $('#autoUpdates_id').parent().hasClass('toggle btn btn-xs btn-primary') ? 1 : 0);
    formData.append('userBlockEmail', $('#userBlockEmail').val());
    formData.append('userBlockContact', $('#userBlockContact').val());
    formData.append('login_from_other_system', $('#login_from_other_system').prop('checked') ? "1" : "0");
    if (type == 1) {
        formData.append('userApplicationsAddEdit[]', $('#userApplicationsAddEdit').val() ?? '');
        formData.append('userBluetoothAddEditInput[]', $('#userBluetoothAddEditInput').val() ?? '');
    } else if (type == 2) {
        formData.append('userWebsitesAddEditInput[]', $('#userWebsitesAddEditInput').val() ?? '');
        formData.append('userBluetoothAddEditInput[]', $('#userBluetoothAddEditInput').val() ?? '');
    } else if (type == 4) {
        formData.append('userWebsitesAddEditInput[]', $('#userWebsitesAddEditInput').val() ?? '');
        formData.append('userApplicationsAddEdit[]', $('#userApplicationsAddEdit').val() ?? '');
    }

    $.ajax({
        type: 'post',
        url: "/" + userType + '/Advance-track-user-setting',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code == 200) {
                successHandler(response.msg);
                location.reload();
                // $('#'+BlockModal).modal('hide')
            }
            else if ([201, 202, 203, 405, 205].includes(response.code)) {
                let errorID = '';
                switch (response.code) {
                    case 201:
                        errorID = 'webMonitorOnlyError';
                        break;
                    case 202:
                        errorID = 'webSuspendOnvisitError';
                        break;
                    case 203:
                        errorID = 'webSuspendKeystrokeError';
                        break;
                    case 205:
                        if (response.errors) {
                            response.errors.userBlockContact !== undefined && ($('#'+ErrorElement).html(response.errors.userBlockContact[0]));
                            response.errors.userBlockEmail !== undefined && ($('#'+ErrorElement).html(response.errors.userBlockEmail[0]));
                        }
                    case 405:
                        errorID = 'BlockingWebsitesErrors';
                        break;
                }
                $('#' + errorID).html(MONITORING_CONTROL_LOCALIZATION.invalidUrls);
            } else {
                errorSwal(response.msg)
            }
        },
        error: function () {
            errorHandler(NotAbleToLoad);
        }
    })
}

//  Adding location Row
function addRowloc(value) {
    GLOBAL_LOCATION = GLOBAL_LOCATION + value;
    var html = '';
    html += '<div class="row inputFormRowloc" id="inputFormRowloc' + GLOBAL_LOCATION + '"><div class="col-sm"><div class="form-group">';
    html += '<input type="text" class="form-control" placeholder=' + enter_location +' onkeyup="longitude(this.value,this.id);" id="location' + GLOBAL_LOCATION + '"/>'
    html += '</div></div>';
    html += '<div class="col-sm"><div class="form-group">';
    html += '<input type="text" class="form-control" disabled placeholder= '+ enter_latitude +' & '+ enter_longitude +' onkeyup="longitude(this.value,this.id);" id="longitude' + GLOBAL_LOCATION + '"/>'
    html += '</div></div>';
    html += '<div class="col-sm"><div class="form-group">';
    html += '<input type="number" class="form-control" disabled placeholder="'+geo_range+'" id="range' + GLOBAL_LOCATION + '"/>'
    html += '</div></div><div class="col-append">';
    html += '<a id="removeRowloc" onclick="removeRow(' + GLOBAL_LOCATION + ')" class="text-danger"><i class="far fa-times-circle"></i></a>';
    html += '</div></div>';
    $('#newRowloc').append(html);
    $('.newRowloc').select2();
    $(".js-example-tokenizer").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });
}

//  Removing Location row
function removeRowloc() {
    $('.inputFormRowloc').empty();
}

function removeRow(id) {
    $('#inputFormRowloc' + id).remove();
}
function addGeoRowloc(value) {
    GLOBAL_NETWORK = GLOBAL_NETWORK + value;
    var html = '';
    html += '<br> <div class="row inputFormRowNetwork" id="inputFormRowNet' + GLOBAL_NETWORK + '"> <div class="col-md-5"><div class="form-group">';
    html += '<input type="text" class="form-control" placeholder="'+enter_network_name+'"   id="NetworkName'+ GLOBAL_NETWORK +'"/>'
    html += '</div></div>';
    html += '<div class="col-md-5"><div class="form-group">';
    html += '<input type="text" class="form-control" placeholder= "'+ enter_network_ip +' " id="MACaddress'+ GLOBAL_NETWORK +'"/>'
    html += '</div></div><div class="col-md-2 text-center">';
    html += '<a id="removeRowloc" onclick="removeRowNetwork(' + GLOBAL_NETWORK + ')" class="text-danger"><i class="far fa-times-circle"></i></a></div>';
    html += '<div class="col-md-5"><div class="form-control form-check bg-primary text-white office_network_div">  <label class="form-check-label"> <input type="checkbox" name="officeNetwork"  class="form-check-input"  id="officeNetwork'+ GLOBAL_NETWORK +'"> '+office_network+' </label> </div></div>';
    html += '  <div class="col-md-6">  <p class="font-weight-bold">'+note+' : <small>'+office_network_note+'</small></p>  </div>';
    html += '</div>';
    $('.newRowNetwork').select2();
    $(".js-example-tokenizer").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });
    html += '</div></div></div></div>';
    $('#newRowNetwork').append(html);
}
function removeRowNetwork(id) {
    $('#inputFormRowNet' + id).remove();
}
function removeRowNet() {
    $('.inputFormRowNetwork').remove();
}
let latlngVal = /^([-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?))$/;
let locVal = /^[a-zA-Z-,]+(\s[a-zA-Z-, ]*)?$/;
//  Checking Regex for longitude
function longitude(value, id) {
    id = id.replace(/\D/g, "");
    if (!locVal.test($("#location" + id).val())) {
        $("#errorLatLng").html(LOCATION_LOCALIZATION.location_validation);
        $("#longitude" + id).attr("disabled", true);
        $("#range" + id).attr("disabled", true);
        $('#SaveButton').attr("disabled", true);
        return false;
    } else {
        $("#errorLatLng").html(LOCATION_LOCALIZATION.location_valid);
        $("#longitude" + id).attr("disabled", false);
        $("#range" + id).attr("disabled", false);
        $('#SaveButton').attr("disabled", false);
        if (!latlngVal.test($("#longitude" + id).val())) {
            $("#errorLatLng").html(LOCATION_LOCALIZATION.not_valid_lattitude_longitude);
            $("#range" + id).attr("disabled", true);
            return false;
        } else {
            $("#errorLatLng").html(LOCATION_LOCALIZATION.valid_lattitude_longitude);
            $("#errorLatLng").addClass('success-msg');
            $("#range" + id).attr("disabled", false);
        }
    }
}

function isManualClock(val) {
    IS_MANUAL = val;
}

function isAttendance(val) {
    IS_ATTENDANCE_OVERRIDE = val;
}
function isUsbDisable(val){
    IS_USB_DISABLE = val;
}
function isSystemLock(val){
    IS_SYSTEM_LOCK = val;
}
function isMobileData(val){
    IS_MOBILE_DATA = val;
}
function userBlockEnableDisable() {
    if ($("#userBlock").prop('checked')) {
        $('.userBlockDiv').show();
    } else {
        $('.userBlockDiv').hide();
    }
}

function getLogo(){
    $.ajax({
        type: 'get',
        url: "/" + userType + '/user-block-logo',
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code === 200 && response.data !== null && response.data !== undefined) {
                userBlockLogo = response.data;
                $('#userBlockLogo').attr('src', userBlockLogo);
            }
        }
    });
}

// To accept only numbers
function acceptNumbersOnly(event) {
    let k;
    document.all ? k = event.keyCode : k = event.which;
    return (k == 8 || (k >= 48 && k <= 57));
}

function storageStatusCheck() {
    if (!storageStatus) {
        $('#SS0').prop("checked", true);
        errorHandler('No active Storage found ....!');
    }
}




// on change redio option it will check the "on" buttown and show and hide the buttom and conetnt
$(document).ready(function () {
    $('input[type="radio"]').change(()=>{
        OnchangeRedioOption();
    });
});
function OnchangeRedioOption() {
    $('input[name="webBlockRadio"]:checked').val() == 1 ? $(".WebBlockModalBtn").css('display', 'block') : $(".WebBlockModalBtn").css('display', 'none');
    $('input[name="appBlockRadio"]:checked').val() == 1 ? $(".AppBlockModalBtn").css('display', 'block') : $(".AppBlockModalBtn").css('display', 'none');
    $('input[name="usbDetecRadio"]:checked').val() == 1 ? ($("#usbBlock").css('display', 'contents'), $(".usbDetecInfo").css('display', 'none')) : ($("#usbBlock").css('display', 'none'), $(".usbDetecInfo").css('display', 'block'));
    $('input[name="blueDetecRadio"]:checked').val() == 1 ? ($("#bluetoothBloc").css('display', 'contents'), $(".bluetoothInfo").css('display', 'none')) : ($("#bluetoothBloc").css('display', 'none'), $(".bluetoothInfo").css('display', 'block'));
    $('input[name="clipDetecRadio"]:checked').val() == 1 ? ($("#clipboardhBlock").css('display', 'contents'), $(".clipboardInfo").css('display', 'none')) : ($("#clipboardhBlock").css('display', 'none'), $(".clipboardInfo").css('display', 'block'));
    $('input[name="blueBlockRadio"]:checked').val() == 1 ? $(".BlueModalBtn").css('display', 'block') : $(".BlueModalBtn").css('display', 'none');
}
