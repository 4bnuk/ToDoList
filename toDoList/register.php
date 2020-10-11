<?php
include "includes/functions.php";
include "includes/header.php";

if (!empty($_SESSION)) {
    header('Location: index.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = test_input($_POST["name"]);
    $email = test_input($_POST["email"]);
    $pass1 = test_input($_POST["pass1"]);
    $pass2 = test_input($_POST["pass2"]);

    if (empty($name)) {
        $error["name"] = "Name is required";
    } else if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
        $error["name"] = "Only letters and white space allowed";
        
    }
    
    if (empty($email)) {
        $error["email"] = "Email is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "Invalid email format";        
    }


    if (empty($pass1)) {
        $error["pass1"] = "Password is required";
    }

    if ($pass2 != $pass1) {
        $error["pass2"] = "The passwords entered do not match";
    }

    if (empty($error)) {
        $hashpass = password_hash($pass1, PASSWORD_DEFAULT);
		$stmt = $con->prepare("SELECT id FROM user WHERE email = ?");
		$stmt->bind_param('s',$email);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows == 1) { 
			$error["general"] = "User allready exists";
		} else { 
			$stmt = $con->prepare("INSERT INTO user (email, password, name) VALUES (?, ?, ?)");
			$stmt->bind_param("sss",$email,$hashpass,$name);
			if ($stmt->execute()){
				$_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $con->insert_id;
                header('Location: index.php');
			} else {
				$error["general"] = "Something went wrong";
			}
		}
		$con->close();
    }
}



?>

<div class="container">
    <div class="row">
        <div class="col s12 m10 offset-m1">
            <h2 class="header center-align">To Do List</h2>
            <div class="card horizontal">
            <div class="card-image hide-on-small-only">
                <img class="login_img" src="imgs/register1.png">
            </div>
            <div class="card-stacked">
                <div class="card-content">        
                    <form action="register.php" method="post">
                        <div class="row">
                            <div class="input-field col s12">
                                <i class="material-icons prefix">person</i>
                                <input id="name" name="name" type="text" value="<?= $name ?? ""; ?>" required>
                                <label for="name">Name</label>
                                <span class="helper-text"><?= $error["name"] ?? ""; ?></span>
                            </div>
                            <div class="input-field col s12">
                                <i class="material-icons prefix">email</i>
                                <input id="email" name="email" type="email" value="<?= $email ?? ""; ?>" required>
                                <label for="email">Email</label>
                                <span class="helper-text"><?= $error["email"] ?? ""; ?></span>
                            </div>
                            <div class="input-field col s12">
                                <i class="material-icons prefix">lock</i>
                                <input id="pass1" name="pass1" type="password" minlength="8" required>
                                <label for="pass1">Password</label>
                                <span class="helper-text"><?= $error["pass1"] ?? ""; ?></span>
                            </div>
                            <div class="input-field col s12">
                                <i class="material-icons prefix">lock</i>
                                <input id="pass2" name="pass2" type="password" required>
                                <label for="pass2">Confirm Password</label>
                                <span class="helper-text"><?= $error["pass2"] ?? ""; ?></span>
                            </div>
                            <span><?= $error["general"] ?? ""; ?></span>
                            <div class="right-align">
                                <button class="btn waves-effect waves-light" type="submit">Register
                                    <i class="material-icons right">person_add_alt_1</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-action">
                Already Have An account? <a href="login.php">login</a>
                </div>
            </div>
            </div>
        </div>
    </div>


















    









</div>

<?php
include "includes/footer.php";
?>