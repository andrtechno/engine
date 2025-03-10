<?php

namespace panix\engine;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\helpers\FileHelper;

/**
 * Дополнительные функции системы.
 *
 * @author PIXELION CMS development team <dev@pixelion.com.ua>
 * @link http://pixelion.com.ua PIXELION CMS
 * @version 1.0
 * @package app
 * @category app
 */
class CMS
{
    const MEMORY_LIMIT = 512; // Minimal memory_limit


    public static function convertPHPSizeToBytes($size)
    {
        //
        $suffix = strtoupper(substr($size, -1));
        if (!in_array($suffix, ['P', 'T', 'G', 'M', 'K'])) {
            return (int)$size;
        }
        $value = substr($size, 0, -1);
        switch ($suffix) {
            case 'P':
                $value *= 1024;
            // Fallthrough intended
            case 'T':
                $value *= 1024;
            // Fallthrough intended
            case 'G':
                $value *= 1024;
            // Fallthrough intended
            case 'M':
                $value *= 1024;
            // Fallthrough intended
            case 'K':
                $value *= 1024;
                break;
        }
        return (int)$value;
    }

    /**
     * Конвертирует число "150" в "000150"
     *
     * @param int $number
     * @param int $n
     * @return string
     */
    public static function idToNumber(int $number, $n = 10)
    {
        return sprintf("%0{$n}d", $number);
    }

    /**
     * Проверка дубликатов в массиве
     *
     * @param $array
     * @return bool
     */
    public static function hasDuplicates($array)
    {
        return count($array) !== count(array_unique($array));
    }

    /**
     * Displays a variable.
     * This method achieves the similar functionality as var_dump and print_r
     * but is more robust when handling complex objects such as Yii controllers.
     * @param mixed $var variable to be dumped
     * @param int $depth maximum depth that the dumper should go into the variable. Defaults to 10.
     * @param bool $highlight whether the result should be syntax-highlighted
     */
    public static function dump($var, int $depth = 10, bool $highlight = true)
    {
        VarDumper::dump($var, $depth, $highlight);
    }

    /**
     * @param string $phone +380XXXXXXX
     * @return string
     */
    public static function phone_format($phone)
    {
        if ($phone) {
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

            try {
                $phoneNumber = $phoneUtil->parse($phone, 'UA');

                $phone2 = $phoneUtil->format($phoneNumber, \libphonenumber\PhoneNumberFormat::NATIONAL);
                if ($phoneUtil->getRegionCodeForNumber($phoneNumber) == 'UA') {
                    $pattern = "/^(\+?\d{2})(\d{3})(\d{3})(\d{2})(\d{2})$/";
                    $phone = preg_replace($pattern, '($2) $3-$4-$5', $phone);
                } elseif ($phoneUtil->getRegionCodeForNumber($phoneNumber) == 'RU') {
                    $pattern = "/^(\+?\d{1})(\d{3})(\d{3})(\d{2})(\d{2})$/";
                    $phone = preg_replace($pattern, '$1 ($2) $3-$4-$5', $phone);
                } else {
                    $phone = $phone2;
                }
            } catch (\libphonenumber\NumberParseException $exception) {

            }
        }
        return $phone;
    }

    public static function isMobile()
    {
        return (preg_match('!(tablet|pad|mobile|phone|symbian|android|ipod|ios|blackberry|webos)!i', Yii::$app->request->getUserAgent())) ? true : false;
    }

    /**
     * Check string of json
     *
     * @param $string
     * @return bool
     */
    public static function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function vote_graphic($votes, $total)
    {

        // Yii::app()->clientScript->registerCssFile($this->getAssetsUrl() . '/css/rating.css');
        $votes = (intval($votes)) ? $votes : 1;
        $width = number_format($total / $votes, 2) * 17;
        $result = substr($total / $votes, 0, 4);
        $title = (intval($votes) && intval($total)) ? Yii::t('app/default', 'RATING_HIT', ['votes' => $votes, 'result' => $result]) : Yii::t('app/default', 'RATING_HIT', ['votes' => 0, 'result' => 0]);
        $content = "<ul class=\"urating\" title=\"" . $title . "\"><li class=\"crating\" style=\"width: " . $width . "px;\"></li></ul>";
        return $content;
    }

