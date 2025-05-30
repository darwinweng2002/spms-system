<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'db.php';

// 🛡️ Access Control
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// 🔍 Sanitize & Validate Admin ID from URL
$admin_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$admin_id) {
    die("Invalid admin ID.");
}

// 🧠 Fetch Admin Data
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = :id");
$stmt->execute(['id' => $admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("Admin not found.");
}

// 📝 Update Logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fields = [];
    $params = ['id' => $admin_id]; // Always include ID

    // Optional updates — only include if filled
    if (!empty(trim($_POST['name']))) {
        $fields[] = "name = :name";
        $params['name'] = trim($_POST['name']);
    }

    if (!empty(trim($_POST['username']))) {
        $fields[] = "username = :username";
        $params['username'] = trim($_POST['username']);
    }

    if (!empty(trim($_POST['position']))) {
        $fields[] = "position = :position";
        $params['position'] = trim($_POST['position']);
    }

    if (!empty(trim($_POST['campus']))) {
        $fields[] = "campus = :campus";
        $params['campus'] = trim($_POST['campus']);
    }

    // Password handling
    if (!empty($_POST['password'])) {
        if ($_POST['password'] !== $_POST['confirm_password']) {
            die("Passwords do not match.");
        }
        $fields[] = "password = :password";
        $params['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Avatar upload
    $existingAvatar = $_POST['existing_avatar'] ?? '';
    $avatarPath = $existingAvatar;

    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = 'upload/';
        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = 'admin_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
        $avatarPath = $uploadDir . $uniqueFileName;

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
            die("Invalid image type. Only JPG, JPEG, and PNG allowed.");
        }

        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath)) {
            die("Failed to upload avatar.");
        }

        $fields[] = "avatar = :avatar";
        $params['avatar'] = $avatarPath;
    }

    // If no fields were updated
    if (empty($fields)) {
        die("No changes made.");
    }

    // Build the SQL
    $sql = "UPDATE admin_users SET " . implode(', ', $fields) . " WHERE id = :id";
    $updateStmt = $pdo->prepare($sql);
    $updateStmt->execute($params);

    header("Location: manage_admin.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin</title>
    <?php require_once 'includes/header_nav.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT+oY9H/gyG3Yspz8y8pU5tT" crossorigin="anonymous">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
            font-family: 'Poppins', sans-serif; 
        }

        /* Page Layout */
        html, body {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* Background Image */
        body { 
            background: url('uploads/background.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Overlay */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* Form Container */
        .form-container {
        background: rgba(255, 255, 255, 0.9);
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        max-width: 900px; /* ✅ Let it resize gracefully */
        width: 100%;
        margin: 100px auto; /* ✅ Center horizontally */
        padding-bottom: 85px;
    }


        h2 { font-size: 22px; margin-bottom: 20px; }
        
        label { 
            display: block; 
            font-size: 14px; 
            font-weight: 500; 
            margin-top: 10px; 
            text-align: left;
        }

        input { 
            width: 100%; 
            padding: 10px; 
            margin-top: 5px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 14px; 
        }

        button { 
            margin-top: 15px; 
            width: 100%; 
            padding: 12px; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: 0.3s; 
        }

        button:hover { background: #0056b3; }

        /* Avatar Preview */
        .avatar-preview { 
            margin-top: 10px; 
            display: flex; 
            justify-content: center; 
        }

        .avatar-preview img { 
            width: 80px; 
            height: 80px; 
            border-radius: 50%; 
            border: 2px solid #ddd; 
        }

        .back-link { 
            display: block; 
            margin-top: 15px; 
            font-size: 14px; 
            text-decoration: none; 
            color: #007bff; 
        }

        .back-link:hover { text-decoration: none; }
        .card-header {
            background: #0080ff;
            font-size: 1.2rem;
            padding: 15px 20px;
            text-align: center;
            font-weight: 600;
            color: #f8f9fa;
      }

        /* Fixed Footer */
        footer {
            width: 100%;
            text-align: center;
            padding: 2px;
            background: #2C3E50;
            color: #fff;
            font-size: 10px;
            position: absolute;
            bottom: 0;
        }

        footer img.footer-logo {
            height: 60px;
            width: auto;
        }
        body {
    background: url('upload/prmsu_logo.png');
    background-size: cover;
    position: relative;
}
    </style>
</head>
<body>

<?php require_once 'includes/side_nav.php'; ?>

<div class="container">
    <div class="form-container card">
    <div class="card-header">
    <i class="bi bi-person-lines-fill"></i> Update Admin
  </div>
        <form method="POST" enctype="multipart/form-data">
    <div class="row g-3">
        <!-- Name -->
        <div class="col-md-6">
            <label>Name</label>
            <input type="text" name="name"
                value="<?= isset($admin['name']) ? htmlspecialchars($admin['name'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                placeholder="Enter full name" class="form-control">
        </div>

        <!-- Username -->
        <div class="col-md-6">
            <label>Username</label>
            <input type="text" name="username"
                value="<?= isset($admin['username']) ? htmlspecialchars($admin['username'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                class="form-control">
        </div>

        <!-- Position -->
        <div class="col-md-6">
            <label>Position</label>
            <input type="text" name="position"
                value="<?= isset($admin['position']) ? htmlspecialchars($admin['position'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                class="form-control">
        </div>

        <!-- Campus -->
        <div class="col-md-6">
            <label>Campus</label>
            <input type="text" name="campus"
                value="<?= isset($admin['campus']) ? htmlspecialchars($admin['campus'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                class="form-control">
        </div>

        <!-- Password -->
        <div class="col-md-6">
            <label>New Password</label>
            <input type="password" name="password" id="password" class="form-control"
                placeholder="Leave blank to keep current password">
        </div>

        <!-- Confirm Password -->
        <div class="col-md-6">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                placeholder="Re-enter password">
            <span id="passwordFeedback" style="font-size: 13px;"></span>
        </div>

        <!-- Upload Avatar -->
        <div class="col-md-6">
            <label>Profile Picture</label>
            <input type="file" name="avatar" id="avatarInput" accept="image/png, image/jpeg, image/jpg" class="form-control">
            <input type="hidden" name="existing_avatar"
                value="<?= isset($admin['avatar']) ? htmlspecialchars($admin['avatar'], ENT_QUOTES, 'UTF-8') : ''; ?>">
        </div>

        <!-- Preview Avatar -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="avatar-preview">
                <img id="avatarPreview"
                    src="<?= !empty($admin['avatar']) ? htmlspecialchars($admin['avatar'], ENT_QUOTES, 'UTF-8') : 'uploads/default-avatar.png'; ?>"
                    alt="Avatar Preview" style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #ddd;">
            </div>
        </div>

        <!-- Submit -->
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Update Admin</button>
        </div>

        <!-- Back Link -->
    </div>
</form>

</div>

<script>
document.getElementById("avatarInput").addEventListener("change", function (event) {
    let reader = new FileReader();
    reader.onload = function () {
        document.getElementById("avatarPreview").src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
});
const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("confirm_password");
const feedback = document.getElementById("passwordFeedback");

function checkPasswordsMatch() {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (!confirmPassword && !password) {
        feedback.textContent = "";
        return;
    }

    if (password === confirmPassword) {
        feedback.textContent = "✅ Passwords match";
        feedback.style.color = "green";
    } else {
        feedback.textContent = "❌ Passwords do not match";
        feedback.style.color = "red";
    }
}

passwordInput.addEventListener("input", checkPasswordsMatch);
confirmPasswordInput.addEventListener("input", checkPasswordsMatch);
</script>
<!-- ✅ Bootstrap Bundle JS (with Popper.js included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6VKHr8PpnxE6BvPpZRmEZ3zDkEVm9zI5nVIp9Sfoef+N" crossorigin="anonymous"></script>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>

