
$jFi('.foxrate-stars').mouseenter(function(){
    $jFi(this).children('.foxrate-tooltip').removeClass('hide');
});

$jFi('.foxrate-stars').mouseleave(function(){
    $jFi(this).children('.foxrate-tooltip').addClass('hide');
});