<?php

namespace panix\engine\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * TranslateBehavior
 *
 * @property ActiveRecord $owner
 */
class TranslateBehavior extends Behavior
{

    /**
     * @var string the translations relation name
     */
    public $relation = 'translations';

    /**
     * @var string the translations model language attribute name
     */
    public $translationLanguageAttribute = 'language_id';

    /**
     * @var string[] the list of attributes to be translated
     */
    public $translationAttributes;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->translationAttributes === null) {
            throw new InvalidConfigException('The "translationAttributes" property must be set.');
        }
    }

    /**
     * Returns the translation model for the specified language.
     * @param string|null $language
     * @return ActiveRecord
     */
    public function translate($language = null)
    {
        return $this->getTranslation($language);
    }

    /**
     * Returns the translation model for the specified language.
     * @param string|null $language
     * @return ActiveRecord
     * @throws InvalidConfigException
     */
    public function getTranslation($language = null)
    {
        if ($language === null) {
            $language = Yii::$app->language;
        }
        $lang = Yii::$app->languageManager->getByCode($language);
        if (!$lang)
            throw new InvalidConfigException('Language not found ' . $language);

        /* @var ActiveRecord[] $translations */
        $translations = $this->owner->{$this->relation};

        foreach ($translations as $translation) {
            if ($translation->getAttribute($this->translationLanguageAttribute) === $lang->id) {
                return $translation;
            }
        }
        /* @var ActiveRecord $class */
        $class = $this->owner->getRelation($this->relation)->modelClass;
        /* @var ActiveRecord $translation */
        $translation = new $class();
        $translation->setAttribute($this->translationLanguageAttribute, $lang->id);
        $translations[] = $translation;
        $this->owner->populateRelation($this->relation, $translations);
        return $translation;
    }

    /**
     * Returns a value indicating whether the translation model for the specified language exists.
     * @param string|null $language
     * @return boolean
     * @throws InvalidConfigException
     */
    public function hasTranslation($language = null)
    {
        if ($language === null) {
            $language = Yii::$app->language;
        }
        $lang = Yii::$app->languageManager->getByCode($language);
        if (!$lang)
            throw new InvalidConfigException('Language not found ' . $language);

        /* @var ActiveRecord $translation */
        foreach ($this->owner->{$this->relation} as $translation) {
            if ($translation->getAttribute($this->translationLanguageAttribute) === $lang->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return void
     */
    public function afterValidate()
    {
        if (!Model::validateMultiple($this->owner->{$this->relation})) {
            $this->owner->addError($this->relation);
        }
    }

    public function afterDelete()
    {
        foreach ($this->owner->{$this->relation} as $translation) {
            $translation::deleteAll(['object_id' => $this->owner->getPrimaryKey()]);
        }
        return true;
    }

    /**
     * @return void
     */
    public function afterSave()
    {
        /* @var ActiveRecord $translation */
        foreach ($this->owner->{$this->relation} as $translation) {
            $this->owner->link($this->relation, $translation);
        }
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->translationAttributes) ?: parent::canGetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return in_array($name, $this->translationAttributes) ?: parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        $translation = $this->getTranslation();
        return $translation->getAttribute($name);
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        $translation = $this->getTranslation();
        $translation->setAttribute($name, $value);
    }

}
