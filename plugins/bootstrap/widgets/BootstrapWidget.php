<?php
namespace panix\engine\plugins\bootstrap\widgets;

use yii\bootstrap4\Html;
use yii\bootstrap4\Widget;

/**
 * Class BootstrapWidget
 * @package panix\engine\plugins\bootstrap\widgets
 */
class BootstrapWidget extends Widget
{
    /** The bootstrap color types*/
    const TYPE_DEFAULT = 'secondary';
    const TYPE_PRIMARY = 'primary';
    const TYPE_INFO = 'info';
    const TYPE_DANGER = 'danger';
    const TYPE_WARNING = 'warning';
    const TYPE_SUCCESS = 'success';

    /** Bootstrap floats*/
    const FLOAT_LEFT = 'float-left';
    const FLOAT_RIGHT = 'float-right';
    const FLOAT_CENTER = 'center-block';
    const NAVBAR_FLOAT_LEFT = 'navbar-left';
    const NAVBAR_FLOAT_RIGHT = 'navbar-right';
    const CLEAR_FLOAT = 'clearfix';

    /** Bootstrap CSS for visibility items */
    const SHOW = 'show';
    const HIDDEN = 'hidden';
    const INVISIBLE = 'invisible';
    const SCREEN_READER = 'sr-only';
    const IMAGE_REPLACER = 'text-hide';

    /** Bootstrap sizes modifier*/
    const SIZE_TINY = 'xs';
    const SIZE_SMALL = 'sm';
    const SIZE_MEDIUM = 'md';
    const SIZE_LARGE = 'lg';

    /**
     * @var string
     */
    public $id;

    /**
     * Content inner shorcode
     * ```
     * [code]...content here...[\code]
     * ```
     * @var string
     */
    public $content;

    /**
     * Extra class for elements
     * @var string
     */
    public $xclass;

    /**
     * @var string
     */
    protected $tag = 'div';

    /**
     * Need for ignore exceptions
     * @param string $name
     * @param mixed $string
     */
    public function __set($name, $string)
    {
        if (property_exists($this, $name)) {
            $this->$name = $string;
        }
    }

    /**
     * init
     */
    public function init()
    {
        $this->options['id'] = $this->id;
        $this->addXClass();
    }

    /**
     * Render row
     * @return string
     */
    public function run()
    {
        return Html::tag($this->tag, $this->content, $this->options);
    }

    /**
     * add extra class in options
     */
    protected function addXClass()
    {
        $this->xclass ? Html::addCssClass($this->options, $this->xclass) : null;
    }
}