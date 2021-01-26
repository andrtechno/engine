<?php

namespace panix\engine\components;

use panix\engine\CMS;
use Yii;
use yii\base\Component;
use yii\helpers\Json;
use yii\helpers\VarDumper;

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

    public static function tableName()
    {
        return '{{%settings}}';
    }

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
                    ->from(static::tableName())
                    ->orderBy('category')
                    ->all();


                if (!empty($settings)) {
                    foreach ($settings as $row) {
                        if (!isset($this->data[$row['category']]))
                            $this->data[$row['category']] = [];

                        $this->data[$row['category']][$row['param']] = $row['value'];
                    }
                }

                Yii::$app->cache->set($this->cache_key, $this->data);
            } catch (\yii\db\Exception $e) {

            }
        }
    }

    /**
     * @param string $category component unique id. e.g: contacts, shop, news
     * @param array $data key-value array. e.g array('key'=>10)
     */
    public function set($category, array $data)
    {
        $tableName = static::tableName();
        if (!empty($data)) {
            if ($category == 'contacts') {
                // VarDumper::dump($data,10,true);die;
            }
            foreach ($data as $key => $value) {

                $value = (is_array($value)) ? Json::encode($value) : $value;
                try {
                    if ($this->get($category, $key) !== null) {
                        Yii::$app->db->createCommand()->update($tableName, ['value' => $value], $tableName . ".category=:category AND {$tableName}.param=:param", [
                            ':category' => $category,
                            ':param' => $key
                        ])->execute();
                    } else {

                        Yii::$app->db->createCommand()->insert($tableName, [
                            'category' => $category,
                            'param' => $key,
                            'value' => $value
                        ])->execute();
                    }
                } catch (\yii\db\Exception $e) {

                }

            }

            if (!isset($this->data[$category]))
                $this->data[$category] = [];
            $this->data[$category] = array_merge($this->data[$category], $data);


            // Update cache
            Yii::$app->cache->set($this->cache_key, $this->data);
        }
    }

    /**
     * @param string $category component unique id.
     * @param null $key option key. If not provided all category settings will be returned as array.
     * @param null|string $default default value if original does not exists
     * @return mixed
     */
    public function get($category, $key = null, $default = null)
    {
        if (!isset($this->data[$category]))
            return $default;

        if ($key === null) {
            $result = [];
            foreach ($this->data[$category] as $key => $data) {
                $result[$key] = (!empty($data) && CMS::isJson($data)) ? Json::decode($data) : $data;
            }
            return (object)$result;
        }
        if (isset($this->data[$category][$key])) {
            return CMS::isJson($this->data[$category][$key]) ? Json::decode($this->data[$category][$key]) : $this->data[$category][$key];
        } else {
            return $default;
        }
    }

    /**
     * Remove category from DB
     * @param string $category
     * @throws \yii\db\Exception
     */
    public function clear($category)
    {
        Yii::$app->db->createCommand()->delete(static::tableName(), 'category=:category', [':category' => $category])->execute();
        if (isset($this->data[$category]))
            unset($this->data[$category]);

        Yii::$app->cache->delete($this->cache_key);
    }

    /**
     * Remove category & key from DB
     * @param string $category
     * @param string $key
     * @throws \yii\db\Exception
     */
    public function delete($category, $key)
    {
        Yii::$app->db->createCommand()->delete(static::tableName(), ['category' => $category, 'param' => $key])->execute();
        if (isset($this->data[$category][$key]))
            unset($this->data[$category][$key]);

        Yii::$app->cache->delete($this->cache_key);
    }

}
