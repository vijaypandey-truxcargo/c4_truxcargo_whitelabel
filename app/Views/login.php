<style>
.form-control{
    border:0;
    padding:10px 30px 10px 39px;
    box-shadow:0px 1px 5px 0px #7939CC;
    border-radius:8px;
    height:40px;
    font-size:14px;
}
.form-group i{
    position:absolute;
    padding:10px 12px 10px 13px;
    color:#d66a6a;
    border-radius:8px 0px 0px 8px;
    height:39px;
    margin-bottom:3px;
}
.purle{color:#7939CC;}
input:focus{
    box-shadow:0px 1px 5px 0px #7939CC !important;
}
.gimg{
    width:23px;
    margin-right:10px;
    vertical-align:middle;
}
</style>

<section class="login-section">
    <div class="klogin" style="background:#fff;">
        <div class="row" style="z-index:1">

            <div class="col-lg-6 di-none">
                <img class="w-100" src="<?= base_url('assets/images/login.png'); ?>">
            </div>

            <div class="col-lg-6">

                <div class="login_form">

                    <div class="brand-logo text-center">
                        <a href="<?= base_url(); ?>">
                            <img class="logo-title w-50 mb-4" src="<?= base_url("uploads/profile/{$data->logo}"); ?>" alt="">
                        </a>
                    </div>

                    <h3 class="main-heading">Sign In</h3>

                    <div class="form-group">
                        <h6>Hello! let's get started</h6>
                    </div>

                    <!-- Flash Message -->

                    <?php if(session()->getFlashdata('error')) : ?>

                        <div class="<?= session()->getFlashdata('error_class'); ?>">

                            <strong><?= session()->getFlashdata('error'); ?></strong>

                        </div>

                    <?php endif; ?>

                    <?php if(session()->getFlashdata('login_warning')) : ?>

                        <div class="alert alert-warning">

                            <?= session()->getFlashdata('login_warning'); ?>

                        </div>

                    <?php endif; ?>

                    <div class="userContent"></div>

                    <form action="<?= site_url('login/insert'); ?>" method="post" autocomplete="off">

                        <?= csrf_field(); ?>

                        <div class="row">

                            <div class="col-lg-12">

                                <div class="form-group mb-4">

                                    <i class="fa fa-user"></i>

                                    <input type="text"
                                           name="username"
                                           value="<?= old('username'); ?>"
                                           class="form-control login-input"
                                           placeholder="Username">

                                    <?php if(isset($validation) && $validation->hasError('username')) : ?>

                                        <span class="text-danger">

                                            <?= $validation->getError('username'); ?>

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <div class="col-lg-12">

                                <div class="form-group mb-1">

                                    <i class="fa fa-lock"></i>

                                    <div style="display:flex">

                                        <input type="password"
                                               id="password"
                                               name="password"
                                               value="<?= old('password'); ?>"
                                               class="form-control login-input"
                                               placeholder="Password">

                                        <span class="fa fa-fw fa-eye field_icon toggle-password"
                                              style="margin-top:11px;margin-left:-27px;"></span>

                                    </div>

                                    <?php if(isset($validation) && $validation->hasError('password')) : ?>

                                        <span class="text-danger">

                                            <?= $validation->getError('password'); ?>

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>

                            <div class="col-lg-12 mt-2 d-flex justify-content-between align-items-center">

                                <div class="form-check">

                                    <label class="form-check-label">

                                        <input type="checkbox" class="form-check-input">

                                        &nbsp;

                                        <small>Keep me signed in</small>

                                    </label>

                                </div>

                                <a href="<?= site_url('login/forgot'); ?>"
                                   class="auth-link text-black"
                                   style="font-size:14px;">

                                    Forgot Password?

                                </a>

                            </div>

                            <input type="hidden"
                                   name="continue_login"
                                   value="<?= session()->getFlashdata('login_warning') ? '1' : ''; ?>">

                            <div class="col-lg-12 mt-3 mb-2">

                                <button type="submit"
                                        class="btn btn-primary btn-login lh-lg w-100">

                                    Login Now

                                </button>

                                <h6 class="text-center mt-2">or</h6>

                            </div>

                        </div>

                    </form>

                    <div class="mb-1">

                        <div class="row">

                            <div class="text-center col-lg-6">

                                <a href="<?= site_url('login/with_otp'); ?>"
                                   class="btn btn-outline-primary w-100">

                                    Login With OTP

                                </a>

                            </div>

                            <div class="text-center col-lg-6">

                                <a href="<?= site_url('login/user_authentication'); ?>">

                                    <button type="button"
                                            class="btn btn-outline-light btn-google w-100">

                                        <img class="gimg"
                                             src="<?= base_url('assets/images/google.png'); ?>">

                                        Sign in Google

                                    </button>

                                </a>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <div class="text-center row">

                        <div class="col-lg-12">

                            Don't have an account?

                            <a href="<?= site_url('register'); ?>" style="color:#7939CC">

                                <b>Register Now</b>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<script>

$("body").on("click",".toggle-password",function(){

    $(this).toggleClass("fa-eye fa-eye-slash");

    let input=$("#password");

    input.attr("type",
        input.attr("type")==="password" ? "text" : "password"
    );

});

</script>