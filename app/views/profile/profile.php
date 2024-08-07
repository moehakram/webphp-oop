<form action="/users/profile" method="post">
    <h1 class="display-4 fw-bold lh-1 mb-3">Profile</h1>
    <input type="hidden" name="csrf_token" value="<?= csrf() ?>">
    <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?= inputs('name') ?: $user->name ?>">
        <small><?= errors('name') ?></small>
    </div>
    <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?= inputs('username') ?: $user->username ?>">
        <small><?= errors('username') ?></small>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= inputs('email') ?: $user->email ?>">
        <small><?= errors('email') ?></small>
    </div>
    <section>
        <button type="submit">Update Profile</button>
        <footer><a href="/">cancel</a></footer>
    </section>
</form>