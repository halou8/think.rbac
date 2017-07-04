<?php
// 测试写入文件
function testwrite($d){
    $tfile = 'cms.txt';
    $d = ereg_replace('/$','',$d);
    $fp = @fopen($d.'/'.$tfile,'w');
    if(!$fp){
        return false;
    }else{
        fclose($fp);
        $rs = @unlink($d.'/'.$tfile);
        if($rs){
            return true;
        }else{
            return false;
        }
    }
}
// 获取文件夹大小
function getdirsize($dir){
    $dirlist = opendir($dir);
    while (false !== ($folderorfile = readdir($dirlist))){
        if($folderorfile != "." && $folderorfile != "..") {
            if (is_dir("$dir/$folderorfile")) {
                $dirsize += getdirsize("$dir/$folderorfile");
            }else{
                $dirsize += filesize("$dir/$folderorfile");
            }
        }
    }
    closedir($dirlist);
    return $dirsize;
}
function getrexie($str){
    return str_replace('@@@','',str_replace('/@@@','',$str.'@@@'));
}
function getaddxie($str){
    return str_replace('@@@','',str_replace('//@@@','/',$str.'/@@@'));
}