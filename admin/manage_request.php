<?php
session_start();
require_once 'db.php';  // Database connection

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// ✅ Fetch Request Letters with Search functionality
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
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .main-container {
            max-width: 1600px;
            margin: 100px 250px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
            font-size: 16px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        img {
            width: 80px;
            height: auto;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }
        img:hover {
            transform: scale(1.1);
        }
        .btn-update {
            padding: 8px 12px;
            background: #28a745;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            border: none;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }
        .btn-delete {
            padding: 8px 12px;
            background: #dc3545;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            border: none;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }
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
        .btn-add:hover {
            background: #0056b3;
        }
        .requested-items ul {
            list-style-type: disc;
            margin-left: 20px;
        }
        .requested-items li {
            text-align: left;
        }

        /* 🔹 Status Styles */
        .status-approved, .status-denied, .status-pending {
            display: inline-block;
            width: 120px;  /* Fixed width for consistent display */
            text-align: center;
            padding: 8px 0;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            color: white;
        }
        .status-approved { background: #28a745; }  /* Green for Approved */
        .status-denied { background: #dc3545; }  /* Red for Denied */
        .status-pending { background: #6c757d; }  /* Gray for Pending */
        
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
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
        .requested-items-wrapper {
    display: flex;
    flex-direction: column;
    gap: 12px; /* Adds spacing between requested items */
}

.requested-item-card {
    background: #ffffff;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    border-left: 4px solid #007bff; /* Blue left border for a clean accent */
}

.requested-item-card h4 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
    color: #007bff; /* Highlight item name */
}

.requested-item-card .description {
    font-size: 14px;
    color: #555;
    margin: 4px 0;
}

.requested-item-card .quantity {
    font-size: 14px;
    color: #333;
}

    </style>
</head>

<body>
<?php require_once 'includes/side_nav.php'; ?>

<div class="main-container">
    <h2>Manage Request Letters</h2>

    <div class="search-container text-center mb-4">
        <form method="GET" action="">
            <input type="text" name="search" class="search-input form-control d-inline-block w-50"
                   placeholder="Search"
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-btn btn btn-primary">Search</button>
        </form>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'Request updated successfully!',
                icon: 'success',
                confirmButtonColor: '#007bff'
            });
        </script>
    <?php endif; ?>

    <a href="add_request.php" class="btn btn-add mb-3">+ Add Request</a>

    <table>
        <thead>
        <tr>
            <th>No.</th>
            <th>Requested By (Department/Position)</th>
            
            <th>Requested Items</th>  <!-- Nested Items List -->
            <th>Date Received</th>
            <th>Description</th>
          
            <th>Status</th>
            <th>Remarks</th>
            <th>Request Letter</th>
              <!-- Status Column Added Back -->
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $counter = 1;
        foreach ($request_letters as $request):
            $request_id = $request['id'];

            // ✅ Fetch related requested items for each request
            $item_stmt = $pdo->prepare("SELECT * FROM request_items WHERE request_id = ?");
            $item_stmt->execute([$request_id]);
            $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

            // ✅ Determine status class
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
            <tr>
                <td><?= $counter++ ?></td>
                <td><?= htmlspecialchars($request['requestor_name']) ?></td>
               
                <td class="requested-items">
                <div class="requested-items-wrapper">
                    <?php foreach ($items as $item): ?>
                        <div class="requested-item-card">
                            <h4><?= htmlspecialchars($item['item_name']) ?></h4>
                            <p class="description"><?= htmlspecialchars($item['item_description']) ?></p>
                            <span class="quantity">Quantity: <strong><?= htmlspecialchars($item['item_quantity']) ?></strong></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </td>


                <td><?= htmlspecialchars($request['date_received']) ?></td>
                <td><?= htmlspecialchars($request['description']) ?></td>
               
                <td>
                    <span class="<?= $status_class ?>"><?= $status ?></span>  <!-- Display status with color -->
                </td>
                <td><?= htmlspecialchars($request['purpose']) ?></td>
                <td>
    <?php 
    $image_path = htmlspecialchars($request['upload_letter']);  // Get the image path from the database

    // Check if the image file exists and display it, otherwise show a default image
    if (!empty($image_path) && file_exists($image_path)): ?>
        <a href="<?= $image_path ?>" data-lightbox="request-letters" data-title="<?= htmlspecialchars($request['requestor_name']) ?>">
            <img src="<?= $image_path ?>?t=<?= time() ?>" alt="Request Letter Image" class="img-thumbnail">
        </a>
    <?php else: ?>
        <img src="uploads/default.png" alt="No Image Available" class="img-thumbnail">
    <?php endif; ?>
</td>
                <td>
                    <a href="update_request.php?id=<?= $request['id'] ?>" class="btn-update">Update</a>
                    <form action="delete_request.php" method="POST" class="delete-form d-inline-block">
                    <input type="hidden" name="id" value="<?= $request['id'] ?>">
                    <button type="submit" class="btn-delete">Delete</button>
                </form>
                </td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($request_letters)): ?>
            <tr>
                <td colspan="9" class="text-center text-danger font-weight-bold">No Requests Found</td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.delete-form');  // Correct selector

    deleteForms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();  // Prevent form submission

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
                    this.submit();  // Submit the form if user confirms
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
