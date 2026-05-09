<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>
<body class="hold-transition login-page bg-navy">
<script>start_loader()</script>
<h2 class="text-center mb-4 pb-3"><?php echo $_settings->info('name') ?></h2>
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center border-0 pb-0">
            <h4 class="font-weight-bold text-primary"><i class="fas fa-lock mr-2"></i>Reset Password</h4>
            <p class="text-muted small">Enter your new password below.</p>
        </div>
        <div class="card-body">
            <div id="msg-reset"></div>
            <?php
            $token = isset($_GET['token']) ? trim($_GET['token']) : '';

            // Validate token using the global $conn (already set by config.php)
            $valid_user = null;
            if(!empty($token)){
                $safe_token = $conn->real_escape_string($token);
                $check = $conn->query("SELECT * FROM users WHERE reset_token = '{$safe_token}' AND reset_expiry > NOW() LIMIT 1");
                if($check && $check->num_rows > 0){
                    $valid_user = $check->fetch_assoc();
                }
            }

            if(!$valid_user):
            ?>
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                This reset link is <strong>invalid or has expired</strong>.<br>
                <a href="login.php" class="text-danger font-weight-bold mt-2 d-block">&#8592; Back to Login</a>
            </div>
            <?php else: ?>
            <form id="reset-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token) ?>">
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" id="new_password"
                        placeholder="New Password" required minlength="6" autocomplete="new-password">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                        placeholder="Confirm Password" required autocomplete="new-password">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save mr-1"></i> Set New Password
                </button>
                <a href="login.php" class="btn btn-link btn-block text-muted small">&#8592; Back to Login</a>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script>
// Define _base_url_ explicitly since this page is standalone (not inside admin wrapper)
var _base_url_ = '<?php echo base_url ?>';

$(document).ready(function() {
    end_loader();

    $('#reset-form').submit(function(e){
        e.preventDefault();
        var pass = $('#new_password').val();
        var conf = $('#confirm_password').val();

        if(pass.length < 6){
            $('#msg-reset').html('<div class="alert alert-warning"><i class="fas fa-exclamation-circle mr-1"></i> Password must be at least 6 characters.</div>');
            return;
        }
        if(pass !== conf){
            $('#msg-reset').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> Passwords do not match.</div>');
            return;
        }

        var btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.post(_base_url_ + 'classes/ForgotPassword.php?f=reset_password', $(this).serialize(), function(resp){
            btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Set New Password');
            try {
                var r = JSON.parse(resp);
                if(r.status === 'success'){
                    $('#msg-reset').html('<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i> Password changed successfully! <a href="login.php"><strong>Login now &rarr;</strong></a></div>');
                    $('#reset-form').hide();
                } else {
                    $('#msg-reset').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> This link has expired or is invalid. Please <a href="login.php">request a new one</a>.</div>');
                }
            } catch(err) {
                $('#msg-reset').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> Server error. Please try again.</div>');
                console.error('Reset response:', resp);
            }
        }).fail(function(){
            btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Set New Password');
            $('#msg-reset').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-1"></i> Connection error. Please try again.</div>');
        });
    });
});
</script>
</body>
</html>
