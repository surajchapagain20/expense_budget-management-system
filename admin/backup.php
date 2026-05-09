<?php 
if($_settings->userdata('type') != 1){
    echo "<script>alert('You do not have permission to access this page.'); location.replace('./');</script>";
    exit;
}

$backup_dir = base_app . 'backup/';
if(!is_dir($backup_dir)){
    mkdir($backup_dir, 0777, true);
}

// Action handlers
if(isset($_GET['action'])){
    if($_GET['action'] == 'create'){
        // Create backup
        $filename = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
        $zip_filename = 'backup_' . date('Y_m_d_H_i_s') . '.zip';
        $filepath = $backup_dir . $filename;
        $zip_filepath = $backup_dir . $zip_filename;
        
        // Dump database using mysqli
        $tables = array();
        $result = $conn->query("SHOW TABLES");
        while($row = $result->fetch_row()){
            $tables[] = $row[0];
        }
        
        $sqlScript = "";
        foreach($tables as $table){
            $result = $conn->query("SHOW CREATE TABLE $table");
            $row = $result->fetch_row();
            $sqlScript .= "\n\n" . $row[1] . ";\n\n";
            
            $result = $conn->query("SELECT * FROM $table");
            $columnCount = $result->field_count;
            
            for($i = 0; $i < $columnCount; $i++){
                while($row = $result->fetch_row()){
                    $sqlScript .= "INSERT INTO $table VALUES(";
                    for($j = 0; $j < $columnCount; $j++){
                        $row[$j] = $row[$j];
                        
                        if(isset($row[$j])){
                            $sqlScript .= '"' . $conn->real_escape_string($row[$j]) . '"';
                        }else{
                            $sqlScript .= 'NULL';
                        }
                        if($j < ($columnCount - 1)){
                            $sqlScript .= ',';
                        }
                    }
                    $sqlScript .= ");\n";
                }
            }
            $sqlScript .= "\n"; 
        }
        
        if(!empty($sqlScript)){
            // Save the sql file
            file_put_contents($filepath, $sqlScript);
            
            // Create zip
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if($zip->open($zip_filepath, ZipArchive::CREATE) === TRUE){
                    $zip->addFile($filepath, $filename);
                    $zip->close();
                    // Delete sql file
                    unlink($filepath);
                    
                    $_settings->set_flashdata('success', 'Backup created successfully');
                }else{
                    $_settings->set_flashdata('error', 'Failed to create zip file');
                }
            } else {
                // Fallback: Use PowerShell to zip the file on Windows
                $ps_command = "powershell -Command \"Compress-Archive -Path '{$filepath}' -DestinationPath '{$zip_filepath}' -Force\"";
                exec($ps_command, $output, $return_var);
                
                if ($return_var === 0 && file_exists($zip_filepath)) {
                    unlink($filepath);
                    $_settings->set_flashdata('success', 'Backup created successfully (using fallback compressor)');
                } else {
                    // If even powershell fails, just keep the sql file
                    $_settings->set_flashdata('success', 'Backup created successfully as .sql (Zip compression unavailable)');
                }
            }
        }else{
            $_settings->set_flashdata('error', 'Failed to create backup');
        }
        echo "<script>location.href='".base_url."admin/?page=backup';</script>";
        exit;
    }
    
    if($_GET['action'] == 'download' && isset($_GET['file'])){
        $file = basename($_GET['file']);
        $filepath = $backup_dir . $file;
        if(file_exists($filepath)){
            ob_clean();
            $ext = pathinfo($filepath, PATHINFO_EXTENSION);
            header('Content-Description: File Transfer');
            if($ext == 'zip'){
                header('Content-Type: application/zip');
            } else {
                header('Content-Type: application/sql');
            }
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        }
    }
    
    if($_GET['action'] == 'delete' && isset($_GET['file'])){
        $file = basename($_GET['file']);
        $filepath = $backup_dir . $file;
        if(file_exists($filepath)){
            unlink($filepath);
            $_settings->set_flashdata('success', 'Backup deleted successfully');
        }
        echo "<script>location.href='".base_url."admin/?page=backup';</script>";
        exit;
    }
}
?>

<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php if($_settings->chk_flashdata('error')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('error') ?>",'error')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Database Backups</h3>
		<div class="card-tools">
			<a href="<?php echo base_url ?>admin/?page=backup&action=create" class="btn btn-flat btn-sm btn-primary"><span class="fas fa-database"></span> Create Backup</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-bordered table-stripped" id="backupTable">
				<colgroup>
					<col width="5%">
					<col width="30%">
					<col width="30%">
					<col width="20%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>File Name</th>
						<th>Date Created</th>
						<th>Size</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
                    if(is_dir($backup_dir)){
                        $files = scandir($backup_dir);
                        $files = array_diff($files, array('.', '..'));
                        rsort($files); // Sort by newest
                        
                        foreach($files as $file):
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            if(in_array($ext, ['zip', 'sql'])):
                                $filepath = $backup_dir . $file;
                                $filemtime = filemtime($filepath);
                                $filesize = filesize($filepath);
                                $filesize_kb = round($filesize / 1024, 2);
					?>
						<tr>
							<td class="text-center font-weight-bold text-muted"><?php echo $i++; ?></td>
							<td><?php echo $file ?></td>
							<td><?php echo date("Y-m-d H:i", $filemtime) ?></td>
							<td><?php echo $filesize_kb ?> KB</td>
							<td align="center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?php echo base_url ?>admin/?page=backup&action=download&file=<?php echo urlencode($file) ?>" class="action-btn btn-primary mr-2" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="action-btn btn-delete delete_data" data-file="<?php echo htmlspecialchars($file) ?>" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
							</td>
						</tr>
					<?php 
                            endif;
                        endforeach; 
                    }
                    ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
            var file = $(this).attr('data-file');
			_conf("Are you sure to delete this backup file permanently?","delete_backup",[file])
		})
        
		$('#backupTable').dataTable({
            paging: true,
            pageLength: 10,
            columnDefs: [
				{ orderable: false, targets: 4 }
			]
        });
	})
	function delete_backup($file){
		start_loader();
        location.href = _base_url_+"admin/?page=backup&action=delete&file="+$file;
	}
</script>
