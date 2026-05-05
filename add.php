<?php
session_start();

// 1. Security Gate
if(!isset($_SESSION['faculty_logged_in'])) {
    header("Location: login.php");
    exit();
}

// 2. Database Connection
include 'db.php'; 

// 3. Save Logic
if(isset($_POST['save'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    
    // Ensure we set the status to active by default
    $query = "INSERT INTO schedules (title, activity_date, activity_time, description, status) 
              VALUES ('$title', '$date', '$time', '$desc', 'active')";
    
    if(mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Add Activity - SchedPro</title>
</head>
<body>
    <!-- SIDEBAR: ONLY ONE BLOCK HERE -->
    <div class="sidebar">
        <div class="sidebar-logo-container">
            <img src="logo.jpg" alt="DCC Logo" class="sidebar-logo">
        </div>

        <h2>DCC Faculty Schedule</h2>
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="add.php" class="nav-link active">Add Activity</a>
        <a href="trash.php" class="nav-link">🗑 Trash Can</a>
        
        <!-- Logout Link placed at the bottom of the sidebar -->
         <a href="logout.php" class="nav-link" style="color: #ef4444; margin-top: auto; border-top: 1px solid #334155; padding-top: 15px;">
        Sign Out
    </a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="card">
            <h2 style="margin-bottom: 25px;">Create Activity</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Activity Title</label>
                    <input type="text" name="title" required placeholder="e.g. Project Launch">
                </div>
                
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label>Date</label>
                        <input type="date" name="date" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Time</label>
                        <input type="time" name="time" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="desc" rows="6" placeholder="Add extra details here..."></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" name="save" class="btn btn-primary" style="padding: 12px 30px;">Save Activity</button>
                    <a href="index.php" class="btn btn-outline" style="padding: 12px 30px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>