<?php

/**
 * Created by PhpStorm.
 * User: Peter Pan
 * Date: 2016/10/9
 * Time: 23:37
 */
namespace Library;

use Enumerations\BusinessConst;
use Enumerations\CacheConst;
use Enumerations\PromotionConst;
use Phalcon\DI;


class Common
{
    /*
   *  生成随机密码
   */
    static function createSalt(int $len = 6): string
    {
        $codeSet = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklnmopqrstuvwxyzz';
        $word = "";
        for ($i = 0; $i < $len; $i++) {
            $word .= strtolower($codeSet[mt_rand(0, strlen($codeSet) - 1)]);
        }
        return $word;
    }

    /**
     * 给密码加盐
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function pwdEncode($password, $salt = '')
    {
        $sha1_word = sha1($password . $salt);
        return md5(substr($sha1_word, 10, 20) . substr($sha1_word, 0, 10) . substr($sha1_word, 30, 10));
    }

    static function createSerialNumber($businessTypeId)
    {
        $number = $businessTypeId;

        $number .= date('ymd');
        $todaySec = time() - strtotime(date('Y-m-d 00:00:00'));
        $cacheName = "SerialNumber:Unique:{$businessTypeId}:{$todaySec}";
        $incr = Di::getDefault()->get('redis')->setIncr($cacheName, 3);
        $number .= str_pad($todaySec, 5, '0', STR_PAD_LEFT);
        $number = $number . str_pad($incr, 3, '0', STR_PAD_LEFT);
        return $number;
    }

    static public function getClientIP($returnLong = TRUE)
    {
        $strIp = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (strstr($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $x = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $_SERVER['HTTP_X_FORWARDED_FOR'] = trim(end($x));
            }
            if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $strIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $strIp = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (empty($strIp)) {
            if (preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['REMOTE_ADDR'])) {
                $strIp = $_SERVER['REMOTE_ADDR'];
            }
        }
        if ($returnLong) {
            $return = empty($strIp) ? 0 : bindec(decbin(ip2long($strIp)));
        } else {
            $return = $strIp;
        }
        return $return;
    }

    /**
     * 获取访问浏览器信息
     * @return array
     */
    public static function getBrowser()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (empty($agent)) {
            return '命令行,机器人来袭!';
        }
        $browser = '';
        $browser_ver = '';

