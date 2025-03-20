<style>
/* Background Image with Grayscale */
body { 
    background: url('../upload/default-avatar.jpg') no-repeat center center fixed; 
    background-size: cover;
    /* Makes the background image gray */
}

/* Semi-transparent overlay */
body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}
</style>
