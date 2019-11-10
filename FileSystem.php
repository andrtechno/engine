<?php

namespace panix\engine;

use panix\engine\assets\codemirror\CodeMirrorPhpAsset;
use yii\base\Component;
use yii\base\Exception;

class FileSystem
{

    private $_file = false;
    private $_path = false;
    private $denie_files = ["index.html", ".htaccess", '.svg'];
    private $denie_folders = ['js', 'admin', 'svg'];

    public function __construct($filename = false, $path = false)
    {
        if ($filename)
            $this->_file = $filename;
        if ($path)
            $this->_path = rtrim((string)$path, '/');
    }

    static public function fs($filename = false, $path = false)
    {
        $obj = new FileSystem($filename, $path);
        return $obj;
    }

    public function setName($name)
    {
        $this->_file = (string)$name;
        return $this;
    }

    public function getName()
    {
        return $this->_file;
    }

    public function setPath($path)
    {
        $this->_path = rtrim((string)$path, '/');

        return $this;
    }

    public function getPath()
    {
        return $this->_path;
    }

    // выдача полного имени
    public function getFullName()
    {

        return ($this->_file) ? $this->_path . '/' . $this->_file : false;
    }

    //проверка существования	
    public function isExists()
    {
        return (is_file($this->getFullName()) && is_dir($this->getFullName())) ? true : false;
    }

    //проверка является ли файлом
    public function isFile()
    {
        return (is_file($this->getFullName())) ? true : false;
    }

    //проверка является ли директорией
    public function isDir()
    {
        return (is_dir($this->getFullName())) ? true : false;
    }

    //перемещение
    public function moveTo($path)
    {
        rename($this->getFullName(), rtrim((string)$path, '/') . '/' . $this->_file);
        $this->_path = rtrim((string)$path, '/');
        return $this;
    }

    //удаление объекта
    public function delete()
    {
        $this->recursiveDelete($this->getFullName());
        $this->_file = false;
    }

    /*
     * очистка директории  - удаляет все вложения, но оставляет папку целой
     */

    public function cleardir()
    {
        $path = $this->getFullName();
        if (is_dir($path)) {
            $dirHandle = opendir($path);
            while (false !== ($file = readdir($dirHandle))) {
                if ($file != '.' && $file != '..') {// исключаем папки с назварием '.' и '..' 
                    $this->recursiveDelete($path . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        return $this;
    }

    protected function id($path)
    {
        //$path = $this->real($path);
        $path = realpath($path);
        $path = substr($path, strlen($this->_path));
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $path = trim($path, '/');
        return strlen($path) ? $path : '/';
    }

    protected function real($path)
    {
        $temp = realpath($path);
        if (!$temp) {
            throw new Exception('Path does not exist: ' . $path);
        }
        //echo $temp;die;
        if ($this->_path && strlen($this->_path)) {
            if (strpos($temp, $this->_path) !== 0) {
                throw new Exception('Path is not inside base (' . $this->_path . '): ' . $temp);
            }
        }
        return $temp;
    }

    public function lst($id, $with_root = false)
    {
        $dir = $this->_path . DIRECTORY_SEPARATOR . $id;
        $lst = @scandir($dir);
        if (!$lst) {
            throw new Exception('Could not list path: ' . $dir);
        }
        $res = [];
        foreach ($lst as $item) {
            if ($item == '.' || $item == '..' || $item === null) {
                continue;
            }
            $tmp = preg_match('([^ a-zа-я-_0-9.]+)ui', $item);
            if ($tmp === false || $tmp === 1) {
                continue;
            }
            if (is_dir($dir . DIRECTORY_SEPARATOR . $item)) {
                if (!in_array($item, $this->denie_folders)) {
                    $res[] = [
                        'text' => $item,
                        'children' => true,
                        'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item),
                        'icon' => 'icon-folder-open'
                    ];
                }
            } else {
                if ($item != "." && $item != ".." && !in_array($item, $this->denie_files) && preg_match("/\./", $item)) {
                    $res[] = [
                        'text' => $item,
                        'children' => false,
                        'id' => $this->id($dir . DIRECTORY_SEPARATOR . $item),
                        'type' => 'file',
                        'icon' => 'file icon-file-' . substr($item, strrpos($item, '.') + 1)
                    ];
                }
            }
        }
        if ($with_root && $this->id($dir) === '/') {
            $res = [
                ['text' => basename($this->_path),
                    'children' => $res,
                    'id' => '/',
                    'icon' => 'icon-folder-open',
                    'state' => [
                        'opened' => true,
                        'disabled' => true
                    ]
                ]
            ];
        }
        return $res;
    }

    public function data($id)
    {
        if (strpos($id, ":")) {
            $id = array_map([$this, 'id'], explode(':', $id));
            return ['type' => 'multiple', 'content' => 'Multiple selected: ' . implode(' ', $id)];
        }
        $dir = $this->_path . DIRECTORY_SEPARATOR . $id;
        if (is_dir($dir)) {
            return ['type' => 'folder', 'content' => $id];
        }
        if (is_file($dir)) {
            $ext = strpos($dir, '.') !== FALSE ? substr($dir, strrpos($dir, '.') + 1) : '';
            $dat = ['type' => $ext, 'content' => '', 'readonly' => false];
            switch ($ext) {
                case 'txt':
                case 'text':
                case 'md':
                case 'js':
                case 'json':
                case 'css':
                case 'scss':
                    $dat['content'] = file_get_contents($dir);
                    break;
                case 'html':
                case 'htm':
                case 'xml':
                case 'c':
                case 'cpp':
                case 'h':
                case 'sql':
                case 'log':
                case 'py':
                case 'rb':
                case 'php':
                    $dat['content'] = file_get_contents($dir);
                    break;
                case 'htaccess':
                    $dat['content'] = 'Access denied';
                    break;
                case 'jpg':
                case 'ico':
                case 'jpeg':
                case 'gif':
                case 'png':
                case 'bmp':
                    //$dat['content']=str_replace('/', DS, $dir);
                    $dat['content'] = 'data:' . finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir) . ';base64,' . base64_encode(file_get_contents($dir));
                    break;
                default:
                    $dat['content'] = 'File not recognized: ' . $this->id($dir);
                    break;
            }
            return $dat;
        }
        throw new Exception('Not a valid selection: ' . $dir);
    }

    //рекурсивное удаление
    private function recursiveDelete($path = false)
    {
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