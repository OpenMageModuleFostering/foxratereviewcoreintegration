<?php

namespace features;

include_once('EshopContext.php');


/**
 * Oxid feature context.
 */
class OxidContext extends EshopContext
{
    /**
     * @Then /^I should see foxrate review summary with "([^"]*)" big stars$/
     */
    public function iShouldSeeFoxrateReviewSummaryWithBigStars($starsCount)
    {
        $stars_width = $this->getSession()->getPage()->find('css', '.frRating-top .yellow')->getAttribute('style');
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
        $productInfoDiv = $this->getSession()->getPage()->find('css', 'h1#productTitle span');

        if ('name' != $productInfoDiv->getAttribute('itemprop'))
        {
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
        $productInfoDiv = $this->getSession()->getPage()->find('css', '#productinfo');
        if ($productInfoDiv->hasAttribute('itemscope') && $itemtype = $productInfoDiv->getAttribute('itemtype'))
        {
            if ($itemtype !== 'http://schema.org/Product') {
                throw new \Exception('Itemtype does not have valid property');
            }
            return true;
        }

        throw new \Exception('Product scope does note have valid annotations');

    }
}
