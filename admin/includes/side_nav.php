<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current page to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background: #ffffff; /* Professional white background */
        border-right: 1px solid #ddd; /* Subtle border for definition */
        box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.05); /* Light shadow for depth */
        padding-top: 20px;
        color: #333;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 20px;
        color: #555; /* Dark gray text */
    }

    .sidebar ul {
        list-style-type: none;
        padding: 0;
    }

    .sidebar ul li {
        padding: 12px 20px;
        text-align: left;
    }

    .sidebar ul li a {
        text-decoration: none;
        color: #333; /* Dark text for contrast */
        font-size: 16px;
        font-weight: 500;
        display: block;
        transition: 0.3s ease;
        border-radius: 6px;
        padding: 10px 15px;
    }

    .sidebar ul li a:hover {
        background: #f5f5f5; /* Light gray hover effect */
        border-left: 4px solid #555; /* Professional indicator */
        color: #000;
    }

    .content {
    margin-left: 250px;
    padding: 20px;
    min-height: 100vh; /* Ensure it extends full height */
    background: #f8f9fa; /* Light gray background */
}
  
        footer {
    width: calc(100% - 250px); /* Adjust width to not overlap sidebar */
    margin-left: 250px; /* Align with content */
    background: #2C3E50;
    color: white;
    text-align: center;
    padding: 15px;
    font-size: 14px;
    position: relative; /* Ensure it flows with content */
}
</style>

<div class="sidebar">
    
    <!-- <h2>Admin Panel</h2> -->
    <ul>
        <br>
        <br>
        <br>
        <li><a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>"> Dashboard</a></li>
        <li><a href="add_employee.php" class="<?= ($current_page == 'manage_users.php') ? 'active' : '' ?>"> Add Employee</a></li>
         <li><a href="manage_request.php" class="<?= ($current_page == 'manage_request.php') ? 'active' : '' ?>"> Request</a></li> 
       <!--   <li><a href="spreadsheet_editor.php" class="<?= ($current_page == 'spreadsheet_editor.php') ? 'active' : '' ?>"> Spreadsheet</a></li> -->
        <li><a href="employee_list.php" class="<?= ($current_page == 'settings.php') ? 'active' : '' ?>"> Employee Records</a></li> 
        <li><a href="summary.php" class="<?= ($current_page == 'settings.php') ? 'active' : '' ?>"> Manage Records</a></li>
        <li><a href="manage_admin.php" class="<?= ($current_page == 'settings.php') ? 'active' : '' ?>"> Manage Accounts</a></li>
      
        <!-- <a href="logout.php" class="logout-btn">Logout</a> -->
        
    </ul>
</div>
