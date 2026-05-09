<?php
require_once('../config.php');

$sqls = [
    "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) NULL DEFAULT NULL AFTER `lastname`",
    "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(255) NULL DEFAULT NULL AFTER `email`",
    "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `reset_expiry` DATETIME NULL DEFAULT NULL AFTER `reset_token`",
];

foreach($sqls as $sql) {
    if($conn->query($sql)) {
        echo "<p style='color:green'>✔ " . htmlspecialchars($sql) . "</p>";
    } else {
        echo "<p style='color:orange'>ℹ " . htmlspecialchars($sql) . " — " . $conn->error . "</p>";
    }
}
echo "<hr><b>Migration complete.</b> <a href='./'>Back to Admin</a>";
?>
