<?php
namespace panix\engine\components;
/**
 * BrowserComponent class
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 * @package app
 * @subpackage addons
 * @uses CApplicationComponent
 * @copyright (c) 2016, Andrew Semenov
 */
use panix\engine\components\Browser;

class BrowserComponent extends \yii\base\Component {

    private $_useragent;
    protected $browser;

    public function init() {

        $this->browser = new Browser;
        $this->_useragent = Yii::app()->request->userAgent;
        parent::init();
    }

    public function getName($userAgent = false) {
        $this->browser->setUserAgent(($userAgent) ? $userAgent : $this->_useragent);
        return $this->browser->getBrowser();
    }

    public function getVersion($userAgent = false) {
        $this->browser->setUserAgent(($userAgent) ? $userAgent : $this->_useragent);
        return $this->browser->getVersion();
    }

    public function getPlatform($userAgent = false) {
        $this->browser->setUserAgent(($userAgent) ? $userAgent : $this->_useragent);
        return $this->browser->getPlatform();
    }

    public function getString($userAgent = false) {
        $this->browser->setUserAgent(($userAgent) ? $userAgent : $this->_useragent);
        return $this->browser->__toString();
    }

    public function isMobile($userAgent = false) {
        $this->browser->setUserAgent(($userAgent) ? $userAgent : $this->_useragent);
        return $this->browser->isMobile();
    }

    public function isTablet($userAgent = false) {
        $this->browser->setUserAgent(($userAgent) ? $userAgent : $this->_useragent);
        return $this->browser->isTablet();
    }

    public function isRobot($userAgent = false) {
        $this->browser->setUserAgent(($userAgent) ? $userAgent : $this->_useragent);
        return $this->browser->isRobot();
    }

    public function isFacebook($userAgent = false) {
        $this->browser->setUserAgent(($userAgent) ? $userAgent : $this->_useragent);
        return $this->browser->isFacebook();
    }

}
