<?php
include 'config.php';
checkLogin();

if ($_POST) {
    if (isset($_POST['add_task'])) {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, due_date, due_time) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $_POST['title'], $_POST['description'], $_POST['due_date'], $_POST['due_time']]);
    }
    
    if (isset($_POST['delete_task'])) {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$_POST['task_id'], $_SESSION['user_id']]);
    }
    
    if (isset($_POST['toggle_complete'])) {
        $stmt = $pdo->prepare("UPDATE tasks SET is_complete = NOT is_complete WHERE id = ? AND user_id = ?");
        $stmt->execute([$_POST['task_id'], $_SESSION['user_id']]);
    }
}

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date, due_time");
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

$calendar_tasks = [];
foreach ($tasks as $task) {
    $calendar_tasks[] = [
        'title' => $task['title'],
        'start' => $task['due_date'] . ($task['due_time'] ? 'T' . $task['due_time'] : ''),
        'color' => $task['is_complete'] ? '#27ae60' : '#e74c3c'
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Task Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <p>Manage your tasks and schedule</p>
            <div class="nav">
                <a href="index.php">Home</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="view-toggle">
            <button class="view-btn active" onclick="showView('list')">List View</button>
            <button class="view-btn" onclick="showView('calendar')">Calendar View</button>
        </div>

        <div class="task-form">
            <h3>Add New Task</h3>
            <form method="post">
                <input type="text" name="title" placeholder="Task title" required>
                <textarea name="description" placeholder="Description"></textarea>
                <input type="date" name="due_date" required>
                <input type="time" name="due_time">
                <button type="submit" name="add_task" class="btn">Add Task</button>
            </form>
        </div>

        <div id="listView">
            <h3>Your Tasks</h3>
            <div class="task-list">
                <?php if (empty($tasks)): ?>
                    <p>No tasks yet.</p>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <div class="task-item <?php echo $task['is_complete'] ? 'task-complete' : ''; ?>">
                            <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                            <p><?php echo htmlspecialchars($task['description']); ?></p>
                            <p><strong>Due:</strong> <?php echo $task['due_date'] . ' ' . $task['due_time']; ?></p>
                            <div class="task-actions">
                                <form method="post">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" name="toggle_complete" class="btn">
                                        <?php echo $task['is_complete'] ? 'Mark Incomplete' : 'Mark Complete'; ?>
                                    </button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" name="delete_task" class="btn btn-danger" onclick="return confirm('Delete this task?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div id="calendarView" class="hidden">
            <h3>Calendar View</h3>
            <div id="calendar"></div>
        </div>

        <script>
            function showView(view) {
                const listView = document.getElementById('listView');
                const calendarView = document.getElementById('calendarView');
                const buttons = document.querySelectorAll('.view-btn');

                if (view === 'list') {
                    listView.classList.remove('hidden');
                    calendarView.classList.add('hidden');
                    buttons[0].classList.add('active');
                    buttons[1].classList.remove('active');
                } else {
                    listView.classList.add('hidden');
                    calendarView.classList.remove('hidden');
                    buttons[0].classList.remove('active');
                    buttons[1].classList.add('active');
                    calendar.render();
                }
            }

            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: <?php echo json_encode($calendar_tasks); ?>,
                eventClick: function(info) {
                    alert('Task: ' + info.event.title);
                }
            });
            calendar.render();
        </script>
    </div>
</body>
</html>