<?php

use Mockery as m;

class OverallStrategyResolverTest extends \PHPUnit_Framework_TestCase
{

    protected $overallFromFeedbackStub;

    protected $overallFromChannelAveragesStub;

    function __construct()
    {
        $this->overallFromFeedbackStub = $this
            ->getMockBuilder('Foxrate_Sdk_Strategy_OverallFromFeedbacks')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->overallFromChannelAveragesStub = $this
            ->getMockBuilder('Foxrate_Sdk_Strategy_OverallFromChannelAverages')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testIsWorking()
    {
        $this->assertTrue(true);
    }

    public function testUserGetsOverallFromChannelAveragesCounting()
    {

        $dataSource = m::mock('Foxrate_Sdk_Interface_DataSource');
        $dataSource->shouldReceive('isGranted')->times(1)->andReturn(true);

        $overallStrategyResolver = $this->getOverallStrategyResolverMock($dataSource);

        $countStrategy = $overallStrategyResolver->getOverallStrategy(1000);
        $this->assertTrue($countStrategy instanceof \Foxrate_Sdk_Strategy_OverallFromChannelAverages);
    }

    public function testUserGetsOverallFromFeedbackObject()
    {
        $dataSource = m::mock('Foxrate_Sdk_Interface_DataSource');
        $dataSource->shouldReceive('isGranted')->times(1)->andReturn(false);

        $countStrategy = $this->getOverallStrategyResolverMock($dataSource)->getOverallStrategy(1000);
        $this->assertTrue($countStrategy instanceof \Foxrate_Sdk_Strategy_OverallFromFeedbacks);
    }
    
    /**
     * @param $dataSource
     *
     * @return Foxrate_Sdk_Strategy_OverallStrategyResolver
     */
    public function getOverallStrategyResolverMock($dataSource)
    {
        return new \Foxrate_Sdk_Strategy_OverallStrategyResolver(
            $dataSource,
            $this->overallFromFeedbackStub,
            $this->overallFromChannelAveragesStub
        );
    }


}
 