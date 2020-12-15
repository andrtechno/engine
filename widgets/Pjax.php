<?php

namespace panix\engine\widgets;

use yii\helpers\Json;

/**
 * Class Pjax
 * @package panix\engine\widgets
 */
class Pjax extends \yii\widgets\Pjax
{
    public $timeout = false;
    public $dataProvider;

    public function init()
    {

        if ($this->dataProvider) {
            if (!isset($this->options['id'])) {
                $this->options['id'] = $this->getId();
                $this->id = 'pjax-grid-' . strtolower((new \ReflectionClass($this->dataProvider->query->modelClass))->getShortName());
            }
        }
        parent::init();

    }


    public function registerClientScript()
    {
        parent::registerClientScript();

        $id = $this->options['id'];

        $this->getView()->registerJs("
            $(document).on('pjax:beforeSend', function() {
                $('#{$id}').addClass('pjax-loader');
                console.log('add loader','{$id}');
            });
            
            $(document).on('pjax:end', function() {
                $('#{$id}').removeClass('pjax-loader');
                console.log('remove loader','{$id}');
            });
        ");
    }

}
