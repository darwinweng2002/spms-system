    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login | Supply and Property Management</title>
        <meta name="description" content="Official Property Management System for President Ramon Magsaysay State University Iba Main Campus (PRMSU). Track, manage, and request property summaries easily.">
        <meta name="keywords" content="PRMSU, Property Management, prmsu-spms, SPMS, Zambales, University, Government Assets">
        <?php require_once 'includes/header_nav2.php'; ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
            #loadingOverlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(255,255,255,0.7);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
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
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.input-icon-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon-wrapper i.bi {
    position: absolute;
    left: 12px;
    font-size: 1.1rem;
    color: #888;
    pointer-events: none;
}

.input-icon-wrapper input {
    width: 100%;
    padding: 10px 40px 10px 38px; /* left for icon, right for eye icon */
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    outline: none;
    transition: 0.3s ease;
}

.input-icon-wrapper input:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}
        </style>
    </head>
    <body>  <!-- 🔄 Loading Spinner Overlay -->
<div id="loadingOverlay" style="display: none;">
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

