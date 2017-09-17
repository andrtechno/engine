<?php

namespace panix\engine\widgets;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;

class Pjax extends \yii\widgets\Pjax
{
    /**
     * @var string
     */
    public $loader;
    public function init()
    {
        Html::addCssClass($this->options, 'dimmable');
        if (!$this->loader) {
            $this->loader = self::dimmer(self::loader(), ['class' => 'active inverted']);
        }
        parent::init();
    }
       public static function dimmer($content, $options = [])
    {
        return static::renderElement('dimmer', $content, $options);
    }
    public function registerClientScript()
    {
        parent::registerClientScript();
        $this->getView()->registerJs('
        var pjaxLoader_' . $this->getSanitizedId() . ' = "' . addslashes($this->loader) . '";
        jQuery(document).on("pjax:start", "#' . $this->options['id'] . '", function() {
            jQuery("#' . $this->options['id'] . '").append(pjaxLoader_' . $this->getSanitizedId() . ');
        });
        ');
    }
       public static function loader($content = '', $options = [])
    {
        return static::renderElement('loader', $content, $options);
    }
    /**
     * @return string
     */
    private function getSanitizedId()
    {
        return Inflector::camelize($this->options['id']);
    }
    
       public static function renderElement($type, $content, $options = [])
    {
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        $class = ArrayHelper::remove($options, 'class');
        Html::addCssClass($options, 'ui ' . $class . ' ' . $type);
        return Html::tag($tag, $content, $options);
    }
}