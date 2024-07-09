<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Login Management' ?></title>
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.css">
</head>

<body>
    <div class="container my-5">
        <div class="position-relative p-5 text-center text-muted bg-body border border-dashed border-radius-xl blur shadow-blur"
            style="border-radius: 1rem;">
            <h1 class="text-body-emphasis">Login Management</h1>
            <p class="col-lg-12 mx-auto mb-4">
                Selamat datang di sistem manajemen login.
            </p>

            <div class="d-inline gap-3 mb-5">
                <a href="/user/login" class="btn btn-primary d-inline-flex align-items-center text-white" type="button">
                    Login
                </a>
                <a href="/user/register" class="btn btn-outline-secondary d-inline-flex align-items-center"
                    type="button">
                    Registrasi
                </a>
            </div>
        </div>
    </div>
    <!-- Bootstrap js-->
    <script src="/assets/js/bootstrap/bootstrap.min.js"></script>
</body>

</html>