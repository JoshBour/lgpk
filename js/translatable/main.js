$(function () {
    var body = $('body');
    var flash = $('#flash');
    var isSchedulePage = body.hasClass('schedulePage');
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(5000);
    }
    var gt = new Gettext({ 'domain' : 'messages' });

    // initialize the date and time pickers
    $('#editPanel').find('.datetimeInput').each(function () {
        $(this).timePicker();
    });
    $('#dateRangeStart, #dateRangeEnd').datePicker();
    $('form .datetimeInput').elDateTimePicker();

    $('span[class$="Toggle"]').on('click',function(){
        var toggler = $(this).attr('class');
        var element = $('#'+toggler.substr(0,toggler.length-6));
        if (element.is(':visible')) {
            element.slideToggle("normal", function () {
                ofh = new $.fn.dataTable.FixedHeader(table);
            });
        } else {
            element.slideToggle();
            body.children(".fixedHeader").each(function () {
                $(this).remove();
            });
        }
    });

    $(window).on('resize', function () {
        body.children(".fixedHeader").each(function () {
            $(this).remove();
        });
        ofh = new $.fn.dataTable.FixedHeader(table);
    });

    $('td').on('mouseover mouseout', function () {
        var cols = $('colgroup');
        var i = $(this).prevAll('td').length - 1;
        if (body.hasClass('schedulePage') || body.hasClass('changelogPage')) i++;
        $(this).parent().toggleClass('hover');
        $(cols[i]).toggleClass('hover');
    });

    $('table').on('mouseleave', function () {
        $('colgroup').removeClass('hover');
    });

    $('#dateRange').find('.button').on('click', function () {
        var start = $('#dateRangeStart').val();
        var end = $('#dateRangeEnd').val();
        if (start == '' || end == '') {
            return null;
        } else {
            var splitStart = start.split('-');
            var splitEnd = end.split('-');
            var startDate = new Date(splitStart[2], splitStart[1], splitStart[0]);
            var endDate = new Date(splitEnd[2], splitEnd[1], splitEnd[0]);
            var daysDiff = daysBetween(startDate, endDate);
            if (!daysDiff || daysDiff > 31) {
                return null;
            }
            window.location.href = baseUrl + '/schedule/from/' + start + '/to/' + end;
            return true;
        }
    });


    $(document).on('submit', '#addWorkerForm, #addAdminForm, #addEntryForm, #addExceptionForm', function () {
        if (confirm(gt.gettext("Are you sure you want to submit the form? All unsaved changes will be lost!"))) {
            var form = $(this);
            var parent = form.parent();
            parent.toggleLoadingImage();
            form.ajaxSubmit({
                target: '#' + form.attr('id'),
                type: "post",
                success: function (responseText) {
                    if (responseText.redirect) {
                        location.reload();
                    }
                    $('.datetimeInput').elDateTimePicker();
                    parent.toggleLoadingImage();
                }
            });
        }
        return false;
    });

    $(document).on('dblclick', '.editableTable td', function () {
        var td = $(this);
        if (!td.hasClass('activeField') && !td.hasClass('delete')) {
            resetActiveField();
            td.addClass('activeField');
            var content = td.text();
            var editPanel = $('#editPanel');
            var editInput = editPanel.children('.editInput');
            var position = td.position();
            var width = td.outerWidth();
            editPanel.css('width', width - 1);
            editPanel.css('margin-left', position.left).show();
            editPanel.css('top', position.top + td.outerHeight() + 85)
            if (td.hasClass('editDatetime')) {
                if (content != '') {
                    var splitDate = content.split('-');
                    var date = new Date(splitDate[2], splitDate[1] - 1, splitDate[0])
                } else {
                    var date = new Date();
                }
                var input = $('<input type="text" id="mydate" value="' + content + '"></input>');
                editInput.replaceWith(input);
                editPanel.css('width', width - 1);
                input.css('width', width - 10).datePicker().focus();
//                editPanel.children('input')
            } else if (td.hasClass('editColor')) {
                var input = $('.colorSelect');
                input.find('option').filter(function () {
                    return $(this).html() == content;
                }).attr('selected', 'selected');
                editInput.replaceWith(input);
                input.css('width', width - 10).focus();
            } else {
                input = $('<textarea>' + content + '</textarea>')
                editInput.replaceWith(input);
                input.css('width', width - 10).focus();
            }
        }
    });

    $(window).keypress(function (e) {
        if (e.keyCode == 13) {
            if ($('#editPanel').is(':visible')) {
                updateEditedField();
            }
        }
    });

    $(document).on('click', '#editDone', function () {
        updateEditedField();
    });

    $('#saveChanges').on('click', function () {
        var entities = [];
        var table = isSchedulePage ? $('.scheduleTable'):$('.editableTable');
        var id = table.attr('id');
        table.find('tbody .unsaved').each(function () {
            var that = $(this);
            var entity = {};
            if(isSchedulePage){
                var entryId = that.children('.entryId').html();
                var entryValue = $.trim(that.children('.entryValue').html());
                if (!(entryValue == "-" && entryId.length == 0) && that.children('.workerName').length == 0 && that.children('.workerPosition').length == 0) {
                    entity["entryId"] = entryId.length != 0 ? entryId : 0;
                    entity["workerId"] = that.children('.workerId').html();
                    entity["exception"] = that.children('.exception').html();
                    entity["startTime"] = that.children('.startDate').html() + " " + that.children('.startTime').html();
                    entity["endTime"] = that.children('.endDate').html() + " " + that.children('.endTime').html();
                    entities.push(entity);
                }
            }else{
                var separator = id.length - 1; // we subtract one because of the plural
                that.children('td').each(function () {
                    var td = $(this);
                    if (!td.hasClass('delete')) {
                        var field = td.attr('class').split(' ')[0].substr(separator);
                        if (td.text == "") {
                            entity[field] = null;
                        } else {
                            entity[field] = td.text();
                        }
                    }
                });
                entities.push(entity);
            }
        });
        if (entities.length > 0) {
            $.ajax({
                url: baseUrl + '/' + id + '/save',
                type: "POST",
                data: {
                    "entities": entities
                }
            }).success(function (data) {
                if (data.success == 1) {
                    location.reload();
                }
                addMessage(data.message);
            }).error(function () {
                addMessage(gt.gettext("Something with wrong, please try again."));
            });
        }else{
            addMessage(gt.gettext("There are no changes to save."));
        }
    });

    if (isSchedulePage) {
        var table = $('.scheduleTable');
        var headersToDisable = [2];
        var headerCount = 2;
        var headers = table.find('thead th').each(function () {
            headersToDisable.push(headerCount++);
        });
        headersToDisable.pop();
        headersToDisable.pop();
        table = table.dataTable({
//            stateSave: true,
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "aoColumnDefs": [
                { 'bSortable': false, 'aTargets': headersToDisable }
            ],
            "order": [
                [ 1, "desc" ]
            ],
            "pagingType": "full_numbers",
            "iDisplayLength": 50
        }).on('length.dt', function (e, settings, len) {
            body.children(".fixedHeader").each(function () {
                $(this).remove();
            });
            table.fnAdjustColumnSizing();
            ofh = new $.fn.dataTable.FixedHeader(table);
        });

        $('.entry').on('mouseenter mouseleave', function () {
            var entry = $(this);
            if (entry.children('.entryValue').text() != '')
                $(this).toggleClass('highlighted');
        });

        $('.entryValue').each(function () {
            var span = $(this);
            if (span.text() != '')
                span.parent().on('hover', function () {
                    $(this).css('background', '#aaa');
                });
        });

        if($(document).width() <= 1280){
            table.css('table-layout','auto');
        }

        $('th').each(function(){
            var th = $(this);
            var split = th.text().split(' ');
            if(split[0] == "Sun"){
                $("td, th").filter(":nth-child("+ (th.index()+1) + ")").css('background-color',"#e1e1e1");
            }
        });

        $(document).on('dblclick', '.scheduleTable .entry', function (e) {
            var entry = $(this);
            if (!entry.hasClass('activeField') && !entry.hasClass('delete')) {
                resetActiveField();
                var td = entry.parent();
                entry.addClass('activeField');
                var editPanel = $('#editPanel');
                editPanel.find('input, select').val('');
                var position = td.position();
                var width = entry.outerWidth() < 100 ? 200 : entry.outerWidth();
                editPanel.css({
                    'width': width - 1,
                    'margin-left': position.left,
                    'top': position.top + td.outerHeight() + 85
                }).show();
                if (entry.children('.entryValue').html() != '') {
                    editPanel.find('input[name="entry[startTime]"]').val(entry.children('.startTime').text());
                    editPanel.find('input[name="entry[endTime]"]').val(entry.children('.endTime').text());
                }
                $('#editPanel input, #editPanel select').css("width", width - 10);
            }
        });

        $(document).on('click', '#formEditDone, #formEditRemove', function () {
            var span = $(this);
            var editPanel = span.parent();
            var activeField = $('.activeField');
            activeField.removeClass('activeField');
            var exception = editPanel.find('select[name="entry[exception]"] option:selected').text();
            var startTime = editPanel.find('input[name="entry[startTime]"]').val();
            var endTime = editPanel.find('input[name="entry[endTime]"]').val();
            if (span.attr('id') == 'formEditDone') {
                activeField.find('.exception').html(exception);
                activeField.find('.startTime').html(startTime);
                activeField.find('.endTime').html(endTime);
                if (exception != "None") {
                    activeField.children('.entryValue').html(exception);
                } else {
                    activeField.children('.entryValue').html(startTime + ' - ' + endTime);
                }
            } else {
                activeField.find('.exception').html("");
                activeField.find('.startTime').html("");
                activeField.find('.startDate').html("");
                activeField.find('.endDate').html("");
                activeField.find('.endTime').html("");
                activeField.children('.entryValue').html(" - ");
            }
            activeField.addClass('unsaved');
            editPanel.hide();
        });
    }else{
        table = $('.editableTable');
        table = $('.editableTable, #changelogs').dataTable({
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "aoColumnDefs": [
                { 'bSortable': false, 'aTargets': table.find('th').length-1 }
            ],
            "pagingType": "full_numbers",
            "dom": 'C<"clear">lfrtip',
            colVis: {
                order: 'alpha'
            },
            "iDisplayLength": 50
        }).on('length.dt', function (e, settings, len) {
            body.children(".fixedHeader").each(function () {
                $(this).remove();
            });
            table.fnAdjustColumnSizing();
            ofh = new $.fn.dataTable.FixedHeader(table);
        });
    }
    var ofh = new $.fn.dataTable.FixedHeader(table);

    $('td.delete').on('click', function () {
            if (confirm(gt.gettext("Are you sure about this action?"))) {
                var table = $('.editableTable');
                var td = $(this);
                var url = table.attr('id');
                var id = td.siblings(':first');
                $.ajax({
                    url: baseUrl + '/' + url + '/remove',
                    type: "POST",
                    data: {
                        "id": id.html()
                    }
                }).success(function (data) {
                    if (data.success == 1) {
                        td.parent().detach();
                    }
                    addMessage(data.message);
                }).error(function () {
                    addMessage(gt.gettext("Something with wrong, please try again."));
                });
            }
        }
    );

    $('#resetChanges').on('click', function () {
        location.reload();
    });
});