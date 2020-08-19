package com.example.testrightnow;


import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Build;
import android.util.Log;

import androidx.core.app.NotificationCompat;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;




//서버에서 알림을 받았을 때 사용되는 class입니다.
public class MyFireBaseMessagingService extends FirebaseMessagingService{
    private static final String TAG = "FCM";

    @Override
    public void onNewToken(String s){
        super.onNewToken(s);
        Log.d(TAG,"token["+s+"]");
    }
    //알람을 받았을때 서버에서 title은 알림의 제목, message는 해당 알림의 내용이며 token은 요청했을때나 사진을보냈을때 알람을 받을 사람을 지정하기위함이며, a는 이 알람이 사진을 보내야하는지 또는 사진을 봐야하는지를
    //판단하기 위한 변수입니다.
    @Override
    public void onMessageReceived(RemoteMessage remoteMessage){
        String title = remoteMessage.getData().get("title");
        String message = remoteMessage.getData().get("message");
        String token = remoteMessage.getData().get("token");
        String a=remoteMessage.getData().get("a");
        Integer b = Integer.parseInt(a);
        //b는 a를 정수로 변환한 값입니다.
        Log.d(TAG,"From: "+ remoteMessage.getFrom());
        Log.d(TAG,"Title: "+title);
        Log.d(TAG,"Message: "+message);
        Log.d(TAG,"token: "+ token);
        //b를 통하여 b가 1라면 CameraActivity로 이동하는 알람을 보내게 될것이고 b가 0이라면 사진을 확인하는 알람을 보내게 될것입니다.
        if(b==1){
            NotificationHelper notificationHelper = new NotificationHelper(getApplicationContext());
            notificationHelper.createNotification(title,message,token);
        }else{
            NotificationHelper notificationHelper = new NotificationHelper(getApplicationContext());
            notificationHelper.createNotification2(title,message,token);
        }

    }
}

