<?php
namespace panix\engine\plugins\extralinks;

use yii\base\Event;

/**
 * Class RedirectController
 * @package panix\mod\plugins\core\extralinks
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class RedirectEvent extends Event
{
    /**
     * @var array the parameter array passed to the [[RedirectController->actionRedirect()]] method.
     */
    public $config;

}