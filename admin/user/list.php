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

<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .badge-admin { background: rgba(114, 9, 183, 0.1); color: var(--secondary); font-weight: 700; border-radius: 8px; padding: 0.4rem 0.8rem; }
    .badge-user { background: rgba(67, 97, 238, 0.1); color: var(--primary); font-weight: 700; border-radius: 8px; padding: 0.4rem 0.8rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-weight-bold mb-0">System Users</h2>
        <p class="text-muted mb-0">Manage system access and roles.</p>
    </div>
    <button id="manage_user" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus mr-2"></i> Add New User
    </button>
</div>

<div class="card shadow-sm border-0" style="border-radius: 1.5rem;">
	<div class="card-body p-4">
		<div class="table-responsive">
			<table class="table" id="userTable">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Avatar</th>
						<th>Name</th>
						<th>Username</th>
						<th>Email</th>
						<th>Type</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from `users` where id != '{$_settings->userdata('id')}' order by concat(firstname,' ',lastname) asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr class="user-row" data-id="<?php echo $row['id'] ?>" style="cursor: pointer;">
							<td class="text-center font-weight-bold text-muted"><?php echo $i++; ?></td>
							<td>
                                <img src="<?php echo validate_image($row['avatar']) ?>" class="user-avatar" alt="Avatar">
                            </td>
							<td>
                                <div class="font-weight-bold"><?php echo $row['firstname'].' '.$row['lastname'] ?></div>
                            </td>
							<td>
                                <span class="text-muted"><?php echo $row['username'] ?></span>
                            </td>
							<td>
                                <?php if(!empty($row['email'])): ?>
                                    <a href="mailto:<?php echo $row['email'] ?>" class="text-primary small"><?php echo $row['email'] ?></a>
                                <?php else: ?>
                                    <span class="text-muted small"><i>Not set</i></span>
                                <?php endif; ?>
                            </td>
							<td>
                                <?php if($row['type'] == 1): ?>
                                    <span class="badge-admin">Admin</span>
                                <?php else: ?>
                                    <span class="badge-user">Normal User</span>
                                <?php endif; ?>
                            </td>
							<td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="javascript:void(0)" class="action-btn btn-edit manage_user mr-2" data-id="<?php echo $row['id'] ?>" title="Edit">
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

<script>
	$(document).ready(function(){
		$('#manage_user').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New User",'user/manage_user.php')
		})
		$('.manage_user').click(function(){
			uni_modal("<i class='fa fa-edit'></i> Update User Details",'user/manage_user.php?id='+$(this).attr('data-id'))
		})
        $('.user-row').dblclick(function(){
            uni_modal("<i class='fa fa-edit'></i> Update User Details",'user/manage_user.php?id='+$(this).attr('data-id'))
        })
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this user permanently?","delete_user",[$(this).attr('data-id')])
		})
		$('#userTable').dataTable({
			columnDefs: [
				{ orderable: false, targets: [1, 6] }
			],
			order: [[0, 'asc']],
            paging: true,
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search users..."
            }
		});
	})
	function delete_user($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Users.php?f=delete",
			method:"POST",
			data:{id: $id},
			success:function(resp){
				if(resp == 1){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>
