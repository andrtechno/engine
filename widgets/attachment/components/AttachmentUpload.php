<?php

/**
 * Validate uploaded product image.
 * Create unique image name.
 */
class AttachmentUpload {

    public $extension = array('jpg', 'jpeg', 'png', 'gif');
    public $maxSize;
    public $path;
    public $genParam;
    public $genType = 'random';

    public function __construct(array $config) {
        $this->genParam = (isset($config['genParam'])) ? $config['genParam'] : 'name';
        $this->genType = (isset($config['genType'])) ? $config['genType'] : 'random';

        $this->path = (isset($config['path'])) ? $config['path'] : 'attachments';
        $this->maxSize = (isset($config['maxSize'])) ? $config['maxSize'] * 1048576 : 5 * 1048576;
        $this->extension = (isset($config['extension'])) ? $config['extension'] : array('jpg', 'jpeg', 'png', 'gif');

    }

    /**
     * @param CUploadedFile $image
     * @return bool
     */
    public function isAllowedSize(CUploadedFile $image) {
        return ($image->getSize() <= $this->maxSize);
    }

    /**
     * @param CUploadedFile $image
     * @return bool
     */
    public function isAllowedExt(CUploadedFile $image) {
        return in_array(strtolower($image->getExtensionName()), $this->extension);
    }

    /**
     * @param CUploadedFile $image
     * @return bool
     */
    public function isAllowedType(CUploadedFile $image) {
        $type = CFileHelper::getMimeType($image->getTempName());
        if (!$type)
            $type = CFileHelper::getMimeTypeByExtension($image->getName());
        return in_array($type, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png'));
    }

    /**
     * @param CUploadedFile $image
     * @return bool
     */
    public function hasErrors2(CUploadedFile $image) {
        return !(!$image->getError() && $this->isAllowedExt($image) === true && $this->isAllowedSize($image) === true && $this->isAllowedType($image) === true);
    }

    public function hasErrors(CUploadedFile $image) {
        return !(!$image->getError() && $this->isAllowedExt($image) === true && $this->isAllowedSize($image) === true);
    }

    /**
     * @return string Path to save product image
     */
    public function getSavePath() {
        return Yii::getPathOfAlias("webroot.uploads.attachments.{$this->path}");
    }

    /**
     * @param Model $model
     * @param CUploadedFile $image
     * @return string
     */
    public function createName($model, CUploadedFile $image) {
        $path = $this->getSavePath();
        if ($this->genType == 'random') {
            $name = $this->generateRandomName($model, $image);
        } elseif ($this->genType == 'static') {
            $name = $this->generateName($model, $image);
        }
        if (!file_exists($path . '/' . $name))
            return $name;
        else
            $this->createName($model, $image);
    }

    /**
     * Generates random name bases on product and image models
     *
     * @param Model $model
     * @param CUploadedFile $image
     * @return string
     */
    private function generateRandomName($model, CUploadedFile $image) {
        return strtolower($model->id . '_' . CMS::gen(10) . '.' . $image->getExtensionName());
    }

    /**
     * Generates random name bases on product and image models
     *
     * @param Model $model
     * @param CUploadedFile $image
     * @return string
     */
    private function generateName($model, CUploadedFile $image) {
        $param = $this->genParam;
        return strtolower($model->id . '_' . $this->str2url($model->$param) . '.' . $image->getExtensionName());
    }

    
    
    
    
    
    
    
    
    
    
private function rus2translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}
private function str2url($str) {
    // переводим в транслит
    $str = $this->rus2translit($str);
    // в нижний регистр
    $str = strtolower($str);
    // заменям все ненужное нам на "-"
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    // удаляем начальные и конечные '-'
    $str = trim($str, "-");
    return $str;
}
    
    
    
    
    
    
    
    
    
    
    
}
