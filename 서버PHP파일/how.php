<?php
    header("Content-Type: text/html; charset=UTF-8");
    
    $conn = mysqli_connect('db-4ha7m.cdb.ntruss.com','sweetboys','whdgk123!','main_db');
    mysqli_query($conn,"set names utf8");
    mysqli_set_charset($conn,"utf8");
    $userid = $_POST['userid'];
    $latitude = (double)$_POST['latitude'];
    $longtitude = (double)$_POST['longtitude'];
    $address = $_POST['address'];
    $space = $address;
    $title = "알림";
    $message = $space."이곳이 궁금해요!";
    $token = $_POST['token'];
    $sql = "insert into howaboutthere(userid,latitude,longtitude,address) values('$userid','$latitude','$longtitude','$address')";
    mysqli_query($conn,$sql);

    //echo "아이디 :$userid \n";
    //echo "위도 : $latitude \n";
    //echo "경도 : $longtitude \n";
    $sql2 = "select tokenID From member_data where member_data.latitude>='$latitude'-0.0005 AND member_data.latitude<='$latitude'+0.0005 AND member_data.longitude>='$longtitude'-0.0005 AND member_data.longitude<='$longtitude'+0.0005 ";
    $result = mysqli_query($conn,$sql2);
    $tokens = array();
    if(mysqli_num_rows($result) > 0 ){
        while ($row = mysqli_fetch_assoc($result)) {
            $tokens[] = $row['tokenID'];
        }
    } else {
        echo '근처에 있는 사람이 없어요!';
        exit;
    }
    $arr = array();
    $arr['title'] = $title;
    $arr['message'] = $message;
    $arr['token'] = $token;
    $arr['a']='1';
    $message_status = Android_Push($tokens, $arr);
    //echo $message_status;
    // 푸시 전송 결과 반환.
    $obj = json_decode($message_status);

    // 푸시 전송시 성공 수량 반환.
    $cnt = $obj->{"success"};
    echo "요청이 완료되었습니다.";
    mysqli_close($conn);

    function Android_Push($tokens, $message) {

        $url = 'https://fcm.googleapis.com/fcm/send';
        $apiKey = "AAAAciSKBv4:APA91bES2l7TZUJHD3lxAHip4cfO5U-HM4Zcc2-ETHfUrRa_kLdMW2lw2AiZze9MiIQs4_Jr8rQyBzNc2b0rBTvusCPHdyZ9mIRdXvawHrSRK5Zv_XwTj1zO9Q3fppImlMCh4pvrQsnx";
    
        $fields = array('registration_ids' => $tokens,'data' => $message);
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