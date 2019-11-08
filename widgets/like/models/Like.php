<?php

namespace panix\engine\widgets\like\models;

use Yii;
use panix\engine\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Class Like
 *
 * @property integer $object_id
 * @property string $handler_hash
 * @property integer $created_at
 * @property boolean $value
 *
 * @package panix\engine\widgets\like\models
 */
class Like extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%like}}';
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new LikeQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['handler_hash'], 'trim'],
            [['handler_hash', 'object_id'], 'required'],
            [['handler_hash'], 'string', 'max' => 8],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    static::EVENT_BEFORE_INSERT => ['created_at'],
                    static::EVENT_BEFORE_UPDATE => ['created_at']
                ],
            ]
        ];
    }

}
