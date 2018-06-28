<?php
/**
 * Created by PhpStorm.
 * User: miaoyanhong
 * Date: 2017/8/1
 * Time: 下午2:32
 */

namespace Core\Tools;

class Xhprof
{
    public static function start() {
        xhprof_enable();
    }

    public static function end() {
        $xhprof_data = xhprof_disable(); //停止监测，返回运行数据
        $xhprof_root = '/workspace/xhprof/';
        //引入当初安装到xhprof虚拟主机目录中的文件
        include_once $xhprof_root."xhprof_lib/utils/xhprof_lib.php";
        include_once $xhprof_root."xhprof_lib/utils/xhprof_runs.php";

        $xhprof_runs = new \XHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof");
        echo '<a href="http://xhprof.imcoming.com.cn/index.php?run='.$run_id.'&source=xhprof" target="_blank">xhprof统计</a>';
    }
}