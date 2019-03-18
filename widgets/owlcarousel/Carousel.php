<?php

namespace panix\engine\widgets\owlcarousel;


use yii\helpers\Json;
use panix\engine\data\Widget;
use yii\web\View;

class Carousel extends Widget {

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
        $js[] = "$(function () {
            $('$this->target').owlCarousel($options);
        });";
        $view->registerJs(implode("\n", $js),View::POS_END);
    }

}
