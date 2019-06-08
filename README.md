# phpToSwagger
PHP 代码 转 Swagger Schema 文档
1.目前开发模式大部分都趋向于前后端分离，后端需要提供API接口，一个好的接口文档(字段，字段描述，默认值，字段类型)，能大大提高工作效率。
2.因此，本脚本应世而生，目前覆盖了能用上的Swagger Schema的各种场景；当然，@OA\Post @OA\RequestBody @OA\Response 部分自己个儿完善去
3.代码后续不断完善.................................................可能不会

# 获取代码
git clone https://github.com/FuLiHai/phpToSwagger.git

# 执行
cd phpToSwagger
php -S localhost:8000

访问：http://localhost:8000/phpToSwagger.php

# 注意
  1.PHP代码块必须是一个数组
  2.每行代码必须有一个有对字段的描述,使用注释符"//",eg:'spu_id'     => 520,//商品Spu编码
  3.每个字段必须有一个默认值，以此默认值，判断此字段类型
