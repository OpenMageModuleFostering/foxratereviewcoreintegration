globalStar = '';
globalSort = '';
globalSearch = '';
jQuery('.starFilter').click(function(){jQuery('#showAll').removeClass('hide')});
jQuery('#showAll').click(function(){jQuery('#showAll').addClass('hide')});
jQuery("#searchExec").click(function(){loadUserRevPage(1 , document.getElementById('shopUrl').value, document.getElementById('productId').value, {frsearch:document.getElementById('frsearch').value})});
jQuery("#sortingExec").change(function(){loadUserRevPage(1, document.getElementById('shopUrl').value, document.getElementById('productId').value, {sort:this.value})});
jQuery("#showAllExec").click(function(){loadUserRevPage(1, document.getElementById('shopUrl').value, document.getElementById('productId').value, {star:''})});
//jQuery('#foxrateProductReviews').ready(cacheOnDemandProdRev(document.getElementById('shopUrl').value, document.getElementById('productId').value));

jQuery('#readReviews').click(function() {
    jQuery('html, body').animate({
        scrollTop: jQuery("#itemTabs").offset().top
    }, 1000);

    jQuery('#foxrateProductReviewsTab').click();
});
jQuery("#search").keypress(function(event) {
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

    jQuery.noConflict();

    call = jQuery.ajax({
        type:"POST",
        url:shopUrl + '?ajax=true',
        data:urlData
    });
    userRevBlock = jQuery("#userReviews");
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
    jQuery.noConflict();
    call = jQuery.ajax({type: 'POST', url:shopUrl, data:urlData});
    call.done(function(msg){
        obj = JSON.parse(msg);
        if(obj.status == "OK")
        {
            jQuery("#review-"+revId).fadeOut(300, function () {
            jQuery("#reviewThanks-"+revId).fadeIn(300, function(){});
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
    jQuery.noConflict();
    call = jQuery.ajax({type: 'POST', url:shopUrl, data:urlData});
    call.done(function(msg){
        obj = JSON.parse(msg);
        if(obj.status == "OK")
        {
            jQuery("#review-"+revId).fadeOut(300, function () {
                jQuery("#reviewThanks-"+revId).fadeIn(300, function(){});
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
    call = jQuery.ajax({type: 'POST', url:shopUrl, data:urlData});
    call.done(function(msg){
        //for debuging
        //alert(msg);
    })
}





