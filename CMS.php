<?php

namespace panix\engine;

use Yii;
use yii\helpers\Url;
/**
 * Дополнительные функции системы.
 * 
 * @author CORNER CMS development team <dev@corner-cms.com>
 * @version 1.0
 * @package app
 * @category app
 */
class CMS {

    const MEMORY_LIMIT = 64; // Minimal memory_limit

    public static function isModile() {
        return (preg_match('!(tablet|pad|mobile|phone|symbian|android|ipod|ios|blackberry|webos)!i', Yii::$app->request->getUserAgent())) ? true : false;
    }

    /**
     * Установление прав доступа.
     * 
     * @param string $path
     * @param int $chm Example 0640
     */
    public static function setChmod($path, $chm) {
        if (!self::isChmod($path, $chm)) {
            @chmod($path, $chm);
        }
    }

    public static function placeholderUrl($params = []) {
        $url=['/placeholder'];
        if (!isset($params['text'])) {
            $params['text'] = 'f138';
        }
        return Url::to(array_merge($url,$params));
    }

    public static function hex2rgb($colour) {
        $colour = preg_replace("/[^abcdef0-9]/i", "", $colour);
        if (strlen($colour) == 6) {
            list($r, $g, $b) = str_split($colour, 2);
            return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
        } elseif (strlen($colour) == 3) {
            list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
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
    public static function isChmod($path, $chm) {
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


    public static function tableName() {
        // return table name with DB name to get relations from different DBs to work
        $name = preg_match("/dbname=([^;]*)/", Yii::$app->db->dsn, $matches);
        return $matches[1];
    }

    public static function textReplace($text, $array) {
        $config = Yii::$app->settings->get('app');
        $tmpArray = array();
        $tmpArray['{sitename}'] = $config['sitename'];
        $tmpArray['{host}'] = Yii::$app->request->serverName;
        $tmpArray['{admin_email}'] = $config['email'];
        $resultArray = \yii\helpers\ArrayHelper::merge($tmpArray, $array);
        foreach ($resultArray as $from => $to) {
            $text = str_replace($from, $to, $text);
        }
        return $text;
    }



    public static function getMemoryLimit() {
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

    public static function truncate($text, $strip, $type = 1) {
        if ($type == 1) {
            $text = (mb_strlen($text, Yii::$app->charset) > $strip) ? mb_substr($text, 0, $strip, Yii::$app->charset) . "..." : $text;
        } else {
            $text = mb_substr($text, 0, $strip, Yii::$app->charset);
        }
        return $text;
    }

    /**
     * 
     * @param type $birth_date
     * @return type
     */
    public static function age($birth_date) {
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
    public static function years($age) {
        $age = abs($age);
        $t1 = $age % 10;
        $t2 = $age % 100;
        $a_str = "";
        if ($t1 == 1)
            $a_str = Yii::t('app', 'YEARS', 1);
        else if (($t1 >= 2) && ($t1 <= 4))
            $a_str = Yii::t('app', 'YEARS', 2);
        if (($t1 >= 5) && ($t1 <= 9) || ($t1 == 0) || ($t2 >= 11) && ($t2 <= 19))
            $a_str = Yii::t('app', 'YEARS', 0);
        return $a_str;
    }

    /**
     * Display Time filter
     * @param type $sec
     * @return type
     */
    public static function display_time($sec) {
        $min = floor($sec / 60);
        $hours = floor($min / 60);
        $seconds = $sec % 60;
        $minutes = $min % 60;
        $content = ($hours == 0) ? (($min == 0) ? $seconds . " " . Yii::t('app', 'SEC') . "." : $min . " " . Yii::t('app', 'MIN') . ". " . $seconds . " " . Yii::t('app', 'SEC') . ".") : $hours . " " . Yii::t('app', 'HOUR') . ". " . $minutes . " " . Yii::t('app', 'MIN') . ". " . $seconds . " " . Yii::t('app', 'SEC') . ".";
        return $content;
    }

    /**
     * Осталось
     * @param int $time
     * @return string
     */
    public static function timeLeft($time) {
        $t = intval($time - self::time());
        return Yii::t('app', 'PURCHASED') . ": " . self::display_time($t);
    }

    /**
     * 
     * @param type $gender
     * @return type
     */
    public static function gender($gender) {
        return Yii::t('app', 'GENDER', $gender);
    }



    /**
     * 
     * @return type
     * @todo водит ссылку без языка - удаляя /en, /ru etc
     */
    public static function currentUrl() {
        $request = Yii::$app->request;
        $parts = explode('/', $request->url);
        $lang = Yii::$app->languageManager;
        if ($lang->default->code == $lang->active->code) {
            $pathInfo = $request->url;
        } else {
            if (in_array($parts[1], $lang->getCodes())) {
                unset($parts[1]);
                $pathInfo = implode($parts, '/');

                if (empty($pathInfo)) {
                    $pathInfo = '/';
                }
            }
        }
        return $pathInfo;
    }

    /**
     * 
     * @param type $str
     * @return type
     */
    public static function decodeHtmlEnt($str) {
        $ret = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        $p2 = -1;
        for (;;) {
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
    public static function dir_size($dir) {
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
     * @param int $size
     * @return type
     */
    public static function files_size($size) {
        $name = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
        $mysize = $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . " " . $name[$i] : $size . " Bytes";
        return $mysize;
    }

    /**
     * Get user agent
     * @return string
     */
    public static function getagent() {
        if (getenv("HTTP_USER_AGENT") && strcasecmp(getenv("HTTP_USER_AGENT"), "unknown")) {
            $agent = getenv("HTTP_USER_AGENT");
        } elseif (!empty($_SERVER['HTTP_USER_AGENT']) && strcasecmp($_SERVER['HTTP_USER_AGENT'], "unknown")) {
            $agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $agent = "unknown";
        }
        return $agent;
    }

    public static function isBot() {
        $bots = array(
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
        );
        $result = array();
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

    public static function domain($url, $str = "") {
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
    public static function getip() {
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
     * 
     * @return string
     * @todo Get referer
     */
    public static function get_referer() {
        $referer = getenv("HTTP_REFERER");
        if (!empty($referer) && $referer != "" && !preg_match("/^unknown/i", $referer) && !preg_match("/^bookmark/i", $referer) && !strpos($referer, $_SERVER["HTTP_HOST"])) {
            $refer = $referer;
        } else {
            $refer = "";
        }
        return $refer;
    }

    /**
     * 
     * @param User $user
     * @return type
     */
    public static function userLink(User $user) {
        $html = Html::link($user->login . ' <b class="caret caret-up"></b>', '#', array('class' => 'btn btn-link dropdown-toggle', 'data-toggle' => "dropdown", 'aria-haspopup' => "true", 'aria-expanded' => "false"));
        return '<div style="position:relative;" class="btn-group">' . $html . '
            <ul class="dropdown-menu drop-up">
            <li><a href="' . Yii::$app->createUrl('/users/profile/view', array('user_id' => $user->id)) . '"><i class="icon-user"></i> ' . Yii::t('app', 'PROFILE') . '</a></li>
            <li><a href="' . $user->getUpdateUrl() . '" target="_blank"><i class="icon-edit"></i> ' . Yii::t('app', 'UPDATE', 1) . '</a></li>
            </ul>
            </div>';
    }

    /**
     * 
     * @param string $ip
     * @param int $type
     * @param type $user
     * @return string or null
     */
    public static function ip($ip, $type = 1, $user = null) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if (Yii::$app->hasComponent('geoip') && self::getMemoryLimit() > self::MEMORY_LIMIT) {
                $geoip = Yii::$app->geoip;
                $code = $geoip->lookupCountryCode($ip);
                $name = $geoip->lookupCountryName($ip);
                if (isset($code)) {
                    if (!empty($code)) {
                        $image = Html::image('/uploads/language/' . strtolower($code) . '.png', $ip, array('title' => Yii::t('app', 'COUNTRY') . ': ' . Yii::t('geoip_country', $name)));
                        if ($type == 1) {
                            $content = Html::link($image . ' ' . $ip, 'javascript:void(0)', array('onClick' => 'common.geoip("' . $ip . '")', 'title' => Yii::t('app', 'COUNTRY') . ': ' . Yii::t('geoip_country', $name)));
                        } elseif ($type == 2 && $user) {
                            $content = Html::link($image . ' ' . $user, 'javascript:void(0)', array('onClick' => 'common.geoip("' . $ip . '")', 'title' => Yii::t('app', 'COUNTRY') . ': ' . Yii::t('geoip_country', $name)));
                        } elseif ($type == 3) {
                            $content = $image . ' ' . $ip;
                        } else {
                            $content = $image;
                        }
                    } else {
                        //return null;
                        $content = $ip;
                    }
                } else {
                    $content = $ip;
                }
                return $content;
            } else {
                return $ip;
            }
        } else {
            return $ip . ' (IPv6)';
        }
    }

    /**
     * 
     * @param type $mail
     * @return type
     */
    static function _______emailLink($mail) {
        if (Yii::$app->hasModule('delivery')) {
            return Html::link($mail, Yii::$app->createAbsoluteUrl('/admin/delivery/send', array('mail' => $mail)), array('onClick' => 'sendEmail("' . $mail . '")'));
        } else {
            return $mail;
        }
    }

    /**
     * 
     * @param string $category файл переводов
     * @param string $message параметр перевода
     * @param int $number число
     * @example CMS::GetFormatWord('app','ENTRY',$num);
     * @example 'ENTRY'=>'0#елемент|1#елемента|2#елементов';
     * @return string message
     */
    public static function GetFormatWord($category, $message, $number) {
        $num = $number % 10;
        if ($num == 1)
            return Yii::t($category, $message, 0);
        elseif ($num > 1 && $num < 5)
            return Yii::t($category, $message, 1);
        else
            return Yii::t($category, $message, 2);
    }

    /**
     * 
     * @param datetime $date Y-m-d H:i:s
     * @param boolean $time Показывать время true|false
     * @param boolean $static Статичная дата. true|false
     * @return string
     */
    public static function date($date, $time = true) {

        $formatted = strtotime($date);
        $oneDay = 86400;
        $df = Yii::$app->formatter;
        //$df->timeZone = 'Europe/Moscow';

        $resDate = $df->asDate($formatted);
        if ($formatted > mktime(0, 0, 0)) {
            $t = $formatted - ($oneDay * 1);
            if ($t >= time()) {
                $result = $resDate;
            } else {
                $result = $df->timeZone.Yii::t('app', 'TODAY_IN', array('time' => $df->asTime($formatted,'php:H:s')));
            }
        } elseif ($formatted > mktime(0, 0, 0) - $oneDay) {
            $result = Yii::t('app', 'YESTERDAY_IN', array('time' => $df->asTime($formatted,'php:H:s')));
        } else {
            if ($time) {
                $result = $resDate . ' ' . Yii::t('app', 'IN') . ' ' . $df->asTime($formatted,'php:H:s');
            } else {
                $result = $resDate;
            }
        }



        return str_replace(array_keys(self::getMonthsLocale(5)), array_values(self::getMonthsLocale(5)), $result);
    }

    public static function getMonthsLocale($type = 0) {
        return [
            "January" => Yii::t('app/month', 'January', ['n' => $type]),
            "February" => Yii::t('app/month', 'February', ['n' => $type]),
            "March" => Yii::t('app/month', 'March', ['n' => $type]),
            "April" => Yii::t('app/month', 'April', ['n' => $type]),
            "May" => Yii::t('app/month', 'May', ['n' => $type]),
            "June" => Yii::t('app/month', 'June', ['n' => $type]),
            "July" => Yii::t('app/month', 'July', ['n' => $type]),
            "August" => Yii::t('app/month', 'August', ['n' => $type]),
            "September" => Yii::t('app/month', 'September', ['n' => $type]),
            "October" => Yii::t('app/month', 'October', ['n' => $type]),
            "November" => Yii::t('app/month', 'November', ['n' => $type]),
            "December" => Yii::t('app/month', 'December', ['n' => $type])
        ];
    }

    /**
     * Текушая дата с часовым поясом.
     * 
     * @param string $format Default is "Y-m-d H:i:s"
     * @return string
     */
    public static function getDate($format = 'Y-m-d H:i:s') {
        try {
            $date = new DateTime('now');
            $date->setTimezone(new DateTimeZone(self::timezone()));
            return $date->format($format);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function time($format = 'Y-m-d H:i:s') {
        try {
            $date = new DateTime('now');
            $date->setTimezone(new DateTimeZone(self::timezone()));
            return strtotime($date->format($format));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * 
     * @return string Timezone 
     */
    public static function timezone() {
        $user = Yii::$app->user;
        $config = Yii::$app->settings->get('app');

        if (!$user->isGuest) {
            if ($user->timezone) {
                $timezone = $user->timezone;
            } elseif (isset(Yii::$app->session['timezone'])) {
                $timezone = Yii::$app->session['timezone'];
            } else {
                $timezone = $config['default_timezone'];
            }
        } else {
            if (isset(Yii::$app->session['timezone'])) {
                $timezone = Yii::$app->session['timezone'];
            } else {
                $timezone = $config['default_timezone'];
            }
        }
        return $timezone; //$timezone;
    }

    /**
     * Youtube parse url
     * 
     * @param string $url
     * @return string
     */
    public static function parse_yturl($url) {
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
     * @param type $arrayofValues
     * @param type $type
     * @return type
     */
    public static function recursiveValuesToType($arrayofValues, $type = 'integer') {
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
     * @param type $subject
     * @return type
     */
    public static function slashNtoBR($subject) {
        $replaced = preg_replace("/\r\n|\r|\n/", '<br />', $subject);
        return $replaced;
    }

    /**
     * Если редактируете поправте translit.js
     * 
     * @param type $message
     * @return string
     */
    static function translit($message) {
        $array = array(
            '/' => '',
            ' ' => '-',
            '(' => '',
            ')' => '',
            '−' => '-',
            ':' => '',
            ';' => '',
            '"' => '',
            '\'' => '',
            '&' => 'and',
            '!' => '',
            '?' => '',
            '—' => '',
            '.' => '',
            ',' => '',
            '»' => '',
            '«' => '',
            '-' => '-',
            '+' => '',
            'ї' => 'i',
            'є' => 'e',
            'і' => 'i',
            'І' => 'i',
            'Я' => 'ja',
            'Ї' => 'ii',
            'Є' => 'e',
            'А' => 'a',
            'Б' => 'b',
            'В' => 'v',
            'Г' => 'g',
            'Д' => 'd',
            'Е' => 'e',
            'Ё' => 'jo',
            'Ж' => 'zh',
            'З' => 'z',
            'И' => 'i',
            'Й' => 'j',
            'К' => 'k',
            'Л' => 'l',
            'М' => 'm',
            'Н' => 'n',
            'О' => 'o',
            'П' => 'p',
            'Р' => 'r',
            'С' => 's',
            'Т' => 't',
            'У' => 'u',
            'Ф' => 'f',
            'Х' => 'kh',
            'Ц' => 'c',
            'Ч' => 'ch',
            'Ш' => 'sh',
            'Щ' => 'shh',
            'Ъ' => '',
            'Ы' => 'y',
            'Ь' => '',
            'Э' => 'eh',
            'Ю' => 'ju',
            'Я' => 'ja',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'jo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'kh',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shh',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'eh',
            'ю' => 'ju',
            'я' => 'ja',
        );

        if (isset($message)) {
            $text = trim(strip_tags(html_entity_decode($message)));
            //$text = implode(array_slice(explode('<br>', wordwrap(trim(strip_tags(html_entity_decode($message))), 255, '<br>', false)), 0, 1));
            foreach ($array as $from => $to) {
                $text = str_replace($from, $to, $text);
            }
            return strtolower($text);
        } else {
            return $array;
        }
    }

// Транслитерация строк.
    public static function transliterate($st) {
        $st = strtr($st, "абвгдежзийклмнопрстуфыэАБВГДЕЖЗИЙКЛМНОПРСТУФЫЭ", "abvgdegziyklmnoprstufieABVGDEGZIYKLMNOPRSTUFIE"
        );
        $st = strtr($st, array(
            'ё' => "yo", 'х' => "h", 'ц' => "ts", 'ч' => "ch", 'ш' => "sh",
            'щ' => "shch", 'ъ' => '', 'ь' => '', 'ю' => "yu", 'я' => "ya",
            'Ё' => "Yo", 'Х' => "H", 'Ц' => "Ts", 'Ч' => "Ch", 'Ш' => "Sh",
            'Щ' => "Shch", 'Ъ' => '', 'Ь' => '', 'Ю' => "Yu", 'Я' => "Ya",
        ));
        return $st;
    }

    /**
     * Генератор случайного кода
     * 
     * @param type $var
     * @return type
     */
    public static function gen($var) {
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

}
