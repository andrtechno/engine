<?php
namespace panix\engine\plugins\goaway;

use yii\base\Event;

/**
 * Class RedirectEvent
 * @package panix\engine\plugins\goaway
 */
class RedirectEvent extends Event
{
    /**
     * @var array the parameter array passed to the [[RedirectController->actionRedirect()]] method.
     */
    public $config;

}