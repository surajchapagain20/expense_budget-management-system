<?php 
if($_settings->userdata('type') != 1){
    echo "<script>alert('You do not have permission to access this page.'); location.replace('./');</script>";
    exit;
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Categories</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped" id="categoryTable">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="35%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Category</th>
						<th>Description</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `categories` order by unix_timestamp(date_created) desc ");
						while($row = $qry->fetch_assoc()):
                            $row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])));
					?>
						<tr class="category-row" data-id="<?php echo $row['id'] ?>" style="cursor: pointer;" title="Double click to edit">
							<td class="text-center font-weight-bold text-muted"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['category'] ?></td>
							<td ><p class="truncate-1 m-0"><?php echo $row['description'] ?></p></td>
							<td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
							<td align="center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:void(0)" class="action-btn btn-edit edit_data mr-2" data-id="<?php echo $row['id'] ?>" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="action-btn btn-delete delete_data" data-id="<?php echo $row['id'] ?>" title="Delete">
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
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this category permanently? The action will remove also the budget and expense under this category","delete_category",[$(this).attr('data-id')])
		})
        $('#create_new').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Category","maintenance/manage_category.php")
		})
		$('.edit_data').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Edit Category","maintenance/manage_category.php?id="+$(this).attr('data-id'))
		})
        $('.category-row').dblclick(function(){
            uni_modal("<i class='fa fa-edit'></i> Edit Category","maintenance/manage_category.php?id="+$(this).attr('data-id'))
        })
		$('#categoryTable').dataTable({
            paging: true,
            pageLength: 10,
            columnDefs: [
				{ orderable: false, targets: 5 }
			]
        });
	})
	function delete_category($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_category",
			method:"POST",
			data:{id: $id},
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