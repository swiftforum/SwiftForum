var IconPicker = (function() {
    function init() {
        var loaded = false;
        var iconList = $('#icon-selector-list');
        var iconPicker = $('.iconpicker');

        iconPicker.click(function()
        {
            if( loaded === false ) {
                // Load list of icons
                $.getJSON('/icons', function(data) {
                    $.each(data, function(key, val) {
                        iconList.append('<div class="col-sm-1"><a class="icon-selector-icon" data-iconid="' + val.id + '" href="#" title="' + val.id + ' : ' + val.name + '"><i class="' + val.name + '"></i></a></div>');
                    });
                    iconList.on('click', '.icon-selector-icon', selectIcon);
                    loaded = true;
                    toggleIconList();
                });

            } else {
                // Has already been loaded, just show the list
                toggleIconList();
            }
        });

        function selectIcon()
        {
            $('#icon-selector-preview').html($(this).html());
            iconPicker.val($(this).data('iconid'));
            toggleIconList();
        }

        function toggleIconList()
        {
            if (iconList.is(":hidden")) {
                iconList.slideDown("slow");
            } else {
                iconList.slideUp("slow");
            }
        }
    }

    return {init: init};
})();
