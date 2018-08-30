<?php

class TagCloudForm extends WidgetFormModel {

    public $maxTags;

    public static function defaultSettings() {
        return array(
            'maxTags' => 32
        );
    }

    public function rules() {
        return array(
            array('maxTags', 'type')
        );
    }

    public function getForm() {
        return array(
            'attributes' => array(
                'class' => 'form-horizontal',
                'type' => 'form',
            ),
            'elements' => array(
                'maxTags' => array(
                    'label' => Yii::t('TagCloudSettingsForm', 'Макс. размер шрифта у тега'),
                    'type' => 'text',
                ),
            ),
            'buttons' => array(
                'submit' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-success',
                    'label' => Yii::t('app', 'SAVE')
                )
            )
        );
    }

}
