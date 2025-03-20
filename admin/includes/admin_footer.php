<footer>
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Supply and Property Management System. All Rights Reserved.</p>
        <img src="upload/prmsu_logo.png" alt="Logo" class="footer-logo">
    </div>
</footer>

<!-- ✅ Updated Footer Styling -->
<style>
    html, body {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .content {
        flex: 1; /* ✅ Pushes footer to bottom */
    }

    footer {
        width: 100%;
        text-align: center;
        padding: 12px 0;
        background:#F8F9FA; /* ✅ White Background */
        color: #333; /* ✅ Dark Gray Text for Visibility */
        font-size: 12px;
        position: fixed; /* ✅ Keeps footer at the bottom */
        bottom: 0;
        left: 0;
        z-index: 1000; /* ✅ Ensures it's always above */
        border-top: 1px solid #ddd; /* ✅ Adds subtle top border for separation */
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.05); /* ✅ Soft shadow effect */
    }

    .footer-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px; /* ✅ Adds spacing between text and logo */
    }

    .footer-logo {
        height: 25px;
        width: auto;
        opacity: 0.9;
    }
</style>
