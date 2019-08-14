<?php

class Foxrate_Sdk_FoxrateRCI_ReviewAbstract {
    /**
     * Foxrate's api url
     * @var string
     */
    protected $foxrateApiUrl = 'http://api.foxrate.com';

    /**
     * Foxrate application link
     * @var string
     */
    protected $foxrateUrl = 'http://foxrate.de/';

    /**
     * Foxrate's api version
     * @var string
     */
    protected $sFoxrateAPI2version = 'v1';

    /**
     * Foxrate's api product
     * @var string
     */
    protected $sFoxrateAPI2products= 'products';

    /**
     * Foxrate's api product
     * @var string
     */
    protected $sFoxrateAPI2reviews= 'reviews';

    /**
     * Foxrate's api vote
     * @var string
     */
    protected $sFoxrateAPI2vote= 'vote';

    /**
     * Foxrate's api abuse
     * @var string
     */
    protected $sFoxrateAPI2abuse = 'abuse';

    /**
     * Foxrate's api sellers
     * @var string
     */
    protected $sFoxrateAPI2sellers= 'sellers';

    /**
     * Foxrate's api ratings
     * @var string
     */
    protected $sFoxrateAPI2ratings = 'ratings';


    /** Foxrate's api channels string
     * @var string
     */
    protected $sFoxrateAPI2channels = 'channels';

    /** Foxrate's config name for last import name in db
     * @var string
     */
    protected $sFoxrateConfNameImportDate = "foxrate_lastProductReviewImport";

    /**
     * Foxrate's api seller id
     * @var string
     */
    protected $sFoxrateAPI2sellerId = '';

    /**
     * Foxrate's locally stored review expiration period (hours)
     * This timeout is for cron update, which will import all reviews
     * This expire time should be equal or less than Cache on Demand timeout
     * @var string
     */
    protected $sReviewsExpirePeriod = '6';

    /**
     * Foxrate's locally single stored review expiration period (hours)
     * This timeout is checked by cache on demand, for review file's timestamp
     * This expiration should be equal or greater than Expire time for Cron import
     * @var string
     */
    protected $sReviewsExpirePeriodCacheDemand = '1';

    /**
     * Lock expire time. (minutes). If this time period has passed since lock creation, it is assumed something broke
     * and lock is treated as expired.
     * @var string
     */
    protected $sReviewsExpireLockCacheDemand = '30';

    /**
     * Foxrate's username for API
     * @var string
     */
    protected $sFoxrateAPIUsername = "";
    /**
     * Foxrate's password for API
     * @var string
     */
    protected $sFoxrateAPIPassword = "";

    /**
     * Foxrate's shop id
     * @var string
     */
    protected $sFoxrateAPIShopId= "";

    /**
     * Foxrate's product review import logger path
     * @var string
     */
    protected $sFoxrateLoggerFileName = 'foxrateProductReviewLog.txt';

    /**
     * Import settings for product reviews
     * @var mixed|string
     */
    protected $sFoxrateSettings=array();

    /**
     * RegExp for date extraction from api
     * @var string
     */
    protected $regExpDateFromApi ="/(\d+-\d+-\d+)T\d+:\d+:\d+\+\d+/";

    /**
     * Review variable from api
     * @var string
     */
    protected $sAPIResRev = 'reviews';

    /**
     * Review count variable from api
     * @var string
     */
    protected $sAPIResRevCount = 'reviews_count';

    /**
     * Page count variable from api
     * @var string
     */
    protected $sAPIResPageCnt = 'pages_count';

    /**
     * Current page from api
     * @var string
     */
    protected $sAPIResCurPage = 'current_page';

    /**
     * Foxrate application link
     * @var string
     */
    protected $sFoxrateProdProfLink = 'product_rate';

    /**
     * Default number of reviews if user has not set it
     * @var int
     */
    protected $sFoxrateDefaultRevsPerPage = 20;

    /**
     * Registry object
     * @var
     *
     */
    public $registry;


    /**
     * Config object
     * @var
     */
    public $config;

    public function setSettings()
    {
        $this->sFoxrateAPIUsername = $this->config->getConfigParam("foxrateUsername");
        $this->sFoxrateAPIPassword = $this->config->getConfigParam("foxratePassword");

        $revsPerPage = $this->config->getConfigParam('foxratePR_RevsPerPage');
        if(!$revsPerPage){
            $revsPerPage = $this->sFoxrateDefaultRevsPerPage;
        }
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_RevsPerPage' => $revsPerPage));
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_SortBy' => $this->config->getConfigParam('foxratePR_SortBy')));
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_SortOrder' => $this->config->getConfigParam('foxratePR_SortOrder')));
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_Summary' => $this->config->getConfigParam('foxratePR_Summary')));
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_OrderRichSnippet' => $this->config->getConfigParam('foxratePR_OrderRichSnippet')));
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_CatalogDisplay' => $this->config->getConfigParam('foxratePR_CatalogDisplay')));
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_CatalogTooltip' => $this->config->getConfigParam('foxratePR_CatalogTooltip')));
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_WriteReview' => $this->config->getConfigParam('foxratePR_WriteReview')));
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array("foxratePR_Page" => 1));
    }


} 