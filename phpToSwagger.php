
<!DOCTYPE html>
<html>
<head>
    <title>PHP代码转Swagger文档</title>
</head>
<form action="phpToSwagger.php" method="post">
schemae: <input type="text" name="schemae" />
    <br/>    <br/>
    PHP代码: <br/>
<textarea name="php_code" rows="20" cols="80">
'spu_id' => 520,//商品Spu编码
'sku_id' => rand(1,100000),//商品SKU编码
"goods_name" => '商品名称'.rand(1,1000),//商品名称
</textarea>
    <br/>
    <br/>
<input type="submit" value="Submit" />
</form>

<?php

/**
*PHP代码块转Swagger文档
*1.根据字符串转PHP数组
*2.正则匹配字符串，提取关键信息
*3.组装Swagger文档
*/

$def = <<<EOL
'spu_id' => 520,//商品Spu编码
'sku_id' => rand(1,100000),//商品SKU编码
"goods_name" => '商品名称'.rand(1,1000),//商品名称
'subhead' => '副标题'.rand(1,1000),//副标题
'brand_id' => rand(1,1000),//商品品牌ID
'brand_name' => '品牌'.rand(1,1000),//品牌-中文描述
'status' => [//商品最终状态
                'status.key' => 1,//状态
                'status.value' => '上架',//状态值
],
'is_listing' => 1,//是否上架
'is_sell_out' => 0,//是否售罄
'is_expire' => 0,//是否失效
'is_self_support' => 1,//是否是平台商品
'shop_id' => 1,//商户id
'is_suit' => 0,//是否为套装
'marking_price' => rand(1,10000).'.00',//划线价
'selling_price' => rand(1,1000).'.00',//售价
'vip_price' => rand(1,10000).'.00',//会员价
'agent_price' => rand(1,10000).'.00',//代理价
'exchange_score' => rand(1,10000).'.00',//兑换收益
'exchange_price' => rand(1,10000).'.00',//兑换价格
'worth_value' => rand(1,10000),//净值
'goods_attr' => '存储容量:64G;机身颜色:黑色',//商品包装规格属性
'details' => '商品详情',//商品详情
'img_url' => 'https://img13.360buyimg.com/n7/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg',//图片地址
'goods_gallery' => [//相册
	[
		'goods_gallery.big' => 'https://img13.360buyimg.com/n7/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg',//大图片地址
		'goods_gallery.small' => 'https://img13.360buyimg.com/n5/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg',//小图片地址
	],
],
'tag_remark' => [ //tag 描述
	'买一赠一'
],
'tag_remark1' => [ //tag 描述1
	1,
],
'remaining_count' => 100, //限购数量
'start_time' => time(), //开始时间
'end_time' => time()+5000,//结束时间
EOL;
$str = isset($_POST['php_code']) && $_POST['php_code']?$_POST['php_code']:$def;
$swaggerName = isset($_POST['schemae']) && $_POST['schemae']?$_POST['schemae']:uniqid();
$str = trim($str,'[');
$str = trim($str,']');
$str = trim($str,';');
$str = <<<EOL
[
$str
]
EOL;

echo '<pre><textarea rows="30" cols="190">';
print_r(PhpToSwagger::getSwagger($str,$swaggerName));
echo '</textarea><pre/>';
exit;

class PhpToSwagger{
	
	/**
	* 替换字符串
	*/
	protected static function getStrReplace($str){
		$str = str_replace(['　',' ','\r','	'],'',$str);
		return $str;
	}
	
	/**
	* 字符串转PHP数组
	*/
	protected static function getPhpArr($str){
		$str = self::getStrReplace($str);
		eval("\$strArr = $str;");
		return $strArr;
	}
	
	/**
	* 字符串转PHP数组
	*/
	protected static function getStrArr($str){
		$str = self::getStrReplace($str);
		$str = str_replace(['[',']'],'',$str);
		$strArr = preg_split("/[\r\n]+/",$str);
		return $strArr;
	}
	
	/**
	* 提取数组信息
	*/
	protected static function getPhpArr1($arr = []){
		if(empty($arr)){
			return [];
		}
		$result = [];
		foreach($arr as $key => $value){
			$valueType = 'string';
			if(is_array($value)){
				$value = self::getPhpArr1($value);
				$valueType = 'array';
				if(!isset($value[0])){//说明是单个对象
                    $valueType = 'object';
				}
			}
			if(is_integer($value)){
				$valueType = 'integer';
				$value = intval($value); 	
			}
			if($valueType == 'string'){
				$value = strval($value); 	
			}
			/*if($key === 'status'){
			    print_r([$key,$valueType]);exit;
            }*/
			$result[$key] = [
				'key' => $key,
				'type' => $valueType,
				'value' => $value,
			];
		}
		return $result;
	}
	
	/**
	* 提取字符串信息
	*/
	protected static function getStrArr1($arr = []){
	    if(empty($arr)){
	        return [];
        }
	    $result = [];
	    //print_r($arr);exit;
        foreach ($arr as $item) {
            #正则放弃
            //preg_match('\'(.*?)\'', $item, $matches, PREG_OFFSET_CAPTURE);
            //print_r($matches);exit;
            $arr1 = explode('=>',$item);
            $arr1[0] = str_replace(['\'','"'],'',$arr1[0]);
            $pos = strpos($item, '//');
            $arr2 = [];
            if ($pos !== false) {
                $arr2 = explode('//',$item);
                //print_r([$arr2,$arr2[count($arr2) - 1]]);exit;
            }
            $result [$arr1[0]] = isset($arr2[count($arr2) - 1])?$arr2[count($arr2) - 1]:'';
	    }
		return $result;
	}

