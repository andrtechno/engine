<?php
namespace panix\engine;

use yii\base\Component;

class FileSystem extends Component {

    private $_file = false;
    private $_path = false;

    public function __construct($filename = false, $path = false) {
        if ($filename)
            $this->_file = $filename;
        if ($path)
            $this->_path = rtrim((string) $path, '/');
    }

    static public function fs($filename = false, $path = false) {
        $obj = new FileSystem($filename, $path);
        return $obj;
    }

    public function setName($name) {
        $this->_file = (string) $name;
        return $this;
    }

    public function getName() {
        return $this->_file;
    }

    public function setPath($path) {
        $this->_path = rtrim((string) $path, '/');
        return $this;
    }

    public function getPath() {
        return $this->_path;
    }

    // выдача полного имени
    public function getFullName() {

        return ($this->_file) ? $this->_path . '/' . $this->_file : false;
    }

    //проверка существования	
    public function isExists() {
        return (is_file($this->getFullName()) && is_dir($this->getFullName())) ? true : false;
    }

    //проверка является ли файлом
    public function isFile() {
        return (is_file($this->getFullName())) ? true : false;
    }

    //проверка является ли директорией
    public function isDir() {
        return (is_dir($this->getFullName())) ? true : false;
    }

    //перемещение
    public function moveTo($path) {
        rename($this->getFullName(), rtrim((string) $path, '/') . '/' . $this->_file);
        $this->_path = rtrim((string) $path, '/');
        return $this;
    }

    //удаление объекта
    public function delete() {
        $this->recursiveDelete($this->getFullName());
        $this->_file = false;
    }

    /*
     * очистка директории  - удаляет все вложения, но оставляет папку целой
     */

    public function cleardir() {
        $path = $this->getFullName();
        if (is_dir($path)) {
            $dirHandle = opendir($path);
            while (false !== ($file = readdir($dirHandle))) {
                if ($file != '.' && $file != '..') {// исключаем папки с назварием '.' и '..' 
                    $this->recursiveDelete($path .DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        return $this;
    }

    //рекурсивное удаление
    private function recursiveDelete($path = false) {
        if (file_exists($path)) {
            if (is_dir($path)) {
                $dirHandle = opendir($path);
                while (false !== ($file = readdir($dirHandle))) {
                    if ($file != '.' && $file != '..') {// исключаем папки с назварием '.' и '..' 
                        $this->recursiveDelete($path . DIRECTORY_SEPARATOR . $file);
                    }
                }
                closedir($dirHandle);
                // удаляем текущую папку
                rmdir($path);
            } elseif (is_file($path)) {
                unlink($path);
            }
        }
    }

}