<?php
session_start();
if(!isset($_SESSION['faculty_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'db.php'; 
// ... the rest of your page code ...
?>
<?php 
include 'db.php'; 

// Get the specific activity data
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM schedules WHERE id=$id");
    $data = mysqli_fetch_assoc($res);
    
    if(!$data) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// Update Logic
if(isset($_POST['update'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    
    mysqli_query($conn, "UPDATE schedules SET title='$title', activity_date='$date', activity_time='$time', description='$desc' WHERE id=$id");
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Activity - SchedPro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <!-- Logo Section -->
        <div class="sidebar-logo-container">
            <img src="logo.jpg" alt="DCC Logo" class="sidebar-logo">
        </div>

        <h2>DCC Faculty Schedule</h2>
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="add.php" class="nav-link">Add Activity</a>
        <a href="trash.php" class="nav-link" style="color: #f87171; margin-top: 20px;">🗑</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="card">
            <h2 style="margin-bottom: 25px;">Edit Activity</h2>
            
            <form method="POST">
                <div class="form-group">
                    <label>Activity Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
                </div>
                
                <!-- Grouped Date and Time for full-screen layout -->
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label>Date</label>
                        <input type="date" name="date" value="<?php echo $data['activity_date']; ?>" required>
                    </div>
                    <div style="flex: 1;">
                        <label>Time</label>
                        <input type="time" name="time" value="<?php echo $data['activity_time']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="desc" rows="6"><?php echo htmlspecialchars($data['description']); ?></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" name="update" class="btn btn-primary" style="padding: 12px 30px;">Update Activity</button>
                    <a href="index.php" class="btn btn-outline" style="padding: 12px 30px;">Discard Changes</a>
                </div>
            </form>
        </div>
    </div>
<div class="sidebar">
    <div class="sidebar-logo-container">
        <img src="logo.jpg" alt="DCC Logo" class="sidebar-logo">
    </div>
    <h2>DCC Faculty Schedule</h2>
    <a href="index.php" class="nav-link">Dashboard</a>
    <a href="add.php" class="nav-link">Add Activity</a>
    <a href="trash.php" class="nav-link">🗑 Trash Can</a>
    
    <!-- Logout Link -->
    <a href="logout.php" class="nav-link" style="color: #ef4444; margin-top: auto; padding-top: 50px;">Sign Out</a>
</div>
</body>
</html>