	protected  static function getAll($arr,$strArr){
	    if(empty($arr)){
	        return [];
        }
        $result = [];
        foreach ($arr as $key => $item) {
            $type = $item['type'];
            $result [$key] = [
                'property' => $key,
                'type' => $type,
                'description' => isset($strArr[$key])?$strArr[$key]:'',
                'example' => $item['value'],
                'default' => $item['value'],
                'child'   => [],
            ];
            if(is_array($item['value'])){
                $child = self::getAll($item['value'],$strArr);
                $result [$key]['child'] = $child;
                $result [$key]['example'] =  $result [$key]['description'];
                $result [$key]['default'] =  $result [$key]['description'];
                if(is_array($child) && $child){
                    if(isset($child[0]) && $child[0] && in_array($child[0]['type'],['string','integer'])){
                        $result [$key]['type'] = sprintf('sing_%s_array',$child[0]['type']);
                    }
                }
            }
        }
        return $result;
    }

    protected static function getSwaggerSchema(){
	    $str = <<<EOL
 *  @OA\Schema(
 *     schema="%s",
%s
 * 
EOL;
        return $str;
    }

    protected static function getSwaggerTemplate($type){
	    $result = [
            'integer' => '*     @OA\Property(property="%s",type="integer",description="%s",example=%d),',
            'string' => '*     @OA\Property(property="%s",type="string",description="%s",example="%s"),',
            'object' => '*     @OA\Property(property="%s",description="%s",ref="#components/schemas/%s"),',
            'array' => '*     @OA\Property(property="%s",type="array",description="%s",@OA\Items(ref="#components/schemas/%s")),',
            'sing_integer_array' => '*     @OA\Property(property="%s",type="array",description="%s",@OA\Items(type="integer",example=%d)),',
            'sing_string_array' => '*     @OA\Property(property="%s",type="array",description="%s",@OA\Items(type="string",example="%s")),',
        ];
	    return $result[$type];
    }

    protected static function formatSwaggerTemplate($arr,$swaggerName){
        $template = self::getSwaggerTemplate($arr['type']);
        if(empty($template)){
            return '';
        }
        if(empty($arr['property'])){
            return '';
        }
        switch ($arr['type']){
            case 'integer':
                return sprintf($template,$arr['property'],$arr['description'],$arr['example']);
                break;
            case 'string':
                return sprintf($template,$arr['property'],$arr['description'],$arr['example']);
                break;
            case 'object':
                return sprintf($template,$arr['property'],$arr['description'],$swaggerName.'_'.$arr['property']);
                break;
            case 'array':
                return sprintf($template,$arr['property'],$arr['description'],$swaggerName.'_'.$arr['property']);
                break;
            case 'sing_integer_array':
                return sprintf($template,$arr['property'],$arr['description'],$swaggerName.'_'.$arr['property']);
                break;
            case 'sing_string_array':
                return sprintf($template,$arr['property'],$arr['description'],$arr['example']);
                break;
            default:
                return '';
                break;
        }
        return '';
    }

    protected static $swagger = [];

    /**
     * 生成Swagger文档
     */
    protected static function getSwaggerDoc($arr = [],$swaggerName = 'goods_index_base_info'){
        if(empty($arr)){
            return [];
        };
        foreach ($arr as $item) {
            $child = isset($item['child'])?$item['child']:[];
            if($child){
                if(empty($item['property'])){
                    self::getSwaggerDoc($child,$swaggerName);
                }else{
                    self::getSwaggerDoc($child,$swaggerName.'_'.$item['property']);
                }
            }
            $property = self::formatSwaggerTemplate($item,$swaggerName);
            if(empty($property)){
                continue;
            }
            self::$swagger[$swaggerName][] = $property;
        }
    }

    protected static function createSwaggerDoc(){
/**
*  @OA\Schema(
*     schema="goods_index_status",
*     @OA\Property(property="key",type="integer",description="1-上架 非1时是其他状态",example=1),
*     @OA\Property(property="value",type="integer",description="上架/售罄/失效",example="上架"),
*  ),
 */
        $str = '';
        foreach (self::$swagger as $schema => $items) {
            $str  .= '*  @OA\Schema('.PHP_EOL;
            $str .= '*     schema="'.$schema.'",'.PHP_EOL;
            foreach ($items as $item) {
                $str .= $item.PHP_EOL;
            }
            $str .= '*  ),'.PHP_EOL;
        }
        echo $str;exit;
        return $str;
    }

	/**
	* 获取Swagger文档信息
	*/
	public static function getSwagger($str = '',$swaggerName){
		if(empty($str)){
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
        //print_r([$arr1,$strArr1]);exit;
        $allArr = self::getAll($arr1,$strArr1);
        //print_r($allArr);exit;
		#生成Swagger文档
        self::getSwaggerDoc($allArr,$swaggerName);
        return self::createSwaggerDoc();
	}
}