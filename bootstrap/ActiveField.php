<?php

namespace panix\engine\bootstrap;

use yii\helpers\ArrayHelper;

class ActiveField extends \yii\bootstrap4\ActiveField
{

    /**
     * @inheritdoc
     */
    public function dropdownList($items, $options = [])
    {
        if (!isset($options['class'])) {
            $options['class'] = 'custom-select w-auto';
        }
        return parent::dropdownList($items, $options);
    }


    /**
     * Render Phone widget. If not found return textInput
     * @param array $options
     * @return $this
     * @throws \Exception
     */
    public function telInput($options = [])
    {
        $jsOptions = ArrayHelper::remove($options, 'jsOptions', []);
        if (class_exists('panix\\ext\\telinput\\PhoneInput')) {
            return parent::widget(\panix\ext\telinput\PhoneInput::class, [
                'options' => $options,
                'jsOptions' => $jsOptions
            ]);
        } else {
            return parent::textInput($options);
        }
    }

    /**
     * Render tinyMce widget. If not found return textarea
     * @param array $options
     * @return $this
     * @throws \Exception
     */
    public function tinyMce($options = [])
    {
        $clientOptions = ArrayHelper::remove($options, 'clientOptions', []);
        if (class_exists('panix\\ext\\tinymce\\TinyMce')) {
            return parent::widget(\panix\ext\tinymce\TinyMce::class, [
                'options' => $options,
                'clientOptions' => $clientOptions
            ]);
        } else {
            return parent::textarea($options);
        }
    }

    /**
     * Render BootstrapSelect widget. If not found return dropdownList
     * @param array $items the option data items. The array keys are option values, and the array values
     * are the corresponding option labels. The array can also be nested (i.e. some array values are arrays too).
     * For each sub-array, an option group will be generated whose label is the key associated with the sub-array.
     * If you have a list of data models, you may convert them into the format described above using
     * [[ArrayHelper::map()]].
     *
     * @param array $options
     * @return $this
     * @throws \Exception
     */
    public function bootstrapSelect($items = [], $options = [])
    {
        $jsOptions = ArrayHelper::remove($options, 'jsOptions', []);
        if (class_exists('panix\\ext\\bootstrapselect\\BootstrapSelect')) {
            return parent::widget(\panix\ext\bootstrapselect\BootstrapSelect::class, [
                'options' => $options,
                'items' => $items,
                'jsOptions' => $jsOptions,
            ]);
        } else {
            return parent::dropdownList($items, $options);
        }
    }
}