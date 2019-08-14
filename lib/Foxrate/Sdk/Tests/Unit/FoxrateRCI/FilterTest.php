<?php
namespace Foxrate\Sdk\Tests\Unit\FoxrateRCI;


class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilteringByRatings()
    {
        $review1 = new \Foxrate_Sdk_Entities_Feedback();
        $review2 = new \Foxrate_Sdk_Entities_Feedback();

        $review1->ratings->overall = 4;
        $review2->ratings->overall = 3;

        $filter = new \Foxrate_Sdk_FoxrateRCI_Filter();
        $filter->setValue(3);

        $this->assertEquals(false, $filter->filterRevs_Ratings($review1));
        $this->assertEquals(true, $filter->filterRevs_Ratings($review2));
    }

    public function testFilteringByCommentSearch()
    {
        $review1 = new \Foxrate_Sdk_Entities_Feedback();
        $review2 = new \Foxrate_Sdk_Entities_Feedback();

        $review1->texts->pros = 'first pros comment';
        $review1->texts->cons = 'first cons comment';
        $review1->texts->conclusion = 'first conclusion comment';
        $review1->texts->comment = 'first comment';

        $review2->texts->pros = 'second pros comment';
        $review2->texts->cons = 'second cons comment';
        $review2->texts->conclusion = 'second conclusion comment';
        $review2->texts->comment = 'second comment';

        $filter = new \Foxrate_Sdk_FoxrateRCI_Filter();
        $filter->setValue('second comment');

        $this->assertEquals(false, $filter->filterRevs_Search($review1));
        $this->assertEquals(true, $filter->filterRevs_Search($review2));
    }

    public function testSortingByRating()
    {
        $review1 = new \Foxrate_Sdk_Entities_Feedback();
        $review2 = new \Foxrate_Sdk_Entities_Feedback();

        $review1->ratings->overall = 3;
        $review2->ratings->overall = 4;

        $filter = new \Foxrate_Sdk_FoxrateRCI_Filter();
        $filter->setValue('rate_desc');

        $this->assertEquals(1, $filter->filterRevs_Sorting($review1, $review2));
    }

    public function testSortingByDate()
    {
        $review1 = new \Foxrate_Sdk_Entities_Feedback();
        $review2 = new \Foxrate_Sdk_Entities_Feedback();

        $review1->created = new \DateTime('2003-02-02 10:00');
        $review2->created = new \DateTime('2002-02-02 10:00');

        $filter = new \Foxrate_Sdk_FoxrateRCI_Filter();
        $filter->setValue('date_desc');

        $this->assertEquals(-1, $filter->filterRevs_Sorting($review1, $review2));
    }
}