    /**
     * @param string $number +380XXXXXXX
     * @return string
     */
    public static function phoneOperator($number)
    {

        if (preg_match('/([\+\380](39|67|68|96|97|98))/i', $number, $match)) {
            return 'Киевстар';
        } elseif (preg_match('/([\+\380](50|66|95|99))/i', $number, $match)) {
            return 'Vodafone (МТС)';

            //} elseif (preg_match('/([\+\79](01|02|04|08|10|11|12|13|14|15|16|17|18|19|50|78|80|81|82|83|84|85|86|87|88|89))/i', $number, $match)) {
            //    return 'МТС russian';

            //} elseif (preg_match('/([\+\79](01|02))/i', $number, $match)) {
            //     return 'МегаФона';

        } elseif (preg_match('/([\+\380](63|93|73))/i', $number, $match)) {
            return 'lifecell';
        } elseif (preg_match('/([\+\380](91))/i', $number, $match)) {
            return 'Utel Украина';
        } elseif (preg_match('/([\+\380](92))/i', $number, $match)) {
            return 'EOPLEnet Украина';
        } elseif (preg_match('/([\+\380](94))/i', $number, $match)) {
            return 'Intertelecom Ukraine';
        } else {
            return 'Unknown';
        }
    }

    public static function processImage($size = false, $filename = 'file.jpg', $uploadAlias = '@uploads', $options = [])
    {
        $dirName = basename($uploadAlias);
        $thumbPath = Yii::getAlias("@app/web/assets/{$dirName}");
        if ($size) {
            $thumbPath .= DIRECTORY_SEPARATOR . $size;
        }


        if (!file_exists($thumbPath)) {
            mkdir($thumbPath, 0775, true);
        }
        // Path to source image
        $fullPath = Yii::getAlias($uploadAlias) . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($fullPath)) {
            // return CMS::placeholderUrl(array('size' => $size));
        }
        // Path to thumb
        $thumbPath = $thumbPath . DIRECTORY_SEPARATOR . $filename;
        $sizes = explode('x', $size);


        if (!is_file($fullPath)) {
            return false;
        }

        $configApp = Yii::$app->settings->get('app');

        //Уделение картинок с папки assets при разработке.
        //if (YII_DEBUG && file_exists($thumbPath)) {
        //die($thumbPath);
        //    unlink($thumbPath);
        //}
        $error = false;
        if (!file_exists($fullPath) && !file_exists($thumbPath)) {
            $fullPath = Yii::getAlias('@uploads') . DIRECTORY_SEPARATOR . 'no-image.jpg';
            $options['watermark'] = false;
            $error = true;
        }
        $hash = time();

        if (!file_exists($thumbPath) && file_exists($fullPath)) {
            $fileInfo = pathinfo($fullPath);
            if(!in_array($fileInfo['extension'], ['png','svg'])){
                $exif = @exif_read_data(FileHelper::normalizePath($fullPath), 0, true);
                if (isset($exif['FILE']['FileDateTime'])) {
                    $hash = $exif['FILE']['FileDateTime'];
                }
            }
            if (!in_array($fileInfo['extension'], ['svg'])) {
                $img = Yii::$app->img;
                $img->load($fullPath);
                if ($error) {
                    $img->grayscale();
                    $img->text(Yii::t('app/default', 'FILE_NOT_FOUND'), Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . '/Exo2-Light.ttf', $img->getWidth() / 100 * 8, [114, 114, 114], $img::POS_CENTER_BOTTOM, 0, $img->getHeight() / 100 * 10, 0, 0);
                }
                if (isset($options['watermark']) && $options['watermark']) {
                    $offsetX = isset($configApp->attachment_wm_offsetx) ? $configApp->attachment_wm_offsetx : 10;
                    $offsetY = isset($configApp->attachment_wm_offsety) ? $configApp->attachment_wm_offsety : 10;
                    $corner = isset($configApp->attachment_wm_corner) ? $configApp->attachment_wm_corner : 4;
                    $path = !empty($configApp->attachment_wm_path) ? $configApp->attachment_wm_path : Yii::getAlias('@uploads') . '/watermark.png';
                    $img->watermark($path, $offsetX, $offsetY, $corner, false);
                }

                if ($size) {
                    $img->resize((!empty($sizes[0])) ? $sizes[0] : 0, (!empty($sizes[1])) ? $sizes[1] : 0);
                }

                $img->save($thumbPath);
            } else {
                if (!file_exists($thumbPath) && !is_file($thumbPath)) {
                    copy($fullPath, $thumbPath);
                }
                $size = false;
            }
            //  $img->show();
        }


