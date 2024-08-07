<form action="/users/password" method="post">
    <h1 class="display-4 fw-bold lh-1 mb-3">Profile</h1>
    <input type="hidden" name="csrf_token" value="<?= csrf() ?>">
    <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?= inputs('username') ?: $username ?>">
        <small><?= errors('username') ?></small>
    </div>
    <div>
        <label for="oldPassword">Password:</label>
        <input type="password" name="oldPassword" id="oldPassword">
        <small><?= errors('oldPassword') ?></small>
    </div>
    <div>
        <label for="password">new Password:</label>
        <input type="password" name="password" id="password">
        <small><?= errors('password') ?></small>
    </div>
    <div>
        <label for="password2">new Password Again:</label>
        <input type="password" name="password2" id="password2">
        <small><?= errors('password2') ?></small>
    </div>
    <section>
        <button type="submit">Change Password</button>
        <footer><a href="/">cancel</a></footer>
    </section>
</form>