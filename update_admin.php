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
    $name     = trim($_POST['name']);
    $username = trim($_POST['username']);
    $position = trim($_POST['position']);
    $campus   = trim($_POST['campus']);
    $existingAvatar = $_POST['existing_avatar'] ?? '';

    // ✅ Basic validation (add more if needed)
    if (empty($name) || empty($username) || empty($position) || empty($campus)) {
        die("All fields are required.");
    }

    // 🖼️ Handle avatar upload
    $avatarPath = $existingAvatar;
    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = 'upload/';
        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $uniqueFileName = 'admin_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
        $avatarPath = $uploadDir . $uniqueFileName;

        // Only allow jpg, jpeg, png
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
            die("Invalid image type. Only JPG, JPEG, and PNG allowed.");
        }

        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath)) {
            die("Failed to upload avatar.");
        }
    }

    // 💾 Update Database
    $updateStmt = $pdo->prepare("
        UPDATE admin_users 
        SET name = :name, username = :username, position = :position, campus = :campus, avatar = :avatar 
        WHERE id = :id
    ");
    $updateStmt->execute([
        'name'     => $name,
        'username' => $username,
        'position' => $position,
        'campus'   => $campus,
        'avatar'   => $avatarPath,
        'id'       => $admin_id
    ]);

    // ✅ Redirect
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
            background: rgba(255, 255, 255, 0.7); /* White overlay with transparency */
            z-index: -1;
        }

        /* Form Container */
        .form-container { 
            background: rgba(255, 255, 255, 0.9);
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); 
            width: 400px; 
            position: relative;
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
    <div class="form-container">
        <h2>Update Admin</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="name" placeholder="Enter full name" required>

            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Position:</label>
            <input type="text" name="position" value="<?php echo htmlspecialchars($admin['position'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Campus:</label>
            <input type="text" name="campus" value="<?php echo htmlspecialchars($admin['campus'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Profile Picture:</label>
            <input type="file" name="avatar" id="avatarInput" accept="image/png, image/jpeg, image/jpg">
            
            <div class="avatar-preview" style="display: none;">
            <img id="avatarPreview" src="" alt="Avatar Preview">
        </div>


            <button type="submit">Update Admin</button>
        </form>
        <a href="manage_admin.php" class="back-link">Back to Admin List</a>
    </div>
</div>

<script>
document.getElementById("avatarInput").addEventListener("change", function (event) {
    let reader = new FileReader();
    reader.onload = function () {
        document.getElementById("avatarPreview").src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
});
</script>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>

