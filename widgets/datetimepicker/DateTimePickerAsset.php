<?php

namespace panix\engine\widgets\datetimepicker;

class DateTimePickerAsset extends \yii\web\AssetBundle {

    /**
     * @inheritdoc
     */
    public function init() {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/bootstrap-datetimepicker', 'css/datetimepicker-kv']);
        $this->setupAssets('js', ['js/bootstrap-datetimepicker']);
        parent::init();
    }

}
