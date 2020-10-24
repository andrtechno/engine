<?php
namespace panix\engine\taggable\tegcloud;

use panix\engine\data\Widget;
use Yii;
use panix\engine\Html;


class TagCloud extends Widget {

    public $alias = 'ext.blocks.tagcloud';

    public function getTitle() {
        return Yii::t('app/default', 'Облако');
    }

    public function run() {
        $module = Yii::$app->getModule(Yii::$app->controller->module->id);
        if (isset($module->tegRoute)) {
            $route = '/' . $module->tegRoute;
        } else {
            $route = '/' . Yii::$app->controller->route;
        }
        $tags = Tag::model()->findTagWeights($this->config['maxTags']);
        if (!empty($tags)) {
            foreach ($tags as $tag => $weight) {
                $link = Html::a(Html::encode($tag), array($route, 'tag' => $tag), array('title' => Html::encode($tag)));
                echo Html::tag('span', array(
                    'class' => 'tag',
                    'style' => "font-size:{$weight}pt",
                        ), $link) . "\n";
            }
        } else {
            echo 'no tags';
            //Yii::app()->tpl->alert('warning', 'Нет неодного тега', false);
        }
    }

}
