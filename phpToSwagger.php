<?php


$def = <<<EOL
'spu_id'     => 520,                        //商品Spu编码
'sku_id'     => rand(1, 100000),            //商品SKU编码
"goods_name" => '商品名称' . rand(1, 1000), //商品名称
'brand_name' => '品牌' . rand(1, 1000),     //品牌-中文描述
'status'     => [                           //商品最终状态
    'key'   => 1,                           //状态
    'value' => '上架',                      //状态中文描述
],
'delete_status'     => [                    //删除最终状态
    'key'   => 1,                           //状态
    'value' => '删除',                      //状态中文描述
],
'is_listing' => 1,                          //是否上架
'tag_remark' => [                           //tag描述
    '买一赠一'
],
'gallery'    => [                           //相册
    [
        'big'   => 'https://img13.360buyimg.com/n7/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg',//大图片地址
        'small' => 'https://img13.360buyimg.com/n5/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg',//小图片地址
    ],
],
'category' => [
    'id'      => 1,                                       //类目ID-一级类目
    'pid'     => 2,                                       //上级类目ID
    'name'    => '一级类目名称',                          //类目名称
    'img_url' => 'https://img.com/2312/2434/1342/1.png',  //类目图片地址
    'child' => [    //二级子类目
            [
                'id'      => 2,                           //类目ID
                'pid'     => 1,                           //上级类目ID
                'name'    => '二级类目名称',              //类目名称
                'img_url' => 'https://img.com/2312/2434/1342/1.png',//类目图片地址
                'child' => [                              //三级子类目
                    [
                        'id'      => 3,                   //类目ID
                        'pid'     => 2,                   //上级类目ID
                        'name'    => '三级类目名称',      //类目名称
                        'img_url' => 'https://img.com/2312/2434/1342/1.png',//类目图片地址
                    ],
                ],
            ],
    ],
],
EOL;

$str = isset($_POST['php_code']) && $_POST['php_code'] ? $_POST['php_code'] : $def;
$swaggerName = isset($_POST['schemae']) && $_POST['schemae'] ? $_POST['schemae'] : 'schemae_' . uniqid();

?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP代码转Swagger文档</title>
</head>
<body>
<form action="phpToSwagger.php" method="post">
    schemae: <input type="text" name="schemae" value="<?php echo $swaggerName;?>" style="height: 26px;width: 300px;"/>
    <br/><br/>
    PHP代码:
    <br/>
    <textarea name="php_code" rows="20" cols="150">
<?php  print_r($str); ?>
    </textarea>
    <br/>
    <br/>
    <input type="submit" value="Submit"/>
</form>
</body>
</html>
<?php
$str = trim($str, '[');
$str = trim($str, ']');
$str = trim($str, ';');
$str = <<<EOL
[
$str
]
EOL;

echo '<pre><textarea rows="30" cols="190">';
print_r(PhpToSwagger::getSwagger($str, $swaggerName));
echo '</textarea><pre/>';
exit;

class PhpToSwagger
{

    /**
     * 替换字符串
     */
    protected static function getStrReplace($str)
    {
        $str = str_replace(['　', ' ', '\r', '	'], '', $str);
        return $str;
    }

    /**
     * 字符串转PHP数组
     */
    protected static function getPhpArr($str)
    {
        $str = self::getStrReplace($str);
        eval("\$strArr = $str;");
        return $strArr;
    }

    /**
     * 字符串转PHP数组
     */
    protected static function getStrArr($str)
    {
        $str = self::getStrReplace($str);
        $str = str_replace(['[', ']'], '', $str);
        $strArr = preg_split("/[\r\n]+/", $str);
        return $strArr;
    }

    /**
     * 提取数组信息
     */
    protected static function getPhpArr1($arr = [])
    {
        if (empty($arr)) {
            return [];
        }
        $result = [];
        foreach ($arr as $key => $value) {
            $valueType = 'string';
            if (is_array($value)) {
                $value = self::getPhpArr1($value);
                $valueType = 'array';
                if (!isset($value[0])) {//说明是单个对象
                    $valueType = 'object';
                }
            }
            if (is_integer($value)) {
                $valueType = 'integer';
                $value = intval($value);
            }
            if ($valueType == 'string') {
                $value = strval($value);
            }
            $result[$key] = [
                'key'   => $key,
                'type'  => $valueType,
                'value' => $value,
            ];
        }
        return $result;
    }

    /**
     * 提取字符串信息
     */
    protected static function getStrArr1($arr = [])
    {
        if (empty($arr)) {
            return [];
        }
        $result = [];
        foreach ($arr as $item) {
            #正则放弃
            //preg_match('\'(.*?)\'', $item, $matches, PREG_OFFSET_CAPTURE);
            //print_r($matches);exit;
            $arr1 = explode('=>', $item);
            $arr1[0] = str_replace(['\'', '"'], '', $arr1[0]);
            $pos = strpos($item, '//');
            $arr2 = [];
            if ($pos !== false) {
                $arr2 = explode('//', $item);
            }
            $result [$arr1[0]] = isset($arr2[count($arr2) - 1]) ? $arr2[count($arr2) - 1] : '';
        }
        return $result;
    }

