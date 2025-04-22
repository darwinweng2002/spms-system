<?php
session_start();
require_once 'db.php'; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// ✅ Fetch Admin Count
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_admins FROM admin_users");
$stmt->execute();
$admin_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_admins'];

// ✅ Fetch Employee Count
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_employees FROM employees");
$stmt->execute();
$employee_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_employees'];

// ✅ Fetch Approved Requests Count
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_approved FROM request_letters WHERE status = 'Approved'");
$stmt->execute();
$approved_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_approved'];

// ✅ Fetch Pending Requests Count
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_pending FROM request_letters WHERE status = 'Pending'");
$stmt->execute();
$pending_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_pending'];

// ✅ Fetch Total Letter Requests Count
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_requests FROM request_letters");
$stmt->execute();
$total_requests = $stmt->fetch(PDO::FETCH_ASSOC)['total_requests'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <?php require_once 'includes/header_nav.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Full Page Layout to Ensure Footer is Always at Bottom */
        html, body {
            height: 100%;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 20px;
        }

        /* Dashboard Container */
        .dashboard-container {
            margin-left: 200px;
            max-width: 1600px;
            width: 100%;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        /* Dashboard Grid */
        .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* Ensures only 4 items per row */
        gap: 20px;
        justify-content: center;
    }

    @media screen and (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: repeat(2, 1fr); /* 2 items per row on smaller screens */
        }
    }

    @media screen and (max-width: 600px) {
        .dashboard-grid {
            grid-template-columns: repeat(1, 1fr); /* 1 item per row on very small screens */
        }
    }

        /* Stat Cards */
        .stat-card {
            background: white;
            padding: 60px 25px;
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 250px;
            height: 120px;
            transition: 0.3s ease-in-out;
        }

        .stat-card:hover {
            transform: scale(1.02);
        }

        .stat-title {
            font-size: 18px;
            font-weight: 600;
            color: #002855;
        }

        .stat-status {
            font-size: 14px;
            color: #6c757d;
            margin-left: 5px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
            color: #001f3f;
            margin-top: 10px;
        }
        .stat-icon {
    font-size: 30px;
    margin-bottom: 10px;
    color: #007bff; /* Blue default icon color */
}

        /* Footer */
        footer {
            width: 100%;
            text-align: center;
            padding: 2px;
            background: #2C3E50;
            color: #fff;
            font-size: 10px;
            position: relative;
            bottom: 0;
        }

        footer img.footer-logo {
            height: 60px;
            width: auto;
        }
    </style>
</head>
<body>

<?php require_once 'includes/side_nav.php'; ?>

<div class="main-content">
    <div class="dashboard-container">
        <br><br><br>
        <h1>Dashboard</h1>

        <div class="dashboard-grid">
            <!-- ✅ Admin Accounts Stat Card -->
            <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-person-lock"></i></div>
            <span class="stat-title">Admin Accounts <span class="stat-status">Active</span></span>
            <span class="stat-number"><?= $admin_count; ?></span>
        </div>


        <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <span class="stat-title">Employees <span class="stat-status">Active</span></span>
        <span class="stat-number"><?= $employee_count; ?></span>
    </div>


        <div class="stat-card">
            <div class="stat-icon text-success"><i class="bi bi-check-circle-fill"></i></div>
            <span class="stat-title">Approved Requests <span class="stat-status">Total</span></span>
            <span class="stat-number"><?= $approved_count; ?></span>
        </div>


            <!-- ✅ Pending Requests Stat Card -->
        <div class="stat-card">
            <div class="stat-icon text-warning"><i class="bi bi-hourglass-split"></i></div>
            <span class="stat-title">Pending Requests <span class="stat-status">Total</span></span>
            <span class="stat-number"><?= $pending_count; ?></span>
        </div>


            <!-- ✅ Total Letter Requests Stat Card -->
            <div class="stat-card">
                <span class="stat-title">Requests Letters <span class="stat-status">Total</span></span>
                <span class="stat-number"><?php echo $total_requests; ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?> 

</body>
</html>