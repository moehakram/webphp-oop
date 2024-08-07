<?php if ($errors) : ?>
    <?= displayAlert($errors); ?> 
<?php endif; ?>

<form action="/users/login" method="post">
    <h1>Login</h1>
    <input type="hidden" name="csrf_token" value="<?= csrf() ?>">
    <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username">
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
    </div>
    <div>
        <label for="remember_me">
            <input type="checkbox" name="remember_me" id="remember_me" value="checked">
            Remember Me
        </label>
        <small><?= errors('agree') ?></small>
    </div>
    <section>
        <button type="submit">Login</button>
        <footer>Not a member yet? <a href="/users/register">Register here</a></footer>
    </section>
</form>