<?php
session_start();
require_once 'db.php';


// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin Users</title>
    <?php require_once 'includes/header_nav.php'; ?>
    <!-- Include Background -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body {
            min-height: 100vh; /* Ensure the body takes at least the full screen height */
            display: flex;
            flex-direction: column;
        }
        /* ✅ Main Page Layout */
        .main-container {
            width: calc(100% - 250px);
            margin-left: 250px;
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
           
        }

        /* ✅ Flexbox for Form & Org-Chart Image */
        .admin-section {
            max-width: 1600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: left;
            margin-bottom: 20px;
        }

        /* ✅ Admin Form */
        .admin-form {
            flex: 1; /* Takes available space */
            max-width: 500px;
        }

        .admin-form label {
            font-size: 16px;
            font-weight: 500;
            display: block;
            margin-top: 10px;
        }

        .admin-form input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .admin-form button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
            margin-top: 10px;
        }   

        .admin-form button:hover {
            background: #0056b3;
        }

        /* ✅ Org-Chart Image */
        .org-chart {
            flex: 1;
            max-width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .org-chart img {
            width: 100%;
            max-width: 900pxpx; /* Adjust image width */
            height: auto;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* ✅ Table */
        .table-container {
            max-width: 1600px;
            margin: auto;
            margin-top: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            flex: 1; /* Makes the container take available space */
            padding-bottom: 80px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background: #ddd;
        }

        /* ✅ Buttons */
        .update-btn {
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

        .update-btn:hover {
            background: #218838;
        }

        .delete-btn {
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

        .delete-btn:hover {
            background: #b02a37;
        }

        /* ✅ Responsive */
        @media (max-width: 768px) {
            .main-container {
                width: 100%;
                margin-left: 0;
                padding: 15px;
            }

            .admin-section {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .admin-form, .org-chart {
                max-width: 100%;
            }

            .table-container {
                width: 95%;
                overflow-x: auto;
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
        #loadingOverlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(255,255,255,0.6);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #007bff;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
    </style>
</head>
<body>
<div id="loadingOverlay" style="display: none;">
    <div class="spinner"></div>
</div>
<?php require_once 'includes/side_nav.php'; ?>
<br>
<br>
    <div class="content">
        <h1>Manage Admin Users</h1>
        <div class="admin-section">
        <!-- ✅ Add Admin Form -->
        <div class="admin-form">
            <h1>Add New Admin</h1>
            <form id="addAdminForm">
                <label for="name">Admin Name</label>
                <input type="text" id="name" name="name" required>

                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="position">Position</label>
                <input type="text" id="position" name="position" required>

                <label for="campus">Campus</label>
                <input type="text" id="campus" name="campus" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label for="avatar">Profile Picture</label>
                <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/jpg">

                <button type="submit">Add Admin</button>
            </form>
        </div>

        <!-- ✅ Org-Chart (Now on the right) -->
        <div class="org-chart">
            <img src="upload/org-chart.png" alt="Organization Chart">
        </div>
    </div>


        <!-- Admins Table -->
        <div class="table-container">
            <h2>Admin Users</h2>
            <table>
            <table>
    <thead>
        <tr>
            <!-- <th>#</th> -->
            <th>Profile</th>
            <th>Name</th>
            <th>Username</th>
            <th>Position</th>
            <th>Campus</th>
            <th>Actions</th> <!-- New column for actions -->
        </tr>
    </thead>
    <tbody id="adminTableBody"></tbody>
</table>

        </div>
        
    </div>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    loadAdmins(); // Load admin list on page load

    const addAdminForm = document.getElementById("addAdminForm");

    // Handle form submission
    addAdminForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        let formData = new FormData(addAdminForm);

        // 🔄 Show Loader
        document.getElementById("loadingOverlay").style.display = "flex";

        try {
            let response = await fetch("add_admin.php", {
                method: "POST",
                body: formData
            });

            let data = await response.json();
            console.log("Server Response:", data);

            // 🔄 Hide Loader
            document.getElementById("loadingOverlay").style.display = "none";

            if (data.status === "success") {
                addAdminForm.reset(); // Clear the form
                loadAdmins(); // Reload admin table
                Swal.fire("Success", "Admin added successfully!", "success");
            } else {
                Swal.fire("Error", data.message, "error");
            }
        } catch (error) {
            console.error("Fetch Error:", error);
            // 🔄 Hide Loader on failure
            document.getElementById("loadingOverlay").style.display = "none";
            Swal.fire("Error", "Failed to add admin. Try again.", "error");
        }
    });

    // Load Admins Function
    async function loadAdmins() {
        try {
            let response = await fetch("fetch_admins.php");
            let data = await response.json();

            let tableBody = document.getElementById("adminTableBody");
            tableBody.innerHTML = "";

            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center;">No admin users found.</td></tr>`;
                return;
            }

            data.forEach((admin) => {
                let avatar = admin.avatar ? admin.avatar : "uploads/default-avatar.png";
                let username = admin.username || "N/A";

                tableBody.innerHTML += `
                    <tr>
                        <td><img src="${avatar}" alt="Avatar" style="width:40px; height:40px; border-radius:50%;"></td>
                        <td>${admin.name}</td>
                        <td>${username}</td>
                        <td>${admin.position}</td>
                        <td>${admin.campus}</td>
                        <td>
                            <button class="update-btn" data-id="${admin.id}">Update</button>
                            <button class="delete-btn" data-id="${admin.id}">Delete</button>
                        </td>
                    </tr>`;
            });

            // Delete button actions
            document.querySelectorAll(".delete-btn").forEach(button => {
                button.addEventListener("click", function () {
                    let adminId = this.getAttribute("data-id");
                    deleteAdmin(adminId);
                });
            });

            // Update button actions
            document.querySelectorAll(".update-btn").forEach(button => {
                button.addEventListener("click", function () {
                    let adminId = this.getAttribute("data-id");
                    window.location.href = "update_admin.php?id=" + adminId;
                });
            });

        } catch (error) {
            console.error("Error loading admins:", error);
        }
    }

    async function deleteAdmin(adminId) {
    Swal.fire({
        title: "Are you sure?",
        text: "This action will permanently delete the admin.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
    }).then(async (result) => {
        if (result.isConfirmed) {
            // 🔄 Show loader
            document.getElementById("loadingOverlay").style.display = "flex";

            try {
                const response = await fetch("delete_admin.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: adminId })
                });

                const data = await response.json();

                // 🔄 Hide loader
                document.getElementById("loadingOverlay").style.display = "none";

                if (data.status === "success") {
                    Swal.fire("Deleted!", "Admin deleted successfully.", "success");
                    loadAdmins();
                } else {
                    Swal.fire("Error", data.message || "Failed to delete admin.", "error");
                }
            } catch (error) {
                console.error("Delete Error:", error);
                document.getElementById("loadingOverlay").style.display = "none";
                Swal.fire("Error", "Something went wrong while deleting.", "error");
            }
        }
    });
}
});
</script>

<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>
