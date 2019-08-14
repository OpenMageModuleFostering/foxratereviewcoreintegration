<?php

namespace features;

include_once('EshopContext.php');

define('PRESTASHOP_ADMIN_PATH', 'http://192.168.59.103/admin1/');
define('PRESTASHOP_ADMIN_USER', 'admin@admin.com');
define('PRESTASHOP_ADMIN_PASSWORD', 'adminadmin1');
define('FOXRATE_DEMO_USER', '17165');

/**
 * Prestashop feature context.
 */
class PrestashopContext extends EshopContext
{
    /**
     * @Then /^I should see foxrate review summary with "([^"]*)" big stars$/
     */
    public function iShouldSeeFoxrateReviewSummaryWithBigStars($starsCount)
    {
        $elCss = '.frRating-top .yellow';
        $this->assertElementOnPage($elCss);

        $el= $this->getSession()->getPage()->find('css', $elCss);

        $stars_width = $el->getAttribute('style');
        preg_match('/width: ?([0-9]*)%;/', $stars_width, $matches);
        if ($starsCount * 20 !=  (int)$matches[1]) {
            throw new \Behat\Gherkin\Exception\Exception("Foxrate review summary shows wrong number of big stars!");
        }
    }

    /**
     * @Then /^I see product name rich snippet$/
     */
    public function iSeeProductNameRichSnippet()
    {
        $productInfoDiv = $this->getSession()->getPage()->find('css', '#primary_block h1');

        $this->assertElementOnPage('#primary_block h1');
        
        if ('name' != $productInfoDiv->getAttribute('itemprop')) {
            throw new \Exception('Name property is not valid');
        }
    }

    /**
     * @Then /^I should see foxrate product reviews$/
     */
    public function iShouldSeeFoxrateProductReviews()
    {
        $this->assertElementContains('#userReviews', 'foxrate-review-list');
    }

    /**
     * @Then /^I see product scope with annotation$/
     */
    public function iSeeProductScopeWithAnnotation()
    {
        $productInfoSelector = '#center_column';

        $productInfoDiv = $this->getSession()->getPage()->find('css', $productInfoSelector);

        $this->assertElementOnPage($productInfoSelector);
        
        if ($productInfoDiv->hasAttribute('itemscope') && $itemtype = $productInfoDiv->getAttribute('itemtype')) {
            if ($itemtype !== 'http://schema.org/Product') {
                throw new \Exception('Itemtype does not have valid property');
            }
            return true;
        }

        throw new \Exception('Product scope does note have valid annotations');

    }

    /**
     * @When /^I go to the first product$/
     */
    public function iGoToTheFirstProduct()
    {
        $this->getSession()->getPage()->find('css', '#featured-products_block_center .first_item a')->click();
    }

    /**
     * @Given /^I am on admin login$/
     */
    public function iAmOnAdminLogin()
    {
        $this->getSession()->visit(PRESTASHOP_ADMIN_PATH.'index.php?controller=AdminLogin');
    }

    /**
     * @When /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        $this->getSession()->getPage()->find('css', 'input[name="email"]')->setValue(PRESTASHOP_ADMIN_USER);
        $this->getSession()->getPage()->find('css', 'input[name="passwd"]')->setValue(PRESTASHOP_ADMIN_PASSWORD);
        $this->getSession()->getPage()->find('css', 'input[name="submitLogin"]')->click();
        $this->getSession()->wait(3000);
    }

    /**
     * @Given /^I am on admin homepage$/
     */
    public function iAmOnAdminHomepage()
    {
        $this->getSession()->visit(PRESTASHOP_ADMIN_PATH);
    }

    /**
     * @Then /^I should see "([^"]*)" on screen$/
     */
    public function iShouldSeeOnScreen($text)
    {
        $this->assertPageContainsText($text);
    }

    /**
     * @When /^I go to admin modules page$/
     */
    public function iGoToAdminModulesPage()
    {
        $this->getSession()->getPage()->find('xpath', '//img[contains(@src, "AdminParentModules")]')->click();
        $this->getSession()->getPage()->find('xpath', '//a[text()="Modules"]')->click();
    }