        if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
            $browser = 'OmniWeb';
            $browser_ver = $regs[2];
        }

        if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Netscape';
            $browser_ver = $regs[2];
        }

        if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Safari';
            $browser_ver = $regs[1];
        }

        if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
            $browser = 'Internet Explorer';
            $browser_ver = $regs[1];
        }

        if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
            $browser = 'Opera';
            $browser_ver = $regs[1];
        }

        if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') NetCaptor';
            $browser_ver = $regs[1];
        }
        if (preg_match('/360SE/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') 360SE[360浏览器]';
            $browser_ver = '';
        }
        if (preg_match('/SE 2.x/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') [搜狗浏览器]';
            $browser_ver = '';
        }

        if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'FireFox[火狐浏览器]';
            $browser_ver = $regs[1];
        }

        if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Lynx';
            $browser_ver = $regs[1];
        }

        if (preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Chrome';
            $browser_ver = $regs[1];
        }
        if (preg_match('/QQBrowser\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'QQBrowser';
            $browser_ver = $regs[1];
        }
        if (preg_match('/UBrowser\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'UBrowser[UC浏览器]';
            $browser_ver = $regs[1];
        }
        if (preg_match('/BIDUBrowser\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'BIDUBrowser[百度浏览器]';
            $browser_ver = $regs[1];
        }
        if (preg_match('/Maxthon\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Maxthon[遨游浏览器]';
            $browser_ver = $regs[1];
        }
        if (preg_match('/TheWorld\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'TheWorld[世界之窗浏览器]';
            $browser_ver = $regs[1];
        }
        //微信浏览器
        if (preg_match('/MicroMessenger\/([^\s]+)/i', $agent, $regs)) {
            if (self::isMobile()) {
                if (strpos($agent, 'QQBrowser')) {
                    $browser = 'QQ浏览器';
                } elseif (strpos($agent, 'UBrowser')) {
                    $browser = 'UC浏览器';
                }
            } else {
                $browser = '电脑端微信浏览器';
            }
            $browser_ver = $regs[1];

        }

        if ($browser != '') {
            return ['browser' => $browser, 'version' => $browser_ver];
        } else {
            return ['browser' => 'unknown browser', 'version' => 'unknown browser version'];
        }
    }

    public static function getOs()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = FALSE;
        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
            $os = 'Windows 98';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10';#添加win10判断
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows 2000';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
            $os = 'Windows NT';
        } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            $os = 'Windows 32';
        } else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } else if (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'SunOS';
        } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        } else if (preg_match('/Mac/i', $agent)) {
            $os = 'Macintosh';
        } else if (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        } else if (preg_match('/AIX/i', $agent)) {
            $os = 'AIX';
        } else if (preg_match('/HPUX/i', $agent)) {
            $os = 'HPUX';
        } else if (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        } else if (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        } else if (preg_match('/OSF1/i', $agent)) {
            $os = 'OSF1';
        } else if (preg_match('/IRIX/i', $agent)) {
            $os = 'IRIX';
        } else if (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        } else if (preg_match('/teleport/i', $agent)) {
            $os = 'teleport';
        } else if (preg_match('/flashget/i', $agent)) {
            $os = 'flashget';
        } else if (preg_match('/webzip/i', $agent)) {
            $os = 'webzip';
        } else if (preg_match('/offline/i', $agent)) {
            $os = 'offline';
        } else if (preg_match('/PostmanRuntime/i', $agent)) {
            $os = 'PostMan调试工具';
        } else {
            $os = '未知操作系统';
        }
        return $os;
    }


    /**
     * 判断是否为手机访问
     * @return  boolean
     */
    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return TRUE;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为false,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;
        }

        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public static function unAbs($a)
    {
        return ($a > 0) ? -$a : $a;
    }

    public static function showBusiness($business)
    {
        $srt = BusinessConst::BUSINESS;
        if (strpos($business, ',')) {
            $arr = explode(',', $business);
            $arr = array_flip($arr);
            return implode(',', array_values(array_intersect_key($srt, $arr)));
        } else {
            return $srt[$business];
        }
    }

    public static function Url2Base64($string)
    {
        $data = str_replace(array('__2B', '__2F'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if (strlen($data) % 4) {
            $data .= substr('====', $mod4);
        }
        return $data;
    }

    /**
     * 计算两个时间段是否有交集（边界重叠不算）
     * @param string $beginTime1 开始时间1
     * @param string $endTime1 结束时间1
     * @param string $beginTime2 开始时间2
     * @param string $endTime2 结束时间2
     * @return bool
     */
    public static function isTimeCross($beginTime1 = '', $endTime1 = '', $beginTime2 = '', $endTime2 = '')
    {
        $beginTime1 = strtotime($beginTime1);
        $beginTime2 = strtotime($beginTime2);
        $endTime1 = strtotime($endTime1);
        $endTime2 = strtotime($endTime2);
        if ($endTime1 <= $beginTime2 || $endTime2 <= $beginTime1) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param $platform
     * @return int|string
     */
    public static function showPlatform($platform)
    {
        $tmp = '000';
        foreach ($platform as $value) {
            $tmp = $tmp | $value;
        }
        return $tmp;
    }

    /**
     * @param $platform
     * @return array
     */
    public static function chunkPlatform($platform)
    {
        $dist = ['100', '010', '001'];
        $tmp = [];
        foreach ($dist as $value) {
            $value = $platform & $value;
            if ($value != '000') {
                $tmp[] = $value;
            }
        }
        return $tmp;
    }

    public static function createTtl($endTime)
    {
        $diff = strtotime($endTime) - time();
        if ($diff >= 0) {
            return $diff + 24 * 3600 * 30;
        } else {
            return CacheConst::DEFAULT_TTL;
        }
    }

    /*
     *  生成uuid
     */
    public static function uuid_generate()
    {
        $chars = md5(uniqid(mt_rand(), TRUE));
        $uuid = substr($chars, 0, 8);
        $uuid .= substr($chars, 8, 4);
        $uuid .= substr($chars, 12, 4);
        $uuid .= substr($chars, 16, 4);
        $uuid .= substr($chars, 20, 12);
        return strtoupper($uuid);
    }
}