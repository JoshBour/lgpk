// adds a new message to the flash messenger
function addMessage(message) {
    var flash = $('#flash');
    if (flash.is(":visible")) {
        flash.html(message);
    } else {
        flash = $('<div />', {
            id: "flash",
            text: message,
            "css" : {
                "display" : "none"
            }
        }).prependTo('body').slideDown("slow");
    }
    flash.setRemoveTimeout(6000);
}

String.prototype.lowerize = function () {
    return this.charAt(0).toLowerCase() + this.slice(1);
};

String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
};


// returns an array that contains the name,extension,original path
// of the provided image
function getImageDetails($imagePath) {
    var splitImg = $imagePath.split('/');
    var fullImageName = splitImg[splitImg.length - 1];
    var fullImageNameArray = fullImageName.split('.');
    var imagelessPath = $imagePath.substring(0, fullImageName.length);
    var imageDetails = [fullImageNameArray[0], fullImageNameArray[1], imagelessPath];
    return imageDetails;
}


var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

function empty(variable) {
    if (variable == "" || variable == null || typeof variable === 'undefined') {
        return true;
    } else {
        return false;
    }
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(key)
{
    var allcookies = document.cookie;

    // Get all the cookies pairs in an array
    var cookiearray  = allcookies.split(';');

    // Now take key value pair out of this array
    for(var i=0; i<cookiearray.length; i++){
        var name = $.trim(cookiearray[i].split('=')[0]);
        if(name == key) return cookiearray[i].split('=')[1];
    }
}

function daysBetween(date1, date2) {

    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24

    // Convert both dates to milliseconds
    var date1_ms = date1.getTime()
    var date2_ms = date2.getTime()

    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1_ms - date2_ms)

    if (date2_ms < date1_ms) {
        return false;
    }

    // Convert back to days and return
    return Math.round(difference_ms / ONE_DAY)

}

// mini plugin that will hide an element according to the timout given
(function ($) {
    $.fn.setRemoveTimeout = function (milisecs) {
        var element = $(this);
        if (element.length > 0) {
            setTimeout(function () {
                $(element).slideUp("slow",function(){
                    $(this).detach();
                });
            }, milisecs);
        }
        return element;
    };
})(jQuery);

// plugin that will show or hide the loading image
(function ($) {
    $.fn.toggleLoadingImage = function () {
        var element = $(this);
        // we use .find for more secure results
        var loadingImg = element.children('.loadingImg');
        if (loadingImg.length > 0) {
            loadingImg.detach();
        } else {
            $('<div />', {'class': 'loadingImg'}).appendTo(element);
        }
    };
})(jQuery);

// "turn off" the lights and focus the specific element
(function ($) {
    $.fn.focusLight = function () {
        // set the default value for focusedDiv
        var element = $(this);
        //focusedDiv = typeof focusedDiv !== 'undefined' ? $(focusedDiv) : $(this);

        // add the shadow to the body
        $('<div />', {'id': 'shadow'}).appendTo('body');

        element.addClass('focused');

        return element;
    };
})(jQuery);

// "turn on" the lights
(function ($) {
    $.fn.unfocusLight = function () {
        var element = $(this);
        $('#shadow').detach();
        element.removeClass('focused');

        return element;
    };
})(jQuery);

// creates a date only datetimepicker
(function ($) {
    $.fn.datePicker = function () {
        // set the default value for focusedDiv
        var element = $(this);
        // add the shadow to the body
        element.datetimepicker({
            timepicker: false,
            format: 'd-m-Y',
            formatDate: 'd-m-Y',
            closeOnDateSelect: true,
            lang: 'el'
        });

        return element;
    };
})(jQuery);

// creates a time only datetimepicker
(function ($) {
    $.fn.timePicker = function () {
        // set the default value for focusedDiv
        var element = $(this);
        // add the shadow to the body
        element.datetimepicker({
            datepicker: false,
            format: 'H:i',
            closeOnDateSelect: true,
            lang: 'el'
        });

        return element;
    };
})(jQuery);

// create a default localized datetimepicker
(function ($) {
    $.fn.elDateTimePicker = function () {
        // set the default value for focusedDiv
        var element = $(this);
        // add the shadow to the body
        element.datetimepicker({
            closeOnTimeSelect: true,
            lang: 'el'
        });

        return element;
    };
})(jQuery);

