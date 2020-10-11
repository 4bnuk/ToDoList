<?php
include "includes/functions.php";
include "includes/header.php";

if (!empty($_SESSION)) {
    header('Location: index.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = test_input($_POST["email"]);
    $pass = test_input($_POST["pass"]);

    
    if (empty($email)) {
        $error["email"] = "Email is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "Invalid email format";        
    }

    if (empty($pass)) {
        $error["pass"] = "Password is required";
    }

    if (empty($error)) {
        $stmt = $con->prepare("SELECT id, password, name FROM user WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->bind_result($user_id, $hashpass, $name);
        $stmt->store_result();
        $stmt->fetch();
        if (password_verify($pass, $hashpass)) {
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $user_id;
            header('Location: index.php');          
        } else {
            $error['general'] = "Wrong email or password";
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
                <img class="login_img" src="imgs/login1.png">
            </div>
            <div class="card-stacked">
                <div class="card-content">        
                    <form action="login.php" method="post">
                        <div class="row">
                            <div class="input-field col s12">
                                <i class="material-icons prefix">email</i>
                                <input id="email" name="email" type="email" value="<?= $email ?? ""; ?>" required>
                                <label for="email">Email</label>
                                <span class="helper-text"><?= $error["email"] ?? ""; ?></span>
                            </div>
                            <div class="input-field col s12">
                                <i class="material-icons prefix">lock</i>
                                <input id="pass" name="pass" type="password" required>
                                <label for="pass">Password</label>
                                <span class="helper-text"><?= $error["pass"] ?? ""; ?></span>
                            </div>
                            <span class="helper-text"><?= $error["general"] ?? ""; ?></span>
                            <div class="right-align">
                                <button class="btn waves-effect waves-light" type="submit">Sign In
                                    <i class="material-icons right">login</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-action">
                Don't Have An account? <a href="register.php">Register</a>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>            

<?php
include "includes/footer.php";
?>