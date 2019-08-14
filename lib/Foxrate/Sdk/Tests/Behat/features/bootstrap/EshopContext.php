<?php

namespace features;

include_once('VMContext.php');

use Behat\Behat\Exception\PendingException;
use Guzzle\Service\Client;

/**
 * Features context.
 */
class EshopContext extends VMContext
{

    private $jsonRsonse;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        parent::__construct($parameters);
        $this->client = new Client();
    }

    /**
     * @When /^I am on a product page$/
     */
    public function iAmOnAProductPage()
    {
        $this->getSession()->visit($this->getDefaultProductUri());
    }

    /**
     * @Given /^Product has one page of foxrate product reviews$/
     */
    public function productHasOnePageOfFoxrateProductReviews()
    {
        $json = '{"current_page":1,"pages_count":1,"reviews_count":3,"reviews":[{"id":1228,"date":"2012-06-25T10:22:01+0200","name":"Laure","stars":3,"comment_pros":"Die St\u00fchle sind etwas schwerg\u00e4ngig und nicht f\u00fcr Hochlehnauflagen geeignet.","comment_cons":"comment ot laura3333","comment_conclusion":"one of the best","comment":"coment from laura1111","anonymous":false,"rating_question_first":2,"rating_question_second":3,"rating_question_third":4,"source":"shop","this_is_useful":{"yes":0,"no":1,"total":1},"reviewer_verified":true,"recommends_for_others":"no_answer","images":{"12px":"https:\/\/foxrate.de\/images\/widgets\/api\/12\/3_00.png","14px":"https:\/\/foxrate.de\/images\/widgets\/api\/14\/3_00.png"}},{"id":1224,"date":"2012-04-10T10:22:01+0200","name":"Test User","stars":2,"comment_pros":"test2 comment2","comment_cons":"test 3comment3","comment_conclusion":"asdasd alles bestens","comment":"test 1 comment","anonymous":false,"rating_question_first":2,"rating_question_second":3,"rating_question_third":4,"source":"shop","this_is_useful":{"yes":23,"no":12,"total":35},"reviewer_verified":true,"recommends_for_others":"no_answer","images":{"12px":"https:\/\/foxrate.de\/images\/widgets\/api\/12\/2_00.png","14px":"https:\/\/foxrate.de\/images\/widgets\/api\/14\/2_00.png"}},{"id":1219,"date":"2012-04-15T10:22:06+0200","name":"bingo","stars":5,"comment_pros":"b comment2","comment_cons":"b comment3","comment_conclusion":"gut gut","comment":"bingo bingocomm","anonymous":false,"rating_question_first":2,"rating_question_second":3,"rating_question_third":4,"source":"shop","this_is_useful":{"yes":0,"no":0,"total":0},"reviewer_verified":true,"recommends_for_others":"no_answer","images":{"12px":"https:\/\/foxrate.de\/images\/widgets\/api\/12\/5_00.png","14px":"https:\/\/foxrate.de\/images\/widgets\/api\/14\/5_00.png"}}]}';
        $path  = $this->parameters['cache_file_dir'] . $this->parameters['product_id'] . '.page1.json';

        $this->write_file($path, $json);
    }

    /**
     * @Given /^I hover over "([^"]*)"$/
     */
    public function iHoverOver($arg1)
    {
        $this->getSession()->getPage()->find('css', $arg1)->mouseOver();
        $this->getSession()->wait(5000, "$('#cart_block_list').hasClass('expanded')");
    }

    /**
     * @Then /^I should see the product title$/
     */
    public function iShouldSeeTheProductTitle()
    {
        $product_title = $this->getSession()->getPage()->find('css', '.cart_block_product_name');
    }

    /**
     * @When /^I click "([^"]*)"$/
     */
    public function iClick($arg1)
    {
        $el = $this->getSession()->getPage()->find('xpath', '//a[text()="' . $arg1 . '"]');
        if ($el === null) {
            throw new \Exception("Element $arg1 not found");
        }
        $el->click();
    }

    /**
     * @Then /^I should see foxrate product review tab content$/
     */
    public function iShouldSeeFoxrateProductReviewTabContent()
    {
        $this->assertElementOnPage('#foxrateProductReviews');
    }

    /**
     * @Then /^I should see foxrate product rating box$/
     */
    public function iShouldSeeFoxrateProductRatingBox()
    {
        $this->assertElementOnPage('.frRating-top');
    }

    /**
     * @Then /^I should not see foxrate product rating box$/
     */
    public function iShouldNotSeeFoxrateProductRatingBox()
    {
        $reviews_tab = $this->getSession()->getPage()->find('css', '.rating-box');
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

        $this->write_file($path, $json);
    }


    /**
     * @Given /^I should see foxrate product review summary$/
     */
    public function iShouldSeeFoxrateProductReviewSummary()
    {
        $this->assertElementOnPage('.frRating-top-all');
    }

    /**
     * @Given /^I have product$/
     */
    public function iHaveProduct()
    {
        //we currently we use default products
    }

    /**
     * @Given /^I have default product$/
     */
    public function iHaveDefaultProduct()
    {
        //we don't add default product as it exists
    }

    /**
     * @Given /^I have product with reviews$/
     */
    public function iHaveProductWithReviews()
    {
        //we currently we use default products
    }

    /**
     * @When /^I am on product details page$/
     */
    public function iAmOnProductDetailsPage()
    {
        return $this->iAmOnAProductPage();
    }



    /**
     * @Then /^I see product aggregating rating rich snippet$/
     */
    public function iSeeProductAggregatingRatingRichSnippet()
    {
        if (!$this->getAggregateRating()) {
            throw new \Exception('Aggregating rich snippet not found');
        }
    }

    /**
     * @Then /^I see product review count rich snippet$/
     */
    public function iSeeProductReviewCountRichSnippet()
    {
        if (!$this->getAggregateRating()) {
            throw new \Exception('Aggregating rich snippet not found');
        }
    }

    /**
     * @Then /^I see product review author rich snippet$/
     */
    public function iSeeProductReviewAuthorRichSnippet()
    {
        if (!$this->getReviewAuthor()) {
            throw new \Exception('Review name rich snippet not found');
        }
    }

    /**
     * @Then /^I see product review name rich snippet$/
     */
    public function iSeeProductReviewNameRichSnippet()
    {
        if (!$this->getReviewName()) {
            throw new \Exception('Review name rich snippet not found');
        }
    }

    /**
     * @Then /^I see product review description rich snippet$/
     */
    public function iSeeProductReviewDescriptionRichSnippet()
    {
        if (!$this->getReviewDescription()) {
            throw new \Exception('Review name rich snippet not found');
        }
    }

    /**
     * @Then /^I see product review publish date rich snippet$/
     */
    public function iSeeProductReviewPublishDateRichSnippet()
    {
        if (!$this->getReviewDatePublished()) {
            throw new \Exception('Review name rich snippet not found');
        }
    }

    /**
     * @Given /^I am on a default category page$/
     */
    public function iAmOnADefaultCategoryPage()
    {
        $this->getSession()->visit($this->getDefaultProductCategoryUri());
    }

    /**
     * @When /^I select "([^"]*)" view$/
     */
    public function iSelectView($view)
    {
        $this->getSession()->visit($this->getDefaultProductCategoryUri() . '?ldtype=' . $view);

    }

    /**
     * @Then /^I should see foxrate product rating stars of default product$/
     */
    public function iShouldSeeFoxrateProductRatingStarsOfDefaultProduct()
    {
        $stars = $this->getSession()->getPage()->find('xpath', '//a[@href="' . $this->getDefaultProductUri() . '"]/parent::*/*[contains(@class, "foxrate-stars")]');
        if(empty($stars)){
            throw new \Behat\Gherkin\Exception\Exception("Stars are not present in this view");
        }
    }

    /**
     * @Then /^I should see foxrate product rating stars$/
     */
    public function iShouldSeeFoxrateProductRatingStars()
    {
        $stars = $this->getSession()->getPage()->find('xpath', '//*[contains(@class, "productData")][1]/descendant::*/*[contains(@class, "foxrate-stars")]');

        if(empty($stars)){
            throw new \Behat\Gherkin\Exception\Exception("Stars are not present in this view");
        }
    }

    /**
     * @Given /^I request "([^"]*)" page$/
     */
    public function iRequestPage($action)
    {
        $response = $this->client->get($this->getRequestUrl($action))->send();

        $this->jsonRsonse = $response;
    }

    /**
     * @Given /^I want to make new "([^"]*)" request$/
     */
    public function iWantToMakeNewRequest($requestType)
    {
        throw new PendingException();
    }

    /**
     * @Then /^The response is Json$/
     */
    public function theResponseIsJson()
    {
        $data = json_decode($this->jsonRsonse->getBody(true));

        if (empty($data)) {
            throw new \Exception("Response was not JSON\n");
        }
    }

    /**
     * @Then /^I should display my Foxrate username$/
     */
    public function iShouldDisplayMyFoxrateUsername()
    {
        $data = json_decode($this->jsonRsonse->getBody(true));
        if (empty($data->foxrate_auth_login)) {
            throw new \Exception("I did not received Foxrate username in JSON\n");
        }
    }

    /**
     * @Given /^I should "([^"]*)" display error$/
     */
    public function iShouldDisplayError($error)
    {
        $errorDisplayed = $error == "not" ? 'false' : 'true';
        $data = json_decode($this->jsonRsonse->getBody(true));

        if ($data->error !== $errorDisplayed) {
            throw new \Exception("I did not received Foxrate username in JSON\n");
        }
    }

    /**
     * @Given /^I should get upload id$/
     */
    public function iShouldGetUploadId()
    {
        $data = json_decode($this->jsonRsonse->getBody(true));

        if (!is_int($data->upload_id)) {
            throw new \Exception("I did not received Foxrate username in JSON\n" . $data);
        }
    }

    /**
     * @Then /^I see product rating minimum value rich snippet$/
     */
    public function iSeeProductRatingMinimumValueRichSnippet()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I see product review rating rich snippet$/
     */
    public function iSeeProductReviewRatingRichSnippet()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I see product rating maximum value rich snippet$/
     */
    public function iSeeProductRatingMaximumValueRichSnippet()
    {
        throw new PendingException();
    }

    /**
     * Get Items
     * @return mixed
     */
    protected function getMicrodataItems()
    {
        $md = new \MicrodataPhp($this->parameters['base_url'] . $this->parameters['product_uri']);
        return $md->obj()->items[0];
    }



    /**
     * Get AggregateRating
     * @return mixed
     */
    protected function getAggregateRating()
    {
        return $this->getMicrodataItems()->properties['aggregateRating'][0];
    }

    protected function getRatingValue()
    {
        return $this->getAggregateRating()->properties['ratingValue'][0];
    }

    protected function getReviewCount()
    {
        return $this->getAggregateRating()->properties['reviewCount'][0];
    }

    /**
     * Get Review
     * @return mixed
     */
    protected function getReview()
    {
        return $this->getMicrodataItems()->properties['review'][0];
    }

    protected function getReviewRating()
    {
        return $this->getReview()->properties['reviewRating'][0];
    }

    protected function getReviewAuthor()
    {
        return $this->getReview()->properties['author'][0];
    }

    protected function getReviewDatePublished()
    {
        return $this->getReview()->properties['datePublished'][0];
    }

    protected function getReviewName()
    {
        return $this->getReview()->properties['name'][0];
    }

    protected function getReviewDescription()
    {
        return $this->getReview()->properties['description'][0];
    }

    private function getDefaultProductUri()
    {
        return $this->parameters['base_url'] . $this->parameters['product_uri'];
    }

    private function getDefaultProductCategoryUri()
    {
        return $this->parameters['base_url'] . $this->parameters['default_category_uri'];
    }

    private function getBridgeUrl()
    {
        return $this->parameters['base_url'] . $this->parameters['bridge_uri'];
    }

    private function getParameter($param)
    {
        return $this->parameters[$param];
    }

    private function getRequestUrl($action)
    {
        return $this->getBridgeUrl() . $this->getParameter($action);
    }

    public function write_file($file, $content) {
        if ($this->parameters['isLocalShop']) {
            $this->saveFileToLocalShop($file, $content);
        } else {
            $this->saveFileToRemoteShop($file, $content);
        }
    }

    private function saveFileToLocalShop($file, $content)
    {
        if (false === $fp = @fopen($file , 'w')) {
            throw new \Exception ("Writing file failed!");
        }
        fwrite($fp, $content);
        fclose($fp);
    }

    private function saveFileToRemoteShop($file, $content)
    {
        file_put_contents('reviews.json', $content);
        exec("ssh {$this->parameters['shop_server_ssh_user']}@{$this->parameters['shop_server_ssh_url']} -i ". __DIR__ ."/../keys/shop_key -p {$this->parameters['shop_server_ssh_port']} 'mkdir -p ".$this->parameters['shop_server_location']."/".$this->parameters['cache_file_dir']."'");
        exec("scp -i ". __DIR__ ."/../keys/shop_key -P {$this->parameters['shop_server_ssh_port']} -o StrictHostKeyChecking=no reviews.json {$this->parameters['shop_server_ssh_user']}@{$this->parameters['shop_server_ssh_url']}:{$this->parameters['shop_server_location']}/".$file);
    }
}
