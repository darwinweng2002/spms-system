<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMS - Supply and Property Management System</title>
    
    <!-- ✅ Favicon Link (Ensure the file is in the correct path) -->
    <link rel="icon" type="image/png" href="upload/prmsu_logo.png"> 
    <link rel="shortcut icon" href="upload/prmsu_logo.png" type="image/png"> 

    <!-- ✅ Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- ✅ SweetAlert2 for Logout Confirmation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    /* ✅ Header Styling */
    .main-header {
        background: #F8F9FA;  
        color: #333;
        padding: 10px 20px;
        display: flex;
        justify-content: space-between; 
        align-items: center;
        position: fixed;
        top: 0;
        width: 100%;
        height: 75px;
        z-index: 1000;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.05);
    }

    /* Header Content */
    .header-content {
        display: flex;
        align-items: center;
        width: 100%;
        justify-content: space-between;
        padding: 0 15px;
    }

    /* Left side (Logo + Title) */
    .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* ✅ Header Text & Subtitle */
    .header-title {
        display: flex;
        flex-direction: column;
    }

    .header-title h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        line-height: 1.2;
    }

    /* ✅ Subtitle Below "SPMS" */
    .subtitle {
        margin: 0;
        font-size: 12px;
        color: #666;
    }

    /* ✅ Adjusted Logo */
    .header-logo {
        width: 50px;
        height: auto;
        opacity: 0.9;
    }

    /* ✅ Logout Button */
    .logout-btn {
        background: red;
        padding: 10px 15px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        text-decoration: none;
        transition: 0.3s ease;
        cursor: pointer;
    }

    .logout-btn:hover {
        background: darkred;
    }
    </style>
</head>
<body>

<header class="main-header">
    <div class="header-content">
        <div class="header-left">
            <img src="upload/prmsu_logo.png" alt="Logo" class="header-logo">
            <div class="header-title">
                <h2>SPMS</h2>
                <p class="subtitle">Supply and Property Management System</p>
            </div>
        </div>
     <!--   <a href="#" class="logout-btn" onclick="confirmLogout()">Logout</a> -->
    </div>
</header>

<script>
function confirmLogout() {
    Swal.fire({
        title: "Are you sure?",
        text: "You will be logged out of the system!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Logout"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "logout.php";
        }
    });
}
</script>

</body>
</html>
