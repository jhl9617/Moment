package com.example.testrightnow;

import android.Manifest;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.os.StrictMode;
import android.provider.MediaStore;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.loader.content.CursorLoader;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.toolbox.Volley;

import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.BreakIterator;
import java.text.SimpleDateFormat;
import java.util.Date;

public class CameraActivity extends AppCompatActivity {

    ImageView imgVwSelected;
    Button btnImageSend, btnImageSelection;
    File tempSelectFile;
    String imgPath;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_camera);
        imgVwSelected = findViewById(R.id.imgVwSelected);

        //사진을 보냈을때 요청자에게 알람을 보내기위해 푸쉬알람을 통해 요청자의 토큰을 가지고 옵니다.
        Intent intent = getIntent();
        final String token = intent.getStringExtra("token");


        if(Build.VERSION.SDK_INT>=Build.VERSION_CODES.M){
            int permissionResult = checkSelfPermission(Manifest.permission.WRITE_EXTERNAL_STORAGE);
            if(permissionResult==PackageManager.PERMISSION_DENIED){
                String[] permissions = new String[]{Manifest.permission.WRITE_EXTERNAL_STORAGE};
                requestPermissions(permissions,10);
            }
        }

        //이미지를 전송하는 보내기에 해당하는 버튼
        btnImageSend = findViewById(R.id.btnImageSend);
        btnImageSend.setEnabled(false);
        btnImageSend.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view)
            {
                FileUploadUtils.send2Server(tempSelectFile,token);//사진파일과 요청자의 토큰을 서버로 보냅니다.
                Toast.makeText(getApplicationContext(),"send",Toast.LENGTH_SHORT).show();
            }
        });

        //이미지를 선택하는 버튼
        btnImageSelection = findViewById(R.id.btnImageSelection);
        btnImageSelection.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                //앨범에서 보낼사진을 선택합니다.
                Intent intent = new Intent(Intent.ACTION_PICK);
                intent.setType("image/*");
                startActivityForResult(intent, 10);
            }
        });

    }

    //외부 메모리 권한 허용
    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults){
        super.onRequestPermissionsResult(requestCode,permissions,grantResults);
        switch(requestCode){
            case 10:
                if(grantResults[0]==PackageManager.PERMISSION_GRANTED){
                    Toast.makeText(this,"외부 메모리 읽기/쓰기 사용 가능",Toast.LENGTH_SHORT).show();
                }else{
                    Toast.makeText(this,"외부 메모리 읽기/쓰기 제한",Toast.LENGTH_SHORT).show();
                }
                break;
        }
    }
    //앨범에서 이미지를 선택했을때 이미지 경로를 띄의고 이미지를 선택하지 않았을 때 이미지를 선택하지 않았다고 토스트 메세지가 뜹니다.
    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        switch (requestCode){
            case 10:
                if(resultCode==RESULT_OK){
                    Toast.makeText(this,"RESULT_OK",Toast.LENGTH_SHORT).show();
                    Uri uri = data.getData();
                    if(uri!=null){
                        imgVwSelected.setImageURI(uri);
                        imgPath = getRealPathFromUri(uri);

                        new AlertDialog.Builder(this).setMessage(uri.toString()+"\n"+"\n"+imgPath).create().show();
                    }
                }else{
                    Toast.makeText(this,"이미지를 선택하지 않았습니다.",Toast.LENGTH_SHORT).show();
                }
                tempSelectFile = new File(imgPath);
                btnImageSend.setEnabled(true);
                break;
        }

    }
    //이미지의 절대경로를 가져오는 함수
    String getRealPathFromUri(Uri uri){
        String[] proj = {MediaStore.Images.Media.DATA};
        CursorLoader loader = new CursorLoader(this,uri,proj,null,null,null);
        Cursor cursor = loader.loadInBackground();
        int column_index = cursor.getColumnIndexOrThrow(MediaStore.Images.Media.DATA);
        cursor.moveToFirst();
        String result = cursor.getString(column_index);
        cursor.close();
        return result;
    }

}













