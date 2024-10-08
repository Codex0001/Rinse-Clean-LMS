<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/login.css">

    <title>Login | Rinse Clean</title>
    <link rel="shortcut icon" href="../../assets/images/icons/laundry-machine.png" type="image/x-icon">
</head>
<body>
    <!--Nav bar starts here-->
    <nav class="navbar navbar-expand-md navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">Rinse Clean</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Our Services</a>
                    </li>
                </ul>
                <a class="btn btn-primary" href="/public/login/login.php" role="button">Get Started</a>
            </div>
        </div>
    </nav>
    <!--Nav bar ends here-->

    <div class="container-fluid login-container">
        <div class="col-md-6 d-flex align-items-center justify-content-center login-form-wrapper">
            <div class="login-form">
                <div class="d-flex align-items-center mb-4">
                    <img src="../../assets/images/icons/laundry-machine.png" alt="Logo" class="logo" />
                    <h2 class="ms-3">Rinse-Clean | LMS</h2>
                </div>
                <h2 class="text-center">Login</h2>
                <form action="../../process/login_process.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="mt-3 text-center">Don't have an account? <a href="../../public/registration/registration.php">Register here</a></p>
            </div>
        </div>
        <!-- Image Column for large screens -->
        <div class="col-md-6 d-none d-md-block image-column"></div>
    </div>

    <!-- Modal for Error Messages -->
    <?php if (isset($_SESSION['error'])): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="errorModalLabel">Error</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script>
      var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {});
      errorModal.show();
    </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
