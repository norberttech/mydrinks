define(['jquery'], function($) {
    $('[data-toggle]').on('click', function(event) {
        var self = $(this);
        event.preventDefault();
        
        $('#' + self.data('toggle')).slideToggle();
    })
});
