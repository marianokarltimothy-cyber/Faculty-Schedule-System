<?php
session_start();

// 1. Security Gate
if(!isset($_SESSION['faculty_logged_in'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; 

// 3. Single Restore Logic
if(isset($_GET['restore'])) {
    $id = intval($_GET['restore']);
    mysqli_query($conn, "UPDATE schedules SET status='active' WHERE id=$id");
    header("Location: trash.php");
    exit();
}

// 4. Single Permanent Delete Logic
if(isset($_GET['force_del'])) {
    $id = intval($_GET['force_del']);
    mysqli_query($conn, "DELETE FROM schedules WHERE id=$id");
    header("Location: trash.php");
    exit();
}

// 5. BULK ACTIONS LOGIC
if(isset($_POST['bulk_trash_action']) && isset($_POST['trash_ids'])) {
    $ids = implode(',', array_map('intval', $_POST['trash_ids']));
    $action = $_POST['bulk_trash_action'];

    if($action == 'restore_bulk') {
        mysqli_query($conn, "UPDATE schedules SET status='active' WHERE id IN ($ids)");
    } elseif($action == 'delete_bulk') {
        mysqli_query($conn, "DELETE FROM schedules WHERE id IN ($ids)");
    }
    header("Location: trash.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Trash Can - SchedPro</title>
</head>
<body>
    <!-- SIDEBAR: ONLY ONE ALLOWED -->
    <div class="sidebar">
        <div class="sidebar-logo-container">
            <img src="logo.jpg" alt="DCC Logo" class="sidebar-logo">
        </div>
        <h2>DCC Faculty Schedule</h2>
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="add.php" class="nav-link">Add Activity</a>
        <a href="trash.php" class="nav-link active">🗑 Trash Can</a>
   <a href="logout.php" class="nav-link" style="color: #ef4444; margin-top: auto; border-top: 1px solid #334155; padding-top: 15px;">
        Sign Out
    </a>
    </div>

    <div class="main-content">
        <div class="card">
            <form id="bulkTrashForm" method="POST">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <h2 style="margin-bottom: 5px;">Recently Deleted</h2>
                        <div id="bulkTrashBar" style="display: none; gap: 10px; align-items: center; margin-top: 10px;">
                            <span id="trashSelectedCount" style="font-size: 0.9rem; font-weight: bold; color: #2563eb;">0 selected</span>
                            <button type="submit" name="bulk_trash_action" value="restore_bulk" class="btn btn-primary" style="background:#16a34a; border-color:#16a34a; padding: 5px 15px; font-size: 0.8rem;">Restore Selected</button>
                            <button type="submit" name="bulk_trash_action" value="delete_bulk" class="btn btn-outline" style="color:#ef4444; border-color:#ef4444; padding: 5px 15px; font-size: 0.8rem;" onclick="return confirm('Delete all selected items permanently?')">Delete Permanently</button>
                        </div>
                    </div>
                </div>
                
                <table width="100%" style="border-collapse: collapse;" id="trashTable">
                    <thead>
                        <tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
                            <th style="padding: 15px; width: 40px;"><input type="checkbox" id="selectAllTrash"></th>
                            <th style="padding: 15px;">ACTIVITY</th>
                            <th style="padding: 15px;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT * FROM schedules WHERE status='deleted' ORDER BY id DESC");
                        if(mysqli_num_rows($res) == 0) {
                            echo "<tr><td colspan='3' style='padding:40px; text-align:center; color:#94a3b8;'>Trash is empty.</td></tr>";
                        }
                        while($row = mysqli_fetch_assoc($res)) {
                            echo "<tr style='border-bottom: 1px solid #f1f5f9;'>
                                <td style='padding: 15px;'><input type='checkbox' name='trash_ids[]' value='{$row['id']}' class='trashCheckbox'></td>
                                <td style='padding: 15px;'>
                                    <div style='font-weight: 700; color: #1e293b;'>".htmlspecialchars($row['title'])."</div>
                                </td>
                                <td style='padding: 15px;'>
                                    <div style='display: flex; gap: 10px;'>
                                        <a href='trash.php?restore={$row['id']}' class='btn btn-primary' style='background:#16a34a; border-color:#16a34a; padding: 5px 12px; font-size: 0.8rem;'>Restore</a>
                                        <a href='trash.php?force_del={$row['id']}' class='btn btn-outline' style='color:#ef4444; border-color:#ef4444; padding: 5px 12px; font-size: 0.8rem;' onclick='return confirm(\"This cannot be undone!\")'>Permanent Delete</a>
                                    </div>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <script>
        const selectAllTrash = document.getElementById('selectAllTrash');
        const trashCheckboxes = document.querySelectorAll('.trashCheckbox');
        const bulkTrashBar = document.getElementById('bulkTrashBar');
        const trashCountText = document.getElementById('trashSelectedCount');

        function updateTrashBar() {
            const checkedCount = document.querySelectorAll('.trashCheckbox:checked').length;
            trashCountText.textContent = `${checkedCount} selected`;
            bulkTrashBar.style.display = checkedCount > 0 ? 'flex' : 'none';
        }

        if(selectAllTrash) {
            selectAllTrash.addEventListener('change', function() {
                document.querySelectorAll('.trashCheckbox').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateTrashBar();
            });
        }

        document.addEventListener('change', function(e) {
            if(e.target.classList.contains('trashCheckbox')) {
                updateTrashBar();
            }
        });
    </script>
</body>
</html>