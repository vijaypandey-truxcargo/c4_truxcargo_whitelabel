<!DOCTYPE HTML>
<html>
<head>
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <script type="application/x-javascript">
        addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
        function hideURLbar(){ window.scrollTo(0,1); }
    </script>
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/favicon.png'); ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.png'); ?>">
    <link rel="stylesheet" href="<?= base_url('backend/css/style.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('backend/css/font-awesome.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('backend/css/bootstrap.min.css'); ?>">
    <script src="<?= base_url('backend/js/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('backend/js/bootstrap.min.js'); ?>"></script>
    <style>
        .app-cam input[type="submit"], .btn-success1 {
            background: linear-gradient(180deg, #12111287 0%, #b63b3b 100%) !important;
        }
    </style>
</head>
<body id="login1">
    <div id="login">
        <div class="app-cam">
            <div class="w-100">
                <div class="login-logo">
                    <img src="<?= base_url("uploads/profile/{$data->logo}"); ?>" alt="Logo">
                </div>

                <?php if (session()->getFlashdata('login_faild')) : ?>
                    <div class="alert alert-dismissible alert-danger">
                        <strong><?= session()->getFlashdata('login_faild'); ?></strong>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-dismissible alert-danger">
                        <strong><?= session()->getFlashdata('error'); ?></strong>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('login_warning')) : ?>
                    <div class="alert alert-warning">
                        <?= session()->getFlashdata('login_warning'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <form action="<?= site_url('admin/login'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>

                <div class="form-group mb-3">
                    <input type="text"
                           name="username"
                           value="<?= old('username'); ?>"
                           class="text form-control"
                           placeholder="Username">
                    <?php if (isset($validation) && $validation->hasError('username')) : ?>
                        <p class="text-danger"><?= $validation->getError('username'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <input type="password"
                           name="password"
                           class="password form-control"
                           placeholder="Password">
                    <?php if (isset($validation) && $validation->hasError('password')) : ?>
                        <p class="text-danger"><?= $validation->getError('password'); ?></p>
                    <?php endif; ?>
                </div>

                <input type="hidden"
                       name="continue_login"
                       value="<?= session()->getFlashdata('login_warning') ? '1' : ''; ?>">

                <div class="submit">
                    <input type="submit" name="submit" value="Login" class="btn btn-primary w-100">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
