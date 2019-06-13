<?php

namespace panix\engine\widgets\like\models;

use Yii;
use panix\engine\db\ActiveRecord;

class Like extends ActiveRecord
{

    const MODULE_ID = 'shop';



    public static function tableName()
    {
        return '{{%like}}';
    }

    public static function find2()
    {
        return new CategoryQuery(get_called_class());
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




    public function behaviors()
    {
        return [

        ];
    }

    public function getCountItems()
    {
        return $this->hasMany(ProductCategoryRef::class, ['category' => 'id'])->count();
    }


}
