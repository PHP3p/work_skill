<?php
/*
 * $a=10;
$b=15;
list($a,$b)=[$b,$a];*/

//冒泡算法 
$arr = [3,5,2,1];
// 2 3  1 5 三次 最大个一个 两两相邻比较 找出一行的一个最大
// 2 1 3 5 二次
//  1 2 3 5 一次
$len=count($arr);
$tmp=0;
$swapped = true; // 假设在一开始就有元素需要交换
for ($i=0;$i<=$len-1&& $swapped;$i++) { #行数
    // 行数中元素
    $swapped = false; // 重置交换标志
    for ($j=0;$j<=$len-$i-1;$j++) {
        if ($arr[$j]<$arr[$j+1]) {
            $tmp=$arr[$j];
            $arr[$j]=$arr[$j+1];
            $arr[$j+1]=$tmp;
            $swapped = true; // 发生了交换，设置标志
        }
var_dump($arr);
    }
}
print_r($arr);die;
