var IconPicker = (function() {
    function init() {
        var loaded = false;
        var iconPicker = $('.iconpicker');
        var iconPickerField = $('.iconpicker-field');
        var iconPickerPreview = $('.iconpicker-preview');

        var icons = '';

        iconPicker.click( function(e) {
            if( loaded === false ) {
                // Load list of icons:
                $.getJSON('/icons', function(data) {
                    $.each(data, function(key, val) {
                        icons = icons + '<div class="col-xs-1 iconpicker-col-icon" data-iconid="' + val.id + '" title="' + val.id + ' : ' + val.name + '"><i class="' + val.name + '"></i></div>';
                    });
                    loaded = true;

                    // Set up the popover:
                    iconPicker.popover({
                        placement : 'auto',
                        container : 'body',
                        html      : true,
                        content   : icons,
                        trigger   : 'manual',
                        template  : '<div class="popover iconpicker-popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
                    });
                    iconPicker.popover('show');
                    $('.iconpicker-popover').on('click', '.iconpicker-col-icon', selectIcon);
                });
            } else {
                // Show the popover:
                iconPicker.popover('toggle');
            }
        });

        function selectIcon(e) {
            e.preventDefault();
            iconPickerPreview.html($(this).html());
            iconPickerPreview.children('i').addClass('icon-large');

            iconPickerField.val($(this).data('iconid'));
            iconPicker.popover('toggle');
        }

        $('body').on('click', function (e) {
            if ($('body > div.iconpicker-popover:visible').length && !iconPicker.is(e.target) && iconPicker.has(e.target).length === 0 && $('.iconpicker-popover').has(e.target).length === 0) {
                iconPicker.popover('toggle');
            }
        });
    }

    return {init: init};
})();