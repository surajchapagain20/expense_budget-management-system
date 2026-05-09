<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-weight-bold mb-0">Financial Analysis</h2>
        <p class="text-muted mb-0">Overview of the overall budgetary system.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0" style="border-radius: 1.5rem;">
            <div class="card-header border-0 bg-transparent p-4">
                <h4 class="card-title font-weight-bold"><i class="fas fa-chart-line text-primary mr-2"></i> Budget vs Expense Trend</h4>
            </div>
            <div class="card-body p-4">
                <canvas id="trendChart" style="min-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 1.5rem;">
            <div class="card-header border-0 bg-transparent p-4">
                <h4 class="card-title font-weight-bold"><i class="fas fa-chart-pie text-success mr-2"></i> Allocation</h4>
            </div>
            <div class="card-body p-4 d-flex align-items-center justify-content-center">
                <canvas id="allocationChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0" style="border-radius: 1.5rem;">
            <div class="card-header border-0 bg-transparent p-4">
                <h4 class="card-title font-weight-bold"><i class="fas fa-list text-info mr-2"></i> Category Summary</h4>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table" id="summaryTable">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-right">Total Budget</th>
                                <th class="text-right">Total Expense</th>
                                <th class="text-right">Balance</th>
                                <th class="text-center">Utilization</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $dept_id = $_settings->userdata('department_id');
                            $is_admin = $_settings->userdata('type') == 1;
                            $where_r = $is_admin ? "" : " AND r.department_id = '{$dept_id}' ";

                            $summary = $conn->query("SELECT 
                                c.category, 
                                SUM(CASE WHEN r.balance_type = 1 THEN r.amount ELSE 0 END) as budget,
                                SUM(CASE WHEN r.balance_type = 2 THEN r.amount ELSE 0 END) as expense,
                                c.balance
                                FROM categories c 
                                LEFT JOIN running_balance r ON c.id = r.category_id {$where_r}
                                WHERE c.status = 1 
                                GROUP BY c.id " . 
                                (!$is_admin ? "HAVING budget > 0 OR expense > 0 " : "") . 
                                "ORDER BY c.category ASC");
                            $cat_names = [];
                            $cat_budgets = [];
                            $cat_expenses = [];
                            while($row = $summary->fetch_assoc()):
                                // If not admin, the category balance should be calculated based on the filtered records
                                if(!$is_admin){
                                    $row['balance'] = $row['budget'] - $row['expense'];
                                }
                                $utilization = $row['budget'] > 0 ? ($row['expense'] / $row['budget']) * 100 : 0;
                                $cat_names[] = $row['category'];
                                $cat_budgets[] = $row['budget'];
                                $cat_expenses[] = $row['expense'];
                            ?>
                            <tr>
                                <td class="font-weight-bold"><?php echo $row['category'] ?></td>
                                <td class="text-right text-primary font-weight-bold"><?php echo number_format($row['budget']) ?></td>
                                <td class="text-right text-danger font-weight-bold"><?php echo number_format($row['expense']) ?></td>
                                <td class="text-right font-weight-bold"><?php echo number_format($row['balance']) ?></td>
                                <td>
                                    <div class="progress" style="height: 10px; border-radius: 5px;">
                                        <div class="progress-bar <?php echo $utilization > 90 ? 'bg-danger' : ($utilization > 70 ? 'bg-warning' : 'bg-success') ?>" role="progressbar" style="width: <?php echo min(100, $utilization) ?>%"></div>
                                    </div>
                                    <div class="text-center small mt-1 font-weight-bold"><?php echo number_format($utilization, 1) ?>%</div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Fetch trend data (last 6 months)
$months = [];
$monthly_budget = [];
$monthly_expense = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date("Y-m", strtotime("-$i months"));
    $months[] = date("M Y", strtotime("-$i months"));
    
    $where_trend = $is_admin ? "" : " AND department_id = '{$dept_id}' ";
    $mb = $conn->query("SELECT SUM(amount) as total FROM running_balance WHERE balance_type = 1 AND DATE_FORMAT(date_created, '%Y-%m') = '$m' {$where_trend}")->fetch_assoc()['total'];
    $me = $conn->query("SELECT SUM(amount) as total FROM running_balance WHERE balance_type = 2 AND DATE_FORMAT(date_created, '%Y-%m') = '$m' {$where_trend}")->fetch_assoc()['total'];
    
    $monthly_budget[] = (float)$mb;
    $monthly_expense[] = (float)$me;
}
?>

<script>
$(function(){
    // Trend Chart
    var trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($months) ?>,
            datasets: [{
                label: 'Budget',
                data: <?php echo json_encode($monthly_budget) ?>,
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Expense',
                data: <?php echo json_encode($monthly_expense) ?>,
                borderColor: '#ef233c',
                backgroundColor: 'rgba(239, 35, 60, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Allocation Chart
    var allocCtx = document.getElementById('allocationChart').getContext('2d');
    new Chart(allocCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($cat_names) ?>,
            datasets: [{
                data: <?php echo json_encode($cat_budgets) ?>,
                backgroundColor: [
                    '#4361ee', '#4cc9f0', '#4895ef', '#7209b7', '#f72585', '#ef233c'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            cutout: '70%'
        }
    });

    $('#summaryTable').dataTable({
        paging: false,
        searching: false,
        info: false,
        order: [[1, 'desc']]
    });
})
</script>
