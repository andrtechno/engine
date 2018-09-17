<?php

namespace panix\engine\widgets\attachment;

use panix\engine\data\Widget;

class Attachment extends Widget
{

    protected $assets;
    public $model;
    public $behaviorName = 'attachment';
    public $type = 'image';

    public function getBehavior()
    {
        return $this->model->{$this->behaviorName};
    }


    public static function actions2()
    {
        return array(
            'delete' => 'ext.attachment.actions.DeleteAction',
        );
    }

    public function init()
    {
       // $this->assets = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/assets', false, -1, YII_DEBUG);

       // $cs = Yii::app()->clientScript;
       // $cs->registerCssFile($this->assets . '/attachment.css');
    }

    public function run()
    {


        //if ($this->max >= count($this->relation)) {
        //    $this->max = round($this->max - count($this->relation), 0);
        //} elseif ($this->max <= count($this->relation)) {
        //    $this->max = round(count($this->relation) - $this->max, 0);
        //}

       // $this->widget('ext.fancybox.Fancybox', array('target' => 'a.attachment-zoom'));


        return $this->render($this->skin);
    }

}
