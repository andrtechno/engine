<?php

namespace panix\engine\base;

use panix\engine\CMS;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\Theme as BaseTheme;
use yii\helpers\Json;

/**
 * Class Theme
 *
 * @property string $name
 *
 * @package panix\engine\base
 */
class Theme extends BaseTheme
{

    public $name = null;
    private $cache_key = 'cached_settings_theme';
    protected $data = [];

    public static function tableName()
    {
        return '{{%settings_theme}}';
    }

    public function init()
    {
        Yii::debug('init', __METHOD__);
        if (preg_match("/admin/", Yii::$app->request->getUrl())) {
            //if (preg_match("/^\/\admin/", Yii::$app->request->getUrl())) {
            $this->name = 'dashboard';
        }
        if ($this->name == null) {
            Yii::debug('Loading null', __METHOD__);
            $this->name = Yii::$app->settings->get('app', 'theme');
        }


        Yii::debug('Loading ' . $this->name, __METHOD__);


        $this->basePath = "@app/web/themes/{$this->name}";
        $this->baseUrl = "@app/web/themes/{$this->name}";
        if (!file_exists(Yii::getAlias($this->basePath))) {
            throw new InvalidConfigException("Error: theme \"{$this->name}\" not found!");
        }

        $modulesPaths = [];
        foreach (Yii::$app->getModules() as $id => $mod) {
            $modulesPaths['@' . $id] = "@app/web/themes/{$this->name}/modules/{$id}";
            //$modulesPaths['@app/modules/' . $id] = "@frontend/themes/{$this->name}/modules/{$id}";
        }

        $this->pathMap = ArrayHelper::merge([
            "@app/views" => "@app/web/themes/{$this->name}/views",
            '@app/modules' => "@app/web/themes/{$this->name}/modules",
            '@app/widgets' => "@app/web/themes/{$this->name}/widgets",
        ], $modulesPaths);


        $this->data = Yii::$app->cache->get($this->cache_key);

        if (!$this->data) {
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


        parent::init();
    }


    public function alert($content, $type = 'secondary')
    {
        return Yii::$app->view->render('@theme/views/_bootstrap/alert', [
            'type' => $type,
            'content' => $content
        ]);
        //return Html::tag('div', $text, ['class' => 'alert alert-' . $type]);
    }


    public function badge($text, $type = 'secondary')
    {
        return Html::tag('span', $text, ['class' => 'badge badge-' . $type]);
    }

    /**
     * @param $category string component unique id. e.g: contacts, shop, news
     * @param array $data key-value array. e.g array('key'=>10)
     */
    public function set($category, array $data)
    {
        $tableName = static::tableName();
        if (!empty($data)) {
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
     * @param $category string component unique id.
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
     * @param $category
     */
    public function clear($category)
    {
        Yii::$app->db->createCommand()->delete(static::tableName(), 'category=:category', [':category' => $category])->execute();
        if (isset($this->data[$category]))
            unset($this->data[$category]);

        Yii::$app->cache->delete($this->cache_key);
    }
}
