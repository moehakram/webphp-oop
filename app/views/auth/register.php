<form action="/users/register" method="post">
    <h1>Sign Up</h1>
    <input type="hidden" name="csrf_token" value="<?= csrf() ?>" >
    <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?= $inputs['name'] ?? '' ?>">
        <small><?= $errors['name'] ?? '' ?></small>
    </div>
    <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?= $inputs['username'] ?? '' ?>">
        <small><?= $errors['username'] ?? '' ?></small>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= $inputs['email'] ?? '' ?>">
        <small><?= $errors['email'] ?? '' ?></small>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" value="<?= $inputs['password'] ?? '' ?>">
        <small><?= $errors['password'] ?? '' ?></small>
    </div>
    <div>
        <label for="password2">Password Again:</label>
        <input type="password" name="password2" id="password2" value="<?= $inputs['password2'] ?? '' ?>">
        <small><?= $errors['password2'] ?? '' ?></small>
    </div>
    <div>
        <label for="agree">
            <input type="checkbox" name="agree" id="agree" value="checked" <?= $inputs['agree'] ?? '' ?> />
            <span>I agree with the </span><a href="#" title="term of services">term of services</a>
        </label>
        <small><?= $errors['agree'] ?? '' ?></small>
    </div>
    <section>
        <button type="submit">Register</button>
        <footer>Already a member? <a href="/users/login">Login here</a></footer>
    </section>
</form>