<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php'; // Ensure database connection

// ✅ Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];

    // ✅ Fetch Admin Data (Including Avatar)
    $stmt = $pdo->prepare("SELECT name, avatar FROM admin_users WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Set Default Avatar if None Exists
    $admin_name = $admin['name'] ?? "Admin";
    $admin_avatar = !empty($admin['avatar']) ? $admin['avatar'] : "uploads/default-avatar.png";
} else {
    $admin_name = "Admin";
    $admin_avatar = "uploads/default-avatar.png";
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
/* ✅ Fix Header Spacing & Alignment */
.main-header {
    background: #F8F9FA;  
    color: #333;
    padding: 10px 30px;
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

/* ✅ Ensure Content is Properly Spaced */
.header-content {
    display: flex;
    align-items: center;
    width: 100%;
    justify-content: space-between;
}

/* ✅ Left Side (Logo + Title) */
.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* ✅ Header Text & Subtitle */
.header-title {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.header-title h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
}

/* ✅ Subtitle Below "SPMS" */
.subtitle {
    margin: 0;
    font-size: 12px;
    color: #666;
}

/* ✅ Adjust Logo */
.header-logo {
    width: 55px;
    height: auto;
    opacity: 0.9;
}

/* ✅ Profile & Logout Section */
.header-right {
    display: flex;
    align-items: center;
    gap: 15px;  /* Ensure spacing between profile & logout */
}

/* ✅ Profile Container */
.profile-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ✅ Profile Avatar */
.profile-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
    transition: 0.3s ease-in-out;
}

/* ✅ Profile Name */
.profile-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
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
    display: flex;
    align-items: center;
}

.logout-btn:hover {
    background: darkred;
}
/* ✅ Red Circular Loader */
.custom-loader {
    border: 5px solid #f3f3f3;        /* Light gray background */
    border-top: 5px solid #0056b3;;    /* Bootstrap danger red */
    border-radius: 50%;
    width: 60px;
    height: 60px;
    margin: 20px auto;
    animation: spin 1s linear infinite;
}

/* 🔁 Spin Animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
    </style>
</head>
<body>

<header class="main-header">
    <div class="header-content">
        <!-- ✅ Left Side: Logo + Title -->
        <div class="header-left">
            <img src="upload/prmsu_logo.png" alt="Logo" class="header-logo">
            <div class="header-title">
                <h2>SPMS</h2>
                <p class="subtitle">Supply and Property Management System</p>
            </div>
        </div>

        <!-- ✅ Right Side: Profile + Logout -->
        <div class="header-right">
            <div class="profile-container">
                <img src="<?= htmlspecialchars($admin_avatar) ?>" alt="Admin Avatar" class="profile-avatar">
                <span class="profile-name"><?= htmlspecialchars($admin_name) ?></span>
            </div>
            <a href="#" class="logout-btn" onclick="confirmLogout()">
                <i class="bi bi-box-arrow-right" style="margin-right: 6px;"></i> Logout
            </a>
        </div>
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
            // ✅ Custom Circular Loader UI
            Swal.fire({
                title: "Logging out...",
                html: `<div class="custom-loader"></div>`,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    // 👁️ Activate loader on modal open
                    setTimeout(() => {
                        window.location.href = "logout.php";
                    }, 1500);
                }
            });
        }
    });
}
</script>
</body>
</html>
