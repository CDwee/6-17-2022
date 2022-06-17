<!-- Started at 11:19 6-17-2022 -->

<?php
	include("includes/config.php");
	include("includes/classes/Account.php");
	include("includes/classes/Constants.php");

	$account = new Account($con);

	include("includes/handlers/register-handler.php");
	include("includes/handlers/login-handler.php");

	function getInputValue($name) {
		if(isset($_POST[$name])) {
			echo $_POST[$name];
		}
	}
?>

<html>
<head>
	<title>Welcome to Slotify!</title>
	<link rel="stylesheet" type="text/css" href="assets/css/register.css">
</head>
<body>
	<div id="background">

		<div id="loginContainer">

			<div id="inputContainer">
				<form id="loginForm" action="register.php" method="POST">
					<h2>Login to your account</h2>
					<p>
						<?php echo $account->getError(Constants::$loginFailed); ?>
						<label for="loginUsername">Username</label>
						<input id="loginUsername" name="loginUsername" type="text" placeholder="e.g. bartSimpson" required>
					</p>
					<p>
						<label for="loginPassword">Password</label>
						<input id="loginPassword" name="loginPassword" type="password" placeholder="Your password" required>
					</p>

					<button type="submit" name="loginButton">LOG IN</button>

					<div class="hasAccountText">
						<span id="hideLogin">Don't have an account yet? Sign up here.</span>
					</div>
					
				</form>



				<form id="registerForm" action="register.php" method="POST">
					<h2>Create your free account</h2>
					<p>
						<?php echo $account->getError(Constants::$usernameCharacters); ?>
						<?php echo $account->getError(Constants::$usernameTaken); ?>
						<label for="username">Username</label>
						<input id="username" name="username" type="text" placeholder="e.g. bartSimpson" value="<?php getInputValue('username') ?>" required>
					</p>

					<p>
						<?php echo $account->getError(Constants::$firstNameCharacters); ?>
						<label for="firstName">First name</label>
						<input id="firstName" name="firstName" type="text" placeholder="e.g. Bart" value="<?php getInputValue('firstName') ?>" required>
					</p>

					<p>
						<?php echo $account->getError(Constants::$lastNameCharacters); ?>
						<label for="lastName">Last name</label>
						<input id="lastName" name="lastName" type="text" placeholder="e.g. Simpson" value="<?php getInputValue('lastName') ?>" required>
					</p>

					<p>
						<?php echo $account->getError(Constants::$emailsDoNotMatch); ?>
						<?php echo $account->getError(Constants::$emailInvalid); ?>
						<?php echo $account->getError(Constants::$emailTaken); ?>
						<label for="email">Email</label>
						<input id="email" name="email" type="email" placeholder="e.g. bart@gmail.com" value="<?php getInputValue('email') ?>" required>
					</p>

					<p>
						<label for="email2">Confirm email</label>
						<input id="email2" name="email2" type="email" placeholder="e.g. bart@gmail.com" value="<?php getInputValue('email2') ?>" required>
					</p>

					<p>
						<?php echo $account->getError(Constants::$passwordsDoNoMatch); ?>
						<?php echo $account->getError(Constants::$passwordNotAlphanumeric); ?>
						<?php echo $account->getError(Constants::$passwordCharacters); ?>
						<label for="password">Password</label>
						<input id="password" name="password" type="password" placeholder="Your password" required>
					</p>

					<p>
						<label for="password2">Confirm password</label>
						<input id="password2" name="password2" type="password" placeholder="Your password" required>
					</p>

					<button type="submit" name="registerButton">SIGN UP</button>

					<div class="hasAccountText">
						<span id="hideRegister">Already have an account? Log in here.</span>
					</div>
					
				</form>


			</div>
		</div>
	</div>

</body>
</html>

<?php
include("includes/config.php");

// session_destroy();

if(isset($_SESSION['userLoggedIn'])) {
    $userLoggedIn = $_SESSION['userLoggedIn'];
}
else {
    header("Location: register.php");
}

?>

<html>
<head>
    <title>Welcome to Slotify!</title>
</head>