        if (!$size) {
            return "/assets/{$dirName}/" . $filename . "?r=" . $hash;
        } else {
            return "/assets/{$dirName}/{$size}/" . $filename . "?r=" . $hash;
        }

    }

    public static function phoneFormat($phone)
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    /**
     * Прячит посление цыфтры телефона
     * Напрммер +XXXXXXXXXXXX
     * Резулитат +XXXXXXXX****
     *
     * @param string $string Строка
     * @param int $end_length Количество чисел обрезаения.
     * @return string
     */
    public static function hideString($string, $end_length = 4)
    {
        return substr($string, 0, -$end_length) . "****";
    }

    /**
     * @param string $text
     * @param string $replacement
     * @param bool $lowercase whether to return the string in lowercase or not. Defaults to `true`.
     * @return string
     */
    public static function slug($text, $replacement = '-', $lowercase = true)
    {
        return (extension_loaded('intl')) ? Inflector::slug($text, $replacement, $lowercase) : $text;
    }

    /**
     * Прячит название почты
     * Например dev@pixelion.com.ua
     * Резулитат ***@pixelion.com.ua
     *
     * @param string $email Почта
     * @return string
     */
    public static function hideEmail($email)
    {
        $mail_part = explode("@", $email);
        $mail_part[0] = str_repeat("*", strlen($mail_part[0]));
        return implode("@", $mail_part);
    }

    /**
     * Прячит первые и последние значение строки
     * Напрммер pixelion
     * Резулитат *ixelio*
     *
     * @param $str
     * @param int $start_length
     * @param int $end_length
     * @return string
     */
    public static function getStarred($str, $start_length = 1, $end_length = 1)
    {
        $str_length = strlen($str);
        return substr($str, 0, $start_length) . str_repeat('*', $str_length - $start_length) . substr($str, $str_length - $end_length, $end_length);
    }


    /**
     * Установление прав доступа.
     *
     * @param string $path
     * @param int $chm Example 0640
     */
    public static function setChmod($path, $chm)
    {
        if (!self::isChmod($path, $chm)) {
            @chmod($path, $chm);
        }
    }

    public static function placeholderUrl($params = [])
    {
        $url = ['/site/placeholder'];
        if (!isset($params['text'])) {
            $params['text'] = 'f138';
        }
        return Url::to(array_merge($url, $params));
    }

    /**
     * Replaces variables in a string
     *
     * @param string $text
     * @param array $array Array of Variables and Values (['{var}'=>'value'])
     * @param bool $flip Replaces the value in the string with variables (Default is false)
     * @return mixed
     */
    public static function textReplace($text, $array = [], $flip = false)
    {
        $config = Yii::$app->settings->get('app');
        $tmpArray = array();
        $tmpArray['{sitename}'] = $config->sitename;
        $tmpArray['{domain}'] = Yii::$app->request->serverName;
        //$tmpArray['{host}'] = Yii::$app->request->hostInfo;
        $tmpArray['{scheme}'] = Yii::$app->request->isSecureConnection ? 'https://' : 'http://';
        $tmpArray['{email}'] = $config->email;
        $resultArray = ArrayHelper::merge($tmpArray, $array);
        if ($flip)
            $resultArray = array_flip($resultArray);
        foreach ($resultArray as $from => $to) {
            $text = str_replace($from, $to, $text);
        }
        return $text;
    }

    /**
     * Change color hex to rgb
     *
     * @param $color Color hex
     * @return array|bool
     * @throws \yii\base\Exception
     */
    public static function hex2rgb($color)
    {
        if (strpos($color, '#') !== false)
            throw new \yii\base\Exception('Цвет должен быть указал без знака "#"', 500);

        $color = preg_replace("/[^abcdef0-9]/i", "", $color);
        if (strlen($color) == 6) {
            list($r, $g, $b) = str_split($color, 2);
            return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
            return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
        }
        return false;
    }

    /**
     * Проверка наличие нужных прав.
     *
     * @param string $path Абсолютный путь.
     * @param int $chm Номер прав. 0640
     * @return boolean
     */
    public static function isChmod($path, $chm)
    {
        if (file_exists($path) && intval($chm)) {
            $pdir = decoct(fileperms($path));
            $per = substr($pdir, -3);
            if ($per != $chm) {
                return false;
            } else {
                return true;
            }
        }
    }

    public static function tableName()
    {
        // return table name with DB name to get relations from different DBs to work
        $name = preg_match("/dbname=([^;]*)/", Yii::$app->db->dsn, $matches);
        return $matches[1];
    }


    public static function getMemoryLimit()
    {
        $memory_limit = ini_get('memory_limit');
        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if ($matches[2] == 'M') {
                $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } else if ($matches[2] == 'K') {
                $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }
        return $matches[1];
    }

    /**
     *
     * @param string $birth_date
     * @return string
     */
    public static function age($birth_date)
    {
        $birth_time = strtotime($birth_date);
        $birth = getdate($birth_time);
        $now = getdate();
        $age = $now['year'] - $birth['year'];
        if ($now['mon'] < $birth['mon'])
            $age--;

        if ($now['mon'] === $birth['mon'])
            if ($now['mday'] < $birth['mday'])
                $age--;

        return $age;
    }

    /**
     * Определение возраста
     *
     * @param int $age
     * @return string
     */
    public static function years($age)
    {
        $age = abs($age);
        $t1 = $age % 10;
        $t2 = $age % 100;
        $a_str = "";
        if ($t1 == 1)
            $a_str = Yii::t('app/default', 'YEARS', 1);
        else if (($t1 >= 2) && ($t1 <= 4))
            $a_str = Yii::t('app/default', 'YEARS', 2);
        if (($t1 >= 5) && ($t1 <= 9) || ($t1 == 0) || ($t2 >= 11) && ($t2 <= 19))
            $a_str = Yii::t('app/default', 'YEARS', 0);
        return $a_str;
    }

    public static function seconds2times($seconds)
    {
        $times = array();

        // считать нули в значениях
        $count_zero = false;

        // количество секунд в году не учитывает високосный год
        // поэтому функция считает что в году 365 дней
        // секунд в минуте|часе|сутках|году
        $periods = array(60, 3600, 86400, 31536000);

        for ($i = 3; $i >= 0; $i--) {
            $period = floor($seconds / $periods[$i]);
            if (($period > 0) || ($period == 0 && $count_zero)) {
                $times[$i + 1] = $period;
                $seconds -= $period * $periods[$i];

                $count_zero = true;
            }
        }

        $times[0] = $seconds;
        return $times;
    }

    public static function getDayHourMinut($sec)
    {

        $min = floor($sec / 60);
        $hours = floor($sec / 3600);
        echo $hours;
        $seconds = $sec % 60;
        $minutes = $min % 60;

        return ' hours=' . $hours . ' minutes=' . $minutes . ' seconds=' . $seconds;
    }

    /**
     * Display Time filter
     * @param integer $sec
     * @return string
     */
    public static function display_time($sec)
    {
        $min = floor($sec / 60);
        $hours = floor($min / 60);
        $seconds = $sec % 60;
        $minutes = $min % 60;
        $content = ($hours == 0) ? (($min == 0) ? $seconds . " " . Yii::t('app/default', 'SEC') . "." : $min . " " . Yii::t('app/default', 'MIN') . ". " . $seconds . " " . Yii::t('app/default', 'SEC') . ".") : $hours . " " . Yii::t('app/default', 'HOUR') . ". " . $minutes . " " . Yii::t('app/default', 'MIN') . ". " . $seconds . " " . Yii::t('app/default', 'SEC') . ".";
        return $content;
    }

    /**
     * Прошло времени
     *
     * @param integer $time
     * @return string
     */
    public static function time_passed($time)
    {
        $date = new \DateTime('now', new \DateTimeZone(CMS::timezone()));
        $now = strtotime($date->format('Y-m-d H:i:s'));
        return self::display_time($now - $time);
    }

    /**
     * водит ссылку без языка - удаляя /en, /ru etc
     *
     * @return string
     */
    public static function currentUrl()
    {
        $request = Yii::$app->request;
        $parts = explode('/', $request->url);
        $lang = Yii::$app->languageManager;
        $pathInfo = $request->url;
        if ($lang->default->code != $lang->active->code) {
            if (in_array($parts[1], $lang->getCodes())) {
                unset($parts[1]);
                $pathInfo = implode('/', $parts);

                if (empty($pathInfo)) {
                    $pathInfo = '/';
                }
            }
        }

        return $pathInfo;
    }

    /**
     * @param $str
     * @return mixed|string
     */
    public static function decodeHtmlEnt($str)
    {
        $ret = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        $p2 = -1;
        for (; ;) {
            $p = strpos($ret, '&#', $p2 + 1);
            if ($p === FALSE)
                break;
            $p2 = strpos($ret, ';', $p);
            if ($p2 === FALSE)
                break;

            if (substr($ret, $p + 2, 1) == 'x')
                $char = hexdec(substr($ret, $p + 3, $p2 - $p - 3));
            else
                $char = intval(substr($ret, $p + 2, $p2 - $p - 2));

            $newchar = iconv(
                'UCS-4', 'UTF-8', chr(($char >> 24) & 0xFF) . chr(($char >> 16) & 0xFF) . chr(($char >> 8) & 0xFF) . chr($char & 0xFF)
            );
            $ret = substr_replace($ret, $newchar, $p, 1 + $p2 - $p);
            $p2 = $p + strlen($newchar);
        }
        return $ret;
    }

    /**
     *
     * @param string $dir Absolute path
     *
     * @return array
     * size - Размер
     * howmany - Количество файлов
     */
    public static function dir_size($dir)
    {
        if (file_exists($dir)) {
            if (is_file($dir))
                return array('size' => filesize($dir), 'howmany' => 0);
            if ($dh = opendir($dir)) {
                $size = 0;
                $n = 0;
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..')
                        continue;
                    $n++;
                    $data = self::dir_size($dir . '/' . $file);
                    $size += $data['size'];
                    $n += $data['howmany'];
                }
                closedir($dh);
                return array('size' => $size, 'howmany' => $n);
            }
        }
        return array('size' => 0, 'howmany' => 0);
    }

    /**
     *
     * @param int $bytes
     * @return string
     */
    public static function fileSize($bytes)
    {
        $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        if ($bytes == 0) return '0 ' . $unit[0];
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . ' ' . (isset($unit[$i]) ? $unit[$i] : 'B');
    }

    public static function isBot()
    {
        $bots = [
            'rambler' => 'Rambler',
            'googlebot' => 'Google Bot',
            'aport' => 'aport',
            'yahoo' => 'Yahoo',
            'msnbot' => 'MSN Bot',
            'turtle' => 'Turtle',
            'mail.ru' => 'Mail.ru',
            'omsktele' => 'omsktele',
            'yetibot' => 'yetibot',
            'picsearch' => 'picsearch',
            'sape.bot' => 'sape',
            'sape_context' => 'sape_context',
            'gigabot' => 'gigabot',
            'snapbot' => 'snapbot',
            'alexa.com' => 'alexa.com',
            'megadownload.net' => 'megadownload.net',
            'askpeter.info' => 'askpeter.info',
            'igde.ru' => 'igde.ru',
            'ask.com' => 'ask.com',
            'qwartabot' => 'qwartabot',
            'yanga.co.uk' => 'yanga.co.uk',
            'scoutjet' => 'scoutjet',
            'similarpages' => 'similarpages',
            'oozbot' => 'oozbot',
            'shrinktheweb.com' => 'shrinktheweb.com',
            'aboutusbot' => 'aboutusbot',
            'followsite.com' => 'followsite.com',
            'dataparksearch' => 'dataparksearch',
            'google-sitemaps' => 'google-sitemaps',
            'appEngine-google' => 'appEngine-google',
            'feedfetcher-google' => 'feedfetcher-google',
            'liveinternet.ru' => 'Live Internet',
            'xml-sitemaps.com' => 'xml-sitemaps.com',
            'agama' => 'agama',
            'metadatalabs.com' => 'metadatalabs.com',
            'h1.hrn.ru' => 'h1.hrn.ru',
            'googlealert.com' => 'googlealert.com',
            'seo-rus.com' => 'seo-rus.com',
            'yaDirectBot' => 'yaDirectBot',
            'yandeG' => 'yandeG',
            'yandex' => 'Yandex',
            'yandexSomething' => 'yandexSomething',
            'Copyscape.com' => 'Copyscape.com',
            'AdsBot-Google' => 'AdsBot-Google',
            'domaintools.com' => 'domaintools.com',
            'Nigma.ru' => 'Nigma.ru',
            'bing.com' => 'bing.com',
            'dotnetdotcom' => 'dotnetdotcom'
        ];
        $result = [];
        foreach ($bots as $key => $bot) {
            if (stripos(Yii::$app->request->userAgent, $key) !== false) {
                $result['success'] = true;
                $result['name'] = $bot;
                break;
            } else {
                $result['success'] = false;
            }
        }
        return $result;
    }

    /**
     * @param $url
     * @param string $str
     * @return mixed
     */
    public static function domain($url, $str = "")
    {
        $massiv = explode(",", $url);
        $str = intval($str);
        foreach ($massiv as $val)
            $dom[] = "<a href=\"$val\" target=\"_blank\">" . (($str) ? mb_substr(preg_replace("/http\:\/\/|www./", "", $val), 0, $str, 'UTF-8') : preg_replace("/http\:\/\/|www./", "", $val)) . "</a>";
        return preg_replace("/http\:\/\/|https\:\/\/|www./", "", $url);
    }

    /**
     * Get IP
     * @return string
     */
    public static function getIp()
    {
        $strRemoteIP = $_SERVER['REMOTE_ADDR'];
        if (!$strRemoteIP) {
            $strRemoteIP = urldecode(getenv('HTTP_CLIENTIP'));
        }
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $strIP = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $strIP = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $strIP = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $strIP = getenv('HTTP_FORWARDED');
        } else {
            $strIP = $_SERVER['REMOTE_ADDR'];
        }

        if ($strRemoteIP != $strIP) {
            $strIP = $strRemoteIP . ", " . $strIP;
        }
        return $strIP;
    }

    /**
     * @param \panix\mod\user\models\User $user
     * @return string
     */
    public static function userLink(\panix\mod\user\models\User $user)
    {
        $html = Html::a($user->login . ' <b class="caret caret-up"></b>', '#', array('class' => 'btn btn-link dropdown-toggle', 'data-toggle' => "dropdown", 'aria-haspopup' => "true", 'aria-expanded' => "false"));
        return '<div style="position:relative;" class="btn-group">' . $html . '
            <ul class="dropdown-menu drop-up">
            <li><a href="' . Yii::$app->createUrl('/users/profile/view', array('user_id' => $user->id)) . '"><i class="icon-user"></i> ' . Yii::t('app/default', 'PROFILE') . '</a></li>
            <li><a href="' . $user->getUpdateUrl() . '" target="_blank"><i class="icon-edit"></i> ' . Yii::t('app/default', 'UPDATE', 1) . '</a></li>
            </ul>
            </div>';
    }

    /**
     * @param string $ip
     * @param boolean $geo
     * @return string|null
     */
    public static function ip($ip = null, $geo = true)
    {
        if (!$ip)
            $ip = Yii::$app->request->getUserIP();


        $options = [];
        $content = null;
        if ($geo && !in_array($ip, ['127.0.0.1'])) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                if (self::getMemoryLimit() > self::MEMORY_LIMIT) {
                    $geoIp = Yii::$app->geoip->ip($ip);
                    $title = Yii::t('app/default', 'COUNTRY') . ': ' . Yii::t('app/geoip_country', $geoIp->country) . '/' . Yii::t('app/geoip_city', $geoIp->city) . ' - ' . $geoIp->timezone;
                    $options['title'] = $title;

                    if ($geoIp->countryCode) {
                        $image = Html::img('/uploads/language/' . strtolower($geoIp->countryCode) . '.png', $options) . ' ';
                        $options['alt'] = $ip;
                        $options['class'] = 'geo';
                        $options['data-ip'] = $ip;
                        $options['data-type'] = "ajax";
                        $options['data-dragToClose'] = "false";
                        //$options['data-fancybox'] = 'data-fancybox';
                        $options['data-width'] = '300';
                        $options['data-src'] = Url::to(['/admin/app/ajax/geo', 'ip' => $ip]);
                        //$options['onClick'] = 'common.geoip("' . $ip . '")';

                        //  data-fancybox  ="https://codepen.io/fancyapps/pen/oBgoqB.html" href="javascript:;"

                        return $image . Html::a($ip, 'javascript:;', $options);
                    }
                }
            } else {
                return $ip . ' (err)';
            }
        }
        return $ip;
    }

    /**
     * @param integer|null $timestamp
     * @param bool $time Show time
     * @param string $timeZone Europe/Kiev
     * @return string
     */
    public static function date(int $timestamp = null, $time = true, $timeZone = null)
    {
        if (!$timestamp) {
            $timestamp = time();
        }

        if (extension_loaded('intl')) {
            $fn = ($time) ? 'asDatetime' : 'asDate';
            if ($timeZone) {
                Yii::$app->formatter->timeZone = $timeZone;
            }
            return Yii::$app->formatter->{$fn}($timestamp);

        } else {
            if ($time) {
                $timestamp = date('Y-m-d H:i:s', $timestamp);
            } else {
                $timestamp = date('Y-m-d', $timestamp);
            }
            $fn = ($time) ? 'datetimeFormat' : 'dateFormat';
            if (!$timeZone) {
                $timeZone = self::timezone();
            }
            $date = new \DateTime($timestamp, new \DateTimeZone($timeZone));
            return $date->format(str_replace('php:', '', Yii::$app->formatter->{$fn}));
        }

    }

    /**
     *
     * @return string Timezone
     */
    public static function timezone()
    {
        $user = Yii::$app->user;
        $config = Yii::$app->settings->get('app');

        if (!$user->isGuest) {
            if ($user->timezone) {
                $timezone = $user->timezone;
            } elseif (isset(Yii::$app->session['timezone'])) {
                $timezone = Yii::$app->session['timezone'];
            } else {
                $timezone = $config->timezone;
            }
        } else {
            if (isset(Yii::$app->session['timezone'])) {
                $timezone = Yii::$app->session['timezone'];
            } else {
                $timezone = $config->timezone;
            }
        }
        return $timezone;
    }

    public static function replace_urls($text = null)
    {
        $regex = '/((http|ftp|https):\/\/)?[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/';
        return preg_replace_callback($regex, function ($m) {
            $link = $name = $m[0];
            if (empty($m[1])) {
                $link = "http://" . $link;
            }
            return '<a href="' . $link . '" target="_blank" rel="nofollow">' . $name . '</a>';
        }, $text);
    }

    public static function getYouTubeImg($url, $thumb = 'default')
    {
        if (!in_array($thumb, [1, 2, 3, 'default', 'maxresdefault', 'hqdefault', 'mqdefault'])) {
            throw new Exception('error video thumb');
        }
        $id = self::parse_yturl($url);
        return "http://i3.ytimg.com/vi/{$id}/{$thumb}.jpg";
    }

    /**
     * Youtube parse url
     *
     * @param string $url
     * @return string
     */
    public static function parse_yturl($url)
    {
        $pattern = '#^(?:https?://)?';    # Optional URL scheme. Either http or https.
        $pattern .= '(?:www\.)?';         #  Optional www subdomain.
        $pattern .= '(?:';                #  Group host alternatives:
        $pattern .= 'youtu\.be/';       #    Either youtu.be,
        $pattern .= '|youtube\.com';    #    or youtube.com
        $pattern .= '(?:';              #    Group path alternatives:
        $pattern .= '/embed/';        #      Either /embed/,
        $pattern .= '|/v/';           #      or /v/,
        $pattern .= '|/watch\?v=';    #      or /watch?v=,    
        $pattern .= '|/watch\?.+&v='; #      or /watch?other_param&v=
        $pattern .= ')';                #    End path alternatives.
        $pattern .= ')';                  #  End host alternatives.
        $pattern .= '([\w-]{11})';        # 11 characters (Length of Youtube video ids).
        $pattern .= '(?:.+)?$#x';         # Optional other ending URL parameters.
        preg_match($pattern, $url, $matches);
        return (isset($matches[1])) ? $matches[1] : false;
    }

    /**
     * Преобразование массива данных
     *
     * @param $arrayofValues
     * @param string $type
     * @return mixed
     */
    public static function recursiveValuesToType($arrayofValues, $type = 'integer')
    {
        if (is_array($arrayofValues))
            foreach ($arrayofValues as &$value)
                self::recursiveValuesToType($value, $type);
        else
            settype($arrayofValues, $type);
        return $arrayofValues;
    }

    /**
     * Замена перевода строк на <br />
     *
     * @param string $subject
     * @param string $to
     * @return mixed
     */
    public static function slashNto($subject, $to = '<br />')
    {
        $replaced = preg_replace("/\r\n|\r|\n/", $to, $subject);
        return $replaced;
    }


    /**
     * Генератор случайного кода (числа и букв)
     *
     * @param int $var
     * @return string
     */
    public static function gen($var)
    {
        $var = intval($var);
        $gen = "";
        for ($i = 0; $i < $var; $i++) {
            $te = mt_rand(48, 122);
            if (($te > 57 && $te < 65) || ($te > 90 && $te < 97))
                $te = $te - 9;
            $gen .= chr($te);
        }
        return $gen;
    }

    /**
     * @param int|float $n
     * @return bool|string
     */
    public static function counterUnit($n)
    {
        // first strip any formatting;
        $n = (0 + str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) return false;

        // now filter it;
        if ($n >= 1000000000000) return round(($n / 1000000000000), 1) . ' трлн.';
        else if ($n >= 1000000000) return round(($n / 1000000000), 1) . ' млрд.';
        else if ($n >= 1000000) return round(($n / 1000000), 1) . ' млн.';
        else if ($n >= 1000) return round(($n / 1000), 1) . ' тыс.';

        return number_format($n);
    }

    /**
     * @param string $string
     * @param boolean $int
     * @return string|integer
     */
    public static function hash($string, $int = false)
    {
        if ($int) {
            return crc32($string);
        } else {
            return sprintf('%x', crc32($string));

        }
    }

    /**
     * @param string $guid
     * @return bool
     */
    public static function isGuid($guid)
    {
        if (preg_match('/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/', $guid)) {
            return true;
        }
        return false;
    }

    public static function fakeImage($savePath, $size = '100x100', $text = false, $bg = 'ccc', $fg = '333')
    {
        $request = Yii::$app->request;
        // Dimensions
        //$getsize = ($request->get('size')) ? $request->get('size') : '100x100';
        $dimensions = explode('x', $size);

        if (empty($dimensions[0])) {
            $dimensions[0] = $dimensions[1];
        }
        if (empty($dimensions[1])) {
            $dimensions[1] = $dimensions[0];
        }

        // Create image
        $image = imagecreate($dimensions[0], $dimensions[1]);

        $bg = CMS::hex2rgb($bg);
        $opacityBg = ($bg) ? 0 : 127;
        //$setbg = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);
        $setbg = imagecolorallocatealpha($image, $bg['r'], $bg['g'], $bg['b'], $opacityBg);


        $fg = CMS::hex2rgb($fg);
        $setfg = imagecolorallocate($image, $fg['r'], $fg['g'], $fg['b']);

        $text = ($text) ? strip_tags($text) : $size;
        $text = str_replace('+', ' ', $text);
        $padding = 10;//($request->get('padding')) ? (int)$request->get('padding') : 0;


        $x = $dimensions[0] / 2;
        $percent = 60;
        $number_percent = $x / 100 * $percent;

        $fontsize = $x - $number_percent;


        if (strlen($text) == 4 && preg_match("/([A-Za-z]{1}[0-9]{3})$/i", $text)) {
            $text = '&#x' . $text . ';';
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Pixelion.ttf';
        } elseif ($text == 'PIXELION' || $text == 'pixelion') {
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Pixelion.ttf';
        } else {
            $font = Yii::getAlias('@vendor/panix/engine/assets/assets/fonts') . DIRECTORY_SEPARATOR . 'Exo2-Light.ttf';
        }

        $textBoundingBox = imagettfbbox($fontsize - $padding, 0, $font, $text);
        // decrease the default font size until it fits nicely within the image
        while (((($dimensions[0] - ($textBoundingBox[2] - $textBoundingBox[0])) < $padding) || (($dimensions[1] - ($textBoundingBox[1] - $textBoundingBox[7])) < $padding)) && ($fontsize - $padding > 1)) {
            $fontsize--;
            $textBoundingBox = imagettfbbox($fontsize - $padding, 0, $font, $text);
        }

        imagettftext($image, $fontsize - $padding, 0, ($dimensions[0] / 2) - (($textBoundingBox[2] - $textBoundingBox[0]) / 2), ($dimensions[1] / 2) - (($textBoundingBox[1] + $textBoundingBox[7]) / 2), $setfg, $font, $text);

        $filename = CMS::gen(10) . '.png';
        imagepng($image, $savePath . '/' . $filename);

        return $filename;
    }
}
