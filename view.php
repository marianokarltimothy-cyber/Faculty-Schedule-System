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

// Fetch the specific schedule entry
if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM schedules WHERE id = $id";
    $res = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($res);

    if(!$data) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Activity - SchedPro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* This hides UI elements when printing */
        @media print {
            .sidebar, .btn, .nav-link, .print-hide {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }
            body {
                background: white !important;
            }
            .card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }
        }
    </style>
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
        <a href="logout.php" class="nav-link" style="color: #ef4444; margin-top: auto; border-top: 1px solid #334155; padding-top: 15px;">
        Sign Out
    </a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="card">
            <!-- Header with Back Button -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                <div>
                    <h2 style="font-size: 2rem;"><?php echo htmlspecialchars($data['title']); ?></h2>
                </div>
                <div style="display: flex; gap: 10px;" class="print-hide">
                    <!-- PRINT BUTTON -->
                    <button onclick="window.print()" class="btn btn-outline" style="display: flex; align-items: center; gap: 8px;">
                        🖨️ Print Details
                    </button>
                    <a href="edit.php?id=<?php echo $data['id']; ?>" class="btn btn-primary">Edit Activity</a>
                </div>
            </div>

            <!-- Details Section -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; border-top: 1px solid #f1f5f9; padding-top: 30px;">
                <div>
                    <label style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem;">Scheduled Date</label>
                    <p style="font-size: 1.1rem; font-weight: 600; margin-top: 5px;">
                        <?php echo date('F d, Y', strtotime($data['activity_date'])); ?>
                    </p>
                </div>
                <div>
                    <label style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem;">Scheduled Time</label>
                    <p style="font-size: 1.1rem; font-weight: 600; margin-top: 5px;">
                        <?php echo date('h:i A', strtotime($data['activity_time'])); ?>
                    </p>
                </div>
            </div>

            <div style="margin-top: 40px;">
                <label style="color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem;">Description & Notes</label>
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-top: 10px; line-height: 1.6; color: var(--text-dark); border: 1px solid #f1f5f9;">
                    <?php echo nl2br(htmlspecialchars($data['description'])); ?>
                </div>
            </div>
            
            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                <span style="font-size: 0.85rem; color: var(--text-muted);">
                    Status: <strong style="color: var(--success); text-transform: capitalize;"><?php echo htmlspecialchars($data['status']); ?></strong>
                </span>
            </div>
        </div>
    </div>

    
    <!-- Logout Link -->
    <a href="logout.php" class="nav-link" style="color: #ef4444; margin-top: auto; padding-top: 50px;">Sign Out</a>
</div>

</body>
</html>