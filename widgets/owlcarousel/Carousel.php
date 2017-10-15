<?php

namespace panix\engine\widgets\owlcarousel;

use panix\engine\widgets\owlcarousel\CarouselAsset;
use yii\helpers\Json;

class Carousel extends \panix\engine\data\Widget {

    public $target;
    public $options = [];

    public function run() {
        $js = [];
        $view = $this->getView();
        CarouselAsset::register($view);

        $defaultOptions = [
            'navText' => [
                '<i class="icon-arrow-left"></i>',
                '<i class="icon-arrow-right"></i>'
            ]
        ];

        $options = Json::encode(array_merge($this->options, $defaultOptions));
        $js[] = "$('$this->target').owlCarousel($options);";
        $view->registerJs(implode("\n", $js));
    }

}
