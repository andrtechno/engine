<?php

namespace panix\engine\db;

use panix\engine\behaviors\TranslateBehavior;
use panix\engine\CMS;
use panix\engine\controllers\AdminController;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use panix\engine\data\Pagination;
use panix\engine\Html;
use panix\engine\widgets\LinkPager;
use yii\web\NotFoundHttpException;
use yii\console\Application as ConsoleApplication;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Class ActiveRecord
 * @package panix\engine\db
 *
 * @property array $disallow_delete IDs
 * @property array $disallow_switch IDs
 * @property array $disallow_update IDs
 * @property integer $user_id
 * @property string $user_agent
 * @property string $ip_create IP адрес
 * @property integer $ordern Sorting drag-n-drop
 */
class ActiveRecord extends \yii\db\ActiveRecord
{

    public $disallow_delete = [];
    public $disallow_switch = [];
    public $disallow_update = [];
    public $tab_errors = []; // Todo test
    const route_update = 'update';
    const route_delete = 'delete';
    const route_switch = 'switch';
    const route_create = 'create';
    const route = null;
    const MODULE_ID = null;
    public $translationClass;
    //  public $translationOptions;
    protected $_hash;

    /**
     * @param null|string $redirect
     * @return string
     */
    public function submitButton($redirect = null)
    {
        $redirect = ($redirect) ? $redirect : \yii\helpers\Url::to(['index']);

        $html = '';
        $html .= Html::submitButton(Yii::t('app/default', $this->isNewRecord ? 'CREATE' : 'SAVE'), ['class' => 'btn btn-success']);
        if (!$this->isNewRecord)
            $html .= Html::submitButton(Yii::t('app/default', $this->isNewRecord ? 'CREATE_RETURN' : 'SAVE_RETURN'), ['class' => 'btn btn-link', 'value' => $redirect, 'name' => 'redirect']);
        return $html;
    }

    public function getColumnSearch($array = [])
    {
        $col = $this->gridColumns;
        $result = [];
        if (isset($col['DEFAULT_COLUMNS'])) {
            foreach ($col['DEFAULT_COLUMNS'] as $t) {
                $result[] = $t;
            }
        }

        foreach ($array as $column) {
            if (isset($col[$column]))
                $result[] = $col[$column];
        }

        if (isset($col['DEFAULT_CONTROL']))
            $result[] = $col['DEFAULT_CONTROL'];

        return $result;
    }

    public function getColumnSearchOLd($array = [])
    {
        $col = $this->gridColumns;
        $result = [];
        if (isset($col['DEFAULT_COLUMNS'])) {
            foreach ($col['DEFAULT_COLUMNS'] as $t) {
                $result[] = $t;
            }
        }
        foreach ($array as $key => $s) {
            $result[] = $col[$key];
        }

        if (isset($col['DEFAULT_CONTROL']))
            $result[] = $col['DEFAULT_CONTROL'];

        return $result;
    }

    /**
     * @param $id
     * @param null|string $message
     * @return null|static
     * @throws NotFoundHttpException
     */
    public static function findModel($id, $message = null)
    {

        if (($model = static::findOne((int)$id)) !== null) {
            //if (($model = static::find()->one((int)$id)) !== null) {
            return $model;
        } else {
            if (!$id)
                return new static();
            throw new NotFoundHttpException($message ? $message : Yii::t('app/error', '404'));
        }
    }


    public static function dropdown()
    {
        // get and cache data
        static $dropdown;
        if ($dropdown === null) {

            // get all records from database and generate
            $models = static::find()->all();
            foreach ($models as $model) {
                $dropdown[$model->id] = $model->name;
            }
        }

        return $dropdown;
    }

    /*
    public function afterFind()
    {
        $logMessage = "    > find " . get_class($this) . " [" . $this->primaryKey . "]".PHP_EOL;

        if (Yii::$app instanceof ConsoleApplication) {
            echo $logMessage;
        }
        parent::afterFind(); // TODO: Change the autogenerated stub
    }*/
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $columns = $this->tableSchema->columns;
        //if (parent::beforeSave($insert)) {
        //create
        if ($this->isNewRecord) {
            if (isset($columns['ip_create'])) {
                //Текущий IP адресс, автора добавление
                $this->ip_create = Yii::$app->request->getUserIP();
            }
            if (!(Yii::$app instanceof ConsoleApplication)) {
                if (isset($columns['user_id']) && !$this->user_id) {
                    $this->user_id = (Yii::$app->user->isGuest) ? NULL : Yii::$app->user->id;
                }
                if (isset($columns['user_agent'])) {
                    $this->user_agent = Yii::$app->request->userAgent;
                }
            }
            /*if (isset($columns['ordern'])) {
                if (!isset($this->ordern)) {
                    $row = static::find()->select('max(ordern) as maxOrdern')->asArray()->one();
                    $this->ordern = $row['maxOrdern'] + 1;
                }
            }*/
        }


