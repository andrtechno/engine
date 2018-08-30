<?php
namespace panix\engine\behaviors;

use panix\engine\Html;
use yii\base\Behavior;
use panix\mod\admin\models\Tag;
use yii\db\ActiveRecord;
class TagsBehavior extends Behavior {

    private $_oldTags;

    public $router;

    public function attach($owner) {
        return parent::attach($owner);
    }


    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function normalizeTags($attribute, $params) {
        $this->tags = Tag::array2string(array_unique(Tag::string2array($this->owner->tags)));
    }

    public function getTagLinks() {
        $links = array();
        foreach (Tag::string2array($this->owner->tags) as $tag)
            $links[] = Html::a(Html::encode($tag), array($this->router, 'tag' => $tag));
        return $links;
    }

    /**
     * Apply object translation
     */
    public function afterFind() {
        $this->_oldTags = $this->owner->tags;

    }


    /**
     * Update model translations
     */
    public function afterSave() {

        $test = Tag::find()->updateFrequency($this->_oldTags, $this->owner->tags);

    }

    /**
     * Delete model related translations
     */
    public function afterDelete() {
        Tag::find()->updateFrequency($this->owner->tags, '');
        return true;
    }

}