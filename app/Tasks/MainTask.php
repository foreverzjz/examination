<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/29
 * Time: 23:49
 */
namespace Tasks;
use Core\Tools\Network\Curl;
use Core\Tools\SeasLogPlugin;
use Phalcon\Cli\Task;

class MainTask extends Task
{
    public function mainAction(){
        echo 'hello';
    }

    public function requestGoodsAction()
    {
        $time = time();
        $dic = 10;
        $curl = curl_init();
        $i = 200;
        while($i>0) {
            $time2 = time();
            if(($time2-$time)>=$dic) {
                $i --;
                $time = $time2;
                curl_setopt($curl, CURLOPT_URL, "http://gc.imcoming.com/v1/public/app/shop/gd?client_type=0&time=1517903374&device_id=862941038079986&app_version=4.0.1&data=%7B%22shopCode%22%3A%2248%22%7D&sign=436c394eca50dac81f765b624b786e53");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                //        if (self::$mode == Curl::MODE_TEST && PHP_OS == 'Darwin') {
                //            curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1:8888');
                //            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                //        }

                curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)');
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curl, CURLOPT_TIMEOUT, 3000);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json; charset=utf-8',
                ]);
                $data = curl_exec($curl);
                //curl_close($curl);
                $startAt = SeasLogPlugin::getMicroTime();
                SeasLogPlugin::requestLog('curl2', "", "", $data, SeasLogPlugin::caleCostTime($startAt));
            }
        }
    }
}