        /*//Для теста изменение дата добавления
        if (isset($this->behaviors['timestamp'])) {
            $createdAt = $this->behaviors['timestamp']->createdAtAttribute;
            if (isset($this->{$createdAt})) {
                $this->created_at = 1676711113;
            }
        }*/

        return parent::beforeSave($insert);
    }


    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (isset($this->behaviors['timestamp'])) {
            $updatedAt = $this->behaviors['timestamp']->updatedAtAttribute;
            if (isset($this->{$updatedAt})) {
                $this->touch($updatedAt);
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attrLabels = [];
        if (isset($this->behaviors['translate'])) {
            if (isset($this->behaviors['translate']->translationAttributes)) {
                foreach ($this->behaviors['translate']->translationAttributes as $attr) {
                    $attrLabels[$attr] = static::t(strtoupper($attr));
                }
            }
        }
        if ($this->attributes) {
            foreach ($this->attributes as $attr => $val) {
                $attrLabels[$attr] = static::t(strtoupper($attr));
            }
        }

        return $attrLabels;
    }

    public function getTranslations()
    {
        if ($this->translationClass) {
            return $this->hasMany($this->translationClass, ['object_id' => 'id']);
        } else {
            return $this;
        }
    }

    public function behaviors()
    {
        $b = [];
        try {
            $columns = $this->tableSchema->columns;

            if ($this->translationClass) {
                $class = $this->translationClass;
                $b['translate']['class'] = TranslateBehavior::class;
                $b['translate']['translationClass'] = $class;
                $b['translate']['translationAttributes'] = $class::$translationAttributes;
            }

            if (isset($columns['ordern'])) {
                $b['sortable'] = [
                    'class' => \panix\engine\grid\sortable\Behavior::class,
                ];
            }
            if (isset($columns['created_at']) && isset($columns['updated_at'])) {

                $timestampAttributes[ActiveRecord::EVENT_BEFORE_UPDATE]='updated_at';
                $timestampAttributes[ActiveRecord::EVENT_BEFORE_INSERT]=['created_at', 'updated_at'];
                if($this->isNewRecord && $this->created_at){ //Если елемент новый и дата указана,то устанавливаем ее
                    $timestampAttributes[ActiveRecord::EVENT_BEFORE_INSERT]=['updated_at'];
                }
                $b['timestamp'] = [
                    'class' => TimestampBehavior::class,
                    'attributes' => $timestampAttributes
                ];
            }


        } catch (\yii\db\Exception $e) {

        }
        return $b;
    }

    /**
     * @param string $message
     * @param array $params
     * @return string
     */
    public static function t($message, $params = array())
    {
        $fileName = (new \ReflectionClass(get_called_class()))->getShortName();
        return Yii::t(strtolower(static::MODULE_ID) . '/' . $fileName, $message, $params);
    }

    /**
     * @param $wheres
     * @return \yii\db\Query
     */
    public function getNext($wheres)
    {
        $db = static::getDb();

        $prev = $db->cache(function ($db) use ($wheres) {

            $q = static::find();
            $subQuery = (new \yii\db\Query())->select('MIN(`id`)')
                ->from(static::tableName() . ' next')
                ->where(['>', 'next.id', $this->getPrimaryKey()]);

            if ($wheres) {
                $subQuery->andWhere($wheres);
            }

            return $q->where(['=', 'id', $subQuery]);

        }, 3600);

        return $prev;
    }

    /**
     * @param $wheres
     * @return \yii\db\Query
     */
    public function getPrev($wheres)
    {
        $db = static::getDb();

        $prev = $db->cache(function ($db) use ($wheres) {

            $q = static::find();
            $subQuery = (new \yii\db\Query())->select('MAX(`id`)')
                ->from(static::tableName() . ' prev')
                ->where(['<', 'prev.id', $this->getPrimaryKey()]);

            if ($wheres) {
                $subQuery->andWhere($wheres);
            }

            return $q->where(['=', 'id', $subQuery]);

        }, 3600);

        return $prev;
    }

    public function getNext_old()
    {
        $next = static::getDb()->cache(function () {
            return static::find()
                ->where(['<', 'id', $this->getPrimaryKey()])
                ->orderBy(['id' => SORT_ASC]);
        }, 3600);
        return $next;
    }

    public function getPrev_OLD()
    {
        $prev = static::getDb()->cache(function () {
            return static::find()
                ->where(['>', 'id', $this->getPrimaryKey()])
                ->orderBy(['id' => SORT_DESC]);
        }, 3600);
        return $prev;
    }


    /**
     * @return string
     * @throws Exception
     */
    public function getCreateUrl()
    {
        if (static::route) {
            return Yii::$app->urlManager->createUrl(static::route . '/' . static::route_create);
        } else {
            throw new Exception(Yii::t('app/default', 'NOTFOUND_CONST_AR', [
                'param' => 'route_create',
            ]));
        }
    }


    public function getDeleteUrl()
    {
        if (static::route) {
            return Yii::$app->urlManager->createUrl([static::route . '/' . static::route_delete,
                'model' => get_class($this),
                'id' => $this->id
            ]);
        } else {
            throw new Exception(Yii::t('app/default', 'NOTFOUND_CONST_AR', [
                'param' => 'route_delete',
                'model' => get_class($this)
            ]));
        }
    }


    public function getUpdateUrl()
    {
        if (static::route) {
            return Yii::$app->urlManager->createUrl([static::route . '/' . static::route_update,
                'id' => $this->id
            ]);
        } else {
            throw new Exception(Yii::t('app/default', 'NOTFOUND_CONST_AR', [
                'param}' => 'route_update',
                'model}' => get_class($this)
            ]));
        }
    }

    public function isString($attribute)
    {
        if (Yii::$app->user->can('admin')) {
            \panix\ext\tinymce\TinyMceInline::widget();
            $html = Html::beginForm($this->getUpdateUrl());
            $html .= '<span id="' . Html::getInputName($this, $attribute) . '" class="edit_mode_title">' . $this->$attribute . '</span>';
            $html .= Html::endForm();
            return $html;
        } else {
            return $this->$attribute;
        }
    }

    public function isText($attribute)
    {
        if (Yii::$app->user->can('admin')) {
            \panix\ext\tinymce\TinyMceInline::widget();
            $html = Html::beginForm($this->getUpdateUrl());
            $html .= '<div id="' . Html::getInputName($this, $attribute) . '" class="edit_mode_text">' . $this->$attribute . '</div>';
            $html .= Html::endForm();
            return $html;
        } else {
            return $this->pageBreak($attribute);
        }
    }

    /**
     * Разделение текста на страницы
     * @param string|boolean $attribute
     * @return string
     */
    public function pageBreak($attribute = false)
    {
        if ($attribute) {
            $pageVar = intval(Yii::$app->request->get('page_break'));
            $pageBreak = explode("<!-- pagebreak -->", $this->{$attribute});
            $pageCount = count($pageBreak);

            $pageVar = ($pageVar == "" || $pageVar < 1) ? 1 : $pageVar;
            if ($pageVar > $pageCount)
                $pageVar = $pageCount;
            $arrayElement = (int)$pageVar;
            $arrayElement--;

            $content = $pageBreak[$arrayElement];
            $content .= LinkPager::widget([
                'linkOptions' => ['class' => 'page-link', 'tabindex' => "-1", 'data-pjax' => "1"],
                'pagination' => new Pagination([
                    'totalCount' => $pageCount,
                    'pageSize' => 1,
                    'pageParam' => 'page_break',
                    'defaultPageSize' => 1,
                ]),
            ]);;
            return Html::text($content);
        } else {
            return false;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSwitchUrl()
    {
        if (static::route) {
            return Yii::$app->urlManager->createUrl(static::route . '/' . static::route_switch, [
                'model' => get_class($this),
                'switch' => 0,
                'id' => $this->id
            ]);
        } else {
            throw new Exception(Yii::t('app/default', 'NOTFOUND_CONST_AR', [
                'param' => 'route_switch',
                'model' => get_class($this)
            ]));
        }
    }


    public function getHash()
    {
        if (!$this->_hash) {
            $this->_hash = '\\' . get_class($this);
        }
        return CMS::hash($this->_hash);
    }

    public function setHash($value)
    {
        $this->_hash = $value;
    }

}
