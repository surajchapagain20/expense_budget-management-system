<?php
// Use absolute path resolution so this works regardless of how it's called
$base_dir = dirname(__DIR__);
require_once $base_dir . '/config.php';
require_once $base_dir . '/classes/EmailHelper.php';

class ForgotPassword extends DBConnection {
    private $settings;
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }
    public function __destruct(){ parent::__destruct(); }

    public function request_reset(){
        if(empty($_POST['email'])){
            return json_encode(['status' => 'not_found', 'msg' => 'Email is required']);
        }

        $email = $this->conn->real_escape_string(trim($_POST['email']));

        // Check if email column exists — graceful fallback
        $col_check = $this->conn->query("SHOW COLUMNS FROM users LIKE 'email'");
        if(!$col_check || $col_check->num_rows < 1){
            return json_encode(['status' => 'mail_error', 'msg' => 'Email column not found. Please run the migration first.']);
        }

        $qry = $this->conn->query("SELECT * FROM users WHERE email = '{$email}' LIMIT 1");
        if(!$qry || $qry->num_rows < 1){
            return json_encode(['status' => 'not_found']);
        }

        $user = $qry->fetch_assoc();
        $token  = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $upd = $this->conn->query("UPDATE users SET reset_token = '{$token}', reset_expiry = '{$expiry}' WHERE id = '{$user['id']}'");
        if(!$upd){
            return json_encode(['status' => 'mail_error', 'msg' => 'DB update failed: ' . $this->conn->error]);
        }

        $reset_link  = base_url . "admin/reset_password.php?token={$token}";
        $name        = htmlspecialchars($user['firstname'] . ' ' . $user['lastname']);
        $system_name = $this->settings->info('name') ?: SMTP_FROM_NAME;

        $content = "
            <h2 style='font-size:20px;font-weight:700;color:#1f2937;margin-bottom:14px;'>Password Reset Request</h2>
            <p style='font-size:15px;line-height:1.7;color:#4b5563;'>Hi <strong>{$name}</strong>,</p>
            <p style='font-size:15px;line-height:1.7;color:#4b5563;'>We received a request to reset the password for your account. Click the button below to set a new password.</p>
            " . email_alert("<strong>&#9203; This link will expire in 1 hour.</strong>", 'warning') . "
            " . email_button($reset_link, '&#128273; Reset My Password') . "
            <hr style='border:none;border-top:1px solid #e5e7eb;margin:20px 0;'>
            <p style='font-size:13px;color:#9ca3af;'>If you did not request a password reset, you can safely ignore this email &mdash; your password will remain unchanged.</p>
            <p style='font-size:13px;color:#9ca3af;'>Or copy this link into your browser:<br>
            <a href='{$reset_link}' style='color:#1a5276;word-break:break-all;'>{$reset_link}</a></p>
        ";

        $html = email_template(
            "Reset Your Password — {$system_name}",
            "You requested a password reset. This link expires in 1 hour.",
            $content
        );

        $result = send_html_email($user['email'], "Reset Your Password — {$system_name}", $html);

        // send_html_email returns true on success, or an error string on failure
        if($result === true){
            return json_encode(['status' => 'sent']);
        } else {
            // Log the error server-side but return generic error to client
            error_log("ForgotPassword SMTP Error: " . print_r($result, true));
            return json_encode(['status' => 'mail_error', 'msg' => $result]);
        }
    }

    public function reset_password(){
        $token    = $this->conn->real_escape_string($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';

        if(empty($token) || empty($password)){
            return json_encode(['status' => 'invalid']);
        }

        $qry = $this->conn->query("SELECT * FROM users WHERE reset_token = '{$token}' AND reset_expiry > NOW() LIMIT 1");
        if(!$qry || $qry->num_rows < 1){
            return json_encode(['status' => 'invalid']);
        }

        $user   = $qry->fetch_assoc();
        $hashed = md5($password);
        $this->conn->query("UPDATE users SET password = '{$hashed}', reset_token = NULL, reset_expiry = NULL WHERE id = '{$user['id']}'");
        return json_encode(['status' => 'success']);
    }
}

$fp     = new ForgotPassword();
$action = isset($_GET['f']) ? strtolower($_GET['f']) : 'none';
switch($action){
    case 'request_reset':
        echo $fp->request_reset();
        break;
    case 'reset_password':
        echo $fp->reset_password();
        break;
    default:
        echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
}
?>
