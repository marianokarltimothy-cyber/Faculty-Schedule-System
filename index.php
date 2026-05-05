<?php
session_start();

// 1. Security Check
if(!isset($_SESSION['faculty_logged_in'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; 

// 2. LOGIC HANDLERS (Bulk Actions, Deletes, Restores)
// Enhanced Bulk Actions Handler
if(isset($_POST['bulk_action_done']) && isset($_POST['done_ids'])) {
    $ids = implode(',', array_map('intval', $_POST['done_ids']));
    $action = $_POST['bulk_action_done'];
    if($action == 'restore') {
        mysqli_query($conn, "UPDATE schedules SET status='active' WHERE id IN ($ids)");
    } elseif($action == 'delete_perm') {
        mysqli_query($conn, "UPDATE schedules SET status='deleted' WHERE id IN ($ids)");
    }
    header("Location: index.php");
    exit();
}

// Bulk Actions Handler
if(isset($_POST['bulk_action']) && isset($_POST['ids'])) {
    $ids = implode(',', array_map('intval', $_POST['ids']));
    $action = $_POST['bulk_action'];
    if($action == 'done') {
        mysqli_query($conn, "UPDATE schedules SET status='done' WHERE id IN ($ids)");
    } elseif($action == 'delete') {
        mysqli_query($conn, "UPDATE schedules SET status='deleted' WHERE id IN ($ids)");
    }
    header("Location: index.php");
    exit();
}

// Move to Trash (Soft Delete)
if(isset($_GET['soft_del'])) {
    $id = intval($_GET['soft_del']);
    mysqli_query($conn, "UPDATE schedules SET status='deleted' WHERE id=$id");
    header("Location: index.php");
    exit();
}

// Restore Logic (Added to handle the Restore button in Section 3)
if(isset($_GET['restore_id'])) {
    $id = intval($_GET['restore_id']);
    mysqli_query($conn, "UPDATE schedules SET status='active' WHERE id=$id");
    header("Location: index.php");
    exit();
}

// Mark as Done
if(isset($_GET['done_id'])) {
    $id = intval($_GET['done_id']);
    mysqli_query($conn, "UPDATE schedules SET status='done' WHERE id=$id");
    header("Location: index.php");
    exit();
}

// 3. Calendar Navigation Logic
$month = isset($_GET['m']) ? intval($_GET['m']) : date('m');
$year = isset($_GET['y']) ? intval($_GET['y']) : date('Y');
$prevMonth = ($month == 1) ? 12 : $month - 1;
$prevYear = ($month == 1) ? $year - 1 : $year;
$nextMonth = ($month == 12) ? 1 : $month + 1;
$nextYear = ($month == 12) ? $year + 1 : $year;
$currentMonthName = date('F Y', mktime(0, 0, 0, $month, 1, $year));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchedPro - Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- FIXED SIDEBAR: ONE BLOCK ONLY -->
<div class="sidebar">
    <div class="sidebar-logo-container">
        <img src="logo.jpg" alt="DCC Logo" class="sidebar-logo">
    </div>
    
    <h2>DCC Faculty Schedule</h2>
    <a href="index.php" class="nav-link active">Dashboard</a>
    <a href="add.php" class="nav-link">Add Activity</a>
    <a href="trash.php" class="nav-link" style="margin-top: 10px;">🗑 Trash Can</a>

    <!-- Sign Out placed correctly at the bottom -->
     <a href="logout.php" class="nav-link" style="color: #ef4444; margin-top: auto; border-top: 1px solid #334155; padding-top: 15px;">
        Sign Out
    </a>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    
    <!-- SECTION 1: CALENDAR VIEW -->
    <div class="card" style="margin-bottom: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin:0;">Calendar View</h2>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="index.php?m=<?php echo $prevMonth; ?>&y=<?php echo $prevYear; ?>" class="btn btn-outline" style="padding: 5px 12px;">&larr;</a>
                <span style="font-weight: 700; color: var(--primary); font-size: 1.1rem; min-width: 140px; text-align: center;">
                    <?php echo $currentMonthName; ?>
                </span>
                <a href="index.php?m=<?php echo $nextMonth; ?>&y=<?php echo $nextYear; ?>" class="btn btn-outline" style="padding: 5px 12px;">&rarr;</a>
            </div>
        </div>

        <div class="month-scrollbar">
            <?php
            for ($i = 1; $i <= 12; $i++) {
                $mName = date('M', mktime(0, 0, 0, $i, 1, $year));
                $activeClass = ($i == $month) ? 'active' : '';
                echo "<a href='index.php?m=$i&y=$year' class='month-item $activeClass'>$mName</a>";
            }
            ?>
        </div>

        <div class="calendar-grid">
            <?php 
            $daysHeader = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach($daysHeader as $day) echo "<div class='calendar-day-head'>$day</div>";

            $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
            $numberDays = date('t', $firstDayOfMonth);
            $dayOfWeek = date('w', $firstDayOfMonth);
            $todayStr = date('Y-m-d');

            for($i = 0; $i < $dayOfWeek; $i++) echo "<div class='calendar-day empty'></div>";

            for($day = 1; $day <= $numberDays; $day++) {
                $fullDate = $year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($day, 2, "0", STR_PAD_LEFT);
                $isToday = ($fullDate == $todayStr) ? 'today' : '';
                
                echo "<div class='calendar-day $isToday'>";
                echo "<span class='day-number'>$day</span>";
                $sql = "SELECT id, title, status FROM schedules WHERE activity_date = '$fullDate' AND status != 'deleted'";
                $cal_res = mysqli_query($conn, $sql);
                while($task = mysqli_fetch_assoc($cal_res)) {
                    $statusClass = ($task['status'] == 'done') ? 'done' : '';
                    echo "<div class='day-activity $statusClass' onclick='location.href=\"view.php?id={$task['id']}\"'>";
                    echo htmlspecialchars($task['title']);
                    echo "</div>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <!-- SECTION 2: ACTIVE TASKS -->
    <div class="card" style="margin-bottom: 40px;">
        <form id="bulkForm" method="POST">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h2 style="margin:0;">Activity</h2>
                    <div id="bulkActionsBar" style="display: none; margin-top: 10px; gap: 10px; align-items: center;">
                        <span id="selectedCount" style="font-size: 0.9rem; font-weight: bold; color: var(--primary);">0 selected</span>
                        <button type="submit" name="bulk_action" value="done" class="btn btn-outline" style="color: var(--success); border-color: var(--success); padding: 4px 12px;">Mark Done</button>
                        <button type="submit" name="bulk_action" value="delete" class="btn btn-outline" style="color: var(--danger); border-color: var(--danger); padding: 4px 12px;">Delete</button>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" id="scheduleSearch" placeholder="Search activities..." style="padding: 8px 15px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem; width: 250px;">
                    <a href="add.php" class="btn btn-primary">+ New Activity</a>
                </div>
            </div>

            <table id="scheduleTable">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                        <th style="width: 35%;">Activity</th>
                        <th style="width: 25%;">Date & Time</th>
                        <th style="width: 35%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM schedules WHERE status='active' ORDER BY activity_date ASC");
                    while($row = mysqli_fetch_assoc($res)) {
                        $d = date('M d, Y', strtotime($row['activity_date']));
                        $t = date('h:i A', strtotime($row['activity_time']));
                        echo "<tr>
                            <td><input type='checkbox' name='ids[]' value='{$row['id']}' class='rowCheckbox'></td>
                            <td class='searchable'>
                                <div style='font-weight: 700;'>".htmlspecialchars($row['title'])."</div>
                                <div style='font-size: 0.85rem; color: var(--text-muted);'>".htmlspecialchars($row['description'])."</div>
                            </td>
                            <td>$d<br><small>$t</small></td>
                            <td>
                                <div style='display: flex; gap: 8px;'>
                                    <a href='index.php?done_id={$row['id']}' class='btn btn-outline' style='color: var(--success); border-color: var(--success);'>Done</a>
                                    <a href='edit.php?id={$row['id']}' class='btn btn-outline'>Edit</a>
                                    <a href='javascript:void(0)' onclick='openDeleteModal(\"index.php?soft_del={$row['id']}\")' class='btn btn-outline btn-delete'>Delete</a>
                                </div>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>

    <!-- SECTION 3: COMPLETED TASKS -->
    <div class="card">
        <form id="bulkDoneForm" method="POST">
            <h3 style="margin-bottom: 15px;">✅ Recently Completed</h3>
            <table id="completedTable">
                <?php
                $done_res = mysqli_query($conn, "SELECT * FROM schedules WHERE status='done' ORDER BY id DESC LIMIT 5");
                while($row = mysqli_fetch_assoc($done_res)) {
                    echo "<tr>
                        <td style='text-decoration: line-through; color: var(--text-muted);'>".htmlspecialchars($row['title'])."</td>
                        <td style='text-align: right;'>
                            <a href='index.php?restore_id={$row['id']}' class='btn btn-outline' style='font-size: 0.75rem; color: var(--primary);'>Restore</a>
                        </td>
                    </tr>";
                }
                ?>
            </table>
        </form>
    </div>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal-card">
        <h3>Move to Trash?</h3>
        <p>You can restore this later from the Trash Can.</p>
        <div class="modal-actions">
            <button onclick="closeModal()" class="btn btn-outline">Cancel</button>
            <a id="confirmDeleteBtn" href="#" class="btn btn-primary" style="background: var(--danger);">Confirm</a>
        </div>
    </div>
</div>

<script>
    // Simple Search & Checkbox Logic
    document.getElementById('scheduleSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        document.querySelectorAll('#scheduleTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
        });
    });

    const selectAll = document.getElementById('selectAll');
    selectAll.addEventListener('change', function() {
        document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = this.checked);
        document.getElementById('bulkActionsBar').style.display = this.checked ? 'flex' : 'none';
    });

    function openDeleteModal(url) {
        document.getElementById('confirmDeleteBtn').href = url;
        document.getElementById('deleteModal').classList.add('show');
    }
    function closeModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }
</script>

</body>
</html>