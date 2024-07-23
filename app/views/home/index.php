<main class="container">
    <div class="position-relative text-center">
        <h1>Login Management</h1>
        <p>
            Hallo <?= $user['name'] ?? '' ?>
            Selamat datang di sistem manajemen login.
        </p>
        <div>
            <a href="/users/password" class=" btn btn-login">
                Password
            </a>
            <a href="/users/logout" class="btn btn-registrasi">
                Logout
            </a>
        </div>
    </div>
</main>