<?php

  header('Access-Control-Allow-Origin: *');
  header("Access-Control-Allow-Credentials: true");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
  header("Content-Type: application/json; charset=utf-8");
  header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');
  include "library/config.php";
  
  $postjson = json_decode(file_get_contents('php://input'), true);
  date_default_timezone_set('Asia/Manila');
  $today    = date('Y-m-d h:i:s');


  if($postjson['aksi']=="login"){
    $password = md5($postjson['password']);
    $username = $postjson['username'];

    $query = mysqli_query($mysqli, "SELECT * FROM users WHERE username='$username'");
    $check = mysqli_num_rows($query);

    if($check>0){
      $data = mysqli_fetch_array($query);
      $datauser = array(
        'id' => $data['id'],
        'name' => $data['name']
      );

      $querys = mysqli_query($mysqli, "SELECT * FROM users WHERE `password`='$password'");
      $check_pass = mysqli_num_rows($querys);

      if($check_pass>0){
          if($data['status']=='approved'){
            $result = json_encode(array('success'=>true, 'result'=>$datauser));
          }else{
            $result = json_encode(array('success'=>false, 'msg'=>'Account Inactive')); 
          }
      }else{
        $result = json_encode(array('success'=>false, 'msg'=>'Wrong Password!'));
      }
    }else{
      $result = json_encode(array('success'=>false, 'msg'=>'Unregister Account'));
    }

    echo $result;
  }

  elseif($postjson['aksi']=="register"){
    $password = md5($postjson['password']);
    $name = $postjson['fullname'];
    $username = $postjson['username'];
    $email = $postjson['email_add'];
    $address = $postjson['address'];
    $contact_number = $postjson['contact_number'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $result = json_encode(array('warning'=>false, 'msg'=>'Invalid Email'));
    }else{
      $validation = mysqli_query($mysqli,"SELECT * FROM users WHERE email='$email' OR username='$username' ");
      $check = mysqli_num_rows($validation);

      if($check>0){
        $result = json_encode(array('warning'=>false, 'msg'=>'E-mail/username is already exist ! '));
      }else{
        $query = mysqli_query($mysqli, "INSERT INTO users SET
          username = '$username',
          `name` = '$name',
          `password` = '$password',
          email = '$email',
          `contact_number` = '$contact_number',
          `address` = '$address',
          `status`   = 'pending',
          created_at = '$today',
          updated_at = '$today'
        ");
        if($query) $result = json_encode(array('success'=>true));
        else $result = json_encode(array('success'=>false, 'msg'=>'error, please try again'));
      }
    }

    echo $result;
  }

  elseif($postjson['aksi']=="getEstablishments"){
    $data = array();
  	$query = mysqli_query($mysqli, "SELECT * FROM establishments ORDER BY id ASC");

  	while($row = mysqli_fetch_array($query)){

  		$data[] = array(
  			'id' => $row['id'],
  			'name' => $row['name'],
  			'icon' => $row['icon'],
  			'created_at' => $row['created_at'],

  		);
  	}

  	if($query) $result = json_encode(array('success'=>true, 'result'=>$data));
  	else $result = json_encode(array('success'=>false));

  	echo $result;

  }

  elseif($postjson['aksi']=="getProducts"){


    $data = array();

    $content = file_get_contents('http://192.168.1.85:8900/api/products');
    $content = json_decode($content,true);	
    $result = json_encode(array('result'=>$content));

    echo $result;


  }

  elseif($postjson['aksi']=="getDiscountedProducts"){
    $data = array();

    $content = file_get_contents('http://192.168.1.85:8900/api/discounted_products');
    $content = json_decode($content,true);	
    $result = json_encode(array('result'=>$content));

    echo $result;
  }

  function imagePath($path,$baseUrl)
  {
      // $path = destination/filename
      if(file_exists($path.'.png')) {
          return asset($path.'.png');
      } else if(file_exists($path.'.jpg')) {
          return asset($path.'.jpg');
      } else if(file_exists($path.'.jpeg')) {
          return asset($path.'.jpeg');
      } else if(file_exists($path.'.gif')) {
          return asset($path.'.gif');
      } else{
          return $baseUrl;
      }
  }

  function isExistFile($filepath){
      $path = $filepath;
      $isExist = false;
      if(file_exists($path.'.png'))
      {
          $isExist = true;
          $path =$path.'.png';
      }
      else if(file_exists($path.'.jpg'))
      {
          $isExist = true;
          $path =$path.'.jpg';
      }
      else if(file_exists($path.'.jpeg'))
      {
          $isExist = true;
          $path =$path.'.jpeg';
      }
      return $response = array(
          'is_exist' =>$isExist,
          'path' => $path
      );

  }


?>