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

        if(!$this->basePath){
            $this->basePath = "@app/web/themes/{$this->name}";
        }else{
            $this->basePath = $this->basePath."/{$this->name}";
        }
        if(!$this->baseUrl) {
            $this->baseUrl = "@app/web/themes/{$this->name}";
        }else{
            $this->baseUrl = $this->baseUrl."/{$this->name}";
        }
        if (!file_exists(Yii::getAlias($this->basePath))) {
            throw new InvalidConfigException("Error: theme \"{$this->name}\" not found!");
        }

        $modulesPaths = [];
        foreach (Yii::$app->getModules() as $id => $mod) {
            $modulesPaths['@' . $id] = $this->basePath."/modules/{$id}";
            //$modulesPaths['@app/modules/' . $id] = "@frontend/themes/{$this->name}/modules/{$id}";
        }

        $this->pathMap = ArrayHelper::merge([
            "@app/views" => $this->basePath."/views",
            '@app/modules' => $this->basePath."/modules",
            '@app/widgets' => $this->basePath."/widgets",
        ], $modulesPaths);


        $this->data = Yii::$app->cache->get($this->cache_key);

        if (!$this->data) {
            try {
                $settings = (new \yii\db\Query())
                    ->from(static::tableName())
                    ->orderBy('theme')
                    ->all();


                if (!empty($settings)) {
                    foreach ($settings as $row) {
                        if (!isset($this->data[$row['theme']]))
                            $this->data[$row['theme']] = [];

                        $this->data[$row['theme']][$row['param']] = $row['value'];
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
     * @param $theme string component unique id. e.g: contacts, shop, news
     * @param array $data key-value array. e.g array('key'=>10)
     */
    public function set($theme, array $data)
    {
        $tableName = static::tableName();
        if (!empty($data)) {
            foreach ($data as $key => $value) {

                $value = (is_array($value)) ? Json::encode($value) : $value;
                try {
                    if ($this->get($theme, $key) !== null) {
                        Yii::$app->db->createCommand()->update($tableName, ['value' => $value], $tableName . ".theme=:theme AND {$tableName}.param=:param", [
                            ':theme' => $theme,
                            ':param' => $key
                        ])->execute();
                    } else {

                        Yii::$app->db->createCommand()->insert($tableName, [
                            'theme' => $theme,
                            'param' => $key,
                            'value' => $value
                        ])->execute();
                    }
                } catch (\yii\db\Exception $e) {

                }

            }

            if (!isset($this->data[$theme]))
                $this->data[$theme] = [];
            $this->data[$theme] = array_merge($this->data[$theme], $data);


            // Update cache
            Yii::$app->cache->set($this->cache_key, $this->data);
        }
    }

    /**
     * @param $theme string component unique id.
     * @param null $key option key. If not provided all theme settings will be returned as array.
     * @param null|string $default default value if original does not exists
     * @return mixed
     */
    public function get($theme, $key = null, $default = null)
    {
        if (!isset($this->data[$theme]))
            return $default;

        if ($key === null) {
            $result = [];
            foreach ($this->data[$theme] as $key => $data) {
                $result[$key] = (!empty($data) && CMS::isJson($data)) ? Json::decode($data) : $data;
            }
            return (object)$result;
        }
        if (isset($this->data[$theme][$key])) {
            return CMS::isJson($this->data[$theme][$key]) ? Json::decode($this->data[$theme][$key]) : $this->data[$theme][$key];
        } else {
            return $default;
        }
    }

    /**
     * Remove category from DB
     * @param $theme
     */
    public function clear($theme)
    {
        Yii::$app->db->createCommand()->delete(static::tableName(), 'theme=:theme', [':theme' => $theme])->execute();
        if (isset($this->data[$theme]))
            unset($this->data[$theme]);

        Yii::$app->cache->delete($this->cache_key);
    }
}
