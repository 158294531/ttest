<?php
header("Content-type: text/html; charset=utf-8"); 

$fsize = $_POST['size'];//文件大小
$findex =$_POST['index'];//片数
$ftotal =$_POST['countIndex'];//总的分片数
$ftype = $_POST['type'];//文件类型
$fdata = $_FILES['file'];
$fname = mb_convert_encoding($_POST['name'],"gbk","utf-8");
$truename = mb_convert_encoding($_POST['trueName'],"gbk","utf-8");
$path='../../source/'; 
$dir=$path.$truename.'-'.$fsize;
$save = $dir."/".$fname;
//创建目录
if(!is_dir($dir)){
  mkdir($dir);
  chmod($dir,0777);
}

/**
//处理上传文件
$temp=$fdata['tmp_name'];
if(is_uploaded_file($temp)){
  move_uploaded_file($temp,$dir.'/'.$findex.'.tmp');

  //判断文件片数等于文件总数时 然后进行合并
  if($findex+1==$ftotal){
    if(file_exists($save)) @unlink($save);//如果源文件相同 则删除
    for ($i=0; $i < $ftotal; $i++) { 
      //读取临时文件
      $handle=fopen($dir.'/'.$i.'.tmp','r+');//读写方式打开
      $writeData=fread($handle,filesize($dir.'/'.$i.'.tmp'));//读取文件大小
      
      $newFile=fopen($save,'a+');
      fwrite($newFile,$writeData);//写入程序
      fclose($newFile);//释放资源
      unset($newFile);
      fclose($handle);//释放资源
      unset($handle);
      @unlink($dir.'/'.$i.'.tmp');//删除临时文件

    }
  }
}
**/


//读取临时文件内容
$temp = fopen($fdata["tmp_name"],"r+");
$filedata = fread($temp,filesize($fdata["tmp_name"]));
//将分段内容存放到新建的临时文件里面
if(file_exists($dir."/".$findex.".tmp")) unlink($dir."/".$findex.".tmp");
$tempFile = fopen($dir."/".$findex.".tmp","w+");
fwrite($tempFile,$filedata);
fclose($tempFile);

fclose($temp);

if($findex+1==$ftotal)
{
    if(file_exists($save)) @unlink($save);
    //循环读取临时文件并将其合并置入新文件里面
    for($i=0;$i<$ftotal;$i++)
    {
        $readData = fopen($dir."/".$i.".tmp","r+");
        $size=filesize($dir."/".$i.".tmp");
        if(!$size) continue;
        $writeData = fread($readData,$size);

        $newFile = fopen($save,"a+");
        fwrite($newFile,$writeData);
        fclose($newFile);
        
        fclose($readData);

        $resu = @unlink($dir."/".$i.".tmp"); 
    }
    $res = array("res"=>"success","url"=>mb_convert_encoding($truename."-".$fsize."/".$fname,'utf-8','gbk'));
    echo json_encode($res);
}































?>