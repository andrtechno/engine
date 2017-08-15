<?php

namespace panix\engine\components;

/**
 * Компонент CManagerLanguage
 * 
 * @author Andrew S. <andrew.panix@gmail.com>
 * @package components.managers
 */
use Yii;
use yii\base\Component;

class Settings extends Component {

    /**
     * @var array
     */
    protected $data = array();
    private $cache_key = 'cached_settings';

    /**
     * Initialize component
     */
    public function init() {
        $this->data = Yii::$app->cache->get($this->cache_key);

        if (!$this->data) {
            // Load settings
            /* $settings = Yii::$app->db->createCommand()
              ->from('{{%settings}}')
              ->order('category')
              ->queryAll(); */

            $settings = (new \yii\db\Query())
                    ->from('{{%settings}}')
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
        }
    }

    /**
     * @param $category string component unique id. e.g: contacts, shop, news
     * @param array $data key-value array. e.g array('param'=>10)
     */
    public function set($category, array $data) {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if ($this->get($category, $key) !== null) {
                    Yii::$app->db->createCommand()->update('{{%settings}}', array(
                        'value' => $value), '{{%settings}}.category=:category AND {{%settings}}.param=:param', array(
                        ':category' => $category,
                        ':param' => $key
                    ))->execute();
                } else {
                    Yii::$app->db->createCommand()->insert('{{%settings}}', array(
                        'category' => $category,
                        'param' => $key,
                        'value' => $value
                    ))->execute();
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
    public function get($category, $key = null, $default = null) {
        if (!isset($this->data[$category]))
            return $default;

        if ($key === null)
            return $this->data[$category];
        if (isset($this->data[$category][$key]))
            return $this->data[$category][$key];
        else
            return $default;
    }

    /**
     * Remove category from DB
     * @param $category
     */
    public function clear($category) {
        Yii::$app->db->createCommand()->delete('{{%settings}}', 'category=:category', array(':category' => $category))->execute();
        if (isset($this->data[$category]))
            unset($this->data[$category]);

        Yii::$app->cache->delete($this->cache_key);
    }

}
