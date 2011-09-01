<?
session_start();

//		PHP Pass Copyright 2005 Howard Yeend
//		www.puremango.co.uk
//      GPL Licensed

//--------------------------
// user definable variables:
//--------------------------

// maximum number of seconds user can remain idle without having to re-login:
// use a value of zero for no timeout
$max_session_time = 28800;

// type of alert to give on incorrect password:
// eg:
// $alert = "joe@foo.com";	- sends email to joe@foo.com
// $alert = "blah";		- appends to file named 'blah'
// $alert = "";			- no alerts

// acceptable passwords:
$cmp_pass = Array();
// replace this with an sha256 encoded password
$cmp_pass[] = hash('sha256', 'password');
// add as many as you like

// maximum number of bad logins before user locked out
// use a value of zero for no hammering protection
$max_attempts = 5;

//-----------------------------
// end user definable variables
//-----------------------------


// save session expiry time for later comparision
$session_expires = $_SESSION['mpass_session_expires'];

// have to do this otherwise max_attempts is actually one less than what you specify.
$max_attempts++;

if(!empty($_POST['mpass_pass']))
{
	// store sha'ed password
	$_SESSION['mpass_pass'] = hash('sha256', $_POST['mpass_pass']);
}

if(empty($_SESSION['mpass_attempts']))
{
	$_SESSION['mpass_attempts'] = 0;
}

// if the session has expired, or the password is incorrect, show login page:
if(($max_session_time>0 && !empty($session_expires) && mktime()>$session_expires) || empty($_SESSION['mpass_pass']) || !in_array($_SESSION['mpass_pass'],$cmp_pass))
{
	if(!empty($alert) && !in_array($_SESSION['mpass_pass'],$cmp_pass))
	{
		// user has submitted incorrect password
		// generate alert:

		$_SESSION['mpass_attempts']++;
		
		$alert_str = $_SERVER['REMOTE_ADDR']." entered ".htmlspecialchars($_POST['mpass_pass'])." on page ".$_SERVER['PHP_SELF']." on ".date("l dS of F Y h:i:s A")."\r\n";
		
		if(stristr($alert,"@")!==false)
		{
			// email alert
			@mail($alert,"Bad Login on ".$_SERVER['PHP_SELF'],$alert_str,"From: ".$alert);
		} else {
			// textfile alert
			$handle = @fopen($alert,'a');
			if($handle)
			{
				fwrite($handle,$alert_str);
				fclose($handle);
			}
		}
	}
	// if hammering protection is enabled, lock user out if they've reached the maximum
	if($max_attempts>1 && $_SESSION['mpass_attempts']>=$max_attempts)
	{
		exit("Too many login failures.");
	}


	// clear session expiry time
	$_SESSION['mpass_session_expires'] = "";

	?>
<!DOCTYPE HTML>
<html lang=en>
<head>
<meta charset=utf-8>
<title>Notes</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<header>
<h2>Notes</h2>
</header>
<p><center>
Please log in to continue:<br>
<form action="." method="post">
<input type="password" name="mpass_pass" autofocus>
<input type="submit" value="Login"></center></p>
</form>
</body>
</html>
	<?

	// and exit
	exit();
}

// if they've got this far, they've entered the correct password:

// reset attempts
$_SESSION['mpass_attempts'] = 0;

// update session expiry time
$_SESSION['mpass_session_expires'] = mktime()+$max_session_time;

// end password protection code

// set the path from which the script is running
$path = getcwd();

?>
<!DOCTYPE HTML>
<html lang=en>
<head>
<meta charset=utf-8>
<title>Trey Deitch: Notes</title>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
</head>
<body>
<header>
<h2><a href=".">Notes</a></h2>
</header>
<?php
if(isset($_POST["notes"]) && !empty($_POST["filename"]) && !isset($_POST["delete"])){
  $filename = str_replace(' ','_',$_POST["filename"]);
  if (substr(realpath($filename),0,strlen($path)) !== $path) {
    echo "<div id='alert'>Can't write to other directories</div><p>";
    echo "<script>$('#alert').fadeOut(2000);</script>";
    $filename = '';
    $content = $_POST["notes"];
  }
  else{
    $file = fopen($filename,'w');
    if(fwrite($file,$_POST["notes"])){ 
    echo "<div id='alert'>Note saved</div><p>";
    echo "<script>$('#alert').fadeOut(2000);</script>";
    }
    fclose($file);
    $file = fopen($filename,'r');
    $content = fread($file,filesize($filename));
    fclose($file);
  }
}
if(isset($_POST["notes"]) && empty($_POST["filename"]) && !isset($_POST["delete"])){
  echo "<div id='alert'>No Filename</div><p>";
  echo "<script>$('#alert').fadeOut(2000);</script>";
  $content = $_POST["notes"];
}
if(!isset($_POST["notes"]) && isset($_POST["filename"]) && !isset($_POST["delete"])){
  $filename = $_POST["filename"];
  if (substr(realpath($filename),0,strlen($path)) !== $path) {
    echo "<div id='alert'>Can't read from other directories</div><p>";
    echo "<script>$('#alert').fadeOut(2000);</script>";
    $filename = '';
  }
  else{
    $file = fopen($filename,'r');
    $content = fread($file,filesize($filename));
    fclose($file);
  }
}
if(!isset($_POST["notes"]) && !isset($_POST["filename"]) && isset($_POST["delete"])){
  $to_delete = $_POST["delete"];
  if (substr(realpath($to_delete),0,strlen($path)) !== $path) {
    echo "<div id='alert'>Can't delete from other directories</div><p>";
    echo "<script>$('#alert').fadeOut(2000);</script>";
  }
  else{
    if (unlink($to_delete)){
      echo "<div id='alert'>Note deleted</div><p>";
      echo "<script>$('#alert').fadeOut(2000);</script>";
    }
  }
}
?>
<p>
<div id="buttons"></div>
<script>
$('#buttons').append('<button style="border:none; background:transparent; cursor:pointer;" id="hide">▾ Hide files</button><button style="border:none; background:transparent; cursor:pointer; display:none;" id="show">‣ Show files</button>');
</script>
<div id="files">
<?php
$files = scandir('.', 1);
foreach (array_reverse($files, true) as $f){
  if($f !== ".." && $f !== "."  && $f !== "index.php" && $f !== "style.css"){
    echo "<form name='".$f."' action='.' method='post'>";
    echo "<input type='hidden' value='".$f."' name='filename'>";
    echo "</form>";
    echo "<form name='".$f."delete' action='.' method='post'>";
    echo "<input type='hidden' value='".$f."' name='delete'>";
    echo "</form>";
    echo "<a href='#' onClick='document.".$f.".submit()'>".htmlentities(str_replace('_',' ',$f))."</a> <a href='#' class='delete' title='delete this note' onClick='document.".$f."delete.submit()'>☒</a>";
}}
echo "</div><p>";
echo "<form action='.' method='post'>";

echo "<textarea name='filename' id='filename' cols='80' rows='1'>".htmlentities(str_replace('_',' ',$filename))."</textarea><br>";
echo "<textarea name='notes' id='notes' cols='80' rows='24'>".htmlentities($content)."</textarea><br>";
echo "<input type='submit' value='Save'>";
echo "</form>";
?>
<script>
$("#hide").hide();
$("#files").hide();
$("#show").show();
$("#hide").click(function () {
$("#hide").hide();
$("#files").hide();
$("#show").show();
});
$("#show").click(function () {
$("#show").hide();
$("#files").show();
$("#hide").show();
});
</script>
</body>
</html>