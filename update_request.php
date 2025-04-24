<?php
session_start();
require_once 'db.php';

// ✅ Check if Admin is Logged In & Request ID is Provided
if (!isset($_SESSION['admin_id']) || !isset($_GET['id'])) {
    header("Location: manage_request.php");
    exit;
}

$request_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM request_letters WHERE id = ?");
$stmt->execute([$request_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    header("Location: manage_request.php");
    exit;
}

// ✅ Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE request_letters SET status = ? WHERE id = ?");
    $stmt->execute([$status, $request_id]);
    header("Location: manage_request.php?success=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Request Status</title>

    <!-- ✅ Include External CSS & Fonts -->
    <?php require_once 'includes/header_nav.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    <style>
        * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        .main-container { max-width: 600px; margin: 100px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; margin-bottom: 20px; font-size: 24px; font-weight: 600; }
        form { display: flex; flex-direction: column; gap: 15px; }
        label { font-weight: 600; margin-bottom: 5px; }
        select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        button { padding: 12px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: 0.3s; }
        button:hover { background: #0056b3; }
        .btn-back { display: block; text-align: center; margin-top: 10px; text-decoration: none; color: #007bff; font-size: 14px; }
        .btn-back:hover { text-decoration: underline; }
        .btn-secondary {
        background: #6c757d;  /* Bootstrap Secondary Color */
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        display: inline-block;
        text-align: center;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-secondary:hover {
        background: #5a6268;
        color: white;
    }
    footer {
            width: 100%;
            text-align: center;
            padding: 10px;
            background: #2C3E50;
            color: #fff;
            font-size: 10px;
            position: relative; /* Change from absolute to relative */
            margin-top: auto;
        }

        footer img.footer-logo {
            height: 60px;
            width: auto;
        }
    </style>
</head>
<body>

<?php require_once 'includes/side_nav.php'; ?>

<div class="main-container">
    <h2>Update Request Status</h2>

    <!-- ✅ Success Message -->
    <?php if (isset($_GET['success'])): ?>
        <script> Swal.fire({ title: 'Success!', text: 'Request status updated!', icon: 'success', confirmButtonColor: '#007bff' }); </script>
    <?php endif; ?>

    <form method="POST">
        <label for="status">Select Status:</label>
        <select name="status" id="status">
            <option value="Pending" <?= ($request['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
            <option value="Approved" <?= ($request['status'] == 'Approved') ? 'selected' : '' ?>>Approved</option>
            <option value="Denied" <?= ($request['status'] == 'Denied') ? 'selected' : '' ?>>Denied</option>
        </select>
        <button type="submit">Update Status</button>
        <a href="manage_request.php" class="btn btn-secondary">← Back to Requests</a>
    </form>
</div>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
