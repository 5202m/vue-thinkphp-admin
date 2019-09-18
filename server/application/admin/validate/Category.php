<?php
/**
 * Created by PhpStorm.
 * User: Jade.Zhu
 * Date: 2019/9/18
 * Time: 10:14
 */
namespace app\admin\validate;

use think\Validate;

class Category extends Validate
{
    protected $rule = [
        'cat_name' => 'require',
        /*'component' => 'require',
        'path' => 'require',
        'name' => 'require|alphaNum',*/
    ];

    protected $message  =   [
        'title.require' => '分类名称不能为空',
        /*'component.require' => '主题不能为空',
        'path.require' => '路径不能为空',
        'name.require' => 'name不能为空',
        'name.alphaNum' => 'name只能为字母数字',*/
    ];

    protected $scene = [

    ];
}