<?php
// writeLog(array('userid'=>$uid,'password'=>'***','loginip'=>$loginip));

function writeLog($message, $file = 'log.txt') {
    $log = '';
    is_string($message)&&$log = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
    is_array($message)&&$log = date('Y-m-d H:i:s') . ' - ' . print_r($message, true) . PHP_EOL;
    !is_array($message)&&!is_string($message)&&$log = date('Y-m-d H:i:s') . ' - ' . json_encode($message, 320) . PHP_EOL;
    file_put_contents($file, $log, FILE_APPEND);
}

$han = new stdClass();
writeLog('热爱大邯郸');
writeLog($han);
writeLog(array('userid'=>1,'password'=>'***','loginip'=>22,'han'=>['userid'=>1,'password'=>'***','loginip'=>22]));