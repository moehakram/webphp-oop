<div>
    <h1>Login Management</h1>
    <p>
       <h4>Hallo <?= $user['name'] ?? '' ?></h4> 
        Selamat datang di sistem manajemen login.
    </p>
    <div>
        <a href="/users/password" class=" btn btn-login">
            Password
        </a>
        <a href="/users/profile" class=" btn btn-login">
            Profile
        </a>
        <a href="/users/logout" class="btn btn-registrasi">
            Logout
        </a>
    </div>
</div>