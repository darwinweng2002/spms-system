<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Supply and Property Management</title>
    <?php require_once 'includes/header_nav2.php'; ?>
    <!-- Import SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Import Custom Login Script -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Import Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="styles.css">
    <style>
        /* Apply Poppins font */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: url('upload/prmsu_logo.png') no-repeat center center fixed;
            background-size: 700px;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 380px;
        }

        header h1 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .login-box h2 {
            font-size: 16px;
            color: #666;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            color: #444;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .input-group input {
            width: 100%;
            padding: 12px; /* Increased padding for better usability */
            font-size: 16px; /* Larger text size */
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            transition: 0.3s ease;
        }

        .input-group input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }
        footer {
            width: 100%;
            text-align: center;
            padding: 15px;
            background: #2C3E50;
            color: #fff;
            font-size: 14px;
            position: absolute;
            bottom: 0;
        }

        footer img.footer-logo {
            height: 60px;
            width: auto;
        }
        .password-container {
    position: relative;
   
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>bayagiansaaaaa and Property Management Services System</h1>
        </header>
        <div class="login-box">
            <h2>Administrator Login</h2>
            <form id="login-form">
    <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="input-group">
        <label for="password">Password</label>
        <div class="password-container">
            <input type="password" id="password" name="password" required>
            <span class="toggle-password" onclick="togglePassword()">
                <i class="fas fa-eye"></i> <!-- Eye icon -->
            </span>
        </div>
    </div>
    <button type="submit">Login</button>
</form>
        </div>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.querySelector("#login-form");

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(loginForm); // Create FormData object

        fetch("admin_login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json()) // Ensure response is JSON
        .then(data => {
            console.log("Server Response:", data); // Debugging

            if (data.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Login Successful",
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "dashboard.php"; // Redirect after success
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Login Failed",
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            Swal.fire({
                icon: "error",
                title: "Server Error",
                text: "Unable to process request. Try again later."
            });
        });
    });
});
</script>
<?php require_once 'includes/admin_footer.php'; ?>
</body>
</html>