<body>
    Hello!
</body>

</html>

<?php
	ob_start();
	session_start();

	$timezone = date_default_timezone_set("America/Chicago");

	$con = mysqli_connect("localhost", "root", "", "slotify");

	if(mysqli_connect_errno()) {
		echo "Failed to connect: " . mysqli_connect_errno();
	}
?>

<?php 

function sanitizeFormPassword($inputText) {
	$inputText = strip_tags($inputText);
	return $inputText;
}

function sanitizeFormUsername($inputText) {
	$inputText = strip_tags($inputText);
	$inputText = str_replace(" ", "", $inputText);
	return $inputText;
}

function sanitizeFormString($inputText) {
	$inputText = strip_tags($inputText);
	$inputText = str_replace(" ", "", $inputText);
	$inputText = ucfirst(strtolower($inputText));
	return $inputText;
}


if(isset($_POST['registerButton'])) {
	//Register button was pressed
	$username = sanitizeFormUsername($_POST['username']);
	$firstName = sanitizeFormString($_POST['firstName']);
	$lastName = sanitizeFormString($_POST['lastName']);
	$email = sanitizeFormString($_POST['email']);
	$email2 = sanitizeFormString($_POST['email2']);
	$password = sanitizeFormPassword($_POST['password']);
	$password2 = sanitizeFormPassword($_POST['password2']);

	$wasSuccessful = $account->register($username, $firstName, $lastName, $email, $email2, $password, $password2);

	if($wasSuccessful == true) {
		$_SESSION['userLoggedIn'] = $username;
		header("Location: index.php");
	}

}


?>

<?php
if(isset($_POST['loginButton'])) {
  //Login button was pressed
  $username = $_POST['loginUsername'];
  $password = $_POST['loginPassword'];

  $result = $account->login($username, $password);

  if($result == true) {
    $_SESSION['userLoggedIn'] = $username;
    header("Location: index.php");
  }

}
?>

<?php
class Constants {

	public static $passwordsDoNoMatch = "Your passwords don't match";
	public static $passwordNotAlphanumeric = "Your password can only contain numbers and letters";
	public static $passwordCharacters = "Your password must be between 5 and 30 characters";
	public static $emailInvalid = "Email is invalid";
	public static $emailsDoNotMatch = "Your emails don't match";
	public static $emailTaken = "This email is already in use";
	public static $lastNameCharacters = "Your last name must be between 2 and 25 characters";
	public static $firstNameCharacters = "Your first name must be between 2 and 25 characters";
	public static $usernameCharacters = "Your username must be between 5 and 25 characters";
	public static $usernameTaken = "This username already exists";

	public static $loginFailed = "Your username or password was incorrect";

}
?>

