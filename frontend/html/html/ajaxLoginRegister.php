<?php
include 'credentials.php';
//echo "<script>alert( 'mysql user : ' . $usermysql . ', password : ' . $passmysql );</script>";

error_reporting(E_ALL);
session_start();
// check user
if(isSet($_GET['action']) && $_GET['action']=='checkuser')
{
	checkuser($_GET['username']);
}
else if(isSet($_GET['action']) && $_GET['action']=='checkemail')
{
	checkemail($_GET['email']);
}
// login form (only used action at the moment) 
else if(isSet($_POST['action']) && $_POST['action']=='login')
{
	login($_POST['username'],$_POST['password'],$usermysql,$passmysql);
}
else if (isSet($_POST['action']) && $_POST['action']=='signup')
{
	register($_POST['username'],$_POST['password'],$_POST['email'],$_POST['firstname'],$_POST['lastname']);
}


// functions
function checkuser($name){
    $db = mysqli_connect('localhost','root','neoveille','rssdata');
    if (!$db) {
    	echo("Erreur de connexion : " . mysqli_connect_error());
    	die;
	}
    $q = "SELECT * FROM users WHERE username='".$name."'";
    //echo($q);
    $result = mysqli_query($db, $q);
    if ( false===$result ) {
  		printf("error: %s\n", mysqli_error($db));
  		die;
	}
    $num = mysqli_num_rows($result);

	if ($num > 0) {
		echo("Already used");
		http_response_code(404);
	}
	else
	{
		echo("OK");
		http_response_code(200);
	}
}

function checkemail($name){
    $db = mysqli_connect('localhost','root','neoveille','rssdata');
    if (!$db) {
    	echo("Erreur de connexion : " . mysqli_connect_error());
    	die;
	}
    $q = "SELECT * FROM users WHERE email='".$name."'";
    //echo($q);
    $result = mysqli_query($db, $q);
    if ( false===$result ) {
  		printf("error: %s\n", mysqli_error($db));
  		die;
	}
    $num = mysqli_num_rows($result);

	if ($num > 0) {
		echo("Already used");
		http_response_code(404);
	}
	else
	{
		echo("OK");
		http_response_code(200);
	}
}

// check $user and $password exist in database/table and put info in global session variable
function login($user,$password,$usermysql,$passmysql){

	$db = mysqli_connect('localhost',$usermysql,$passmysql,'rssdata');
	if (!$db) {
		echo "<script>alert( 'erreur de connexion : ' . mysqli_connect_error() . '. Contactez l administrateur.' );</script>";
    	die;
	}
	$q = "SELECT uid, username, user_rights, language FROM users WHERE username='$user' and password='$password'";
	$result=mysqli_query($db,$q);
	if ( false===$result ) {
  		printf("error: %s\n", mysqli_error($db));
  		die;
	}
    $count = mysqli_num_rows($result);
    $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
	if($count==1)	
	{
		$_SESSION['login_user']=$row['uid'];
		$_SESSION['user']=$row['username'];
		$_SESSION['user_rights']=$row['user_rights'];
		echo "<script>console.log( 'LANGUAGE  : " . $row['language'] . "' );</script>";
		$langcode = get_country_code($row['language'],$usermysql,$passmysql);
		$_SESSION['language']=$langcode;
		echo $row['uid'];
	}
}

// retrieve country code and return it to calling function
function get_country_code($lang,$usermysql,$passmysql){

        $db = mysqli_connect('localhost',$usermysql,$passmysql,'rssdata');
        if (!$db) {
        echo("Erreur de connexion : " . mysqli_connect_error());
        die;
        }
        $q = "SELECT CODE_LANGUE FROM RSS_LANGUE WHERE ID_LANGUE='$lang'";
        $result=mysqli_query($db,$q);
        if ( false===$result ) {
                printf("error: %s\n", mysqli_error($db));
                die;
        }
    $count = mysqli_num_rows($result);
    $row=mysqli_fetch_array($result,MYSQLI_ASSOC);
        if($count==1)   
        {
                return $row['CODE_LANGUE'];
        }
}

function register($uname,$upass,$email,$firstname,$lastname){       
    $db = mysqli_connect('localhost','root','neoveille','rssdata');
    if (!$db) {
    	echo("Erreur de connexion : " . mysqli_connect_error());
    	die;
	}
   	$stmt=mysqli_prepare($db,"INSERT INTO users(username,email,password,firstname,lastname) 
	  	VALUES(?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt,"sssss", $uname, $email, $upass, $firstname, $lastname);
    $res = mysqli_stmt_execute($stmt);
	if ($res) {
    	echo "Enregistrement réussi. Vous pouvez vous connecter";
	} 
	else {
    	echo "Error: " . $stmt . "<br>" . mysqli_error($db);
	}
  mysqli_close($db);
 }

function send_mail($email,$message,$subject){      
  require_once('mailer/class.phpmailer.php');
  $mail = new PHPMailer();
  $mail->IsSMTP(); 
  $mail->SMTPDebug  = 0;                     
  $mail->SMTPAuth   = true;                  
  $mail->SMTPSecure = "ssl";                 
  $mail->Host       = "smtp.gmail.com";      
  $mail->Port       = 465;             
  $mail->AddAddress($email);
  $mail->Username="ecartierdijon@gmail.com";  
  $mail->Password="dijon200";            
  $mail->SetFrom('admin@neoveille.org','Néoveille');
  $mail->AddReplyTo("admin@neoveille.org","Néoveille");
  $mail->Subject    = $subject;
  $mail->MsgHTML($message);
  $mail->Send();
 } 
?>
