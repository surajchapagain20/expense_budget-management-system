<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" style="height: auto;">
<?php require_once('inc/header.php') ?>

<body class="hold-transition login-page bg-navy">
    <script>start_loader()</script>
    <h2 class="text-center mb-4 pb-3"><?php echo $_settings->info('name') ?></h2>
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div id="msg-area"></div>

            <!-- LOGIN FORM -->
            <div id="login-section">
			
                <div class="card-body"><center><img src="img/logo.png" height="150" width="250"></center>
                    <p class="login-box-msg text-dark font-weight-bold">Sign in to start your session</p>
                    <form id="login-frm" action="" method="post">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="username" placeholder="Username" autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text"><span class="fas fa-user"></span></div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Password">
                            <div class="input-group-append">
                                <div class="input-group-text"><span class="fas fa-lock"></span></div>
                            </div>
                        </div>
                        <div class="row justify-content-center mb-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt mr-1"></i> Sign In</button>
                        </div>
                        <p class="text-center mt-2 mb-0">
                            <a href="#" id="show-forgot" class="text-muted small"><i class="fas fa-key mr-1"></i>Forgot your password?</a>
                        </p>
                    </form>
                </div>
            </div>

            <!-- FORGOT PASSWORD FORM -->
            <div id="forgot-section" style="display:none;">
                <div class="card-body">
                    <p class="login-box-msg text-dark font-weight-bold"><i class="fas fa-envelope mr-1"></i> Reset Password</p>
                    <p class="text-muted small text-center mb-3">Enter your registered email address. We will send you a reset link.</p>
                    <div id="msg-forgot"></div>
                    <form id="forgot-frm">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" name="email" id="forgot-email" placeholder="Email address" required>
                            <div class="input-group-append">
                                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane mr-1"></i> Send Reset Link</button>
                        <p class="text-center mt-2 mb-0">
                            <a href="#" id="show-login" class="text-muted small">&#8592; Back to Login</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script>
    $(document).ready(function() {
        end_loader();

        $('#show-forgot').click(function(e){
            e.preventDefault();
            $('#login-section').hide();
            $('#forgot-section').show();
            $('#forgot-email').focus();
        });
        $('#show-login').click(function(e){
            e.preventDefault();
            $('#forgot-section').hide();
            $('#login-section').show();
        });

        $('#login-frm').submit(function(e){
            e.preventDefault();
            start_loader();
            $.ajax({
                url: _base_url_+'classes/Login.php?f=login',
                method: 'POST',
                data: $(this).serialize(),
                success: function(resp){
                    var r = JSON.parse(resp);
                    if(r.status === 'success'){
                        location.reload();
                    } else {
                        end_loader();
                        $('#msg-area').html('<div class="alert alert-danger text-center m-2 mb-0 rounded-0"><i class="fas fa-times-circle mr-1"></i> Invalid username or password.</div>');
                    }
                }
            });
        });

        $('#forgot-frm').submit(function(e){
            e.preventDefault();
            var btn = $(this).find('button[type=submit]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');
            $.post(_base_url_+'classes/ForgotPassword.php?f=request_reset', $(this).serialize(), function(resp){
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Reset Link');
                try {
                    var r = JSON.parse(resp);
                    if(r.status === 'sent'){
                        $('#msg-forgot').html('<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i> Reset link sent! Check your email inbox.</div>');
                        $('#forgot-frm input[type=email]').val('').prop('disabled', true);
                        $('#forgot-frm button').prop('disabled', true);
                    } else if(r.status === 'not_found'){
                        $('#msg-forgot').html('<div class="alert alert-warning"><i class="fas fa-exclamation-circle mr-1"></i> No account found with that email address.</div>');
                    } else if(r.status === 'mail_error'){
                        var detail = r.msg ? '<br><small class="text-muted">' + r.msg + '</small>' : '';
                        $('#msg-forgot').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> Could not send email. Please check SMTP settings.' + detail + '</div>');
                    } else {
                        $('#msg-forgot').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> An unexpected error occurred. Please try again.</div>');
                    }
                } catch(e) {
                    $('#msg-forgot').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> Server error. Check PHP error log.</div>');
                    console.error('Forgot password response:', resp);
                }
            }).fail(function(){
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Reset Link');
                $('#msg-forgot').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> Connection failed. Please try again.</div>');
            });
        });
    })
    </script>
</body>
</html>