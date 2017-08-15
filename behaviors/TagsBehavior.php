<?php

class TagsBehavior extends CActiveRecordBehavior {

    private $_oldTags;

    public $router;

    public function attach($owner) {
        return parent::attach($owner);
    }

    public function normalizeTags($attribute, $params) {
        $this->tags = Tag::array2string(array_unique(Tag::string2array($this->getOwner()->tags)));
    }

    public function getTagLinks() {
        $links = array();
        foreach (Tag::string2array($this->getOwner()->tags) as $tag)
            $links[] = Html::link(Html::encode($tag), array($this->router, 'tag' => $tag));
        return $links;
    }

    /**
     * Apply object translation
     */
    public function afterFind($event) {
        $this->_oldTags = $this->getOwner()->tags;
        return parent::afterFind($event);
    }


    /**
     * Update model translations
     */
    public function afterSave($event) {

        Tag::model()->updateFrequency($this->_oldTags, $this->getOwner()->tags);
        return parent::afterSave($event);
    }

    /**
     * Delete model related translations
     */
    public function afterDelete($event) {
        Tag::model()->updateFrequency($this->getOwner()->tags, '');
        return true;
    }

}