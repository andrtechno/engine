<?php

class StarRating extends CStarRating {

    public $model;
    public $maxRating = 5;
    public $minRating = 1;

    public function init() {


        $this->name = 'rating_' . $this->model->id;
        $this->id = 'rating_' . $this->model->id;
        if (isset(Yii::app()->request->cookies[$this->id]->value)) {
            $this->readOnly = true;
        }
        $this->allowEmpty = false;
        $this->readOnly = isset(Yii::app()->request->cookies['rating_' . $this->model->id]);
        $this->value = ($this->model->rating + $this->model->votes) ? round($this->model->rating / $this->model->votes) : 0;
        $this->callback = 'js:function(){rating(' . $this->model->id . ')}';

        for ($x = 1; $x <= $this->maxRating; $x++) {
            $this->titles[$x] = Yii::t('app', 'RATING', $x);
        }
        parent::init();
    }

    public function registerClientScript($id) {

        $assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/assets', false, -1, YII_DEBUG);


        $jsOptions = $this->getClientOptions();
        $jsOptions = empty($jsOptions) ? '' : CJavaScript::encode($jsOptions);
        $js = "jQuery('#{$id} > input').rating({$jsOptions});";
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('rating');
        $cs->registerScriptFile($assetsUrl . '/js/rating.js');
        $cs->registerScript('Yii.CStarRating#' . $id, $js);

        if ($this->cssFile !== false)
            self::registerCssFile($this->cssFile);
    }

}

?>
