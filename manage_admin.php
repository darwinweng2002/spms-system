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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->

    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: #f8f9fa;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.main-container {
    width: calc(100% - 250px);
    margin-left: 250px;
    padding: 40px;
}

/* --- Section Layout --- */
.admin-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    align-items: stretch;
    background: transparent;
    margin-bottom: 30px;
}

/* --- Add Admin Form --- */
.

.admin-form h1 {
    font-size: 22px;
    margin-bottom: 10px;
    color: #002855;
}

.admin-form label {
    font-weight: 500;
    font-size: 15px;
    color: #333;
    margin-top: 8px;
}

.admin-form input {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
    transition: border 0.3s;
}

.admin-form input:focus {
    outline: none;
    border-color: #007bff;
}

.admin-form button {
    background: #007bff;
    color: #fff;
    border: none;
    padding: 12px;
    font-size: 16px;
    border-radius: 6px;
    margin-top: 15px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.admin-form button:hover {
    background: #0056b3;
}
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 15px;
    margin-bottom: 6px;
    color: #333;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="password"],
.form-group input[type="file"] {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #fff;
    transition: border 0.3s ease-in-out;
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
}

.form-group span#passwordFeedback {
    font-size: 13px;
    margin-top: 4px;
    color: red;
}

.form-group.full-width {
    grid-column: span 2;
}

.admin-form button,
.form-group button {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.form-group button:hover {
    background-color: #0056b3;
}

/* --- Org Chart Display --- */
.org-chart {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
}

.org-chart img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
}

/* --- Table Area --- */
.table-container {
    background: #ffffff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow-x: auto;
}

.table-container h2 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #002855;
}

/* --- Table Styling --- */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
    padding-bottom: 80px;
}

table th {
    background-color: #007bff;
    color: white;
    padding: 12px;
    font-size: 15px;
    font-weight: 600;
}

table td {
    padding: 12px;
    background-color: #ffffff;
    text-align: center;
    border-bottom: 1px solid #eee;
}

table tr:nth-child(even) td {
    background-color: #f9f9f9;
}

/* --- Buttons --- */
.update-btn,
.delete-btn {
    padding: 8px 14px;
    font-size: 14px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}

.update-btn {
    background: #28a745;
    color: white;
}

.update-btn:hover {
    background: #218838;
}

.delete-btn {
    background: #dc3545;
    color: white;
}

.delete-btn:hover {
    background: #c82333;
}

/* --- Avatar Image in Table --- */
table img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
}

/* --- Feedback text --- */
#passwordFeedback {
    font-size: 13px;
    margin-top: 4px;
}

/* --- Loader Overlay --- */
#loadingOverlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
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

/* --- Responsive Layout --- */
@media (max-width: 992px) {
    .admin-section {
        grid-template-columns: 1fr;
    }

    .main-container {
        padding: 20px;
        margin-left: 0;
        width: 100%;
    }
}

footer {
    text-align: center;
    padding: 10px;
    background: #2C3E50;
    color: #fff;
    font-size: 12px;
    margin-top: auto;
}

footer img.footer-logo {
    height: 60px;
    width: auto;
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
        <h1><i class="bi bi-person-gear me-2"> Manage Admin Users</h1>
        <div class="admin-section">
        <!-- ✅ Add Admin Form -->
        <div class="admin-form">
            <h1>Add New Admin</h1>
            <form id="addAdminForm" class="admin-grid">
    <div class="form-group">
        <label for="name">Admin Name</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
    </div>

    <div class="form-group">
        <label for="position">Position</label>
        <input type="text" id="position" name="position" required>
    </div>

    <div class="form-group">
        <label for="campus">Campus</label>
        <input type="text" id="campus" name="campus" required>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <span id="passwordFeedback"></span>
    </div>

    <div class="form-group full-width">
        <label for="avatar">Profile Picture</label>
        <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg, image/jpg">
    </div>

    <div class="form-group full-width">
        <button type="submit">Add Admin</button>
    </div>
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

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    // Validate if passwords match
    if (password !== confirmPassword) {
        Swal.fire("Error", "Passwords do not match!", "error");
        return;
    }

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
const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("confirm_password");
const feedback = document.getElementById("passwordFeedback");

function checkPasswordsMatch() {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (!confirmPassword) {
        feedback.textContent = ""; // Clear message if confirm field is empty
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

<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>