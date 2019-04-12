<?php

namespace panix\engine\db;


use panix\engine\data\Pagination;
use panix\engine\widgets\LinkPager;
use Yii;
use yii\base\Exception;
use panix\mod\shop\models\Category;
use yii\behaviors\TimestampBehavior;
use yii\widgets\Pjax;

class ActiveRecord extends \yii\db\ActiveRecord
{



    /**
     * Disallow actions
     * @disallow_delete array ids
     * @disallow_switch array ids
     * @disallow_update array ids
     */
    public $disallow_delete = [];
    public $disallow_switch = [];
    public $disallow_update = [];

    const route_update = 'update';
    const route_delete = 'delete';
    const route_switch = 'switch';
    const route_create = 'create';
    const route = null;
    const MODULE_ID = null;

    public function getColumnSearch($array = array())
    {
        $col = $this->gridColumns;
        $result = array();
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
            if (isset($columns['user_id'])) {
                $this->user_id = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
            }
            if (isset($columns['user_agent'])) {
                $this->user_agent = Yii::$app->request->userAgent;
            }
            if (isset($columns['ordern'])) {
                if (!isset($this->ordern)) {
                    $row = static::find()->select('max(ordern) as maxOrdern')->asArray()->one();
                    $this->ordern = $row['maxOrdern'] + 1;
                }
            }
            //update
        } else {
            if (isset($columns['date_update'])) {
                $this->date_update = date('Y-m-d H:i:s');
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave222($insert, $changedAttributes)
    {
        if (isset($this->tableSchema->columns['date_update'])) {
            //echo $this->date_update;
            //die('s');
            //   $this->date_update = date('Y-m-d H:i:s');
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function attributeLabels()
    {
        $lang = Yii::$app->language;
        $attrLabels = [];
        foreach ($this->behaviors() as $key => $b) {
            if (isset($b['translationAttributes'])) {
                foreach ($b['translationAttributes'] as $attr) {
                    $attrLabels[$attr] = self::t(strtoupper($attr));
                }
            }
        }
        foreach ($this->attributes as $attr => $val) {
            $attrLabels[$attr] = self::t(strtoupper($attr));
        }

        return $attrLabels;
    }

    public function behaviors()
    {
        $columns = $this->tableSchema->columns;
        $b = [];
        if (isset($columns['ordern'])) {
            $b['sortable'] = [
                'class' => \panix\engine\grid\sortable\Behavior::class,
            ];
        }
        if (isset($columns['created_at']) && isset($columns['updated_at'])) {
            $b['timestamp'] = [
                'class' => TimestampBehavior::class,
            ];
        }

        return $b;
    }

    public static function t($message, $params = array())
    {
        $fileName = (new \ReflectionClass(get_called_class()))->getShortName();
        return Yii::t(strtolower(static::MODULE_ID) . '/' . $fileName, $message, $params);
    }


    public function getObjectNext()
    {
        /*$next = $this->find()
            ->where(['<', 'id', $this->id])
            ->orderBy(['id' => SORT_ASC])
            //->addOrderBy(['id'=>SORT_ASC])
            ->one();*/
        $next = Yii::$app->db->cache(function ($db) {
            return $this->find()
                ->where(['<', 'id', $this->id])
                ->orderBy(['id' => SORT_ASC])
                ->one();
        }, 3600);
        //$next->detachBehavior('discountsBehavior');
        return $next;
    }

    public function getObjectPrev()
    {

        $prev = Yii::$app->db->cache(function ($db) {
            return $this->find()
                ->where(['>', 'id', $this->id])
                ->orderBy(['id' => SORT_DESC])
                ->one();
        }, 3600);

        /*$prev = $this->find()
            ->where(['>', 'id', $this->id])
            ->orderBy(['id' => SORT_DESC])
            ->one();*/
        //$prev->detachBehavior('discountsBehavior');
        return $prev;
    }


    public function getNextOrPrev($nextOrPrev, $cid = false, $options = array())
    {
        $records = NULL;

        if ($nextOrPrev == "prev")
            $order = [$this::tableName() . '.id' => SORT_ASC];
        if ($nextOrPrev == "next")
            $order = [$this::tableName() . '.id' => SORT_DESC];

        if (!isset($options['select']))
            $options['select'] = [$this::tableName() . '.*'];

        if ($cid) {//TODO: no work need job.
            $options['params'] = array(':cid' => $cid);
        }

        //$modelParams
        $records = $this::find()
            ->select($options['select'])
            ->where([$this::tableName() . '.switch' => 1])
            ->andWhere([Category::tableName() . '.id' => 45])
            ->joinWith('mainCategory')
            ->orderBy($order)
            ->all();

        echo count($records);
        echo '<br>';
        foreach ($records as $i => $r)
            if ($r->id == $this->id)
                return (isset($records[$i + 1])) ? $records[$i + 1] : NULL;

        return NULL;
    }

    /**
     * Special for widget ext.admin.frontControl
     *
     * @return string
     */
    public function getCreateUrl()
    {
        if (static::route) {
            return Yii::$app->urlManager->createUrl(static::route . '/' . static::route_create);
        } else {
            throw new Exception(Yii::t('app', 'NOTFOUND_CONST_AR', [
                'param' => 'route_create',
            ]));
        }
    }

    public function isString($attribute)
    {
        if (Yii::$app->user->can('admin')) {
            $html = '<form action="' . $this->getUpdateUrl() . '" method="POST">';
            $html .= '<span id="' . basename(get_class($this)) . '[' . $attribute . ']" class="edit_mode_title">' . $this->$attribute . '</span>';
            $html .= '</form>';
            return $html;
        } else {
            return $this->$attribute;
        }
    }

    public function isText($attribute)
    {
        if (Yii::$app->user->can('admin')) {
            $html = '<form action="' . $this->getUpdateUrl() . '" method="POST">';
            $html .= '<div id="' . basename(get_class($this)) . '[' . $attribute . ']" class="edit_mode_text">' . $this->$attribute . '</div>';
            $html .= '</form>';
            return $html;
        } else {
            return Html::text($this->$attribute);
        }
    }

    /**
     * Special for widget ext.admin.frontControl
     * @return string
     */
    public function getDeleteUrl()
    {
        if (static::route) {
            return Yii::$app->urlManager->createUrl([static::route . '/' . static::route_delete,
                'model' => get_class($this),
                'id' => $this->id
            ]);
        } else {
            throw new Exception(Yii::t('app', 'NOTFOUND_CONST_AR', array(
                'param' => 'route_delete',
                'model' => get_class($this)
            )));
        }
    }

    /**
     * Special for widget ext.admin.frontControl
     * @return string
     */
    public function getUpdateUrl()
    {
        if (static::route) {
            return Yii::$app->urlManager->createUrl([static::route . '/' . static::route_update,
                'id' => $this->id
            ]);
        } else {
            throw new Exception(Yii::t('app', 'NOTFOUND_CONST_AR', array(
                'param}' => 'route_update',
                'model}' => get_class($this)
            )));
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
            $pageVar = intval(Yii::$app->request->get('page'));
            $pageBreak = explode("<!-- pagebreak -->", $this->{$attribute});
            $pageCount = count($pageBreak);

            $pageVar = ($pageVar == "" || $pageVar < 1) ? 1 : $pageVar;
            if ($pageVar > $pageCount)
                $pageVar = $pageCount;
            $arrayelement = (int)$pageVar;
            $arrayelement--;

            ;

            $content = $pageBreak[$arrayelement];
            $content .= LinkPager::widget([
                'pagination' => new Pagination([
                    'totalCount' => $pageCount,
                    'pageSize' => 1,
                    'defaultPageSize'=>1,
                ]),
            ]);
            ;
            return $content;
        }else{
            return false;
        }
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if (static::route) {
            return Yii::$app->urlManager->createUrl(static::route . '/' . static::route_switch, array(
                'model' => get_class($this),
                'switch' => 0,
                'id' => $this->id
            ));
        } else {
            throw new Exception(Yii::t('app', 'NOTFOUND_CONST_AR', array(
                'param' => 'route_switch',
                'model' => get_class($this)
            )));
        }
    }

}
