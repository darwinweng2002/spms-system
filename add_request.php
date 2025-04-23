<?php
session_start();
require_once 'db.php';  // Database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestor_name = trim($_POST['requestor_name']);
    $purpose = trim($_POST['purpose']);
    $description = trim($_POST['description']);
    $date_received = $_POST['date_received'];

    $upload_dir = "uploads/";
    $file_path = "";

    if (!empty($_FILES['upload_letter']['name'])) {
        $file_tmp  = $_FILES['upload_letter']['tmp_name'];
        $file_name = $_FILES['upload_letter']['name'];
        $file_size = $_FILES['upload_letter']['size'];
        $file_type = mime_content_type($file_tmp);
        $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_mime = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
        $allowed_ext  = ['pdf', 'png', 'jpg', 'jpeg'];

        if (!in_array($file_type, $allowed_mime) || !in_array($ext, $allowed_ext)) {
            die(" Invalid file format. Only PDF, JPG, PNG allowed.");
        }

        $safe_name = time() . "_" . uniqid() . "." . $ext;
        $file_path = $upload_dir . $safe_name;

        if (!move_uploaded_file($file_tmp, $file_path)) {
            die(" Failed to upload the file.");
        }
    }

    // ✅ Insert into request_letters
    $stmt = $pdo->prepare("INSERT INTO request_letters 
        (requestor_name, purpose, description, date_received, upload_letter, created_at)
        VALUES (:requestor_name, :purpose, :description, :date_received, :upload_letter, NOW())");

    $stmt->execute([
        'requestor_name' => $requestor_name,
        'purpose' => $purpose,
        'description' => $description,
        'date_received' => $date_received,
        'upload_letter' => $file_path
    ]);

    $request_id = $pdo->lastInsertId();

    // ✅ Insert items
    if (!empty($_POST['items'])) {
        foreach ($_POST['items'] as $item) {
            $item_name = trim($item['name']);
            $item_description = trim($item['description']);
            $item_quantity = (int) $item['quantity'];

            $stmt = $pdo->prepare("INSERT INTO request_items 
                (request_id, item_name, item_description, item_quantity)
                VALUES (:request_id, :item_name, :item_description, :item_quantity)");

            $stmt->execute([
                'request_id' => $request_id,
                'item_name' => $item_name,
                'item_description' => $item_description,
                'item_quantity' => $item_quantity
            ]);
        }
    }

    header("Location: manage_request.php?success=added");
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Request Letter</title>

    <!-- ✅ Include External CSS & Fonts -->
    <?php require_once 'includes/header_nav.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    <style>
        * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }

        /* ✅ Layout */
        .main-container {
            max-width: 1200px;
            margin: 100px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding-bottom: 90px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        /* ✅ Form Design */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            resize: none;
            height: 100px;
        }

        /* ✅ File Upload Styling */
        .file-input {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .file-input label {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .file-input label:hover {
            background: #0056b3;
        }

        .file-input input {
            display: none;
        }

        /* ✅ File Name & Preview */
        .file-name {
            font-size: 14px;
            color: #333;
        }

        #file-preview {
            display: none;
            margin-top: 10px;
            max-width: 100px;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* ✅ Date Picker Styling */
        input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #007bff;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            background-color: #fff;
            color: #333;
            outline: none;
            transition: 0.3s ease-in-out;
        }

        input[type="date"]:hover,
        input[type="date"]:focus {
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* ✅ Submit Button */
        button {
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        /* ✅ Responsive Fix */
        @media (max-width: 768px) {
            .main-container {
                width: 95%;
                padding: 15px;
            }
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

<!-- ✅ Include Side Navigation -->
<?php require_once 'includes/side_nav.php'; ?>

<div class="main-container">
    <h2>Add Request Letter</h2>

    <!-- ✅ Success Message -->
    <?php if (isset($_GET['success'])): ?>
        <script>
            Swal.fire({ title: 'Success!', text: 'Request added successfully!', icon: 'success', confirmButtonColor: '#007bff' });
        </script>
    <?php endif; ?>

    <!-- ✅ Add Request Form -->
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label>Requested By (Department/Position)</label>
            <input type="text" name="requestor_name" placeholder="Enter Requestor Name" required>
        </div>
        <div id="items-container">
            <h5>Requested Items</h5>
            <div class="item-row d-flex gap-2 mb-2">
                <input type="text" name="items[0][name]" placeholder="Item Name" class="form-control" required>
                <input type="text" name="items[0][description]" placeholder="Description" class="form-control" required>
                <input type="number" name="items[0][quantity]" placeholder="Quantity" class="form-control" required>
                <button type="button" class="btn btn-danger remove-item">X</button>
            </div>
        </div>
       
        <div>
            <label>Date Received</label>
            <input type="date" id="date_received" name="date_received" required>
        </div>
        
        
        <div>
            <label>Description</label>
            <input type="text" name="description" placeholder="Enter description" required>
        </div>
       <!-- <div>
            <label>Stock Availability</label>
            <input type="text" name="quantity" placeholder="" required>
        </div> -->
        <button type="button" class="btn btn-success mb-3" id="add-item">+ Add Item</button>
        <!-- ✅ File Upload with Preview -->
        <div>
            <label>Remarks</label>
            <textarea name="purpose" placeholder="Enter Remarks" required></textarea>
        </div>
        <label>Upload Request Letter (Image or PDF)</label>
        <div class="file-input">
            <label for="file-upload">Choose File</label>
            <input type="file" id="file-upload" name="upload_letter" accept="image/png, image/jpeg, image/jpg, application/pdf" required>
            <span class="file-name" id="file-name">No file chosen</span>
        </div>

        <!-- ✅ Image Preview -->
        <img id="file-preview" src="#" alt="Image Preview">

        <button type="submit">Add Request</button>
    </form>
</div>

<script>
   document.getElementById('file-upload').addEventListener('change', function(event) {
    let file = event.target.files[0];
    let fileName = document.getElementById('file-name');
    let filePreview = document.getElementById('file-preview');

    if (file) {
        fileName.textContent = file.name;
        let isImage = file.type.startsWith("image/");
        
        if (isImage) {
            let reader = new FileReader();
            reader.onload = function() {
                filePreview.src = reader.result;
                filePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            filePreview.style.display = 'none'; // No preview for PDF
        }
    }
});

    let itemIndex = 1;
    document.getElementById('add-item').addEventListener('click', function() {
        let container = document.getElementById('items-container');
        let newRow = document.createElement('div');
        newRow.classList.add('item-row', 'd-flex', 'gap-2', 'mb-2');
        newRow.innerHTML = `
            <input type="text" name="items[${itemIndex}][name]" placeholder="Item Name" class="form-control" required>
            <input type="text" name="items[${itemIndex}][description]" placeholder="Description" class="form-control" required>
            <input type="number" name="items[${itemIndex}][quantity]" placeholder="Quantity" class="form-control" required>
            <button type="button" class="btn btn-danger remove-item">X</button>
        `;
        container.appendChild(newRow);
        itemIndex++;
    });

    document.getElementById('items-container').addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-item')) {
            event.target.parentElement.remove();
        }
    });
</script>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
