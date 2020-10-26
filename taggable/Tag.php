<?php

namespace panix\engine\taggable;

use yii\db\ActiveRecord;

/**
 * Class Tag
 *
 * @property integer $id
 * @property string $name
 * @property integer $frequency
 *
 * @package panix\engine\taggable
 */
class Tag extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
        ];
    }

    public static function string2array($tags)
    {
        return preg_split('/\s*,\s*/', trim($tags), -1, PREG_SPLIT_NO_EMPTY);
    }

    public static function array2string($tags)
    {
        return implode(', ', $tags);
    }


}