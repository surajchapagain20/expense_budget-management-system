<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .table-container {
        background: white;
        border-radius: 1.5rem;
        padding: 1.5rem;
    }
    .action-btn {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: none;
    }
    .action-btn:hover {
        transform: scale(1.1);
    }
    .btn-edit { background: rgba(67, 97, 238, 0.1); color: var(--primary); }
    .btn-delete { background: rgba(239, 35, 60, 0.1); color: var(--danger); }
    
    .badge-amount {
        background: rgba(67, 97, 238, 0.05);
        color: var(--primary);
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 700;
        display: inline-block;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-weight-bold mb-0">Budget Management</h2>
        <p class="text-muted mb-0">Manage and track your allocated budgets.</p>
    </div>
    <button id="manage_budget" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus mr-2"></i> Add New Budget
    </button>
</div>

<div class="card shadow-sm border-0" style="border-radius: 1.5rem;">
	<div class="card-body p-4">
		<div class="table-responsive">
			<table class="table" id="budgetTable">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Info</th>
						<th>Category</th>
						<th>Amount</th>
						<th>Quantity</th>
						<th>Dates</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
                        $where = "";
                        if($_settings->userdata('type') != 1){
                            $where = " and r.department_id = '{$_settings->userdata('department_id')}' ";
                        }
						$qry = $conn->query("SELECT r.*,c.category,c.balance from `running_balance` r inner join `categories` c on r.category_id = c.id where c.status=1 and r.balance_type = 1 {$where} order by unix_timestamp(r.date_created) desc");
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
							}
                            $row['remarks'] = strip_tags(stripslashes(html_entity_decode($row['remarks'])));
					?>
						<tr class="budget-row" data-id="<?php echo $row['id'] ?>" style="cursor: pointer;" title="Double click to edit">
							<td class="text-center font-weight-bold text-muted"><?php echo $i++; ?></td>
							<td>
                                <div class="small">
                                    <div class="font-weight-bold"><i class="fas fa-hashtag text-primary mr-1"></i> PO: <?php echo !empty($row['po_number']) ? $row['po_number'] : 'N/A' ?></div>
                                    <div class="text-muted"><i class="far fa-calendar-alt mr-1"></i> <?php echo date("M d, Y",strtotime($row['date_created'])) ?></div>
                                </div>
                            </td>
							<td>
                                <span class="font-weight-bold"><?php echo $row['category'] ?></span>
                                <div class="small text-muted truncate-1" style="max-width: 150px;"><?php echo $row['remarks'] ?></div>
                            </td>
							<td>
                                <span class="badge-amount">
                                    <?php echo number_format($row['amount']) ?>
                                </span>
                            </td>
							<td class="text-center font-weight-bold">
                                <?php echo number_format($row['quantity']) ?>
                            </td>
							<td>
                                <div class="small" style="line-height: 1.2;">
                                    <?php if(!empty($row['purchase_date'])): ?>
                                        <div class="mb-1"><span class="badge badge-info">P: <?php echo date("M d, Y",strtotime($row['purchase_date'])) ?></span></div>
                                    <?php endif; ?>
                                    <?php if(!empty($row['expiry_date'])): ?>
                                        <div class="mb-1"><span class="badge badge-warning">E: <?php echo date("M d, Y",strtotime($row['expiry_date'])) ?></span></div>
                                    <?php endif; ?>
                                    <?php if(!empty($row['bill_date'])): ?>
                                        <div class="mb-1"><span class="badge badge-secondary">B: <?php echo date("M d, Y",strtotime($row['bill_date'])) ?></span></div>
                                    <?php endif; ?>
                                    <?php if(!empty($row['memo_approved_date'])): ?>
                                        <div><span class="badge badge-success">M: <?php echo date("M d, Y",strtotime($row['memo_approved_date'])) ?></span></div>
                                    <?php endif; ?>
                                </div>
                            </td>
							<td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:void(0)" class="action-btn btn-edit manage_budget mr-2" data-id="<?php echo $row['id'] ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="action-btn btn-delete delete_data" data-id="<?php echo $row['id'] ?>" data-category_id="<?php echo $row['category_id'] ?>" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('#manage_budget').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Budget",'budget/manage_budget.php','modal-lg')
		})
		$('.manage_budget').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Update Budget",'budget/manage_budget.php?id='+$(this).attr('data-id'),'modal-lg')
		})
        $('.budget-row').dblclick(function(){
            uni_modal("<i class='fa fa-edit'></i> Update Budget",'budget/manage_budget.php?id='+$(this).attr('data-id'),'modal-lg')
        })
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this budget permanently?","delete_budget",[$(this).attr('data-id'),$(this).attr('data-category_id')])
		})
		$('#uni_modal').on('show.bs.modal',function(){
			$('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'para', [ 'ol', 'ul' ] ],
		            [ 'view', [ 'undo', 'redo'] ]
		        ]
		    })
		})
		$('#budgetTable').dataTable({
			columnDefs: [
				{ orderable: false, targets: 6 }
			],
			order: [[0, 'asc']],
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records..."
            }
		});
	})
	function delete_budget($id,$category_id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_budget",
			method:"POST",
			data:{id: $id,category_id: $category_id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>