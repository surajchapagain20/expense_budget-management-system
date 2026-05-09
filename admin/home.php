<style>
  .info-tooltip,.info-tooltip:focus,.info-tooltip:hover{
    background:unset;
    border:unset;
    padding:unset;
  }
  .welcome-banner {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 2.5rem;
    border-radius: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
    position: relative;
    overflow: hidden;
  }
  .welcome-banner::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
  }
  .stat-card {
    border-radius: 1.5rem;
    padding: 1.5rem;
    background: white;
    display: flex;
    align-items: center;
    gap: 1rem;
    height: 100%;
  }
  .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
  }
  .stat-details h3 {
    margin: 0;
    font-weight: 700;
    font-size: 1.5rem;
  }
  .stat-details span {
    color: var(--gray);
    font-size: 0.85rem;
  }
  .cat-card {
    background: white;
    border-radius: 1.5rem;
    padding: 1.5rem;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
  }
  .cat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    border-color: var(--primary);
  }
</style>

<div class="welcome-banner">
    <h1 class="font-weight-bold">Welcome back!</h1>
    <p class="mb-0 opacity-75">Here's what's happening with the budget today.</p>
</div>

<div class="row mb-4">
  <div class="col-12 col-sm-6 col-md-4 mb-3">
    <div class="stat-card shadow-sm">
      <div class="stat-icon" style="background: var(--primary)">
        <i class="fas fa-wallet"></i>
      </div>
      <div class="stat-details">
        <span>Overall Budget</span>
        <h3>
          <?php 
            $is_admin = $_settings->userdata('type') == 1;
            $dept_id = $_settings->userdata('department_id');
            if($is_admin){
                $cur_bul = $conn->query("SELECT sum(balance) as total FROM `categories` where status = 1 ")->fetch_assoc()['total'];
            }else{
                // For non-admin, sum up the balance from the department's running_balance
                $cur_bul = $conn->query("SELECT SUM(CASE WHEN balance_type = 1 THEN amount ELSE -amount END) as total FROM `running_balance` where department_id = '{$dept_id}' ")->fetch_assoc()['total'];
            }
            echo number_format($cur_bul);
          ?>
        </h3>
      </div>
    </div>
  </div>
  
  <div class="col-12 col-sm-6 col-md-4 mb-3">
    <div class="stat-card shadow-sm">
      <div class="stat-icon" style="background: var(--success)">
        <i class="fas fa-plus-circle"></i>
      </div>
      <div class="stat-details">
        <span>Today's Entries</span>
        <h3>
          <?php 
            $where_today = $is_admin ? "" : " AND department_id = '{$dept_id}' ";
            $today_budget = $conn->query("SELECT sum(amount) as total FROM `running_balance` where category_id in (SELECT id FROM categories where status =1) and date(date_created) = '".(date("Y-m-d"))."' and balance_type = 1 {$where_today} ")->fetch_assoc()['total'];
            echo number_format($today_budget);
          ?>
        </h3>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-4 mb-3">
    <div class="stat-card shadow-sm">
      <div class="stat-icon" style="background: var(--warning)">
        <i class="fas fa-minus-circle"></i>
      </div>
      <div class="stat-details">
        <span>Today's Expenses</span>
        <h3>
        <?php 
            $today_expense = $conn->query("SELECT sum(amount) as total FROM `running_balance` where category_id in (SELECT id FROM categories where status =1) and date(date_created) = '".(date("Y-m-d"))."' and balance_type = 2 {$where_today} ")->fetch_assoc()['total'];
            echo number_format($today_expense);
          ?>
        </h3>
      </div>
    </div>
  </div>
</div>

