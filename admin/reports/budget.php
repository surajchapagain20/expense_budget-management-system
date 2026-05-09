<?php 
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] :  date("Y-m-d",strtotime(date("Y-m-d")." -7 days")) ;
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] :  date("Y-m-d") ;
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] :  'all' ;
?>
<div class="card card-primary card-outline shadow-sm border-0" style="border-radius: 1.5rem;">
    <div class="card-header border-0 bg-transparent p-4">
        <h4 class="card-title font-weight-bold m-0"><i class="fas fa-file-invoice-dollar mr-2"></i> Budget Report</h4>
    </div>
    <div class="card-body p-4 pt-0">
        <form id="filter-form" class="mb-4 p-3 bg-light" style="border-radius: 1rem;">
            <div class="row align-items-end">
                <div class="form-group col-md-3 mb-0">
                    <label for="date_start" class="small font-weight-bold">Date Start</label>
                    <input type="date" class="form-control form-control-sm" name="date_start" value="<?php echo date("Y-m-d",strtotime($date_start)) ?>">
                </div>
                <div class="form-group col-md-3 mb-0">
                    <label for="date_end" class="small font-weight-bold">Date End</label>
                    <input type="date" class="form-control form-control-sm" name="date_end" value="<?php echo date("Y-m-d",strtotime($date_end)) ?>">
                </div>
                <div class="form-group col-md-3 mb-0">
                    <label for="category_id" class="small font-weight-bold">Category</label>
                    <select name="category_id" id="category_id" class="form-control form-control-sm custom-select select2">
                        <option value="all" <?php echo $category_id == 'all' ? 'selected' : '' ?>>All Categories</option>
                        <?php 
                        $cat_qry = $conn->query("SELECT * FROM categories where status = 1 order by category asc");
                        while($crow = $cat_qry->fetch_assoc()):
                        ?>
                        <option value="<?php echo $crow['id'] ?>" <?php echo $category_id == $crow['id'] ? 'selected' : '' ?>><?php echo $crow['category'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group col-md-2 mb-0">
                    <button class="btn btn-primary btn-sm btn-block"><i class="fa fa-filter mr-1"></i> Filter</button>
                </div>
            </div>
        </form>

        <div id="printable">
            <div class="text-center mb-4">
                <h4 class="font-weight-bold m-0"><?php echo $_settings->info('name') ?></h4>
                <h3 class="m-0 text-primary"><b>Budget Report</b></h3>
                <p class="text-muted m-0 small">
                    Period: <?php echo date("M d, Y",strtotime($date_start)) ?> - <?php echo date("M d, Y",strtotime($date_end)) ?>
                    <?php if($category_id != 'all'): 
                        $cname = $conn->query("SELECT category FROM categories where id = '{$category_id}'")->fetch_assoc()['category'];
                    ?>
                    | Category: <?php echo $cname ?>
                    <?php endif; ?>
                </p>
            </div>
            <table class="table table-bordered table-hover" id="reportTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-center py-2">#</th>
                        <th class="py-2">Date</th>
                        <th class="py-2">Category</th>
                        <th class="py-2 text-right">Amount</th>
                        <th class="py-2">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $total = 0;
                    $where = "";
                    if($category_id != 'all') $where .= " and r.category_id = '{$category_id}' ";
                    if($_settings->userdata('type') != 1) $where .= " and r.department_id = '{$_settings->userdata('department_id')}' ";
                    
                    $qry = $conn->query("SELECT r.*,c.category from `running_balance` r inner join `categories` c on r.category_id = c.id where c.status=1 and r.balance_type = 1 and date(r.date_created) between '{$date_start}' and '{$date_end}' {$where} order by unix_timestamp(r.date_created) asc");
                    while($row = $qry->fetch_assoc()):
                        $row['remarks'] = (stripslashes(html_entity_decode($row['remarks'])));
                        $total += $row['amount'];
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++ ?></td>
                        <td><?php echo date("M d, Y",strtotime($row['date_created'])) ?></td>
                        <td class="font-weight-bold"><?php echo $row['category'] ?></td>
                        <td class="text-right font-weight-bold text-success"><?php echo number_format($row['amount']) ?></td>
                        <td><div class="small"><?php echo $row['remarks'] ?></div></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-light">
                        <th class="text-right py-3 px-3" colspan="3">Grand Total</th>
                        <th class="text-right py-3 text-success h5 m-0 font-weight-bold"><?php echo number_format($total) ?></th>
                        <th class="bg-light"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.select2').select2({
            placeholder: "Select Category",
            width: '100%'
        })
        $('#filter-form').submit(function(e){
            e.preventDefault()
            var start = $('[name="date_start"]').val()
            var end = $('[name="date_end"]').val()
            var cat = $('[name="category_id"]').val()
            location.href = "./?page=reports/budget&date_start="+start+"&date_end="+end+"&category_id="+cat
        })

        $('#reportTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-secondary shadow-sm mr-1',
                    text: '<i class="fa fa-print mr-1"></i> Print',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (win) {
                        $(win.document.body).css('font-size', '10pt');
                        $(win.document.body).find('table').addClass('compact').css({
                            'font-size': 'inherit',
                            'width': '100%'
                        });
                    }
                },
                {
                    extend: 'excelHtml5',
                    title: 'Budget Report - ' + '<?php echo date("M d Y", strtotime($date_start)) ?> to <?php echo date("M d Y", strtotime($date_end)) ?>',
                    className: 'btn btn-sm btn-success shadow-sm mr-1',
                    text: '<i class="far fa-file-excel mr-1"></i> Excel'
                },
                {
                    extend: 'csvHtml5',
                    title: 'Budget Report',
                    className: 'btn btn-sm btn-info shadow-sm mr-1',
                    text: '<i class="fas fa-file-csv mr-1"></i> CSV'
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Budget Report',
                    className: 'btn btn-sm btn-danger shadow-sm mr-1',
                    text: '<i class="far fa-file-pdf mr-1"></i> PDF',
                    customize: function(doc) {
                        doc.content[1].table.widths = ['5%', '20%', '25%', '15%', '35%'];
                    }
                }
            ],
            paging: true,
            info: true,
            language: {
                search: "",
                searchPlaceholder: "Search report..."
            }
        })
    })
</script>