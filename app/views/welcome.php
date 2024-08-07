<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Login Management' ?></title>
    <link rel="stylesheet" type="text/css" href="/assets/styles.css">
</head>
<body>
    <main class="container">
        <div>
            <h1>Login Management</h1>
            <p>
                Selamat datang di sistem manajemen login.
            </p>
            <div>
                <a href="/users/login" class=" btn btn-login">
                    Login
                </a>
                <a href="/users/register" class="btn btn-registrasi">
                    Registrasi
                </a>
            </div>
        </div>
    </main>
</body>
</html>