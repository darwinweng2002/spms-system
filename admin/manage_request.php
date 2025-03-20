<?php
session_start();
require_once 'db.php'; // Database connection

// ✅ Fetch Request Letters with Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM request_letters 
                         WHERE requestor_name LIKE :search 
                         OR purpose LIKE :search 
                         OR description LIKE :search 
                         ORDER BY created_at DESC");
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM request_letters ORDER BY created_at DESC");
}

$request_letters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Request Letters</title>

    <?php require_once 'includes/header_nav.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    <style>
        * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        .main-container { max-width: 1600px; margin: 100px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; margin-bottom: 20px; font-size: 24px; font-weight: 600; }
        table { width: 1300px; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        th, td { padding: 12px; text-align: center; border: 1px solid #ddd; }
        th { background: #007bff; color: white; font-size: 16px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        img { width: 80px; height: auto; border-radius: 5px; cursor: pointer; transition: transform 0.3s ease-in-out; }
        img:hover { transform: scale(1.1); }
        .btn-add { 
    background: #007bff; 
    color: white; 
    padding: 10px 20px; 
    border: none; 
    border-radius: 5px; 
    font-size: 16px; 
    display: block; 
    margin: 0 auto; 
    text-align: center; 
    transition: 0.3s; 
}

.btn-add:hover { background: #0056b3; }

/* 🔹 Update Button */
.btn-update {
    background: #28a745;  /* ✅ Match the green tone */
    color: white;  /* ✅ Ensure text color is always white */
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
     
}

.btn-update:hover {
    background: #218838;  /* ✅ Slightly darker green on hover */
    color: white;  /* ✅ Prevent text from turning black */
}

/* 🔹 Delete Button */
.btn-danger {
    background: #dc3545; 
    color: white; 
    border: none; 
    padding: 5px 10px; 
    border-radius: 5px; 
    cursor: pointer; 
    transition: 0.3s;

}

.btn-danger:hover {
    background: #b02a37;
    color: white;
}

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
        /* 🔹 Status Styles */
/* 🔹 Status Label Styles */
.status-approved,
.status-denied,
.status-pending {
    display: inline-block;
    width: 120px;  /* ✅ Set a fixed width */
    text-align: center;  /* ✅ Center text */
    padding: 8px 0;  /* ✅ Ensure same vertical padding */
    border-radius: 5px;
    font-weight: bold;
    font-size: 14px;
    color: white;
}

/* 🔹 Status Colors */
.status-approved { background: #28a745; }  /* Green */
.status-denied { background: #dc3545; }  /* Red */
.status-pending { background: #6c757d; }  /* Gray */
.search-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-input {
            width: 60%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .search-btn {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-btn:hover {
            background: #0056b3;
        }

    </style>
</head>
<body>

<?php require_once 'includes/side_nav.php'; ?>

<div class="main-container">
    <h2>Manage Request Letters</h2>
    <div class="search-container">
        <form method="GET" action="">
            <input 
                type="text" 
                name="search" 
                class="search-input" 
                placeholder="Search by Requestor Name, Item/Supply, or Description..." 
                value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-btn">Search</button>
        </form>
    </div>
    <?php if (isset($_GET['success'])): ?>
        <script> Swal.fire({ title: 'Success!', text: 'Request updated successfully!', icon: 'success', confirmButtonColor: '#007bff' }); </script>
    <?php endif; ?>

    <a href="add_request.php" class="btn btn-add mb-3">+ Add Request</a>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Requestor Name</th> 
                <th>Item/Supply Requested</th>
                <th>Date Received</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Request Letter</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php 
    $counter = 1;
    if (!empty($request_letters)): 
        foreach ($request_letters as $request): ?>
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($request['requestor_name']) ?></td>
                <td><?= htmlspecialchars($request['purpose']) ?></td>
                <td><?= htmlspecialchars($request['date_received']) ?></td>
                <td><?= htmlspecialchars($request['description']) ?></td>
                <td><?= htmlspecialchars($request['quantity']) ?></td>
                <td>
                    <?php 
                    $image_path = htmlspecialchars($request['upload_letter']);
                    if (!empty($image_path) && file_exists($image_path)): ?>
                        <a href="<?= $image_path ?>" data-lightbox="request-letters" data-title="<?= htmlspecialchars($request['requestor_name']) ?>">
                            <img src="<?= $image_path ?>?t=<?= time() ?>" alt="Request Letter">
                        </a>
                    <?php else: ?>
                        <img src="uploads/default.png" alt="No Image">
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                        $status = htmlspecialchars($request['status']); 
                        $status_class = '';

                        if ($status === 'Approved') {
                            $status_class = 'status-approved';
                        } elseif ($status === 'Denied') {
                            $status_class = 'status-denied';
                        } else {
                            $status_class = 'status-pending';
                        }
                    ?>
                    <span class="<?= $status_class ?>"><?= $status ?></span>
                </td>
                <td>
                    <a href="update_request.php?id=<?= $request['id'] ?>" class="btn btn-update">Update</a>
                    <form action="delete_request.php" method="POST" class="delete-form" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $request['id'] ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="9" style="text-align: center; padding: 20px; font-weight: bold; color: #dc3545;">
                Results not found
            </td>
        </tr>
    <?php endif; ?>
</tbody>

    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent form submission

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This request will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Submit the form if confirmed
                    }
                });
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function () {
        const searchTerm = searchInput.value.toLowerCase().trim();

        tableRows.forEach(row => {
            const requestorName = row.children[1].textContent.toLowerCase();
            const itemRequested = row.children[2].textContent.toLowerCase();
            const description = row.children[4].textContent.toLowerCase();

            const isVisible = [requestorName, itemRequested, description].some(text => 
                text.includes(searchTerm)
            );

            row.style.display = isVisible ? '' : 'none';
        });
    });
});
</script>


<?php if (isset($_GET['success'])): ?>
        <script> Swal.fire({ title: 'Success!', text: 'Request updated successfully!', icon: 'success', confirmButtonColor: '#007bff' }); </script>
    <?php endif; ?>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
