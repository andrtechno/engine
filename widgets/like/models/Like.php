<?php

namespace panix\engine\widgets\like\models;

use Yii;
use panix\engine\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Like extends ActiveRecord
{

    const MODULE_ID = 'shop';



    public static function tableName()
    {
        return '{{%like}}';
    }

    public static function find()
    {
        return new LikeQuery(get_called_class());
    }


    public function rules()
    {
        return [
            [['handler_hash'], 'trim'],
            [['handler_hash', 'object_id'], 'required'],
            [['handler_hash'], 'string', 'max' => 8],
            ['model', 'safe']
        ];
    }




    public function behaviors2()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    'created_at',
                ]
            ]
        ];
    }

}
