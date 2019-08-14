<?php

namespace features;

use \Behat\Behat\Context\ClosuredContextInterface,
    \Behat\Behat\Context\TranslatedContextInterface,
    \Behat\Behat\Context\BehatContext,
    \Behat\MinkExtension\Context\MinkContext,
    \Behat\Behat\Exception\PendingException;
use \Behat\Gherkin\Exception\Exception;
use \Behat\Gherkin\Node\PyStringNode,
    \Behat\Gherkin\Node\TableNode,
    \Behat\Mink\Mink,
    \Behat\Mink\Session as MinkSession;
use \Behat\Mink\Driver\Selenium2Driver;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{

    static $selServer='http://selenium.vm:4444/wd/hub';   //selenium server ip
    static $selBrowser='firefox';   // selenium browser to test against
    static $mink;
    private $session;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $mink = self::prepareMink();
        $this->session = $mink->getSession('session');
        $this->parameters = $parameters;
    }

    public static function prepareMink()
    {
        if(!isset(self::$mink)){
            $driver = new Selenium2Driver(self::$selBrowser, null, self::$selServer);
            $session = new MinkSession($driver);
            $mink = new Mink();
            $mink->registerSession('session', $session);
            self::$mink=$mink;
        }
        return self::$mink;
    }

    /**
     * @Given /^I am on a product page$/
     */
    public function iAmOnAProductPage()
    {
        $this->session->visit($this->parameters['base_url'] . $this->parameters['product_uri']);
    }

    /**
     * @Given /^I hover over "([^"]*)"$/
     */
    public function iHoverOver($arg1)
    {
        $this->session->getPage()->find('css', $arg1)->mouseOver();
        $this->session->wait(5000, "$('#cart_block_list').hasClass('expanded')");
    }

    /**
     * @Then /^I should see the product title$/
     */
    public function iShouldSeeTheProductTitle()
    {
        $product_title = $this->session->getPage()->find('css', '.cart_block_product_name');
    }

    /**
     * @When /^I click "([^"]*)"$/
     */
    public function iClick($arg1)
    {
        $el = $this->session->getPage()->find('xpath', '//a[text()="' . $arg1 . '"]');
        if ($el === null) {
            throw new Exception("Element $arg1 not found");
        }
        $el->click();
        $this->session->wait(5000, "$('#more_info_tabs a.idTabHrefShort').hasClass('selected')");
    }

    /**
     * @Then /^I should see product reviews$/
     */
    public function iShouldSeeProductReviews()
    {
        $reviews_tab = $this->session->getPage()->find('css', '#product_comments_block_tab');
        if(empty($reviews_tab)){
            throw new \Behat\Gherkin\Exception\Exception("Reviews tab is not present");
        }
    }

    /**
     * @Then /^I should see foxrate product reviews$/
     */
    public function iShouldSeeFoxrateProductReviews()
    {

        $reviews_tab = $this->session->getPage()->find('css', 'a[href^="#'  . $this->parameters['review_tab'] . '"]');
        //$reviews_tab_content = $this->session->getPage()->find('css', '#foxrateProductReviews');
        if(empty($reviews_tab) )
        {
            throw new \Behat\Gherkin\Exception\Exception("Foxrate Reviews tab is not present");
        }

        $reviews_tab_content = $this->session->getPage()->find('css', '#foxrateProductReviews');
        if(empty($reviews_tab_content) )
        {
            throw new \Behat\Gherkin\Exception\Exception("Foxrate Reviews tab Content is not present");
        }
    }

    /**
     * @Then /^I should see foxrate product rating box$/
     */
    public function iShouldSeeFoxrateProductRatingBox()
    {
        $reviews_tab = $this->session->getPage()->find('css', '.rating-box');
        if(empty($reviews_tab)){
            throw new \Behat\Gherkin\Exception\Exception("Foxrate Rating Box is not present");
        }
    }

    /**
     * @Then /^I should not see foxrate product rating box$/
     */
    public function iShouldNotSeeFoxrateProductRatingBox()
    {
        $reviews_tab = $this->session->getPage()->find('css', '.rating-box');
        if(!empty($reviews_tab)){
            throw new \Behat\Gherkin\Exception\Exception("Foxrate Rating Box is present");
        }
    }

    /**
     * @Given /^Product has one review with "([^"]*)" rating$/
     */
    public function productHasOneReviewWithRating($rating)
    {

        $json = '{"sum":' . $rating . ',"count":1,"average":"' . $rating . '.00","counts":{"1":0,"2":0,"3":0,"4":0,"5":"1"},"recommends":{"yes":"0","yes_percent":"0","no":"0","count":"0"},"images":{"12px":"https:\/\/foxrate.de\/images\/widgets\/api\/12\/5_00.png","14px":"https:\/\/foxrate.de\/images\/widgets\/api\/14\/5_00.png"},"product_name":"Daiktas","questions_averages":{"price":0,"quality":0,"usability":0}}';
        $path  = $this->parameters['cache_file_dir'] . $this->parameters['product_id'] . '.gener.json';

        self::write_file($path, $json);
    }

    /**
     * @Then /^I should see foxrate review summary with "([^"]*)" big stars$/
     */
    public function iShouldSeeFoxrateReviewSummaryWithBigStars($starsCount)
    {
        $stars_width = $this->session->getPage()->find('css', '.frRating-top .yellow')->getAttribute('style');
        preg_match('/width: ?([0-9]*)%;/', $stars_width, $matches);
        if ($starsCount * 20 !=  (int)$matches[1]) {
            throw new \Behat\Gherkin\Exception\Exception("Foxrate review summary shows wrong number of big stars!");
        }
    }

    /**
     * @Given /^I should see text "([^"]*)" foxrate rating$/
     */
    public function iShouldSeeTextFoxrateRating($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see text "([^"]*)"$/
     */
    public function iShouldSeeText($arg1)
    {
        throw new PendingException();
    }


    public static function write_file($file, $content) {
        $fp = fopen($file , 'w');
        fwrite($fp, $content);
        fclose($fp);
    }

}
