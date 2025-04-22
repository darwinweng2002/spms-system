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
        <li><a href="add_employee.php" class="<?= ($current_page == 'manage_users.php') ? 'active' : '' ?>"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-plus-icon lucide-user-round-plus"><path d="M2 21a8 8 0 0 1 13.292-6"/><circle cx="10" cy="8" r="5"/><path d="M19 16v6"/><path d="M22 19h-6"/></svg> Add Employee</a></li>
         <li><a href="manage_request.php" class="<?= ($current_page == 'manage_request.php') ? 'active' : '' ?>"> Request</a></li> 
       <!--   <li><a href="spreadsheet_editor.php" class="<?= ($current_page == 'spreadsheet_editor.php') ? 'active' : '' ?>"> Spreadsheet</a></li> -->
        <li><a href="employee_list.php" class="<?= ($current_page == 'settings.php') ? 'active' : '' ?>"> Employee Records</a></li> 
        <li><a href="manage_admin.php" class="<?= ($current_page == 'settings.php') ? 'active' : '' ?>"> Manage Accounts</a></li>
      
        <!-- <a href="logout.php" class="logout-btn">Logout</a> -->
        
    </ul>
</div>
