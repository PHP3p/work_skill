
<?php
/**
 * 白领招聘配置项
 */
// 定义一个函数，递归地比较两个二维数组的所有层级
function recursiveArrayDiff1($array1, $array2)
{
    // 初始化一个空数组，用于存放差异数据
    $diff = array();

    // 遍历第一个数组的所有元素
    foreach ($array1 as $key => $value) {
        // 如果当前元素是一个数组
        if (is_array($value)) {
            // 如果第二个数组中不存在当前键，或者第二个数组中的当前键对应的值不是一个数组   ++考虑同键 同值类型
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                // 则将当前值直接放入差异数组中
                $diff[$key] = $value;
            } else {
                // 如果第二个数组中存在当前键，且对应的值也是一个数组
                // 则递归地调用此函数，比较两个子数组
                $recursiveDiff = recursiveArrayDiff1($value, $array2[$key]);
                // 如果递归比较的结果不为空，即存在差异
                if (!empty($recursiveDiff)) {
                    // 则将递归比较的结果放入差异数组中
                    $diff[$key] = $recursiveDiff;
                }
            }
        } else {
            // 如果当前元素不是一个数组
            // 如果第二个数组中不存在当前键，或者第二个数组中的当前键对应的值与第一个数组中的当前值不相等
            if (!isset($array2[$key]) || $array2[$key] !== $value) {
                // 则将当前值放入差异数组中
                $diff[$key] = $value;
            }
        }
    }
    // 返回差异数组
    return $diff;
}

// 定义两个二维数组数据
$bankData = array(
    'A' => array('amount' => 100),
    'B' => array('amount' => 200),
    'C' => array('amount' => 300)
);

$systemData = array(
    'A' => array('amount' => 100),
    'B' => array('amount' => 250),
    'C' => array('amount' => 300)
);

// 调用函数，比较两个数组
$diff = recursiveArrayDiff1($bankData, $systemData);

// 判断比较结果
if (empty($diff)) {
    // 如果差异数组为空，则两个数组数据一致
    echo "数据一致，对账成功！";
} else {
    // 如果差异数组不为空，则两个数组数据不一致
    echo "数据不一致，对账失败！";
    echo "不一致数据及差额：";
    // 打印出差异数据
    print_r($diff);
}


// 定义一个函数，递归地比较两个二维数组的所有层级，并计算差额  
function recursiveArrayDiff2($array1, $array2)
{
    $diff = array();

    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                // 如果第二个数组没有对应的键或对应的值不是数组，则整个数组视为差异  
                $diff[$key] = $value;
            } else {
                $recursiveDiff = recursiveArrayDiff2($value, $array2[$key]);
                if (!empty($recursiveDiff)) {
                    // 如果递归比较的结果不为空，则记录差异  
                    $diff[$key] = $recursiveDiff;
                }
            }
        } else {
            if (!isset($array2[$key])) {
                // 如果第二个数组没有对应的键，则视为差异  
                $diff[$key] = array('value' => $value, 'diff' => $value);
            } elseif ($array2[$key] !== $value) {
                // 如果两个数组在对应键上的值不相等，则计算差额  
                $diff[$key] = array('value' => $value, 'diff' => $value - $array2[$key]);
            }
        }
    }

    // 检查第二个数组是否有第一个数组没有的键  
    foreach ($array2 as $key => $value) {
        if (!isset($array1[$key])) {
            if (is_array($value)) {
                $diff[$key] = null; // 或者可以记录整个数组为null，取决于你的需求  
            } else {
                $diff[$key] = array('value' => null, 'diff' => -$value); // 第二个数组有而第一个数组没有的，差额为负数  
            }
        }
    }

    return $diff;
}

// 定义两个二维数组数据  
$bankData = array(
    'A' => array('amount' => 100),
    'B' => array('amount' => 200),
    'C' => array('amount' => 300)
);

$systemData = array(
    'A' => array('amount' => 100),
    'B' => array('amount' => 250),
    'C' => array('amount' => 300)
);

// 调用函数，比较两个数组并计算差额  
$diff = recursiveArrayDiff2($bankData, $systemData);

