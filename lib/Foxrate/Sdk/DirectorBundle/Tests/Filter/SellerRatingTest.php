<?php

use Mockery as m;

class Foxrate_Sdk_DirectorBundle_Tests_Filter_SellerRatingTest extends \PHPUnit_Framework_TestCase
{
    protected $minDate = '1';

    protected $maxDate = '5';

    public function getDataSourceMock()
    {
        return m::mock('Foxrate_Sdk_Interface_DataSource');
    }

    public function getFilterBuilder($dataSource)
    {
        return new Foxrate_Sdk_DirectorBundle_Filter_SellerRating(
            $this->minDate,
            $this->maxDate,
            $dataSource
        );
    }

    public function testIsWorking()
    {
        $this->assertTrue(true);
    }

    public function testGetStandardFilters()
    {
        $filterDirector = $this->getFilterBuilder($this->getDataSourceMock());
        $this->assertEquals(2, count($filterDirector->getStandart()->getFilters()));
    }

    public function testGetDateLimitFilters()
    {
        $filterDirector = $this->getFilterBuilder($this->getDataSourceMock());
        $this->assertEquals(1, count($filterDirector->getDateLimited()->getFilters()));
    }

    public function testGetSellerRatingBuilderForStandartFilter()
    {
        $filterDirector = $this->getFilterBuilder($this->getDataSourceMock());
        $this->assertTrue($filterDirector->getStandart() instanceof \Foxrate_Sdk_Builder_Filter_SellerRating);
    }

    public function testGetSellerRatingBuilderForDateLimitedFilter()
    {
        $filterDirector = $this->getFilterBuilder($this->getDataSourceMock());
        $this->assertTrue($filterDirector->getDateLimited() instanceof \Foxrate_Sdk_Builder_Filter_SellerRating);
    }

    public function testGetSellerRatingBuilderForUserFilter()
    {
        $dataSourceMock = $this->getDataSourceMock();
        $dataSourceMock->shouldReceive('isGranted')->times(1)->andReturn(true);
        $filterDirector = $this->getFilterBuilder($dataSourceMock);

        $this->assertTrue($filterDirector->getDateLimited() instanceof \Foxrate_Sdk_Builder_Filter_SellerRating);
    }
}