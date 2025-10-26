<?php
include 'config.php';

if ($_POST) {
    $username = cleanInput($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid login";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Task Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Login</h2>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <input type="text" name="username" class="form-input" placeholder="Username" required>
                <input type="password" name="password" class="form-input" placeholder="Password" required>
                <button type="submit" class="btn">Login</button>
            </form>
            
            <p style="margin-top: 15px; text-align: center;">
                Need an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>