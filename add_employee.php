    <?php
    session_start();
    require_once 'db.php'; // Database connection

    // Check if admin is logged in
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit;
    }

    // ✅ Handle Employee Insertion
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_employee'])) {
        $last_name = trim($_POST['last_name']);
        $first_name = trim($_POST['first_name']);
        $middle_name = trim($_POST['middle_name']);
        $position = trim($_POST['position']);
        $campus = trim($_POST['campus']); 

        // ✅ Insert into Database
        $stmt = $pdo->prepare("INSERT INTO employees (last_name, first_name, middle_name, position, campus, created_at) 
                            VALUES (:last_name, :first_name, :middle_name, :position, :campus, NOW())");
        $stmt->execute([
            'last_name' => $last_name,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'position' => $position,
            'campus' => $campus
        ]);

        // ✅ Redirect back to the same page with a success message
        header("Location: add_employee.php?success=1");
        exit;
    }

    // ✅ Fetch Employees from Database
    // ✅ Fetch Employees from Database Sorted by Last Name
$employees = $pdo->query("SELECT * FROM employees ORDER BY last_name ASC")->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Employee</title>
        <?php require_once 'includes/header_nav.php'; ?>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            * { font-family: 'Poppins', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }

            /* ✅ Main Page Layout */
            .main-container {
                max-width: 1600px;
                margin: 100px;
                margin-left: 250px;
                background: white;
                padding: 25px;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                margin-right: 2 00px;
            }

            .sub-header {
                text-align: center;
                margin-bottom: 20px;
                font-size: 24px;
            }

            /* ✅ Form Design */
            form {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
            }

            label {
                font-weight: 600;
                display: block;
                margin-top: 10px;
            }

            input {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                margin-top: 5px;
            }

            button {
                
                padding: 12px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                margin-top: 10px;
                cursor: pointer;
                font-size: 16px;
                transition: 0.3s;
            }
            
            button:hover {
                background: #0056b3;
            }

            /* ✅ Employee Table */
            table {
                width: 1400px;
                border-collapse: collapse;
                margin-top: 20px;
                margin-left: 40px;
            }

            th, td {
                padding: 12px;
                text-align: center;
                border: 1px solid #ddd;
            }

            th {
                background: #007bff;
                color: white;
                font-weight: bold;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            /* ✅ Action Buttons */
            .btn {
                padding: 8px 12px;
                text-decoration: none;
                color: white;
                border-radius: 5px;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .btn-add { background: #4CAF50; }
            .btn-add:hover { background: #388E3C; }
            .btn-view { background: #007bff; }

            .btn i {
                font-size: 14px;
            }

            /* ✅ Responsive Fix */
            @media (max-width: 768px) {
                form {
                    grid-template-columns: 1fr;
                }
                table {
                    display: block;
                    overflow-x: auto;
                    white-space: nowrap;
                }
                button {
                    grid-column: span 1;
                }
            }
            .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 30%;
            text-align: center;
        }
        .close-btn {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
        /* ✅ Ensure Select Dropdown Matches Input Fields */
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-top: 5px;
        font-size: 16px;
        background-color: white;
        appearance: none; /* Removes default dropdown styling */
    }

    /* ✅ Fix for Select Dropdown Arrow */
    select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    /* ✅ Optional: Style Dropdown Arrow */
    select {
        -webkit-appearance: none; /* Safari */
        -moz-appearance: none; /* Firefox */
        appearance: none; /* Standard browsers */
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18"><path fill="black" d="M7 10l5 5 5-5z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 12px;
        padding-right: 30px; /* Ensure text doesn't overlap with arrow */
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
        </style>
    </head>
    <body>
    <?php require_once 'includes/side_nav.php'; ?>

    <div class="main-container">
        <h2 class="sub-header">Property Employee Records</h2>

        <!-- ✅ Display success message when an employee is added -->
        <?php if (isset($_GET['success'])): ?>
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: 'Employee added successfully!',
                    icon: 'success',
                    confirmButtonColor: '#007bff'
                });
            </script>
        <?php endif; ?>

        <form method="POST">
            <div>
                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Enter Last Name" required>
            </div>

            <div>
                <label>First Name</label>
                <input type="text" name="first_name" placeholder="Enter First Name" required>
            </div>

            <div>
                <label>Middle Name</label>
                <input type="text" name="middle_name" placeholder="Enter Middle Name">
            </div>

            <div>
                <label>Position</label>
                <input type="text" name="position" placeholder="Enter Position" required>
            </div>

            <div>
                <label>Campus</label>
                <select name="campus" required>
                    <option value="" disabled selected>Select Campus</option>
                    <option value="Iba (Main)">Iba (Main)</option>
                    <option value="Botolan">Botolan Campus</option>
                    <option value="Candelaria">Candelaria</option>
                    <option value="Castillejos">Castillejos</option>
                    <option value="Masinloc">Masinloc</option>
                    <option value="San Marcelino">San Marcelino</option>
                    <option value="Sta. Cruz">Sta. Cruz</option>
                </select>
            </div>

            <button type="submit" name="add_employee">Add Employee</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>No.</th> <!-- ✅ New Column for Numbering -->
                    <th>Employee Name</th>
                    <th>Position</th>
                    <th>Campus</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $counter = 1; // Start numbering from 1
            foreach ($employees as $employee): ?>
                <tr>
                    <td><?= $counter++ ?></td> <!-- ✅ Display Row Number -->
                    <td><?= htmlspecialchars($employee['last_name'] . ', ' . $employee['first_name'] . ' ' . $employee['middle_name']) ?></td>
                    <td><?= htmlspecialchars($employee['position']) ?></td>
                    <td><?= htmlspecialchars($employee['campus']) ?></td>
                    <td><?= htmlspecialchars($employee['created_at']) ?></td>
                    <td style="display: flex; justify-content: center; gap: 10px;">
                        <button class="btn btn-action btn-add" onclick="openUploadModal(<?= $employee['id'] ?>)">Add File</button>
                        <a href="view_employee.php?id=<?= $employee['id'] ?>" class="btn btn-action btn-view">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        </table>
    </div>

    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeUploadModal()">&times;</span>
            <h2>Upload File</h2>
            <form action="upload_file.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="modal_employee_id" name="employee_id">
                <label for="fileInput">Select Excel Files:</label>
                <input type="file" id="fileInput" name="files[]" multiple accept=".xlsx, .xls">
                <button type="submit" name="upload_file">Upload</button>
            </form>
        </div>
    <script>
    function openUploadModal(employeeId) {
        document.getElementById("modal_employee_id").value = employeeId;
        document.getElementById("uploadModal").style.display = "flex";
    }

    function closeUploadModal() {
        document.getElementById("uploadModal").style.display = "none";
    }
    </script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <?php require_once 'includes/admin_footer.php'; ?>
    </body>
    </html>
