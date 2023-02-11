<?php

namespace panix\engine\bootstrap;

use panix\engine\CMS;
use Yii;
use yii\bootstrap4\InputWidget;
use panix\engine\Html;

class TinyMceLang extends InputWidget
{

    public function init22()
    {
        if ($this->name === null && !$this->hasModel()) {
            throw new InvalidConfigException("Either 'name', or 'model' and 'attribute' properties must be specified.");
        }
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
            //$this->options['id'] .= $this->options['id'].CMS::gen(14);
        }
        parent::init();
    }


    public function run()
    {
        $languages = Yii::$app->languageManager->getLanguages();


        echo '<ul class="nav nav-tabs" role="tablist">';
        foreach ($languages as $language => $value) {
            $active = ($language == Yii::$app->language) ? 'active' : '';
            echo '<li class="nav-item" role="presentation">';
            echo '<a class="nav-link ' . $active . '" id="home-tab" data-toggle="tab" href="#tab-' . md5($this->options['id'].$language) . '" role="tab" aria-controls="tab-' . md5($this->options['id'].$language) . '" aria-selected="true">';
            echo Html::img("/uploads/language/{$value->slug}.png");
            echo ' <span class="d-none d-md-inline-block">' . $value->name . '</span>';
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';

        echo '<div class="tab-content" id="tab-'.$this->options['id'].'">';
        foreach ($languages as $language => $value) {
            $active = ($language == Yii::$app->language) ? 'show active' : '';
            echo '<div class="tab-pane fade ' . $active . '" id="tab-' . md5($this->options['id'].$language) . '" role="tabpanel" aria-labelledby="tab-' . md5($this->options['id'].$language) . '-tab">';

            if ($this->hasModel()) {
                echo Html::activeTextInput($this->model, $this->attribute."[{$value->id}]", $this->options);
            } else {
                echo Html::textInput($this->name, $this->value, $this->options);
            }
            echo '</div>';
        }
        echo '</div>';


    }
}