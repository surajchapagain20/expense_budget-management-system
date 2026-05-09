<?php 
require_once('../../config.php');
if(isset($_GET['id'])){
    $user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    }
}
?>
<div class="container-fluid">
    <div id="msg"></div>
    <form action="" id="manage-user-form">	
        <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
        <div class="form-row">
            <div class="form-group col-md-6 mb-2">
                <label for="firstname" class="control-label small">First Name</label>
                <input type="text" name="firstname" id="firstname" class="form-control form-control-sm" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
            </div>
            <div class="form-group col-md-6 mb-2">
                <label for="lastname" class="control-label small">Last Name</label>
                <input type="text" name="lastname" id="lastname" class="form-control form-control-sm" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 mb-2">
                <label for="username" class="control-label small">Username</label>
                <input type="text" name="username" id="username" class="form-control form-control-sm" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required autocomplete="off">
            </div>
            <div class="form-group col-md-6 mb-2">
                <label for="type" class="control-label small">User Type</label>
                <select name="type" id="type" class="form-control form-control-sm custom-select select2-modal" required>
                    <option value="0" <?php echo isset($meta['type']) && $meta['type'] == 0 ? 'selected': '' ?>>Normal User</option>
                    <option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected': '' ?>>Admin</option>
                </select>
            </div>
        </div>
        <div class="form-group mb-2">
            <label for="email" class="control-label small">Email Address</label>
            <input type="email" name="email" id="email" class="form-control form-control-sm" value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>" placeholder="user@example.com" autocomplete="off">
            <small class="text-muted"><i>Used for expiry notifications and password reset.</i></small>
        </div>
        <div class="form-group mb-2">
            <label for="department_id" class="control-label small">Department</label>
            <select name="department_id" id="department_id" class="form-control form-control-sm custom-select select2-modal" required>
                <option value="" disabled <?php echo !isset($meta['department_id']) ? 'selected' : '' ?>>Select Department</option>
                <?php 
                $dept_qry = $conn->query("SELECT * FROM departments where status = 1 order by name asc");
                while($drow = $dept_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $drow['id'] ?>" <?php echo isset($meta['department_id']) && $meta['department_id'] == $drow['id'] ? 'selected' : '' ?>><?php echo $drow['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group mb-2">
            <label for="password" class="control-label small">Password</label>
            <input type="password" name="password" id="password" class="form-control form-control-sm" value="" autocomplete="off" <?php echo !isset($_GET['id']) ? 'required' : '' ?>>
            <?php if(isset($_GET['id'])): ?>
                <small class="text-muted"><i>Leave blank to keep current password.</i></small>
            <?php endif; ?>
        </div>
        <div class="form-row align-items-center">
            <div class="form-group col-md-8 mb-0">
                <label for="" class="control-label small">Avatar</label>
                <div class="custom-file custom-file-sm">
                    <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
                    <label class="custom-file-label" for="customFile">Choose file</label>
                </div>
            </div>
            <div class="form-group col-md-4 mb-0 text-center">
                <img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg-modal" class="img-fluid img-thumbnail" style="height: 60px; width: 60px; object-fit: cover; border-radius: 50%;">
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        // Using a specific class to avoid conflicts with global select2
        $('.select2-modal').select2({
            placeholder: "Please select here",
            width: "100%",
            dropdownParent: $('#uni_modal')
        })
    })

    function displayImg(input,_this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg-modal').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $('#manage-user-form').submit(function(e){
        e.preventDefault();
        start_loader()
        $.ajax({
            url:_base_url_+'classes/Users.php?f=save',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp == 1){
                    location.reload()
                }else{
                    $('#msg').html('<div class="alert alert-danger">Username already exists</div>')
                    end_loader()
                }
            }
        })
    })
</script>
