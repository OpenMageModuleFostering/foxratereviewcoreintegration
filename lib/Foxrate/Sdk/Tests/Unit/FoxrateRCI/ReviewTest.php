<?php
namespace Foxrate\Sdk\Tests\Unit\FoxrateRCI;

use \Mockery as m;

class ReviewTest extends \PHPUnit_Framework_TestCase
{
    private $configMock;
    private $dataManagerMock;
    private $reviewModelMock;

    public function setup()
    {
        $feedbackInfo = new \stdClass();
        $productReviewEntity = new \Foxrate_Sdk_Entities_ProductReview();
        $productReviewEntity->id = 1228;
        $feedbackInfo->reviews = array($productReviewEntity);
        $feedbackInfo->pages_count = 1;

        $this->configMock = $this->getMock('Foxrate_Sdk_FoxrateRCI_ConfigInterface');

        $this->configMock
            ->expects($this->any())
            ->method('getConfigParam')
            ->will($this->returnValue(''));

        $this->dataManagerMock = $this
            ->getMockBuilder('Foxrate_Sdk_FoxrateRCI_DataManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataManagerMock
            ->expects($this->any())
            ->method('loadCachedProductReviews')
            ->will($this->returnValue($feedbackInfo));

        $this->reviewModelMock = $this
            ->getMockBuilder('Foxrate_Sdk_FoxrateRCI_Review')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testReturnsReviewsAsEntities()
    {
        $reviewModel = $this->getReviewModel();

        $feedbackCollection = $reviewModel->loadProductsAllRevs_Cache(1);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_ProductReview', $feedbackCollection->reviews[0]);
        $this->assertEquals(1228, $feedbackCollection->reviews[0]->id);
    }

    public function testReturnReviewsStdClass()
    {
        $reviewModel = $this->getReviewModel();
        $reviewModel->setFoxrateSellerId(1);
        $callResult = $this->getReviewModel()->callProductsWithReviews();
        $this->assertTrue($callResult->reviews instanceof \stdClass);
    }

    public function testApiCallBuilderReviewUrl()
    {
        $reviewModel = $this->getReviewModel();
        $reviewModel->setFoxrateSellerId(1);
        $this->assertEquals('/v1/sellers/1/products/reviews.json', $reviewModel->apiCallBuilder("reviews"));
    }

    protected function getReviewModel()
    {
        return new \Foxrate_Sdk_FoxrateRCI_Review(
            $this->configMock,
            $this
                ->getMockBuilder('Foxrate_Sdk_ApiBundle_Controllers_Authenticator')
                ->disableOriginalConstructor()
                ->getMock(),
            $this->dataManagerMock,
            $this->getMock('Foxrate_Sdk_FoxrateRCI_ProductInterface'),
            $this->getMock('Foxrate_Sdk_ApiBundle_Resources_ApiEnvironmentInterface'),
            $this->getCallerMock()
        );
    }

    public function getCallerMock()
    {
        $service = m::mock('Foxrate_Sdk_ApiBundle_Caller_FoxrateApiCaller');
        $service->shouldReceive('makeCall')->times(1)->andReturn(
            json_decode(
            '{
            "reviews": {
                "1": [
                    {
                        "id": 572,
                        "date": "2014-08-19T13:39:24+0200",
                        "name": "",
                        "stars": 3,
                        "comment_pros": "",
                        "comment_cons": "Der Zoll und die Geb\u00fchren waren leider so hoch dass es schlussendlich teurer kam als das Produkt in der CH zu 110.-- SFR einzukaufen.",
                        "anonymous": false,
                        "rating_question_first": 0,
                        "rating_question_second": 5,
                        "rating_question_third": 3,
                        "source": "ebay",
                        "this_is_useful": {"yes": 0, "no": 0, "total": 0},
                        "reviewer_verified": true,
                        "recommends_for_others": true,
                        "images": {
                            "12px": "https:\/\/foxrate.de\/images\/widgets\/api\/12\/3_00.png",
                            "14px": "https:\/\/foxrate.de\/images\/widgets\/api\/14\/3_00.png"
                        }
                    }
                ],
                "2": [
                    {
                        "id": 571,
                        "date": "2014-08-13T09:08:02+0200",
                        "name": "",
                        "stars": 5,
                        "comment_pros": "Sehr intensives Reinigungsgef\u00fchl\r\nlange Akkulaufzeit, gutes Handling",
                        "comment_cons": "bisher keine",
                        "anonymous": false,
                        "rating_question_first": 0,
                        "rating_question_second": 5,
                        "rating_question_third": 4,
                        "source": "ebay",
                        "this_is_useful": {"yes": 0, "no": 0, "total": 0},
                        "reviewer_verified": true,
                        "recommends_for_others": true,
                        "images": {
                            "12px": "https:\/\/foxrate.de\/images\/widgets\/api\/12\/5_00.png",
                            "14px": "https:\/\/foxrate.de\/images\/widgets\/api\/14\/5_00.png"
                        }
                    }
                ],
                "3": [
                    {
                        "id": 570,
                        "date": "2014-08-12T21:45:08+0200",
                        "name": "",
                        "stars": 4,
                        "comment_pros": "Die Zahnb\u00fcrste ist sehr leise und liegt gut in der Hand\r\nWenn man mit der Zahnb\u00fcrste lang genug putzt (mind. 6 min), dann f\u00fchlen sich die Z\u00e4hne auch wunderbar glatt an",
                        "comment_cons": "3 min (ein Putzintervall) reichen bei mir leider nicht aus. Ich brauch die doppelte Zeit oder sogar l\u00e4nger bis ich das Gef\u00fchl habe, das meine Z\u00e4hne wirklich sauber sind. Auch die Zwischenr\u00e4ume der Z\u00e4hne werden d\u00fcrftig sauber. Das hat meine elektrische Zahnb\u00fcrste vorher besser gemacht.",
                        "anonymous": false,
                        "rating_question_first": 0,
                        "rating_question_second": 4,
                        "rating_question_third": 4,
                        "source": "ebay",
                        "this_is_useful": {"yes": 0, "no": 0, "total": 0},
                        "reviewer_verified": true,
                        "recommends_for_others": true,
                        "images": {
                            "12px": "https:\/\/foxrate.de\/images\/widgets\/api\/12\/4_00.png",
                            "14px": "https:\/\/foxrate.de\/images\/widgets\/api\/14\/4_00.png"
                        }
                    },
                    {
                        "id": 569,
                        "date": "2014-08-09T21:54:47+0200",
                        "name": "",
                        "stars": 4,
                        "comment_pros": "Sehr gute Qualit\u00e4t und gute Kommunikation",
                        "comment_cons": "",
                        "anonymous": false,
                        "rating_question_first": 0,
                        "rating_question_second": 5,
                        "rating_question_third": 4,
                        "source": "ebay",
                        "this_is_useful": {"yes": 0, "no": 0, "total": 0},
                        "reviewer_verified": true,
                        "recommends_for_others": true,
                        "images": {
                            "12px": "https:\/\/foxrate.de\/images\/widgets\/api\/12\/4_00.png",
                            "14px": "https:\/\/foxrate.de\/images\/widgets\/api\/14\/4_00.png"
                        }
                    },
                    {
                        "id": 566,
                        "date": "2014-07-26T18:23:26+0200",
                        "name": "",
                        "stars": 4,
                        "comment_pros": "Nach ca.6 Wo.Benutzung zusammen mit einer aufhellenden Zahnpasta\r\nkann ich nur feststellen, da\u00df meine Z\u00e4hne seit langer Zeit nicht mehr so gepflegt ausgesehen haben. Der sichtbare Erfolg ist auch mit dem Zungentest sp\u00fcrbar! Die Zahnb\u00fcrste putzt sehr sanft,liegt gut in der Hand und ist einfach zu reinigen. Dagegen ist die alte elektrische B\u00fcrste geradezu ein Schrubber. Der Akku scheint gut durchzuhalten. Da ich aber mind. jeweils 2 x 3 Min. putze, sind es eben weniger als 2 Wochen. Mich st\u00f6rt das nicht.",
                        "comment_cons": "Der Preis k\u00f6nnte geringer sein.",
                        "anonymous": false,
                        "rating_question_first": 0,
                        "rating_question_second": 4,
                        "rating_question_third": 3,
                        "source": "ebay",
                        "this_is_useful": {"yes": 0, "no": 0, "total": 0},
                        "reviewer_verified": true,
                        "recommends_for_others": true,
                        "images": {
                            "12px": "https:\/\/foxrate.de\/images\/widgets\/api\/12\/4_00.png",
                            "14px": "https:\/\/foxrate.de\/images\/widgets\/api\/14\/4_00.png"
                        }
                    }
                ]
            }
            }'
            )
        );

        return $service;
    }
}
