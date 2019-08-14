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

class VMContext extends MinkContext
{
    protected $mink;

    protected static $selServer = 'http://selenium.vm:4444/wd/hub';   //selenium server ip

    protected static $selBrowser = 'firefox';   // selenium browser to test against

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param   array   $parameters     context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->prepareMink();
        $this->setMink($this->mink);
        $this->parameters = $parameters;
    }

    public function prepareMink()
    {
        if (!isset($this->mink)) {
            $mink = new Mink();
            $mink->registerSession('vmSession', $this->createSession());
            $mink->setDefaultSessionName('vmSession');
            $this->mink = $mink;
        }
        return $this->mink;
    }

    private function createSession()
    {
        return new MinkSession(
            new Selenium2Driver(
                self::$selBrowser,
                null,
                static::$selServer
            )
        );
    }

    public function getMink()
    {
        return $this->mink;
    }
}
