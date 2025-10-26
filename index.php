<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Task Tracker</h1>
            <p>Keep your tasks organized in one place</p>
        </div>

        <div class="nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>

        <div>
            <h2>Welcome to our Task Tracker</h2>
            <p>A simple websitet to store a task!</p>
            
            <div class="features">
                <div class="feature">
                    <h3>Add Tasks</h3>
                    <p>Create tasks with a title and a due date</p>
                </div>
                <div class="feature">
                    <h3>View Calendar</h3>
                    <p>See your tasks in a calandar</p>
                </div>
                <div class="feature">
                    <h3>Track Progress</h3>
                    <p>Mark tasks as complete when done</p>
                </div>
            </div>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <p><a href="register.php" class="btn">Get Started - Register Now</a></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>