    /**
     * 组装生成Swagger结构的数组格式
     * @param $arr
     * @param $strArr
     * @return array
     */
    protected static function getAll($arr, $strArr)
    {
        if (empty($arr)) {
            return [];
        }
        $result = [];
        foreach ($arr as $key => $item) {
            $type = $item['type'];
            $result [$key] = [
                'property'    => $key,
                'type'        => $type,
                'description' => isset($strArr[$key]) ? $strArr[$key] : '',
                'example'     => $item['value'],
                'default'     => $item['value'],
                'child'       => [],
            ];
            if (is_array($item['value'])) {
                $child = self::getAll($item['value'], $strArr);
                $result [$key]['child'] = $child;
                $result [$key]['example'] = $result [$key]['description'];
                $result [$key]['default'] = $result [$key]['description'];
                if (is_array($child) && $child) {
                    if (isset($child[0]) && $child[0] && in_array($child[0]['type'], ['string', 'integer'])) {
                        $result [$key]['type'] = sprintf('sing_%s_array', $child[0]['type']);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Swagger场景配置
     * @param $type
     * @return mixed
     */
    protected static function getSwaggerTemplate($type)
    {
        $result = [
            'integer'            => '*     @OA\Property(property="%s",type="integer",description="%s",example=%d),',
            'string'             => '*     @OA\Property(property="%s",type="string",description="%s",example="%s"),',
            'object'             => '*     @OA\Property(property="%s",description="%s",ref="#components/schemas/%s"),',
            'array'              => '*     @OA\Property(property="%s",type="array",description="%s",@OA\Items(ref="#components/schemas/%s")),',
            'sing_integer_array' => '*     @OA\Property(property="%s",type="array",description="%s",@OA\Items(type="integer",example=%d)),',
            'sing_string_array'  => '*     @OA\Property(property="%s",type="array",description="%s",@OA\Items(type="string",example="%s")),',
        ];
        return $result[$type];
    }

    /**
     * 格式 Swagger
     * @param $arr
     * @param $swaggerName
     * @return string
     */
    protected static function formatSwaggerTemplate($arr, $swaggerName)
    {
        $template = self::getSwaggerTemplate($arr['type']);
        if (empty($template)) {
            return '';
        }
        if (empty($arr['property'])) {
            return '';
        }
        switch ($arr['type']) {
            case 'integer':
                return sprintf($template, $arr['property'], $arr['description'], $arr['example']);
                break;
            case 'string':
                return sprintf($template, $arr['property'], $arr['description'], $arr['example']);
                break;
            case 'object':
                return sprintf($template, $arr['property'], $arr['description'], $swaggerName . '_' . $arr['property']);
                break;
            case 'array':
                return sprintf($template, $arr['property'], $arr['description'], $swaggerName . '_' . $arr['property']);
                break;
            case 'sing_integer_array':
                return sprintf($template, $arr['property'], $arr['description'], $swaggerName . '_' . $arr['property']);
                break;
            case 'sing_string_array':
                return sprintf($template, $arr['property'], $arr['description'], $arr['example']);
                break;
            default:
                return '';
                break;
        }
        return '';
    }

    /**
     * Schema 块
     * @var array
     */
    protected static $swagger = [];

    /**
     * 生成Swagger文档
     */
    protected static function getSwaggerDoc($arr = [], $swaggerName = 'goods_index_base_info')
    {
        if (empty($arr)) {
            return [];
        };
        foreach ($arr as $item) {
            $child = isset($item['child']) ? $item['child'] : [];
            if ($child) {
                if (empty($item['property'])) {
                    self::getSwaggerDoc($child, $swaggerName);
                } else {
                    self::getSwaggerDoc($child, $swaggerName . '_' . $item['property']);
                }
            }
            $property = self::formatSwaggerTemplate($item, $swaggerName);
            if (empty($property)) {
                continue;
            }
            self::$swagger[$swaggerName][] = $property;
        }
    }

    /**
     * 组装Swagger文档
     * @return string
     */
    protected static function createSwaggerDoc()
    {
        $str = '';
        foreach (self::$swagger as $schema => $items) {
            $str .= '*  @OA\Schema(' . PHP_EOL;
            $str .= '*     schema="' . $schema . '",' . PHP_EOL;
            foreach ($items as $item) {
                $str .= $item . PHP_EOL;
            }
            $str .= '*  ),' . PHP_EOL;
        }
        return $str;
    }

    /**
     * 获取Swagger文档信息
     */
    public static function getSwagger($str = '', $swaggerName)
    {
        if (empty($str)) {
            return '';
        }
        #PHP数组
        $arr = self::getPhpArr($str);
        #字符串数组
        $strArr = self::getStrArr($str);
        #提取数组信息
        $arr1 = self::getPhpArr1($arr);
        #提取字符串信息
        $strArr1 = self::getStrArr1($strArr);
        #组装数据
        $allArr = self::getAll($arr1, $strArr1);
        #生成Swagger文档
        self::getSwaggerDoc($allArr, $swaggerName);
        #组装Swagger文档
        return self::createSwaggerDoc();
    }
}