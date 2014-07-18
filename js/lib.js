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
    flash.setRemoveTimeout(5000);
}

function refreshMarkItUp() {
    $('textarea').each(function () {
        var textarea = $(this);
        if (!textarea.parent().hasClass('markItUpContainer')) {
            textarea.markItUp(mySettings);
        }
    });
}

String.prototype.lowerize = function () {
    return this.charAt(0).toLowerCase() + this.slice(1);
};

String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

function resetActiveField() {
    $('td, .entry').each(function () {
        $(this).removeClass('activeField');
    });
    $('#editPanel').hide();
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

function setEditableTextfields() {
    $('textarea').each(function () {
        var area = $(this);
        if (area.siblings('.textEditorToggle').length <= 0) {
            area.hide();
            $('<span />', {
                "class": "textEditorToggle",
                "html": "Click to edit"
            }).appendTo(area.closest('td'));
        }
    });
}

function updateEditedField() {
    var span = $('#editDone');
    var activeField = $('.activeField');
    var input = span.siblings();
    var val;
    if (activeField.hasClass('editImage') || activeField.hasClass('editFile')) {
        if (input.find('.editInput').val() != "") {
            console.log(input.formSerialize());
            input.ajaxSubmit({
                type: "post",
                success: function (responseText) {
                    activeField.addClass('uploadedFile');
                    if (responseText.success != 1) {
                        addMessage("There was an issue when uploading the file, please try again.");
                    } else {
                        if (activeField.hasClass('editImage')) {
                            var src = activeField.children('.fileMeta').html().split('/')[1];
                            var fileImage = activeField.children('.fileImage').addClass('changed');
                            var split = responseText.name.split('.');
                            if (fileImage.find('img').length <= 0) {
                                fileImage.html("");
                                $('<img/>', {
                                    "src": baseUrl + "/images/" + src + "/" + split[0] + "-temp." + split[1]
                                }).appendTo(fileImage);
                            } else {
                                fileImage.find('img').attr('src', baseUrl + "/images/" + src + "/" + split[0] + "-temp." + split[1]);
                            }
                        } else {
                            activeField.children('.fileName').html(responseText.name).addClass('changed');
                        }
                        activeField.closest('tr').addClass('unsaved');
                    }
                }
            });
        }
    } else if (activeField.hasClass('editTextfield')) {
        val = input.find('textarea').val();
        if (val != activeField.html()) {
            var activeTextfield = activeField.find('.activeTextfield');
            activeTextfield.val(input.find('textarea').val()).removeClass('activeTextfield');
            activeField.closest('tr').addClass('unsaved');
        }
    } else if (activeField.hasClass('editSelect') || activeField.hasClass('editMultiSelect')) {
        activeField.html('');
        input.find('option:selected').each(function () {
            var option = $(this);
            activeField.append($('<span />', {
                'class': "option-" + option.val(),
                'html': option.html()
            }));
        });
        activeField.closest('tr').addClass('unsaved');
    } else {
        val = input.val();
        if (val != activeField.html()) {
            activeField.html(val);
            activeField.closest('tr').addClass('unsaved');
        }
    }
    input.val('');
    activeField.removeClass('activeField');
    span.parent().hide();
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

