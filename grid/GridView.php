<?php

namespace panix\engine\grid;

class GridView extends \yii\grid\GridView {

    public $layoutOptions = [];
    public $emptyTextOptions = ['class' => 'alert alert-info empty'];

    public function init() {
        parent::init();

        $this->layout = $this->render('@admin/views/layouts/_grid_layout', $this->layoutOptions);
    }

}
