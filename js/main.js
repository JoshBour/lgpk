$(function () {
    var body = $('body');
    var flash = $('#flash');
    var focusedDiv;
    if (flash.is(':visible')) {
        flash.setRemoveTimeout(6000);
    }
    var gt = new Gettext({ 'domain': 'messages' });

    $('.printTrigger').on('click', function (e) {
        e.preventDefault();
        window.print();
    });

    // This will make all images with class hoverable to be hovered (duh)
    var previousImage = '';
    $(document).on('mouseenter', 'img[class*="hoverable"]',function () {
        var src = $(this).attr('src');
        previousImage = src;
        var details = getImageDetails(src);
        $(this).attr('src', baseUrl + '/images/' + details[0] + '-hover.' + details[1]);
    }).on('mouseleave', 'img[class*="hoverable"]', function () {
        $(this).attr('src', previousImage);
        previousImage = '';
    });

    // if the shadow div exists, when you click outside of the
    // focused box it should be reverted to normal
    $(document).on('click', function (event) {
        if (event.target.id == 'shadow') {
            focusedDiv.unfocusLight();

            $('body').removeClass('unscrollable');
//            if (focusedDiv.attr('id') == 'stage') {
//                focusedDiv.find('iframe').detach();
//                focusedDiv.hide();
//            } else {
            focusedDiv.detach();
//            }
        }
    });

    $('#generatedVideos').children('li').children('.button').on('click', function () {
        var btn = $(this);
        $.ajax({
            url: baseUrl + '/admin/tutorials/save',
            type: "POST",
            data: {
                "player": btn.siblings("input[name='player']").val(),
                "champion": btn.siblings("input[name='champion']").val(),
                "opponent": btn.siblings("input[name='opponent']").val(),
                "position": btn.siblings("input[name='position']").val(),
                "videoId": btn.siblings("input[name='videoId']").val()
            }
        }).success(function (data) {
            if (data.success == 1) {
                btn.parent().detach();
            }
            addMessage(data.message);
        }).error(function () {
            addMessage(gt.gettext("Something with wrong, please try again."));
        });
    });

    $('#announcement').find('.remove').on('click', function () {
        var elem = $(this).parent();
        var key = elem.attr('data-target');
        var cookieArray = jQuery.parseJSON(decodeURIComponent(readCookie("announcements")));
        cookieArray[key] = false;
        console.log(cookieArray);
        var cookie = encodeURIComponent(JSON.stringify(cookieArray));
        elem.detach();
        createCookie("announcements", cookie, 5);
    });

    if (!isMobile) {
        $('.selectable').each(function () {
            var select = $(this);
            select.selectbox();
            if (select.hasClass('championSelect')) {
                select.next('.sbHolder').find('ul li').each(function () {
                    var listItem = $(this);
                    var link = listItem.children('a').addClass('championName');
                    var name = link.html();
                    var span = $('<span />', {
                        "class": "nameValue",
                        "html": name
                    });
                    link.html(span);
                    $('<img />', {
                        'src': baseUrl + '/images/champions/' + name + '.png',
                        'alt': name + ' thumbnail',
                        'class': 'championThumbnail'
                    }).prependTo(link);
                });
                var holder = select.next('.sbHolder').css('width', '263px');
                holder.children('.sbSelector, .sbOptions').css('width', '263px');
                holder.after($('<img class="search hoverable" title="Search for a champion" src="' + baseUrl + '/images/search.png" />'));
            }
        });
    }

    $('.extraOptionsToggle').on('click', function () {
        var toggler = $(this);
        var extraOptions = $('#extraOptions');
        if (extraOptions.is(":visible")) {
            extraOptions.slideUp();
            toggler.html('Click for additional options +');
        } else {
            extraOptions.slideDown();
            toggler.html('Click for additional options -');
        }
    });

    $(document).on('click', 'img.search', function () {
        var img = $(this);
        var holder = img.siblings('.sbHolder');
        var search = img.siblings('.championSearch');
        if (holder.is(":visible")) {
            holder.hide();
            if (search.length <= 0) {
                search = $('<input />', {
                    'class': "championSearch",
                    'placeholder': "Type a champion's name..",
                    'css': {
                        "width": "263px",
                        "padding": "0"
                    }
                }).insertBefore(img);
                search.on('keyUp', function (e) {
                    alert(e);
                });
                $('<ul />', {
                    "class": "championResults"
                }).insertAfter(img);
            } else {
                search.show();
                img.siblings('.championResults').show();
            }
        } else {
            holder.show();
            search.hide();
            img.siblings('.championResults').hide();
        }
    });

    $(document).on('click', '.championResults li', function () {
        var li = $(this),
            resultBox = li.parent(),
            selectBox = resultBox.siblings('select'),
            input = resultBox.siblings('input'),
            value = li.children('a').attr('rel'),
            holder = selectBox.siblings('.sbHolder').show(),
            options = holder.find('.sbOptions');

//        selectBox.selectbox('detach');
//        selectBox.val(li.children('a').attr('rel'));
//        selectBox.selectbox('attach');

        var option = selectBox.find("option[value=" + value + "]");
        selectBox.selectbox("change", option.attr('value'), option.html());
        options.find('a.sbActiveSelection').removeClass('sbActiveSelection');
        options.children('li').each(function () {
            var link = $(this).children('a');
            if (link.attr('rel') == value) {
                link.addClass('sbActiveSelection');
            }
        });
        input.val('');
        input.hide();
        resultBox.html('').hide();
        holder.show();

    });

    $('#tutorials').perfectScrollbar();

    var isSearching = false;
    $(document).on('keyup', '.championSearch', function (event) {
        var input = $(this),
            value = input.val().trim().toLowerCase(),
            options = input.siblings('.sbHolder').find('.sbOptions'),
            resultBox = input.siblings('.championResults');
        delay(function () {
            event.preventDefault();
            if (value.length <= 0) {
                resultBox.html('');
            } else {
                var results = "";
                options.find('li').each(function () {
                    var name = $(this).find('.nameValue').html().toLowerCase();
                    if (name.indexOf(value) > -1) {
                        results += $(this).prop('outerHTML');
                    }
                });
                resultBox.html(results);
            }
        }, 150);

    });

    $('#saveResults').on('click', function (e) {
        e.preventDefault();
        html2canvas($('#searchResultBox'), {
            onrendered: function (canvas) {
//            var myImage = canvas.toDataURL("image/png");
//            var win = window.open(myImage);
//            win.focus();
                var img = canvas.toDataURL("image/png");
                var output = encodeURIComponent(img);
                $.ajax({
                    type: "POST",
                    url: baseUrl + "/download",
                    data: {
                        "image": img
                    }
                }).success(function (data) {
                    if (data.name){
                        window.location = baseUrl + '/download/' + data.name;
                        $.ajax({
                            type: "POST",
                            url: baseUrl + "/download",
                            data: {
                                "image": data.name,
                                "delete" : "delete"
                            }
                        });
                    }else{
                        addMessage("Something went wrong, please try again!");
                    }
                });

            }
        });
    });

    /**
     * @description Creates the stage and plays a video.
     */
    $(document).on('click', '.videoMask, #tutorials li a', function (e) {
        if (!isMobile) {
            e.preventDefault();
            e.stopPropagation();
            var playBtn = $(this).parent();
            var listItem = playBtn.closest('li');
            var videoId = listItem.attr('class').substr(9);
            var stageWrapper = $('<div/>', {
                'id': "stageWrapper"
            }).prependTo($('body'));

            var stage = $('<div/>', {
                'id': 'stage'
            }).appendTo(stageWrapper);

            var videoWrapper = $('<div/>', {
                'id': 'videoWrapper'
            }).appendTo(stage);

            var videoPlayer = $('<iframe/>', {
                "id": "youtubeVideo",
                "frameborder": 0,
                "src": "http://www.youtube.com/embed/" + videoId + "?autoplay=1"
            }).appendTo(videoWrapper);

            focusedDiv = stageWrapper;
            stageWrapper.addClass('target-' + videoId).focusLight();
            body.addClass('unscrollable');
        } else {
            var playLink = $(this).siblings('a');
            window.location.href = playLink.attr('href');
        }
    });
});