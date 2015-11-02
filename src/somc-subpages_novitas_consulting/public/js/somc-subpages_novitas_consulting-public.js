/**
 * JS for the public-facing functionality of the Somc Sub Pages Plugin
 *
 */

(function( $ ) {
	'use strict';
    $(document).on('click', 'li.expandable .expand', function (){
        $(this).parent().parent().children('ul').slideToggle();
        var expand = $(this);
        if($(expand).hasClass('open-arrow')){
            $(expand).removeClass('open-arrow').addClass('close-arrow');
        }
        else{
            $(expand).removeClass('close-arrow').addClass('open-arrow');
        }
        return false;
    });

})( jQuery );
