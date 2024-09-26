<?php
// 找寻数组中出现频率最多的值 note 方法一 测试代码
$arr = array(1, 6, 2, 6, 4, 6, 5);
//
$result = findMostVal($arr);

function findMostVal($arr){
    //map
    if (empty($arr)) {
        return $arr;
    }

    $tmpMap = [];
    foreach ($arr as $val){
        if (isset($tmpMap[$val])) {
            $tmpMap[$val]=$tmpMap[$val]+1;
        }else{
            $tmpMap[$val]=1;
        }
    }
    $maxNum = 0;
    $finalVal = NULL;
    foreach ($tmpMap as $k=> $v ){
        if ($v>$maxNum) {
            $maxNum = $v;
            $finalVal = $k;
        }

    }
    return $finalVal;
}

echo "重复出现次数最多的元素是: " . $result; // 输出: 重复出现次数最多的元素是: 3


//note 方法二
$arr = array(7,7,8,9,10,10,10);

$arr = array_count_values($arr);   // 统计数组中所有值出现的次数
arsort($arr);                                   // 按照键值对数组进行降序排序
print_r($arr);
// reset current
$max_number = current($arr);  //出现最多的次数值
// print_r(next($arr));
$more_value = key($arr);           //出现次数最多的值
var_dump("数组中出现次数最多的值为：{$more_value},总共出现{$max_number}次");
