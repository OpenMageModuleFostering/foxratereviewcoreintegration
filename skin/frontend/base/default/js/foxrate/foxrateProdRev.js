globalStar = '';
globalSort = '';
globalSearch = '';

var j183 = new function(){

    // j183 v1.7
    var jQuery = window.jQuery.noConflict(true); // create jQuery varible in local scope
    var $ = jQuery;

    this.version = function() {
        // in this plase we always have 1.7 version of jQuery
        return jQuery.fn.jquery;
    }

    this.getjQuery = function() {
        return jQuery;
    }
}

$j183 = j183.getjQuery();

$j183('.starFilter').click(function(){j183('#showAll').removeClass('hide')});
$j183('#showAll').click(function(){j183('#showAll').addClass('hide')});
$j183("#searchExec").click(function(){loadUserRevPage(1 , document.getElementById('shopUrl').value, document.getElementById('productId').value, {frsearch:document.getElementById('frsearch').value})});
$j183("#sortingExec").change(function(){loadUserRevPage(1, document.getElementById('shopUrl').value, document.getElementById('productId').value, {sort:this.value})});
$j183("#showAllExec").click(function(){loadUserRevPage(1, document.getElementById('shopUrl').value, document.getElementById('productId').value, {star:''})});
$j183('#foxrateProductReviews').ready(cacheOnDemandProdRev(document.getElementById('shopUrl').value, document.getElementById('productId').value));
$j183('#readReviews').click(function() {
    $j183('html, body').animate({
        scrollTop: $j183("#itemTabs").offset().top
    }, 1000);

    $j183('#foxrateProductReviewsTab').click();
});
$j183("#search").keypress(function(event) {
    if (event.which == 13) {
        event.preventDefault();
        loadUserRevPage(1 , document.getElementById('shopUrl').value, document.getElementById('productId').value, {frsearch:document.getElementById('frsearch').value});
    }
});

/**
 * ajax for user page review loading
 * @param revPage
 * @param shopUrl
 * @param prodId
 * @param params
 */
function loadUserRevPage(revPage, shopUrl, prodId, params)
{
    urlData = { cl:"foxrate_userreviews", page:revPage, product:prodId}

    if (typeof params == 'undefined') {
        params = {};
    }

    if ((typeof params['star'] != 'undefined')) {
        urlData['star_filter'] = params['star'];
        globalStar = params['star'];
    } else {
        urlData['star_filter'] = globalStar;
    }

    if (typeof params['sort'] != 'undefined') {
        urlData['sort'] = params['sort'];
        globalSort = params['sort'];
    } else {
        urlData['sort'] = globalSort;
    }

    if (typeof params['frsearch'] != 'undefined') {
        urlData['frsearch'] = params['frsearch'];
        globalSearch = params['frsearch'];
    } else {
        urlData['frsearch'] = globalSearch;
    }

    call = $j183.ajax({
        type:"POST",
        url:shopUrl + '?ajax=true',
        data:urlData
    });
    userRevBlock = $j183("#userReviews");
    call.done(function (msg) {
        userRevBlock.fadeOut(300, function () {
            userRevBlock.html(msg);
            userRevBlock.fadeIn(230, function () {
            });
        });
    });
}

/**
 * ajax for submitting review votes
 * @param shopUrl
 * @param revId
 * @param useful
 */
function voteReview(shopUrl, revId, useful)
{
    urlData = { cl:"foxrate_apicall", fnc:"voteReview", review:revId, useful: useful};
    call = $j183.ajax({type: 'POST', url:shopUrl, data:urlData});
    call.done(function(msg){
        obj = JSON.parse(msg);
        if(obj.status == "OK")
        {
            $j183("#review-"+revId).fadeOut(300, function () {
                $j183("#reviewThanks-"+revId).fadeIn(300, function(){});
            });

        }
    })
}

/**
 * ajax for reporting abuse
 * @param shopUrl
 * @param revId
 * @param useful
 */
function abuseReview(shopUrl, revId, abuse)
{
    urlData = { cl:"foxrate_apicall", fnc:"abuseReview", review:revId, abuse: abuse};
    call = $j183.ajax({type: 'POST', url:shopUrl, data:urlData});
    call.done(function(msg){
        obj = JSON.parse(msg);
        if(obj.status == "OK")
        {
            $j183("#review-"+revId).fadeOut(300, function () {
                $j183("#reviewThanks-"+revId).fadeIn(300, function(){});
            });

        }
    })
}

/**
 * Ajax call for 'Caching on Demand'
 */
function cacheOnDemandProdRev(shopUrl, prodId)
{
    urlData = { cl:"foxrate_apicall", fnc:"cache_demand", product:prodId};
    call = $j183.ajax({type: 'POST', url:shopUrl, data:urlData});
    call.done(function(msg){
        //for debuging
        //alert(msg);
    })
}
