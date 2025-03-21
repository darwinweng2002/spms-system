<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sample code to test error reporting (Removed errors)
echo "Testing PHP Error Reporting<br>";

// Ensure variable is defined before use
$undefinedVariable = "Defined variable";
echo $undefinedVariable;

// Prevent division by zero
$divisor = 1; // Avoid zero
if ($divisor != 0) {
    $result = 10 / $divisor;
    echo "<br>Result: $result";
}

// Remove the undefined function
// undefinedFunction(); // This line has been removed

session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Get admin ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid admin ID.");
}

$admin_id = $_GET['id'];

// Fetch admin data
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = :id");
$stmt->execute(['id' => $admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("Admin not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $position = trim($_POST['position']);
    $campus = trim($_POST['campus']);

    // Handle avatar upload
    if (!empty($_FILES['avatar']['name'])) {
        $avatarPath = 'upload/' . basename($_FILES['avatar']['name']);
        move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath);
    } else {
        $avatarPath = $admin['avatar']; // Keep existing avatar if no new file is uploaded
    }

    $stmt = $pdo->prepare("UPDATE admin_users SET name = :name, username = :username, position = :position, campus = :campus, avatar = :avatar WHERE id = :id");
    $stmt->execute([
        'name' => $name,
        'username' => $username,
        'position' => $position,
        'campus' => $campus,
        'avatar' => $avatarPath,
        'id' => $admin_id
    ]);

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
            <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Position:</label>
            <input type="text" name="position" value="<?php echo htmlspecialchars($admin['position'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Campus:</label>
            <input type="text" name="campus" value="<?php echo htmlspecialchars($admin['campus'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Profile Picture:</label>
            <input type="file" name="avatar" id="avatarInput" accept="image/png, image/jpeg, image/jpg">
            
            <div class="avatar-preview">
                <img id="avatarPreview" src="<?php echo htmlspecialchars($admin['avatar']); ?>" alt="Admin Avatar">
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

