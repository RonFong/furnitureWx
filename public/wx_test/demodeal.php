<?php  
    error_reporting(0);  
    if(isset($_POST) && !empty($_POST)){  
  
        $localData = $_POST['localData'];  
        $url = explode(',',$localData);  
        $targetName = "./demoUploads/".date("YmdHis").rand(1000,9999).".jpg";  
//        $fp = fopen("./log.text", "w+");  
//        fwrite($fp, $localData);  
//        fclose($fp);  
  
        file_put_contents($targetName, base64_decode($url[1]));//返回的是字节数  
        if(file_exists($targetName)){  
            echo json_encode(['code'=>'0001','localData' => $localData],JSON_UNESCAPED_UNICODE);  
            exit(0);  
        }else{  
            echo json_encode(['code'=>'0002' ,'localData'=>$localData],JSON_UNESCAPED_UNICODE);  
            exit(0);  
        }  
    }  
