
jQuery('.foxrate-stars').mouseenter(function(){
    jQuery(this).children('.foxrate-tooltip').removeClass('hide');
});

jQuery('.foxrate-stars').mouseleave(function(){
    jQuery(this).children('.foxrate-tooltip').addClass('hide');
});