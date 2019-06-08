# phpToSwagger
PHP 代码 转 Swagger Schema 文档
1. 目前开发模式大部分都趋向于前后端分离，后端需要提供API接口，一个好的接口文档(字段，字段描述，默认值，字段类型)，能大大提高工作效率。
2. 因此，本脚本应世而生，目前覆盖了能用上的Swagger Schema的各种场景；当然，@OA\Post @OA\RequestBody @OA\Response 部分自己个儿完善去
3. 代码后续不断完善...................................................................................................................可能不会

# 获取代码
git clone https://github.com/FuLiHai/phpToSwagger.git

# 执行
cd phpToSwagger
php -S localhost:8000

访问：http://localhost:8000/phpToSwagger.php

# 注意
  1. PHP代码块必须是一个数组
  2. 每行代码必须有一个有对字段的描述,使用注释符"//",eg:'spu_id'     => 520,//商品Spu编码
  3. 每个字段必须有一个默认值，以此默认值，判断此字段类型

# PHP代码
```php
'spu_id'     => 520,                        //商品Spu编码 整型
'sku_id'     => rand(1, 100000),            //商品SKU编码
"goods_name" => '商品名称' . rand(1, 1000), //商品名称     字符串
'brand_name' => '品牌' . rand(1, 1000),     //品牌-中文描述
'status'     => [                           //商品最终状态 单个对象
    'key'   => 1,                           //状态
    'value' => '上架',                      //状态中文描述
],
'delete_status'     => [                    //删除最终状态
    'key'   => 1,                           //状态
    'value' => '删除',                      //状态中文描述
],
'is_listing' => 1,                          //是否上架
'tag_remark' => [                           //tag描述  字符串数组/列表
    '买一赠一'
],
'gallery'    => [                           //相册     数组/列表
    [
        'gallery.big'   => 'https://img13.360buyimg.com/n7/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg',//大图片地址
        'gallery.small' => 'https://img13.360buyimg.com/n5/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg',//小图片地址
    ],
],
'category' => [                           //分类  多层级嵌套
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
```

# 最终生成Swagger Schema
```php
*  @OA\Schema(
*     schema="schemae_5cfb24156100e",
*     @OA\Property(property="spu_id",type="integer",description="商品Spu编码",example=520),
*     @OA\Property(property="sku_id",type="integer",description="商品SKU编码",example=22016),
*     @OA\Property(property="goods_name",type="string",description="商品名称",example="商品名称747"),
*     @OA\Property(property="brand_name",type="string",description="品牌-中文描述",example="品牌139"),
*     @OA\Property(property="status",description="商品最终状态",ref="#components/schemas/schemae_5cfb24156100e_status"),
*     @OA\Property(property="delete_status",description="删除最终状态",ref="#components/schemas/schemae_5cfb24156100e_delete_status"),
*     @OA\Property(property="is_listing",type="integer",description="是否上架",example=1),
*     @OA\Property(property="tag_remark",type="array",description="tag描述",@OA\Items(type="string",example="tag描述")),
*     @OA\Property(property="gallery",type="array",description="相册",@OA\Items(ref="#components/schemas/schemae_5cfb24156100e_gallery")),
*     @OA\Property(property="category",description="分类",ref="#components/schemas/schemae_5cfb24156100e_category"),
*  ),
*  @OA\Schema(
*     schema="schemae_5cfb24156100e_status",
*     @OA\Property(property="key",type="integer",description="状态",example=1),
*     @OA\Property(property="value",type="string",description="状态中文描述",example="上架"),
*  ),
*  @OA\Schema(
*     schema="schemae_5cfb24156100e_delete_status",
*     @OA\Property(property="key",type="integer",description="状态",example=1),
*     @OA\Property(property="value",type="string",description="状态中文描述",example="删除"),
*  ),
*  @OA\Schema(
*     schema="schemae_5cfb24156100e_gallery",
*     @OA\Property(property="gallery.big",type="string",description="大图片地址",example="https://img13.360buyimg.com/n7/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg"),
*     @OA\Property(property="gallery.small",type="string",description="小图片地址",example="https://img13.360buyimg.com/n5/jfs/t1/41729/34/1969/283035/5cc80ed4Eab41eae2/dc784f471bc44ce2.jpg"),
*  ),
*  @OA\Schema(
*     schema="schemae_5cfb24156100e_category",
*     @OA\Property(property="id",type="integer",description="类目ID",example=1),
*     @OA\Property(property="pid",type="integer",description="上级类目ID",example=2),
*     @OA\Property(property="name",type="string",description="类目名称",example="一级类目名称"),
*     @OA\Property(property="img_url",type="string",description="类目图片地址",example="https://img.com/2312/2434/1342/1.png"),
*     @OA\Property(property="child",type="array",description="三级子类目",@OA\Items(ref="#components/schemas/schemae_5cfb24156100e_category_child")),
*  ),
*  @OA\Schema(
*     schema="schemae_5cfb24156100e_category_child",
*     @OA\Property(property="id",type="integer",description="类目ID",example=2),
*     @OA\Property(property="pid",type="integer",description="上级类目ID",example=1),
*     @OA\Property(property="name",type="string",description="类目名称",example="二级类目名称"),
*     @OA\Property(property="img_url",type="string",description="类目图片地址",example="https://img.com/2312/2434/1342/1.png"),
*     @OA\Property(property="child",type="array",description="三级子类目",@OA\Items(ref="#components/schemas/schemae_5cfb24156100e_category_child_child")),
*  ),
*  @OA\Schema(
*     schema="schemae_5cfb24156100e_category_child_child",
*     @OA\Property(property="id",type="integer",description="类目ID",example=3),
*     @OA\Property(property="pid",type="integer",description="上级类目ID",example=2),
*     @OA\Property(property="name",type="string",description="类目名称",example="三级类目名称"),
*     @OA\Property(property="img_url",type="string",description="类目图片地址",example="https://img.com/2312/2434/1342/1.png"),
*  ),
```
