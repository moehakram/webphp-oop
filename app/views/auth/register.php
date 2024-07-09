<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <?php if(isset($error)): ?>
    <div class="row">
        <div class="alert alert-danger" role="alert">
            <?= $error ?>
        </div>
    </div>
    <?php endif ?>
    <div class="row align-items-center g-lg-5 py-5">
        <div class="col-lg-7 text-center text-lg-start">
            <h1 class="display-4 fw-bold lh-1 mb-3">Register</h1>
        </div>
        <div class="col-md-10 mx-auto col-lg-5">
            <form class="p-4 p-md-5 border rounded-3 bg-light" method="post" action="/user/register">    
            <input type="hidden" name="csrf_token" value=<?= $csrf_token ?? $_POST['csrf_token'] ?>>
            <div class="form-floating mb-3">
                    <input name="id" type="text" class="form-control" id="id" value="<?=$_POST['id']??'' ?>" placeholder="id">
                    <label for="id">Id</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="name" type="text" class="form-control" id="name" value="<?=$_POST['name']??'' ?>" placeholder="name">
                    <label for="name">Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="password" type="password" class="form-control" id="password" placeholder="password">
                    <label for="password">Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Register</button>
            </form>
        </div>
    </div>
</div>