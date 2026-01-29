// HTML Sanitization function to prevent XSS attacks
function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;")
        .replace(/\//g, "&#x2F;");
}

// Sanitize attributes (more strict for use in HTML attributes)
function escapeAttribute(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#x27;")
        .replace(/\//g, "&#x2F;")
        .replace(/\(/g, "&#40;")
        .replace(/\)/g, "&#41;");
}

// for Moment load
let momentJS = document.createElement('script');
momentJS.src = 'https://MomentJS.com/downloads/moment.js';
// let momentJSLocale = document.createElement('script');
// momentJSLocale.src = 'https://MomentJS.com/downloads/moment-with-locales.js';

function updateMoment() {
    document.head.appendChild(momentJS);
    // setTimeout(() => {
    //     document.head.appendChild(momentJSLocale);
    //     moment.locale(LOCALE) !== LOCALE ? updateMoment() : null;
    // }, 500);
}

setTimeout(() => {
    if (window.location.href.includes('attendance-history') || window.location.href.includes('get-employee-details') || window.location.href.includes('myTimeline') || window.location.href.includes('employee/get-details?') || window.location.href.includes('internal-tasks')){
        return false;
    } else  {
        updateMoment();
    }
}, 3000);

// for Alert Logo and the panel:
let ALERT_COUNT = Number(localStorage.getItem(userType + '_ALERT_COUNT')) || 0;
let ALERT_LIST = JSON.parse(localStorage.getItem(userType + '_ALERT_LIST')) || [];
let BLUR_VALUE = '5px';

// Math.max to set minimum 0
// Security: Ensure ALERT_COUNT is properly sanitized before display
let appendNumberOnNotificationIcon = () => {
    let count = Math.max(0, Number(ALERT_COUNT));
    let displayCount = count > 99 ? '99+' : String(count);
    $('#unReadPushNotificationCount').text('\u00A0' + displayCount + '\u00A0');
};
appendNumberOnNotificationIcon();

const bgColoursArray = [{type: 'NR', color: '#00ad42'}, {type: 'LR', color: '#c3d100'}, {
    type: 'MR',
    color: '#ffa300'
}, {type: 'HR', color: '#ff2d00'}, {type: 'CR', color: 'red'}];

// for web page active status:
let hidden, visibilityChange;
if (typeof document.hidden !== "undefined") { // Opera 12.10 and Firefox 18 and later support
    hidden = "hidden";
    visibilityChange = "visibilitychange";
} else if (typeof document.msHidden !== "undefined") {
    hidden = "msHidden";
    visibilityChange = "msvisibilitychange";
} else if (typeof document.webkitHidden !== "undefined") {
    hidden = "webkitHidden";
    visibilityChange = "webkitvisibilitychange";
}

ALERT_COUNT === 0 ? ($('#toastNotifications').attr({
    'disabled': true,
    'title': ALERTS_TOAST_JS.noNewNotif
})) : $('#toastNotifications').attr({'disabled': false, 'title': ALERTS_TOAST_JS.clickToSeeNotif});

let lastMessageId = 0;
const client = new SockJS(WEBSOCKET_SERVER_URL);
const start = (serverUrl = WEBSOCKET_SERVER_URL) => {
    const initServerUrl = serverUrl;
    // const client = new SockJS(serverUrl);
    const write = (message) => {
        const payload = JSON.stringify(message);
        return client.send(payload);
    };

    client.onopen = () => {
        const auth = {
            type: 'auth',
            lastMessageId,
            token: BROWSER,
        };
        write(auth);
    };

    client.onmessage = (e) => {
        const message = JSON.parse(e.data);
        switch (message.type) {
            case 'messages':
                const delivered = message.messages.filter(message => message.delivered_at);
                const undelivered = message.messages.filter(message => !message.delivered_at);

                //append to the notifications and display them.
                ALERT_LIST.push(...undelivered);

                ALERT_LIST.splice(undelivered.length - 3, undelivered.length);
                // localStorage.setItem(userType + '_ALERT_LIST', JSON.stringify(ALERT_LIST));
                undelivered.length > 0 ?
                    (undelivered.length < 3 ?
                        appendNotificationsData(undelivered, 'Load') : (
                            ALERT_COUNT += undelivered.length - 3, localStorage.setItem(userType + '_ALERT_COUNT', '0'), appendNotificationsData(undelivered.slice((undelivered.length - 3), undelivered.length), 'Load')))
                    : null;

                lastMessageId = Math.max(
                    lastMessageId,
                    ...delivered.map(message => message.id),
                    ...undelivered.map(message => message.id),
                );
                return;

            case 'newMessages':
                $('#toastHeader').nextAll().remove();
                //append to the notifications and display them.
                appendNotificationsData(message.messages, 'new');
                lastMessageId = Math.max(
                    lastMessageId,
                    ...message.messages.map(message => message.id),
                );
                return;

            case 'newReport':
                statusOfCSVReport();
                reportDownloadNotify(REPORT_PDF_CSV.newReport);
                return;

            case 'newReportBeforeDelete':
                reportDownloadNotify(REPORT_PDF_CSV.newReportBeforeDelete + message.message.split("Files will be removed from the Storage within")[1]);
                return;

            case 'newReportAfterDelete' :
                reportDownloadNotify(REPORT_PDF_CSV.newReportAfterDelete);
                removeLinks();
                return;
        }
    };

    client.onclose = () => {
        setTimeout(() => {
            start(initServerUrl);
        }, 5000);
    };
};

function markAsRead(unReadMessages, place) {
    ALERT_COUNT -= unReadMessages.length;
    localStorage.setItem(userType + '_ALERT_COUNT', ALERT_COUNT);
    appendNumberOnNotificationIcon();
    Number(place) === 2 ? ($('#toastNotifications').attr({
        'disabled': true,
        'title': ALERTS_TOAST_JS.noNotifyShow
    })) : $('#toastNotifications').attr({'disabled': false, 'title': ALERTS_TOAST_JS.clickToSeeNotif});

    Number(place) === 2 ? unReadMessages = unReadMessages.map(message => message.id) : {};
    const read = (message) => {
        const payload = JSON.stringify(message);
        Number(place) === 2 ? $('#toastHeader').nextAll().remove() : {};
        Number(place) === 2 ? closeToast() : {};
        Number(place) === 2 ? (ALERT_LIST = [], localStorage.setItem(userType + '_ALERT_LIST', JSON.stringify(ALERT_LIST))) :
            (ALERT_LIST = ALERT_LIST.filter((alert) => {
                return alert.id !== unReadMessages[0];
            }), localStorage.setItem(userType + '_ALERT_LIST', JSON.stringify(ALERT_LIST)));
        return client.send(payload);
    };
    read({type: 'delivered', delivered: unReadMessages});
}

function appendNotificationsData(newAlerts, type) {
    // these are the colours of the create rule page
    ALERT_COUNT += newAlerts.length;
    localStorage.setItem(userType + '_ALERT_COUNT', ALERT_COUNT);
    appendNumberOnNotificationIcon();
    if (type === 'new') {
        document.addEventListener(visibilityChange, function () {
            if (document[hidden]) {
                return appendPushNotifications(newAlerts)
            } else {
                return appendTheToastsData(newAlerts, type);
            }
        });
    } else {
        return appendTheToastsData(newAlerts, type);
    }
}

function appendTheToastsData(alerts, type) {
    alerts.map(notification => {
        ALERT_LIST.push(notification);
        localStorage.setItem(userType + '_ALERT_LIST', JSON.stringify(ALERT_LIST));
        let [bgColor,] = bgColoursArray.filter(({type}) => type === notification.risk_level).map(obj => obj.color);
        
        // Sanitize all user-provided data to prevent XSS attacks
        const safeBgColor = escapeAttribute(bgColor || '#666');
        const safeRule = escapeHtml(notification.rule || '');
        const safeCreatedAt = escapeHtml(moment(notification.created_at).fromNow() || '');
        const safeId = escapeAttribute(notification.id || '');
        const safeMessage = escapeHtml(convertStringLanguage(notification.type, notification.message) || '');
        
        $('<div class="toast mainToast" data-autohide="false" role="alert" aria-live="assertive" aria-atomic="true">\n' +
            '                <div class="toast-header" style="background-color: ' + safeBgColor + '" >\n' +
            '                    <img src="' + escapeAttribute(ALERTS_LOGO) + '" height="30px" width="30px"\n' +
            '                         class="rounded mr-2" alt="...">\n' +
            '                    <strong class="mr-auto text-light">' + safeRule + '</strong>\n' +
            '                    <small class="text-light">' + safeCreatedAt + '</small>\n' +
            '                    <button type="button" class="ml-2 mb-1 close popUp" data-dismiss="toast" aria-label="Close" title="' + escapeAttribute(ALERTS_TOAST_JS.markRead) + '" id="btnMarkRead" onclick="markAsRead([Number(\'' + safeId + '\')])">\n' +
            '                        <span aria-hidden="true">&times;</span>\n' +
            '                    </button>\n' +
            '                </div><div class="toast-body" style="padding-top: 5px !important; padding-bottom: 5px !important;">\n' +
            '                    ' + safeMessage + '\n' +
            '                </div></div>').insertAfter('#toastHeader');
    });
    $('.notification_brw').attr('hidden', false);
    $('[data-toggle="tooltip"]').tooltip();
    type === 'new' ? openToast('', 'new') : type === 'Load' ? openToast('', 'Load') : openToast();
    checkAndRemoveHead();
    activateBodyClose();
}

function appendPushNotifications(newAlerts) {
    newAlerts.map(notification => {
        ALERT_LIST.push(notification);
        localStorage.setItem(userType + '_ALERT_LIST', JSON.stringify(ALERT_LIST));
        $.notify(convertStringLanguage(notification.type, notification.message), {
            title: ALERTS_TITLE,
            icon: ALERTS_LOGO
        });
    });
}

function reportDownloadNotify(message) {
    // has to get localized.
    $.notify(message, {
        title: ALERTS_TITLE,
        icon: ALERTS_LOGO
    });
}

function checkAndRemoveHead() {
    $('.popUp').on('click', function () {
        let lengths = $('.toast').length - 1;
        $(this).closest('.toast-header').parent('div').remove();
        lengths <= 1 ? closeToast() : {};
    });
}

function closeToast() {
    // remove blur
    bgBlurEffect('unBlur');
    $('.toast').toast('hide');
}

function bgBlurEffect(status) {
    if (status === 'blur') {
        $('.page-inner').css('filter', "blur(" + BLUR_VALUE + ")");
        $('.page-header').css('filter', "blur(" + BLUR_VALUE + ")");
        $('.secondary-sidebar').css('filter', "blur(" + BLUR_VALUE + ")");
    } else {
        $('.page-inner').css('filter', '');
        $('.page-header').css('filter', '');
        $('.secondary-sidebar').css('filter', '');
    }
}

function openToast(type, time) {
    //add blur effect
    bgBlurEffect('blur');

    $('.notification_brw').attr('hidden', false);
    type === 'All' ? ALERT_LIST.length > 0 ? ($('#toastNotifications').attr({
        'disabled': false,
        'title': ALERTS_TOAST_JS.clickToSeeNotif
    }), openAfterHeadClosed()) : appendEmpty() : ($('#toastNotifications').attr({
        'disabled': false,
        'title': ALERTS_TOAST_JS.clickToSeeNotif
    }), ($('.toast').toast('show')));
    time === 'new' ? setTimeout(() => {
        closeToast();
    }, 5000) : time === 'Load' ? setTimeout(() => {
        closeToast();
    }, 7000) : {};
}

function appendEmpty() {
    $('#toastHeader').nextAll().remove();
    
    // Sanitize user-provided data to prevent XSS
    const safeAlertsLogo = escapeAttribute(ALERTS_LOGO);
    const safeMarkRead = escapeAttribute(ALERTS_TOAST_JS.markRead);
    const safeClickToSeeNotif = escapeHtml(ALERTS_TOAST_JS.clickToSeeNotif);
    const safeListLink = escapeAttribute($('#listLink').attr('value') || '');
    const safeClickOldList = escapeHtml(ALERTS_TOAST_JS.clickOldList);
    
    $('<div class="toast mainToast" data-autohide="false" role="alert" aria-live="assertive" aria-atomic="true">\n' +
        '                <div class="toast-header bg-secondary">\n' +
        '                    <img src="' + safeAlertsLogo + '" height="30px" width="30px"\n' +
        '                         class="rounded mr-2" alt="...">\n' +
        '                    <strong class="mr-auto">EMP Monitor</strong>\n' +
        '                    <button type="button" class="ml-2 mb-1 close popUp" data-dismiss="toast" aria-label="Close" title="' + safeMarkRead + '">\n' +
        '                        <span aria-hidden="true">&times;</span>\n' +
        '                    </button>\n' +
        '                </div><div class="toast-body"><i class="fa fa-exclamation-triangle text-danger pr-2" aria-hidden="true"></i>' + safeClickToSeeNotif + ', <a href="' + safeListLink + '">' + safeClickOldList + '</a></div></div>').insertAfter('#toastHeader');
    $('.notification_brw').attr('hidden', false);
    $('[data-toggle="tooltip"]').tooltip();
    openToast();
    checkAndRemoveHead();
    activateBodyClose();
}

function openAfterHeadClosed() {
    $('#toastHeader').nextAll().remove();
    let oldAlerts = ALERT_LIST;
    ALERT_LIST = [];
    ALERT_COUNT = 0;
    localStorage.setItem(userType + '_ALERT_COUNT', '0');
    ALERT_LIST.push(...oldAlerts);
    ALERT_LIST.splice(oldAlerts.length - 3, oldAlerts.length);
    localStorage.setItem(userType + '_ALERT_LIST', JSON.stringify(ALERT_LIST));
    oldAlerts.length > 0 ? (oldAlerts.length < 3 ? appendNotificationsData(oldAlerts, 'old') : (ALERT_COUNT += oldAlerts.length - 3, localStorage.setItem(userType + '_ALERT_COUNT', String(oldAlerts.length - 3)), appendNotificationsData(oldAlerts.slice((oldAlerts.length - 3), oldAlerts.length)))) : null;
}

function showPreviousAlerts() {
    $('#toastHeader').nextAll().remove();
    if (ALERT_LIST.length <= 0) {
        closeToast();
        return;
    } else {
        let oldAlerts = ALERT_LIST;
        ALERT_LIST = [];
        localStorage.setItem(userType + '_ALERT_LIST', JSON.stringify(ALERT_LIST));
        ALERT_COUNT = 0;
        localStorage.setItem(userType + '_ALERT_COUNT', '0');
        appendNotificationsData(oldAlerts);
    }
}

$('.notification_brw').attr('hidden', true);
Number(localStorage.getItem(userType + '_ALERT_COUNT')) === 0 ? start() : {};

// start();

function activateBodyClose() {
    $('div.secondary-sidebar').on('click', function () {
        closeToast();
    });
    $('a:not(#toastNotifications)').on('click', function () {
        closeToast();
    });
    $('body').click(function (evt) {
        if (evt.target.id == "toastList" ||
            $(evt.target).closest('#toastList').length ||
            $(evt.target).closest('.toast-header').length ||
            $(evt.target).closest('#toastNotifications').length) return;
        else closeToast();
    });
}

