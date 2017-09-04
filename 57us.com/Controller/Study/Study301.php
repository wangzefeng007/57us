<?php
class Study301{

    public function __construct(){

    }
    
    //高中
    public function HighSchool(){
        header("Location:/highschool/");
    }
    
    //本科
    public function College(){
        header("Location:/collge/");
    }
    
    //服务
    public function Service(){
        switch($_SERVER['REQUEST_URI']){
            case '/consultant/_px0_fwnr1/':
                header('Location:/consultant_service/?t=1');
                exit;
            case '/consultant/_px0_fwnr2/':
                header('Location:/consultant_service/?t=2');
                exit;
            case '/consultant/_px0_fwnr3/':
                header('Location:/consultant_service/?t=4');
                exit;
            case '/consultant/_px0_fwnr4/':
                header('Location:/consultant_service/?t=3');
                exit;
            case '/consultant/_px0_fwnr5/':
                header('Location:/consultant_service/?t=5');
                exit;
            case '/consultant/_px0_fwnr6/':
                header('Location:/consultant_service/?t=6');
                exit;
        }
        header('Location:/consultant_service/');
    }
    
    //课程
    public function Course(){
        switch($_SERVER['REQUEST_URI']){
            case '/teacher/_px0_pxnr1/':
                header('Location:/teacher_course/?t=1');
                exit;
            case '/teacher/_px0_pxnr2/':
                header('Location:/teacher_course/?t=2');
                exit;
            case '/teacher/_px0_pxnr3/':
                header('Location:/teacher_course/?t=3');
                exit;
            case '/teacher/_px0_pxnr4/':
                header('Location:/teacher_course/?t=6');
                exit;
            case '/teacher/_px0_pxnr5/':
                header('Location:/teacher_course/?t=5');
                exit;
            case '/teacher/_px0_pxnr6/':
                header('Location:/teacher_course/?t=4');
                exit;
            case '/teacher/_px0_pxnr7/':
                header('Location:/teacher_course/?t=7');
                exit;                
        }
        header('Location:/teacher_course/');        
    }
}
