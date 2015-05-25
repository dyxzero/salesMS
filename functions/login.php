<?php
session_start();
include '../include_fns.php';
//检测用户是否登录
checkSessionUsername();
if(checkPost($_POST)){
	$conn = db_connect();
	//防止对数据库注入式攻击，对获取变量进行处理
	$username = $conn->real_escape_string(trim($_POST['username']));
	$password = $conn->real_escape_string(trim($_POST['password']));
	if(checkUsername($username)){
		//如果用户名存在，则检测用户名和密码是否能匹配正确
		$query = "select * from accountlogin
				where username = '".$username."'
				and password = '".md5($password)."'";
		$result = $conn->query($query);
		if($array = $result->fetch_assoc()){
			//如果登录成功，获取数据库中的员工信息，并分别存入到session变量中
			$_SESSION['username'] = $array['username'];
			$query = "select * from account
					where accountId = ".$array['accountId'];
			$result = $conn->query($query);
			$array = $result->fetch_assoc();
			$_SESSION['name'] = $array['name'];
			$_SESSION['accountId'] = $array['accountId'];
			$_SESSION['position'] = $array['position'];
			$conn->close();
			printf("sucessfull.");
			//五秒自动跳转
			header( "refresh:5;url=../index.php" ); 
	  		printf('You\'ll be redirected in about 5 secs. If not, click <a href="../login.php">here</a>.');
		}else{
			//如果错误，提示密码错误
			$conn->close();
			header("Location:../login.php?login=2");
		}
	}else{
		//提示用户名不存在
		$conn->close();
		header("Location:../login.php?login=1");
	}
}else{
	//提示请输入完整信息
	header("Location:../login.php?login=0");
}
?>