<?php require_once('../config.php'); ?>
 <!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?>
  <body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed sidebar-mini-md sidebar-mini-xs" data-new-gr-c-s-check-loaded="14.991.0" data-gr-ext-installed="" style="height: auto;">
    <div class="wrapper">
     <?php require_once('inc/topBarNav.php') ?>
     <?php require_once('inc/navigation.php') ?>
              
     <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home';  ?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper pt-3" style="min-height: 567.854px;">
     
        <!-- Main content -->
        <section class="content  text-dark">
          <div class="container-fluid">
            <?php 
              if(!file_exists($page.".php") && !is_dir($page)){
                  include '404.html';
              }else{
                if(is_dir($page))
                  include $page.'/index.php';
                else
                  include $page.'.php';

              }
            ?>
          </div>
        </section>
        <!-- /.content -->
  <div class="modal fade" id="confirm_modal" role='dialog' data-keyboard="true" data-backdrop="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog' data-keyboard="true" data-backdrop="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog' data-keyboard="true" data-backdrop="true">
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog' data-keyboard="true" data-backdrop="true">
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
      </div>
      <!-- /.content-wrapper -->
      <?php require_once('inc/footer.php') ?>
      <?php 
        require_once('../classes/EmailHelper.php');
        $is_admin = $_settings->userdata('type') == 1;
        $dept_id = $_settings->userdata('department_id');
        $where_expiry = $is_admin ? "" : " AND r.department_id = '{$dept_id}' ";
        // Include both expired and expiring within 7 days
        $expiry_items = $conn->query("SELECT r.*, c.category FROM `running_balance` r INNER JOIN categories c ON r.category_id = c.id WHERE r.expiry_date IS NOT NULL AND r.expiry_date <= DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY) {$where_expiry} ORDER BY r.expiry_date ASC");
        $expiry_count = $expiry_items->num_rows;
        if($expiry_count > 0):
            $items_list = "<ul>";
            $email_rows = [];
            $expiry_items->data_seek(0);
            while($row = $expiry_items->fetch_assoc()){
                $is_expired = strtotime($row['expiry_date']) < strtotime(date('Y-m-d'));
                $color = $is_expired ? '#ff0000' : 'inherit';
                $label = $is_expired ? 'EXPIRED' : date("M d", strtotime($row['expiry_date']));
                $items_list .= "<li style='text-align:left; margin-bottom:5px;'><a href='javascript:void(0)' class='open-expiry' data-id='{$row['id']}' data-type='{$row['balance_type']}' style='color:{$color}'>{$row['category']} (PO: {$row['po_number']}) - {$label}</a></li>";
                $email_rows[] = ['row' => $row, 'is_expired' => $is_expired, 'label' => $label];
            }
            $items_list .= "</ul>";

            // Send email alerts once per day per session login
            $email_session_key = 'expiry_email_sent_' . date('Y-m-d') . '_' . $_settings->userdata('id');
            if(!isset($_SESSION[$email_session_key])) {
                // Get all users with emails in the same department(s) that have expiring items
                $dept_ids_with_expiry = [];
                foreach($email_rows as $er) { $dept_ids_with_expiry[] = (int)$er['row']['department_id']; }
                $dept_ids_with_expiry = array_unique($dept_ids_with_expiry);

                if(!empty($dept_ids_with_expiry)) {
                    $ids_str = implode(',', $dept_ids_with_expiry);
                    $email_users = $conn->query("SELECT * FROM users WHERE email IS NOT NULL AND email != '' AND department_id IN ({$ids_str})");
                    
                    if($email_users && $email_users->num_rows > 0) {
                        $system_name = $_settings->info('name') ?: 'Expense Budget System';

                        // Build styled email table rows
                        $email_table_rows = '';
                        foreach($email_rows as $er) {
                            $r = $er['row'];
                            $badge = $er['is_expired']
                                ? "<span class='badge-expired'>EXPIRED</span>"
                                : "<span class='badge-warning'>Expires " . date('M d, Y', strtotime($r['expiry_date'])) . "</span>";
                            $email_table_rows .= "
                                <tr>
                                    <td>{$r['category']}</td>
                                    <td>{$r['po_number']}</td>
                                    <td>" . date('M d, Y', strtotime($r['expiry_date'])) . "</td>
                                    <td>{$badge}</td>
                                </tr>";
                        }

                        while($eu = $email_users->fetch_assoc()) {
                            $name = htmlspecialchars($eu['firstname'] . ' ' . $eu['lastname']);
                            $dept_name_q = $conn->query("SELECT name FROM departments WHERE id = '{$eu['department_id']}'");
                            $dept_name = $dept_name_q && $dept_name_q->num_rows > 0 ? $dept_name_q->fetch_assoc()['name'] : 'Your Department';

                            $content = "
                                <h2 style='font-size:20px;font-weight:700;color:#1f2937;margin-bottom:14px;'>&#9888; Expiry Alert</h2>
                                <p style='font-size:15px;line-height:1.7;color:#4b5563;'>Hi <strong>{$name}</strong>,</p>
                                <p style='font-size:15px;line-height:1.7;color:#4b5563;'>The following budget/expense items in <strong>{$dept_name}</strong> are <strong>expired or expiring within the next 7 days</strong>. Please take action immediately.</p>
                                " . email_alert("<strong>&#128680; {$expiry_count} item(s) require your immediate attention.</strong>", 'danger') . "
                                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;width:100%;margin:16px 0;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;'>
                                    <thead>
                                        <tr style='background:#f3f4f6;'>
                                            <th style='padding:10px 14px;font-size:13px;text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;'>Category</th>
                                            <th style='padding:10px 14px;font-size:13px;text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;'>PO Number</th>
                                            <th style='padding:10px 14px;font-size:13px;text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;'>Expiry Date</th>
                                            <th style='padding:10px 14px;font-size:13px;text-align:left;color:#374151;border-bottom:1px solid #e5e7eb;'>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$email_table_rows}
                                    </tbody>
                                </table>
                                <p style='font-size:15px;line-height:1.7;color:#4b5563;margin-top:16px;'>Please log in to the system to review and update these records.</p>
                                " . email_button(base_url . "admin/", "&#128279; Go to Dashboard") . "
                            ";

                            $html = email_template(
                                "Expiry Alert — {$system_name}",
                                "{$expiry_count} item(s) in {$dept_name} are expired or expiring soon.",
                                $content
                            );
                            send_html_email($eu['email'], "&#9888; Expiry Alert — {$system_name}", $html);
                        }
                    }
                }
                $_SESSION[$email_session_key] = true;
            }
      ?>
      <script>
          $(function(){
            Swal.fire({
                icon: 'warning',
                title: 'Expiry Alerts (<?php echo $expiry_count ?>)',
                html: 'The following items are expired or nearing expiry:<br><br><?php echo $items_list ?>',
                confirmButtonColor: '#4361ee',
                allowEscapeKey: true,
                allowOutsideClick: true,
                customClass: {
                    container: 'expiry-swal-container'
                }
            })

            $(document).on('click', '.open-expiry', function(){
                var id = $(this).attr('data-id')
                var type = $(this).attr('data-type')
                var page = type == 1 ? 'budget/manage_budget.php' : 'expense/manage_expense.php';
                var title = type == 1 ? 'Update Budget' : 'Update Expense';
                uni_modal("<i class='fa fa-edit'></i> "+title, page+'?id='+id, 'modal-lg')
                Swal.close()
            })
          })
      </script>
      <?php endif; ?>
  </body>
</html>
