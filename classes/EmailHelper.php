<?php
/**
 * Email Helper — PHPMailer SMTP via mail.nepallife.com.np
 * PHPMailer classes are referenced with full namespace to avoid `use` issues.
 */

// Load PHPMailer files
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

// ─── SMTP CONFIGURATION ──────────────────────────────────────────────────────
if (!defined('SMTP_HOST'))
  define('SMTP_HOST', 'SMTP_SERVER');
if (!defined('SMTP_PORT'))
  define('SMTP_PORT', 25);
if (!defined('SMTP_USERNAME'))
  define('SMTP_USERNAME', '<EMAIL ADDRESS>');  // ← update
if (!defined('SMTP_PASSWORD'))
  define('SMTP_PASSWORD', '<PASSWORD>');        // ← update
if (!defined('SMTP_FROM_EMAIL'))
  define('SMTP_FROM_EMAIL', '<EMAIL ADDRESS>');  // ← update
if (!defined('SMTP_FROM_NAME'))
  define('SMTP_FROM_NAME', 'Expense And Budget Management System');
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Send an HTML email via SMTP using PHPMailer.
 * @return true|string  Returns true on success, error string on failure.
 */
function send_html_email($to, $subject, $body_html, $from_name = null, $from_email = null)
{
  $f_email = $from_email ?: SMTP_FROM_EMAIL;
  $f_name = $from_name ?: SMTP_FROM_NAME;

  // Use full namespace — no `use` statement needed
  $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

  try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;

    // Allow self-signed certs (common on internal mail servers)
    $mail->SMTPOptions = [
      'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true,
      ]
    ];

    $mail->setFrom($f_email, $f_name);
    $mail->addAddress($to);
    $mail->addReplyTo($f_email, $f_name);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = $subject;
    $mail->Body = $body_html;
    $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body_html));

    $mail->send();
    return true;

  } catch (\PHPMailer\PHPMailer\Exception $e) {
    error_log("EmailHelper SMTP Error to [{$to}]: " . $mail->ErrorInfo);
    return $mail->ErrorInfo;
  }
}

/**
 * Renders a professional HTML email using inline table-based layout.
 */
function email_template($title, $preheader, $content_html, $footer_text = '')
{
  global $_settings;
  $system_name = (isset($_settings) && $_settings->info('name')) ? $_settings->info('name') : SMTP_FROM_NAME;
  $year = date('Y');
  if (empty($footer_text)) {
    $footer_text = "&copy; {$year} {$system_name}. All rights reserved.";
  }

  return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . htmlspecialchars($title) . '</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4f8;font-family:Arial,Helvetica,sans-serif;color:#374151;">

<div style="display:none;max-height:0;overflow:hidden;">' . htmlspecialchars($preheader) . '</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4f8;padding:30px 10px;">
  <tr><td align="center">
    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

      <!-- Header -->
      <tr>
        <td style="background:linear-gradient(135deg,#1a5276 0%,#2980b9 100%);padding:32px 40px;text-align:center;">
          <div style="color:#ffffff;font-size:22px;font-weight:700;margin:0;">&#127970; ' . $system_name . '</div>
          <div style="color:rgba(255,255,255,0.85);font-size:13px;margin-top:8px;">' . htmlspecialchars($preheader) . '</div>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:36px 40px;font-size:15px;line-height:1.7;color:#4b5563;">
          ' . $content_html . '
        </td>
      </tr>

      <!-- Divider -->
      <tr><td style="padding:0 40px;"><div style="border-top:1px solid #e5e7eb;"></div></td></tr>

      <!-- Footer -->
      <tr>
        <td style="background:#f9fafb;padding:20px 40px;text-align:center;font-size:12px;color:#9ca3af;line-height:1.6;">
          <div>' . $footer_text . '</div>
          <div style="margin-top:6px;">This is an automated message. Please do not reply to this email.</div>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>';
}

/**
 * Generates a styled call-to-action button (inline-table layout for email clients).
 */
function email_button($url, $text, $color = '#1a5276')
{
  return "<table role='presentation' cellpadding='0' cellspacing='0' style='margin:20px auto;border-collapse:collapse;'>"
    . "<tr><td style='background:{$color};border-radius:50px;text-align:center;'>"
    . "<a href='" . htmlspecialchars($url) . "' target='_blank' style='display:inline-block;padding:13px 30px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:50px;'>{$text}</a>"
    . "</td></tr></table>";
}

/**
 * Generates a styled alert box (inline styles for email compatibility).
 */
function email_alert($html, $type = 'warning')
{
  $colors = [
    'warning' => ['bg' => '#fff7ed', 'border' => '#f97316', 'text' => '#92400e'],
    'danger' => ['bg' => '#fef2f2', 'border' => '#ef4444', 'text' => '#991b1b'],
    'success' => ['bg' => '#f0fdf4', 'border' => '#22c55e', 'text' => '#166534'],
    'info' => ['bg' => '#eff6ff', 'border' => '#3b82f6', 'text' => '#1e40af'],
  ];
  $c = $colors[$type] ?? $colors['warning'];
  return "<div style='background:{$c['bg']};border-left:4px solid {$c['border']};padding:14px 18px;"
    . "border-radius:0 8px 8px 0;margin:16px 0;color:{$c['text']};font-size:14px;'>{$html}</div>";
}
?>