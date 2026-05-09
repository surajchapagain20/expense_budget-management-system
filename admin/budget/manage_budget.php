<?php
require_once("../../config.php");
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `running_balance` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="conteiner-fluid">
<form action="" id="budget-form">
    <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
    <input type="hidden" name ="balance_type" value="1">
    <div class="form-row">
        <div class="form-group col-md-8 mb-1">
            <label for="category_id" class="control-label small">Category</label>
            <?php if(!isset($id)): ?>
                <select name="category_id" id="category_id" class="custom-select custom-select-sm select2" required>
                    <option value=""></option>
                    <?php 
                        $qry = $conn->query("SELECT * FROM `categories` order by category asc");
                        while($row= $qry->fetch_assoc()):
                    ?>
                    <option value="<?php echo $row['id'] ?>" <?php echo isset($category_id) && $category_id == $row['id'] ? 'selected' : '' ?> data-balance="<?php echo $row['balance'] ?>"><?php echo $row['category'] ?></option>
                    <?php endwhile; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="category_id" value="<?php echo $category_id ?>">
                <?php 
                    $cat_res = $conn->query("SELECT category FROM `categories` where id = '{$category_id}'")->fetch_assoc();
                ?>
                <div class="form-control form-control-sm bg-light"><b><?php echo $cat_res['category'] ?></b></div>
            <?php endif; ?>
        </div>
        <div class="form-group col-md-4 mb-1">
            <label for="amount" class="control-label small">Amount</label>
            <input name="amount" id="amount" class="form-control form-control-sm text-right number" value="<?php echo isset($amount) ? ($amount) : ''; ?>" placeholder="0.00">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4 mb-1">
            <label for="quantity" class="control-label small">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control form-control-sm text-right" step="any" value="<?php echo isset($quantity) ? $quantity : ''; ?>" placeholder="0">
        </div>
        <div class="form-group col-md-8 mb-1">
            <label for="po_number" class="control-label small">PO-Number</label>
            <input type="text" name="po_number" id="po_number" class="form-control form-control-sm" value="<?php echo isset($po_number) ? $po_number : ''; ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6 mb-1">
            <label for="purchase_date" class="control-label small">Purchase Date</label>
            <input type="date" name="purchase_date" id="purchase_date" class="form-control form-control-sm" value="<?php echo isset($purchase_date) ? $purchase_date : ''; ?>">
        </div>
        <div class="form-group col-md-6 mb-1">
            <label for="expiry_date" class="control-label small">Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" class="form-control form-control-sm" value="<?php echo isset($expiry_date) ? $expiry_date : ''; ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6 mb-1">
            <label for="bill_date" class="control-label small">Bill Date</label>
            <input type="date" name="bill_date" id="bill_date" class="form-control form-control-sm" value="<?php echo isset($bill_date) ? $bill_date : ''; ?>">
        </div>
        <div class="form-group col-md-6 mb-1">
            <label for="memo_approved_date" class="control-label small">Memo Date</label>
            <input type="date" name="memo_approved_date" id="memo_approved_date" class="form-control form-control-sm" value="<?php echo isset($memo_approved_date) ? $memo_approved_date : ''; ?>">
        </div>
    </div>
    <div class="form-group mb-0">
        <label for="remarks" class="control-label small">Remarks</label>
        <textarea name="remarks" id="" cols="30" rows="1" class="form-control form no-resize summernote"><?php echo isset($remarks) ? $remarks : ''; ?></textarea>
    </div>
</form>
</div>
<script>
	$(document).ready(function(){
        $('.select2').select2({placeholder:"Please Select here",width:"100%"})
        $('.summernote').summernote({
            height: 80,
            toolbar: [
                [ 'font', [ 'bold', 'italic', 'clear'] ],
                [ 'para', [ 'ol', 'ul' ] ]
            ]
        })
        $('.number').on('load input change',function(){
            var txt = $(this).val()
                var p = (txt.match(/[.]/g) || []).length;
                    console.log(p)
                if(txt.slice(-1) == '.' && p > 1){
                    $(this).val(txt.slice(0,-1))
                    return false;
                }
                if(txt.slice(-1) == '.'){
                    txt = txt
                }else{
                    txt = txt.split('.')
                    ntxt = ((txt[0]).replace(/\D/g,''));
                    if(!!txt[1])
                    ntxt += "."+txt[1]
                    ntxt = ntxt > 0 ? ntxt : 0;
                    txt = parseFloat(ntxt).toLocaleString('en-US')
                }
                $(this).val(txt)
        })
        $('.number').trigger('change')
		$('#budget-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_budget",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload()
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

	})
</script>