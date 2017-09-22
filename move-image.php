<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/9/20
 * Time: 16:39
 */

set_time_limit(0);
global $source_dir;
global $target_dir;
$source_dir = "./House/";
$target_dir = './Finish/';

/*开始扫描文件夹*/
scan_dir($source_dir);
exit('finish');


function log_msg($msg){
    echo "$msg\r\n";
}

/**
 * 转移文件
 * @param $path_file
 * @return array
 */
function move_file($path_file){
    $path_file = rtrim($path_file,'/');
    if (!is_file($path_file)) return array('msg'=>'图片不存在','status'=>false);

    global $source_dir;
    global $target_dir;

    $file_name = substr($path_file,strrpos($path_file,'/')+1);
    $dir = substr($path_file,0,strrpos($path_file,'/')+1);
    $imginfo= getimagesize($path_file);
    $ext = strtolower(substr(end($imginfo),strrpos(end($imginfo),'/')+1));
    $new_dir = str_replace($source_dir,$target_dir,$dir);
    if (!is_dir($new_dir)){
        $rs = mkdir($new_dir,0777,true);
        if (!$rs) return array('msg'=>'创建文件夹失败','status'=>false);
    }
    /*新文件名*/
    $new_file_name = substr($file_name,0,strrpos($file_name,'.')).".jpg";
    $path_new_file = "{$new_dir}{$new_file_name}";
    switch ($ext){
        case "png":
            $file = imagecreatefrompng($path_file);
            imagejpeg($file,$path_new_file);
            break;
        case "gif":
            $file = imagecreatefromgif($path_file);
            imagejpeg($file,$path_new_file);
            break;
        case "jpg":
        case "jpeg":
        default:
            $file = imagecreatefromjpeg($path_file);
            imagejpeg($file,$path_new_file);
            break;
    }
    return array('msg'=>'success','status'=>true);
}


/**
 * 扫描文件夹
 * @param $path_dir
 */
function scan_dir($path_dir){
    if (!is_dir($path_dir)) return false;
    $rs = scandir($path_dir);
    foreach ($rs as $k => $v){
        if ($v === '.' || $v === '..') continue;
        $path = $path_dir.rtrim($v,'/');
        if (is_file($path)){
            $rs = move_file($path);
            if ($rs['status']){
                $rs = "finish";
            }else{
                $rs = $rs['msg'];
            }
            log_msg("{$path}---{$rs}");
            continue;
        }
        if (is_dir($path."/")) scan_dir($path."/");
    }
}