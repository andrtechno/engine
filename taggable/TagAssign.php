<?php
namespace panix\engine\taggable;

use yii\db\ActiveRecord;

class TagAssign extends ActiveRecord {

    /**
     * @return string the associated database table name
     */
    public static function tableName() {
        return '{{%post_tag_assign}}';
    }



    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['name', 'required'],
        ];
    }

    public static function string2array($tags) {
        return preg_split('/\s*,\s*/', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function array2string($tags) {
        return implode(', ', $tags);
    }



}