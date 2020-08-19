<?php
    header("Content-Type:text/html; charset=UTF-8");
    header("Content-Type: image/png; charset=UTF-8");
    header("Content-Type: application/octet-stream; charset=UTF-8");
    //언어를 utf-8로 설정부분

    //여기 밑에 $file부터 $result까지는 신경안써도돼 선언만하고 안써가지고 여기서 에러는 안났을꺼야
    $file = $_FILES['files'];
    $tok = $_POST['token'];

    $file_name = $_FILES['files']['name'];
    $tmp_file = $_FILES['files']['tmp_name'];

    $srcName = $file['files']['name'];
    $tmpName = $file['tmp_name'];//php파일을 받으면 임시저장소에 넣는다. 그곳이 tmp

    //임시 저장소 이미지를 원하는 폴더로 이동
    $dstName ='uploads/'.$file_name;
    $result = move_uploaded_file($tmp_file,$dstName);
    //바로 밑에 if절은 변수에다가 저장따로안하고 바로 $_FILES로 불러와서 위에 변수를 안썼어
    if($_FILES['files']['error']>0)echo "Error raised";//애뮬에서 뜬 에러
    else{
        echo "FIle type : ".$_FILES['files']['type']." \n";
        $fileaddr = "uploads/" . $_FILES['files']['name'];//파일이 저장되는 경로
        if(is_uploaded_file($_FILES['files']['tmp_name']))
        {
            if(!move_uploaded_file($_FILES['files']['tmp_name'],$fileaddr))//사진을 지정된 경로로 보내는 함수 성공하면 1반환 실패하면 0반환 나는 여기서 에러가뜸 조건에 !가 붙어있으니까 0을 반환했다는건데 왜 서버에 이미지가 보내졌는지는 모르겠어
            echo "Error raised from file moving process\n";
        }
        else echo "File upload failed.";
    }
    /*if($result){
        echo "upload success\n";
    }else{
        echo "upload fail\n";
        echo $result;
    }*/
    
    //echo "$dstName\n";
    $now = date('Y-m-d H:i:s');
    $con = mysqli_connect('db-4ha7m.cdb.ntruss.com','sweetboys','whdgk123!','main_db');
    //$conn = mysqli_connect("db-4ha7m.cdb.ntruss.com","sweetboys","whdgk123!","image");
    mysqli_query($con,"set names utf8");
    $sql="insert into image(imgPath,date,tokenID) values('$dstName','$now','$tok')";
    $result = mysqli_query($con,$sql);
    if($result) echo "insert success \n";
    else echo "insert fail \n";
    mysqli_close($con);
    ////////////////////////////////////////////////////////////
  



    $title = "알림";
    $message = "사진이 도착했습니다 확인해보세요";
  
    //echo "아이디 :$userid \n";
    //echo "위도 : $latitude \n";
    //echo "경도 : $longtitude \n";
    
    
    //$imgPath =
    $arr = array();
    $arr['title'] = $title;
    $arr['message'] = $message;
    $arr['a']='0';
    $token123 = array();
    $token123[0]=$tok;
    $message_status = Android_Push($token123, $arr);
    //echo $message_status;
    // 푸시 전송 결과 반환.
    $obj = json_decode($message_status);

    // 푸시 전송시 성공 수량 반환.
    $cnt = $obj->{"success"};
    echo "요청이 완료되었습니다.";
    mysqli_close($conn);

    function Android_Push($tok, $message) {

        $url = 'https://fcm.googleapis.com/fcm/send';
        $apiKey = "AAAAciSKBv4:APA91bES2l7TZUJHD3lxAHip4cfO5U-HM4Zcc2-ETHfUrRa_kLdMW2lw2AiZze9MiIQs4_Jr8rQyBzNc2b0rBTvusCPHdyZ9mIRdXvawHrSRK5Zv_XwTj1zO9Q3fppImlMCh4pvrQsnx";
    
        $fields = array('registration_ids' => $tok,'data' => $message);
        $headers = array('Authorization:key='.$apiKey,'Content-Type: application/json');
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
    
    
        return $result;
    }
    
?>

    