<?php 
$is_admin = $_settings->userdata('type') == 1;
$dept_id = $_settings->userdata('department_id');
$where_expiry = $is_admin ? "" : " AND department_id = '{$dept_id}' ";
$expiry_near = $conn->query("SELECT r.*, c.category FROM `running_balance` r INNER JOIN categories c ON r.category_id = c.id WHERE r.expiry_date IS NOT NULL AND r.expiry_date <= DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY) {$where_expiry} ORDER BY r.expiry_date ASC");
if($expiry_near->num_rows > 0):
?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 bg-danger text-white" style="border-radius: 1.5rem; background: linear-gradient(135deg, #ef233c, #d90429) !important;">
            <div class="card-header border-0 bg-transparent p-4 d-flex align-items-center">
                <h4 class="card-title m-0 text-white font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Expiry & Past Due Alerts</h4>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row">
                    <?php while($row = $expiry_near->fetch_assoc()): 
                        $is_expired = strtotime($row['expiry_date']) < strtotime(date('Y-m-d'));
                    ?>
                    <div class="col-md-4 mb-2">
                        <div class="p-3" style="background: rgba(255,255,255,0.1); border-radius: 1rem; border: 1px solid rgba(255,255,255,0.2);">
                            <div class="font-weight-bold d-flex justify-content-between align-items-center">
                                <span><?php echo $row['category'] ?></span>
                                <?php if($is_expired): ?>
                                    <span class="badge badge-warning text-dark">EXPIRED</span>
                                <?php endif; ?>
                            </div>
                            <div class="small">PO: <?php echo $row['po_number'] ?> | Amount: <?php echo number_format($row['amount']) ?></div>
                            <div class="font-weight-bold mt-1 <?php echo $is_expired ? 'text-warning' : '' ?>">
                                <?php echo $is_expired ? 'Expired On: ' : 'Expires: ' ?>
                                <?php echo date("M d, Y", strtotime($row['expiry_date'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title m-0">Category Breakdown</h3>
    <div class="card-tools">
      <div class="input-group input-group-sm" style="width: 250px;">
        <input type="text" id="search" class="form-control" placeholder="Search categories...">
        <div class="input-group-append">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="row" id="categoryList">
      <?php 
      $categories = $conn->query("SELECT * FROM `categories` where status = 1 order by `category` asc ");
        while($row = $categories->fetch_assoc()):
      ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 cat-items">
        <div class="cat-card">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="font-weight-bold mb-0"><?php echo $row['category'] ?></h5>
            <button type="button" class="btn btn-link p-0 text-muted info-tooltip" data-toggle="tooltip" data-html="true" title='<?php echo (html_entity_decode($row['description'])) ?>'>
              <i class="fas fa-info-circle"></i>
            </button>
          </div>
          <div class="d-flex align-items-end justify-content-between">
            <div class="small text-muted">Current Balance</div>
            <h4 class="mb-0 font-weight-bold text-primary">
                <?php 
                if($is_admin){
                    echo number_format($row['balance']);
                }else{
                    $cat_bal = $conn->query("SELECT SUM(CASE WHEN balance_type = 1 THEN amount ELSE -amount END) as total FROM `running_balance` where category_id = '{$row['id']}' and department_id = '{$dept_id}' ")->fetch_assoc()['total'];
                    echo number_format($cat_bal);
                }
                ?>
            </h4>
          </div>
          <div class="progress mt-3" style="height: 6px; border-radius: 10px;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%; border-radius: 10px;"></div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <div id="noData" class="text-center py-5" style="display:none">
        <img src="<?php echo base_url ?>dist/img/no-data.svg" alt="No data" style="width: 150px; opacity: 0.5" class="mb-3">
        <h3 class="text-muted">No categories found matching your search.</h3>
    </div>
  </div>
</div>

<script>
  function check_cats(){
    if($('.cat-items:visible').length > 0){
      $('#noData').hide()
      $('#categoryList').show()
    }else{
      $('#noData').show()
      $('#categoryList').hide()
    }
  }
  $(function(){
    $('[data-toggle="tooltip"]').tooltip({
      html:true
    })
    check_cats()
    $('#search').on('input',function(){
      var _f = $(this).val().toLowerCase()
      $('.cat-items').each(function(){
        var _c = $(this).find('h5').text().toLowerCase()
        if(_c.includes(_f) == true)
          $(this).toggle(true);
        else
          $(this).toggle(false);
      })
    check_cats()
    })
  })
</script>
