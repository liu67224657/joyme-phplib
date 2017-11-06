<?php

declare(ticks = 1);
$bWaitFlag = TRUE; /// 是否等待进程结束
$intNum = 100;      /// 进程总数
$pids = array();    /// 进程PID数组
file_put_contents('count.txt', 0);
echo "\n";
//172.16.75.61  11211
//echo ("Start\n");
for ($i = 0; $i < $intNum; $i++) {
    $pids[$i] = pcntl_fork(); /// 产生子进程，而且从当前行之下开试运行代码，而且不继承父进程的数据信息
    if (!$pids[$i]) {
        // 子进程进程代码段_Start
        usleep(rand(1000,30000));
        //memcached 锁
        $memcache = new Memcache;
        $memcache->addServer('172.16.75.61', 11211);
        $key = 'testLock2';
        for ($k = 0; $k < 3; $k++) {
            $ret = $memcache->add($key, 1);
            if ($ret == false) {
                //已经有在处理， 可以循环尝试几次 或者直接返回错误提示  看业务需要
                //echo "pid " . posix_getpid() . " get clock failded \n";
                usleep(100);
            } else {
                $file = 'count.txt';
                $count = intval(file_get_contents($file));
                $count++;
                file_put_contents($file, $count);
                echo "pid " . posix_getpid() . " get count[$count]\n";
                //释放锁
                $memcache->delete($key);
                break;
            }
        }
        exit();
        // 子进程进程代码段_End
    }
}
if ($bWaitFlag) {
    for ($i = 0; $i < $intNum; $i++) {
        pcntl_waitpid($pids[$i], $status, WUNTRACED);
//        echo "wait $i -> " . time() . "\n";
    }
}
echo "\n";
//echo ("End\n");
?>