<?php
	class Account {

		private $con;
		private $errorArray;

		public function __construct($con) {
			$this->con = $con;
			$this->errorArray = array();
		}

		public function login($un, $pw) {

			$pw = md5($pw);

			$query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$un' AND password='$pw'");

			if(mysqli_num_rows($query) == 1) {
				return true;
			}
			else {
				array_push($this->errorArray, Constants::$loginFailed);
				return false;
			}

		}

		public function register($un, $fn, $ln, $em, $em2, $pw, $pw2) {
			$this->validateUsername($un);
			$this->validateFirstName($fn);
			$this->validateLastName($ln);
			$this->validateEmails($em, $em2);
			$this->validatePasswords($pw, $pw2);

			if(empty($this->errorArray) == true) {
				//Insert into db
				return $this->insertUserDetails($un, $fn, $ln, $em, $pw);
			}
			else {
				return false;
			}

		}

		public function getError($error) {
			if(!in_array($error, $this->errorArray)) {
				$error = "";
			}
			return "<span class='errorMessage'>$error</span>";
		}

		private function insertUserDetails($un, $fn, $ln, $em, $pw) {
			$encryptedPw = md5($pw);
			$profilePic = "assets/images/profile-pics/head_emerald.png";
			$date = date("Y-m-d");

			$result = mysqli_query($this->con, "INSERT INTO `users` VALUES (NULL, '$un', '$fn', '$ln', '$em', '$encryptedPw', '$date', '$profilePic')");

			return $result;
		}

		private function validateUsername($un) {

			if(strlen($un) > 25 || strlen($un) < 5) {
				array_push($this->errorArray, Constants::$usernameCharacters);
				return;
			}

			$checkUsernameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username='$un'");
			if(mysqli_num_rows($checkUsernameQuery) != 0) {
				array_push($this->errorArray, Constants::$usernameTaken);
				return;
			}

		}

		private function validateFirstName($fn) {
			if(strlen($fn) > 25 || strlen($fn) < 2) {
				array_push($this->errorArray, Constants::$firstNameCharacters);
				return;
			}
		}

		private function validateLastName($ln) {
			if(strlen($ln) > 25 || strlen($ln) < 2) {
				array_push($this->errorArray, Constants::$lastNameCharacters);
				return;
			}
		}

		private function validateEmails($em, $em2) {
			if($em != $em2) {
				array_push($this->errorArray, Constants::$emailsDoNotMatch);
				return;
			}

			if(!filter_var($em, FILTER_VALIDATE_EMAIL)) {
				array_push($this->errorArray, Constants::$emailInvalid);
				return;
			}

			$checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email='$em'");
			if(mysqli_num_rows($checkEmailQuery) != 0) {
				array_push($this->errorArray, Constants::$emailTaken);
				return;
			}

		}

		private function validatePasswords($pw, $pw2) {
			
			if($pw != $pw2) {
				array_push($this->errorArray, Constants::$passwordsDoNoMatch);
				return;
			}

			if(preg_match('/[^A-Za-z0-9]/', $pw)) {
				array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
				return;
			}

			if(strlen($pw) > 30 || strlen($pw) < 5) {
				array_push($this->errorArray, Constants::$passwordCharacters);
				return;
			}

		}


	}
?>

html, body {
    padding: 0;
    margin: 0;
    height: 100%;
}

#background {
    background-color: #000;
    background-image: url(../images/bg.jpg);
    background-position: center;
    background-size: cover;
    display: table;
    height: 100%;
    width: 100%;
}

#loginContainer * {
    color:  #fff;
    font-family: "Helvetica Neue", "Helvetica", "Arial", sans-serif;
    font-weight: normal;
    line-height: 1em;
    box-sizing: border-box;
}

#loginContainer {
    width: 80%;
    margin: 0 auto;
    position: relative;
    max-width: 1024px;
}

::-webkit-input-placeholder { /* WebKit, Blink, Edge */
    color:    #fff;
}
:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
   color:    #fff;
   opacity:  1;
}
::-moz-placeholder { /* Mozilla Firefox 19+ */
   color:    #fff;
   opacity:  1;
}
:-ms-input-placeholder { /* Internet Explorer 10-11 */
   color:    #fff;
}
::-ms-input-placeholder { /* Microsoft Edge */
   color:    #fff;
}

::placeholder { /* Most modern browsers support this now. */
   color:    #fff;
}

#inputContainer {
    width: 400px;
    padding: 45px;
    float: left;
    border-right: 1px solid #999;
}

#inputContainer h2 {
    text-align: center;
}

#inputContainer input[type="text"],
#inputContainer input[type="email"],
#inputContainer input[type="password"]   {
    display: block;
    background-color: transparent;
    border: 0;
    border-bottom: 1px solid #fff;
    height: 27px;
    line-height: 27px;
    width: 100%;
}

#inputContainer label {
    color: #a0a0a0;
    font-size: 13px;
    margin-top: 15px;
}

#inputContainer button {
    background-color: transparent;
    border: 2px solid #fff;
    border-radius: 250px;
    color: #fff;
    display: block;
    font-size: 14px;
    letter-spacing: 1px;
    margin: 20px auto;
    height: 41px;
    width: 100%;
}

#inputContainer button:hover {
    cursor: pointer;
}

.hasAccountText span {
    font-weight: bold;
    font-size: 12px;
}

.hasAccountText {
    text-align: center;
}

<!-- Ended at 6:21 6-17-2022 -->
