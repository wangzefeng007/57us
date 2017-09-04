<?php

/**
 * Created by PhpStorm.
 * User: Foliage
 * Date: 2016/10/21
 * Time: 15:19
 */
class Tools
{
    public function __construct(){

    }

    //GPA计算器
    public function GPA(){
        $Title='GPA计算器_ GPA计算方法_ GPA计算器在线- 57美国网';
        $Keywords='GPA, GPA计算器, gpa计算方法, gpa怎么算, 平均绩点gpa计算器, gpa计算器在线计算, gpa计算器在线';
        $Description=' 57美国网GPA计算器，最方便快捷的GPA绩点计算工具，基于多种主流GPA算法，适时计算出GPA数值，应用于留学申请、GPA查询和在线计算，是出国留学生的首选平均绩点查询工具。';  
        include template('ToolsGPA');
    }
}
