var IconPicker = (function() {
    function init() {
        var loaded = false;
        var iconPicker = $('.iconpicker');
        var icons = '';

        iconPicker.click( function(e) {
            if( loaded === false ) {
                // Load list of icons:
                $.getJSON('/icons', function(data) {
                    $.each(data, function(key, val) {
                        icons = icons + '<div class="col-xs-1 icon-picker-col-icon" data-iconid="' + val.id + '" href="#" title="' + val.id + ' : ' + val.name + '"><a class="icon-picker-icon"><i class="' + val.name + '"></i></a></div>';
                    });
                    loaded = true;

                    // Set up the popover:
                    iconPicker.popover({
                        placement : 'auto',
                        container : 'body',
                        html      : true,
                        content   : icons,
                        trigger   : 'manual',
                        template  : '<div class="popover icon-picker-popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
                    });
                    iconPicker.popover('show');
                    $('.icon-picker-popover').on('click', '.icon-picker-col-icon', selectIcon);
                });
            } else {
                // Show the popover:
                iconPicker.popover('toggle');
            }
        });

        function selectIcon(e) {
            e.preventDefault();
            $('#icon-selector-preview').html($(this).html());
            iconPicker.val($(this).data('iconid'));
            iconPicker.popover('hide');
        }

        $('body').on('click', function (e) {
            if (!iconPicker.is(e.target) && iconPicker.has(e.target).length === 0 && $('.icon-picker-popover').has(e.target).length === 0) {
                iconPicker.popover('hide');
            }
        });
    }

    return {init: init};
})();