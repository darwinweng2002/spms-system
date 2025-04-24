<?php
session_start();
require_once 'db.php';  // Database connection

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';  // New filter for status

$noResults = false;  // Initialize a flag for tracking empty search results

// ✅ Fetch Request Letters with search and filter functionality
$query = "SELECT * FROM request_letters WHERE 1";

if ($search) {
    $query .= " AND (requestor_name LIKE :search OR purpose LIKE :search OR description LIKE :search)";
}
if ($filter) {
    $query .= " AND status = :status";
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);

$params = [];
if ($search) $params['search'] = "%$search%";
if ($filter) $params['status'] = $filter;

$stmt->execute($params);
$request_letters = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($request_letters)) {
    $noResults = true;  // Set to true if no results are found
}
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
            margin-left: 250px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding-bottom: 80px;
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
        
        .sub-header {
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
@media print {
    .btn, .search-container, form, footer, .btn-update, .btn-delete, .delete-form {
        display: none !important;  /* Hides all non-data elements */
    }

    /* Make the table look clean during printing */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
}
.print-button {
    background-color: #007bff; /* Blue background */
    color: #fff; /* White text */
    border: none; /* No border */
    padding: 10px 20px; /* Padding for size */
    cursor: pointer;
    border-radius: 5px; /* Rounded corners */
    transition: background-color 0.3s;
}

.print-button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}
#noResultsMessage {
    margin-top: 20px;
    font-size: 18px;
    font-weight: bold;
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
        .dropdown-menu .dropdown-item i {
    margin-right: 6px;
}
/* Hover state to simulate button feel inside dropdown */
.dropdown-item.text-danger:hover {
  background-color: #f8d7da;
  color: #842029;
}

    </style>
</head>

<body>
<?php require_once 'includes/side_nav.php'; ?>

<div class="main-container">
    <h2 class="sub-header">Manage Request Letters</h2>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <form method="GET" class="d-flex w-75">
        <span class="input-group-text bg-primary text-white">
                        <i class="bi bi-search"></i>
                    </span>
            <input type="text" name="search" class="form-control me-2" value="<?= htmlspecialchars($search) ?>" placeholder="Search">
            
            <select name="status_filter" class="form-select me-2">
                <option value="">All</option>
                <option value="Approved" <?= $filter === 'Approved' ? 'selected' : '' ?>>Approved</option>
                <option value="Pending" <?= $filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Denied" <?= $filter === 'Denied' ? 'selected' : '' ?>>Denied</option>
            </select>

            <button class="btn btn-primary" type="submit">Search</button>
        </form>

        <button class="print-button" onclick="printTable()">
            <i class="bi bi-printer"></i> Print Request Letters
        </button>
    </div>
    <?php if (isset($_GET['success'])): ?>
<script>
    let successType = "<?= $_GET['success'] ?>";
    let message = "";

    switch (successType) {
        case "added":
            message = "Request added successfully!";
            break;
        case "updated":
            message = "Request updated successfully!";
            break;
        default:
            message = "Success!";
    }

    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        confirmButtonColor: '#007bff'
    });

    // ✅ Clean the URL
    if (window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url.pathname + url.search);
    }
</script>
<?php endif; ?>
    <a href="add_request.php" class="btn btn-add mb-3">+ Add Request</a>

    <table>
    <div id="noResultsMessage" class="alert alert-danger text-center d-none">No results found</div>  
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
                $file_path = htmlspecialchars($request['upload_letter']);  // File path stored in DB
                $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

                if (!empty($file_path) && file_exists($file_path)):
                    if ($file_extension === 'pdf'): ?>
                        <a href="<?= $file_path ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-earmark-pdf-fill"></i> View PDF
                        </a>
                    <?php elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                        <a href="<?= $file_path ?>" data-lightbox="request-letters" data-title="<?= htmlspecialchars($request['requestor_name']) ?>">
                            <img src="<?= $file_path ?>?t=<?= time() ?>" alt="Request Letter Image" class="img-thumbnail">
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Unsupported file</span>
                    <?php endif; ?>
                <?php else: ?>
                    <img src="uploads/default.png" alt="No File Available" class="img-thumbnail">
                <?php endif; ?>

