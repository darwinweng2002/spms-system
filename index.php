<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | PRMSU SPMS</title>
    <meta name="description" content="Official Property Management System for President Ramon Magsaysay State University.">
    <meta name="keywords" content="PRMSU, Property Management, SPMS, Zambales, Government Assets">

    <?php require_once 'includes/header_nav2.php'; ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(to right, #007bff, #5f27cd);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            position: relative;
            animation: fadeIn 0.8s ease;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }

        .login-header img {
            width: 60px;
            height: auto;
            margin-bottom: 10px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            color: #444;
            margin-bottom: 6px;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i.bi {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            color: #888;
        }

        .input-icon-wrapper input {
            width: 100%;
            padding: 12px 38px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s;
        }

        .input-icon-wrapper input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1rem;
            color: #888;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        #loadingOverlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255,255,255,0.75);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media(max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<!-- 🔄 Loading Spinner Overlay -->
<div id="loadingOverlay">
    <div class="spinner"></div>
</div>
        <div class="container">
            <header>
                <h1>Supply and Property Management Services System</h1>
            </header>
            <div class="login-box">
                <h2>Administrator Login</h2>
                <form id="login-form">
       <!-- Username Field with Icon -->
<div class="input-group">
    <label for="username">Username</label>
    <div class="input-icon-wrapper">
        <i class="bi bi-person-fill"></i>
        <input type="text" id="username" name="username" required>
    </div>
</div>
<!-- Password Field with Icon and Toggle Eye -->
<div class="input-group">
    <label for="password">Password</label>
    <div class="input-icon-wrapper">
        <i class="bi bi-lock-fill"></i>
        <input type="password" id="password" name="password" required>
        <span class="toggle-password" onclick="togglePassword()">
            
        </span>
    </div>
</div>
        </div>
        <button type="submit">Login</button>
    </form>
            </div>
        </div>
        <script>
document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.querySelector("#login-form");
    const loadingOverlay = document.getElementById("loadingOverlay");

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();

        loadingOverlay.style.display = "flex"; // 🔄 Show spinner

        let formData = new FormData(loginForm);

        fetch("admin_login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loadingOverlay.style.display = "none"; // ✅ Hide spinner
            if (data.status === "success") {
                Swal.fire({
                    icon: "success",
                    title: "Login Successful",
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "dashboard.php";
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
            loadingOverlay.style.display = "none"; // ❌ Hide on error
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