    /**
     * @Given /^looking for module$/
     */
    public function lookingForModule()
    {
        $filterElement = 'input[name="quicksearch"]';
        $this->assertElementOnPage($filterElement);
        $filter = $this->getSession()->getPage()->find('css', $filterElement);
        $filter->setValue('foxratereviews');

        //$this->getSession()->getPage()->find('css', '.button-filter input[type="submit"]')->click();
        $this->assertPageContainsText('Foxrate Reviews');
    }

    /**
     * @Given /^module must be uninstalled$/
     */
    public function moduleMustBeUninstalled()
    {
        $this->iGoToAdminModulesPage();
        $this->lookingForModule();
        $flag = $this->getSession()->getPage()->find('css', '#anchorFoxratereviews .non-install');
        if (is_null($flag)) {
            $this->getSession()->getPage()->find('css', '#list-action-button a')->click();
        }
    }

    /**
     * @Given /^module must be installed$/
     */
    public function moduleMustBeInstalled()
    {
        $this->iGoToAdminModulesPage();
        $this->lookingForModule();
        $flag = $this->getSession()->getPage()->find('css', '#anchorFoxratereviews .non-install');
        if (!is_null($flag)) {
            $this->getSession()->getPage()->find('css', '#list-action-button a')->click();
        }
        $this->iGoToAdminModulesPage();
        $this->lookingForModule();
        $this->clickLink("Configure");
        $this->iSetConfiguration();
    }

    /**
     * @Given /^click install$/
     */
    public function clickInstall()
    {
        $flag = $this->getSession()->getPage()->find('css', '#anchorFoxratereviews .non-install');
        if (is_null($flag)) {
            throw new \Exception("Module already installed");
        }
        $this->getSession()->getPage()->find('css', 'tr:not([style*="display: none"]) #list-action-button a')->click();

    }

    /**
     * @Given /^click uninstall$/
     */
    public function clickUninstall()
    {
        $flag = $this->getSession()->getPage()->find('css', '#anchorFoxratereviews .non-install');
        if (!is_null($flag)) {
            throw new Exception("Module already uninstalled");
        }
        $this->getSession()->getPage()->find('css', '#list-action-button a')->click();
    }

    /**
     * @Given /^I set configuration$/
     */
    public function iSetConfiguration()
    {
        $this->getSession()->getPage()->find('css', '#foxrate-widget-cfg input[name="seller_id"]')->setValue(FOXRATE_DEMO_USER);
        $this->getSession()->getPage()->find('css', '#foxrate-widget-cfg textarea[name="widget_code"]')->setValue(FOXRATE_DEMO_WIDGET);
        $this->getSession()->getPage()->find('css', '#foxrate-widget-cfg input[name="submitForm"]')->click();

        $this->compareValues(
            'foxratewidget configuration',
            FOXRATE_DEMO_USER,
            $this->getSession()->getPage()->find('css', '#foxrate-widget-cfg input[name="seller_id"]')->getValue()
        );

        $this->compareValues(
            'foxratewidget configuration',
            FOXRATE_DEMO_WIDGET,
            $this->getSession()->getPage()->find('css', '#foxrate-widget-cfg textarea[name="widget_code"] ')->getValue()
        );
    }

    /**
     * @Then /^I should see widget in "([^"]*)" block$/
     */
    public function iShouldSeeWidgetInColumn($column)
    {
        $columnSelector = $this->getSelector($column);
        $this->assertElementContains($columnSelector, 'fxrt_w_c_order');
    }

    private function getSelector($name)
    {
        $selectors = array(
            'right' => '#right_column',
            'left' => '#left_column',
            'header' => '#header',
            'footer' => '#footer',
            'home' => '#center_column',
            'top' => '#header_right',
        );

        return $selectors[$name];
    }

    private function compareValues($name, $expected, $value)
    {
        if ($value != $expected) {
            throw new Exception("$name error.\nExpected:\n\t'$expected'\nWas:\n\t'$value'");
        }
    }

}