</td>
<td>
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Actions
        </button>
        <ul class="dropdown-menu">
            <!-- Update Link -->
            <li>
                <a class="dropdown-item text-success" href="update_request.php?id=<?= $request['id'] ?>">
                    <i class="bi bi-pencil-square"></i> Update
                </a>
            </li>
            <!-- Delete Action Styled Like Link -->
            <li>
                <a href="#" class="dropdown-item text-danger delete-request" data-id="<?= $request['id'] ?>">
                    <i class="bi bi-trash3-fill"></i> Delete
                </a>
            </li>
        </ul>
    </div>
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
    const deleteButtons = document.querySelectorAll('.delete-request');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const requestId = this.getAttribute('data-id');

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
                    // Redirect to PHP delete handler (you must have this in your backend)
                    window.location.href = `delete_request.php?id=${requestId}`;
                }
            });
        });
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.form-control');  // Select search input
    const tableRows = document.querySelectorAll('tbody tr');
    const noResultsMessage = document.getElementById('noResultsMessage');  // Select the message div

    searchInput.addEventListener('input', function () {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleRowCount = 0;

        tableRows.forEach(row => {
            const requestorName = row.children[1].textContent.toLowerCase();
            const itemRequested = row.children[2].textContent.toLowerCase();
            const description = row.children[4].textContent.toLowerCase();

            const isVisible = [requestorName, itemRequested, description].some(text => 
                text.includes(searchTerm)
            );

            row.style.display = isVisible ? '' : 'none';  // Toggle visibility
            if (isVisible) visibleRowCount++;  // Count visible rows
        });

        // Display "No results found" if no rows match
        noResultsMessage.classList.toggle('d-none', visibleRowCount !== 0);
    });
});


function printTable() {
    // Extract only the table content for printing
    const tableContent = document.querySelector('table').outerHTML;

    // Create a new window and write the HTML for the print layout
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.open();
    printWindow.document.write(`
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Print Request Letters</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Poppins', sans-serif;
                    padding: 20px;
                }

                .header-container {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 10px 20px;
                    border-bottom: 2px solid black;
                }

                .header-left {
                    width: 100px; /* Adjust logo size */
                }

                .header-left img {
                    width: 100%;
                    height: auto;
                }

                .header-right {
                    text-align: center;
                    flex-grow: 1;
                }

                .header-right h1 {
                    font-size: 18px;
                    font-weight: bold;
                    text-transform: uppercase;
                }

                .header-right p {
                    font-size: 14px;
                    margin: 5px 0;
                }

                .progress-report {
                    text-align: center;
                    margin-top: 10px;
                    font-size: 16px;
                    font-weight: bold;
                    letter-spacing: 1px;
                    border-top: 2px solid black;
                    padding-top: 5px;
                }

                a {
                    color: blue;
                    text-decoration: none;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }

                th, td {
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: center;
                }

                th {
                    background: #007bff;
                    color: white;
                }

                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }

                h2 {
                    text-align: center;
                    margin-bottom: 20px;
                }
                
                td:last-child, th:last-child {
                    display: none;
                }

                td img {
                    width: 200px;
                    height: auto;
                    display: block;
                    margin: 0 auto;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    padding: 5px;
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                }
            </style>
        </head>
        <body>

            <div class="header-container">
                <div class="header-left">
                    <img src="upload/prmsu_logo.png" alt="University Logo"> 
                </div>
                <div class="header-right">
                    <h1>Republic of the Philippines</h1>
                    <h1>President Ramon Magsaysay State University</h1>
                    <p>Iba, Zambales, Philippines</p>
                    <p>Tel./Fax No. (047) 811-1683 | <a href="mailto:rmtupresident@yahoo.com">rmtupresident@yahoo.com</a> | 
                        <a href="http://www.prmsu.edu.ph" target="_blank">www.prmsu.edu.ph</a></p>
                </div>
            </div>

            <h2>Request Letters</h2>
            ${tableContent}

        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
}

document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const requestId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action will permanently delete the request.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX Request to delete the request
                    fetch('delete_request.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${encodeURIComponent(requestId)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Deleted!', 'The request has been deleted.', 'success');
                            setTimeout(() => window.location.reload(), 1500); // Refresh the page after deletion
                        } else {
                            Swal.fire('Error!', 'Failed to delete the request.', 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error!', 'An error occurred.', 'error'));
                }
            });
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>