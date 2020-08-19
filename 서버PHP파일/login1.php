<?php
    header("Content-Type: text/html; charset=UTF-8");
    $con = mysqli_connect('db-4ha7m.cdb.ntruss.com','sweetboys','whdgk123!','main_db');
    mysqli_query($conn,"set names utf8");
    mysqli_set_charset($con,"utf8");
    $u_id = $_POST["u_id"];
    $u_pw = $_POST["u_pw"];
    $longtitude = $_POST["longtitude"];
    $latitude = $_POST["latitude"];
    $Token = $_POST["Token"];
    $address = $_POST["address"];
    $statement = mysqli_prepare($con,"select * from member_id where u_id=? and u_pw=?");
    mysqli_stmt_bind_param($statement,"ss",$u_id,$u_pw);
    mysqli_stmt_execute($statement);
    mysqli_stmt_store_result($statement);
    mysqli_stmt_bind_result($statement,$u_id,$u_pw);

    
    $sql = "select uid from member_id where u_id= '$u_id' and u_pw='$u_pw'";
    $result = mysqli_query($con,$sql);
    while($row = mysqli_fetch_array($result)){
        $memberuid =$row[uid];
    }
    mysqli_query($conn,"set names utf8");
    $sql1 = "insert into member_data(memberuid,tokenID,latitude,longitude,address) values ('$memberuid','$Token','$latitude','$longtitude','$address')";
    $sql2 = "update  member_data set tokenID='$Token', latitude='$latitude', longitude='$longtitude',address='$address' where memberuid='$memberuid'";
    mysqli_query($con,$sql1);
    mysqli_query($con,$sql2);
    

    $response = array();
    $response["success"]=false;

    while(mysqli_stmt_fetch($statement)){
        $response["success"]=true;
        $response["u_id"]=$u_id;
        $response["u_pw"]=$u_pw;
    }
    mysqli_close($con);
    echo json_encode($response);
    ?>
