<?php
namespace panix\engine\behaviors\wizard\models\wizard\survey;

class Model extends \yii\base\Model
{
    public $answer;

    public function rules()
    {
        return [['answer', 'required']];
    }
}