// 输出结果  
if (empty($diff)) {
    echo "数据一致，对账成功！";
} else {
    echo "数据不一致，对账失败！";
    echo "不一致数据及差额：";
    print_r($diff);
}
die;
// 递归比较两个二维数组的所有层级
function recursiveArrayDiff($array1, $array2)
{
    $diff = array();
    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                $diff[$key] = $value;
            } else {
                $recursiveDiff = recursiveArrayDiff($value, $array2[$key]);
                if (!empty($recursiveDiff)) {
                    $diff[$key] = $recursiveDiff;
                }
            }
        } else {
            if (!isset($array2[$key]) || $array2[$key] !== $value) {
                $diff[$key] = $value;
            }
        }
    }
    return $diff;
}

// 二维数组数据
$bankData = array(
    'A' => array('amount' => 100),
    'B' => array('amount' => 200),
    'C' => array('amount' => 300)
);

$systemData = array(
    'A' => array('amount' => 100),
    'B' => array('amount' => 250),
    'C' => array('amount' => 300)
);

// 比对数据
$diff = recursiveArrayDiff($bankData, $systemData);

if (empty($diff)) {
    echo "数据一致，对账成功！";
} else {
    echo "数据不一致，对账失败！";
    echo "不一致数据及差额：";
    print_r($diff);
}


die;


// 银行流水数据
$bankData = array(100, 200, 300);

// 系统记录数据
$systemData = array(100, 250, 300);

// 比对数据
$diff = array_diff_assoc($bankData, $systemData);

if (empty($diff)) {
    echo "数据一致，对账成功！";
} else {
    echo "数据不一致，对账失败！";
    echo "不一致数据及差额：";
    foreach ($diff as $key => $value) {
        $difference = $bankData[$key] - $systemData[$key];
        $key += 1;
        echo "数据{$key}不一致，差额为：{$difference}\n";
    }
}


die;
echo strrchr('name.kph.jpg', '.');

echo substr(strrchr('name.kph.jpg', '.'), 1);
die;

//  strrchr($_FILES[$fields]['name'], '.'), 1);

$arr = [4, 5, 6];
$arr1 = [5, 6];

$mergedArray = array_merge($arr, $arr1);

$mergedArray = array_merge($arr, $arr1, $mergedArray);

print_r($mergedArray);
die;

$ss = "cert_info #>> '{0,value}' like '%%%s%%'";
$formatted = sprintf($ss, '01');
echo $formatted;
die;
$json = '{"1393": [100411, 100419, 100451, 100456, 100464, 100465, 100469, 100473, 100475, 100477, 100478, 100479, 100480, 100481, 100482, 100483, 100485, 100488, 100493, 156726, 167901, 167933, 198763, 198773, 198860, 198894, 198898, 198900, 198901, 198906, 198907, 198910, 199023, 203952, 206281, 206886], "1414": [167901], "2122": [165648]}';
$json = '[156939, 157185, 157312, 157313, 157314, 157315, 157316, 157317, 157318, 157319, 157320, 157321, 157322, 157323, 157324, 157325, 157326, 157327, 157328, 157329, 157330, 157331, 157332, 157333, 157334, 157335, 157336, 157337, 157338, 157339, 157340, 157341, 157342, 157343, 157344, 157345, 157346, 157347, 157348, 157349, 157350, 157351, 157352, 157353, 157354, 157355, 157356, 157357, 157358, 157359, 157360, 157361, 157362, 157363, 157364, 157365, 157366, 157367, 157368, 157369, 157370, 157371, 157372, 157373, 157374, 157375, 157376, 157377, 157378, 157379, 157380, 157381, 157382, 157383, 157384, "165329", "165330", "165331", "165332"]';
// 将JSON字符串解码为数组  
$array = json_decode($json, true);
print_r(uniArray($array));
function uniArray($array)
{
    if (count($array) == count($array, 1)) {
        return array_unique($array);
    }
    $uniqueValues = [];
    foreach ($array as $subArray) {
        $uniqueValues = array_merge($uniqueValues, array_unique($subArray));
    }
    return $uniqueValues;
} 