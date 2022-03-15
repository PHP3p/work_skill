<?php
/**
 * 解决问题： 减少字段不对应的低级错误次数
 */
$sql = "CREATE TABLE `live` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exid` bigint(20) unsigned NOT NULL COMMENT '直播exid',
  `live_minute` int(11) unsigned NOT NULL COMMENT '直播时长分钟',
  `likes_count` int(11) unsigned NOT NULL COMMENT '获赞数',
  `shares_count` int(11) unsigned NOT NULL COMMENT '分享数',
  `comments_count` int(11) unsigned NOT NULL COMMENT '评论 & 弹幕 数',
  `rq_count` int(11) unsigned NOT NULL COMMENT '当前人气',
  `yl_count` int(11) NOT NULL DEFAULT '0' COMMENT '抖音-音浪',
  `dd_count` int(11) NOT NULL DEFAULT '0' COMMENT '订单数',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态 1:未开始 2:直播中 3:已结束',
  `origin` tinyint(4) NOT NULL COMMENT '数据来源',
  `last_update` datetime NOT NULL COMMENT '最后更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='直播信息表';";


convertToArr($sql);
/**
 * DDL 转数组
 */
function convertToArr($sql)
{
    $pattern = '/`(.*)`/';
    $new_arr = [];
    preg_match_all($pattern, $sql, $new_arr);
    $res_arr = array_flip($new_arr[1]);
    ksort($res_arr);
    echo '<pre/>';
    function test_print($item2, $key)
    {
        $str = "'%s'=>'%s',";
        $str = sprintf($str, $key, $key);
        echo PHP_EOL . $str;
    }

    echo '$arr=[';
    array_walk($res_arr, 'test_print');
    echo PHP_EOL . '];';
}