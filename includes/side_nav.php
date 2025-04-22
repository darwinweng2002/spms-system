<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current page to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background: #ffffff;
        border-right: 1px solid #ddd;
        box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.05);
        padding-top: 20px;
        color: #333;
    }

    .sidebar ul {
        list-style-type: none;
        padding: 0;
    }

    .sidebar ul li {
        padding: 12px 20px;
    }

    .sidebar ul li a {
        text-decoration: none;
        color: #333;
        font-size: 16px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: 0.3s ease;
        border-radius: 6px;
        padding: 10px 15px;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background: #f5f5f5;
        border-left: 4px solid #555;
        color: #000;
    }

    .sidebar ul li a i {
        font-size: 18px;
    }

    .content {
        margin-left: 250px;
        padding: 20px;
        min-height: 100vh;
        background: #f8f9fa;
    }

    footer {
        width: calc(100% - 250px);
        margin-left: 250px;
        background: #2C3E50;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 14px;
        position: relative;
    }
</style>

<div class="sidebar">
    <ul>
        <br><br><br>
        <li>
            <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="add_employee.php" class="<?= ($current_page == 'add_employee.php') ? 'active' : '' ?>">
                <i class="bi bi-person-plus-fill"></i> Add Employee
            </a>
        </li>
        <li>
            <a href="manage_request.php" class="<?= ($current_page == 'manage_request.php') ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Request
            </a>
        </li>
        <li>
            <a href="employee_list.php" class="<?= ($current_page == 'employee_list.php') ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Employee Records
            </a>
        </li>
        <li>
            <a href="manage_admin.php" class="<?= ($current_page == 'manage_admin.php') ? 'active' : '' ?>">
                <i class="bi bi-person-gear"></i> Manage Accounts
            </a>
        </li>
    </ul>
</div>
