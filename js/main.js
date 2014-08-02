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

    if (!isMobile) {
        $('.selectable').each(function () {
            var select = $(this);
            select.selectbox();
            if (select.hasClass('championSelect')) {
                select.next('.sbHolder').find('ul li').each(function () {
                    var listItem = $(this);
                    var link = listItem.children('a').addClass('championName');
                    var name = link.html();
                    $('<img />', {
                        'src': 'images/champions/' + name + '.png',
                        'alt': name + ' thumbnail',
                        'class': 'championThumbnail'
                    }).prependTo(listItem);
                });
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
//
//    $('.sbHolder').on('click', function () {
//        var holder = $(this);
//        if (holder.prev().hasClass('championSelect')) {
//            if (!holder.hasClass('championsSaved')) {
//                holder.addClass('championsSaved');
//                holder.find('ul li').each(function () {
//                    var listItem = $(this);
//                    var name = listItem.children('a').html();
//                    $('<img />', {
//                        'src': 'images/champions/' + name + '.png',
//                        'alt': name + ' thumbnail',
//                        'class': 'championThumbnail'
//                    }).prependTo(listItem);
//                });
//            }
//        }
//    });
});