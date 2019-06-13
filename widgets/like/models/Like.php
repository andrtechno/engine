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
            [['model'], 'trim'],
            [['model', 'object_id'], 'required'],
            [['model'], 'string', 'max' => 100],
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

    public function getCountItems()
    {
        return $this->hasMany(ProductCategoryRef::class, ['category' => 'id'])->count();
    }


}
