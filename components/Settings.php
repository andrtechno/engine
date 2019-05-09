<?php

namespace panix\engine\components;

use Yii;
use yii\base\Component;

/**
 * Class Settings
 * @package panix\engine\components
 */
class Settings extends Component
{

    /**
     * @var array
     */
    protected $data = [];
    private $cache_key = 'cached_settings';
    public static $tableName = '{{%settings}}';

    /**
     * Initialize component
     */
    public function init()
    {
        $this->data = Yii::$app->cache->get($this->cache_key);

        if (!$this->data) {
            // Load settings
            /* $settings = Yii::$app->db->createCommand()
              ->from('{{%settings}}')
              ->order('category')
              ->queryAll(); */
            try {
                $settings = (new \yii\db\Query())
                    ->from(static::$tableName)
                    ->orderBy('category')
                    ->all();


                if (!empty($settings)) {
                    foreach ($settings as $row) {
                        if (!isset($this->data[$row['category']]))
                            $this->data[$row['category']] = array();
                        $this->data[$row['category']][$row['param']] = $row['value'];
                    }
                }
                Yii::$app->cache->set($this->cache_key, $this->data);
            } catch (\yii\db\Exception $e) {

            }
        }
    }

    /**
     * @param $category string component unique id. e.g: contacts, shop, news
     * @param array $data key-value array. e.g array('key'=>10)
     */
    public function set($category, array $data)
    {
        $tableName = static::$tableName;
        if (!empty($data)) {
            foreach ($data as $key => $value) {

                try {
                    if ($this->get($category, $key) !== null) {
                        Yii::$app->db->createCommand()->update($tableName, array(
                            'value' => $value), $tableName . ".category=:category AND {$tableName}.param=:param", array(
                            ':category' => $category,
                            ':param' => $key
                        ))->execute();
                    } else {
                        Yii::$app->db->createCommand()->insert($tableName, array(
                            'category' => $category,
                            'param' => $key,
                            'value' => $value
                        ))->execute();
                    }
                } catch (\yii\db\Exception $e) {

                }

            }

            if (!isset($this->data[$category]))
                $this->data[$category] = array();
            $this->data[$category] = array_merge($this->data[$category], $data);

            // Update cache
            Yii::$app->cache->set($this->cache_key, $this->data);
        }
    }

    /**
     * @param $category string component unique id.
     * @param null $key option key. If not provided all category settings will be returned as array.
     * @param null|string $default default value if original does not exists
     * @return mixed
     */
    public function get($category, $key = null, $default = null)
    {
        if (!isset($this->data[$category]))
            return $default;

        if ($key === null)
            return (object)$this->data[$category];
        if (isset($this->data[$category][$key]))
            return $this->data[$category][$key];
        else
            return $default;
    }

    /**
     * Remove category from DB
     * @param $category
     */
    public function clear($category)
    {
        Yii::$app->db->createCommand()->delete(static::$tableName, 'category=:category', array(':category' => $category))->execute();
        if (isset($this->data[$category]))
            unset($this->data[$category]);

        Yii::$app->cache->delete($this->cache_key);
    }

}
