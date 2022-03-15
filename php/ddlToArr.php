<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>sql ddl convert to array</title>
</head>
<body>
<form action="" method="post">
   <textarea rows="20" cols="100"
             placeholder="DDL。。。"
             name="ddl"></textarea><br>
    <input type="submit" value="转换">
    <input type="reset" value="重置">

</form>
</body>
</html>
<?php
echo '<pre/>';
$sql = $_POST['ddl'] ?? '';
if ($sql)
{
    convertToArr($sql);
}

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
//        $str = sprintf($str, $key, $item2);
        echo PHP_EOL . $str;
    }

    echo '$arr=[';
    array_walk($res_arr, 'test_print');
    echo PHP_EOL . '];';
}