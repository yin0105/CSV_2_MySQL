<?php
$req = $_GET['r'];
$res = "";
$link = new mysqli("localhost", "root", "", "information_schema");
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
mysqli_options($link, MYSQLI_OPT_LOCAL_INFILE, true);
if($req == "get_db_list") {
    $sql = "SELECT schema_name FROM schemata WHERE schema_name NOT IN ('information_schema', 'mysql', 'performance_schema')";
    if($result = mysqli_query($link, $sql)) {
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result)){
                $res.="<option >" . $row[0] . "</option>";
            }
            mysqli_free_result($result);
        }
    }
    echo $res;    
    
} else if($req == "get_tbl_list") {
    $sql = "SELECT table_name FROM TABLES WHERE table_schema='" . $_GET["db"] . "'";
    if($result = mysqli_query($link, $sql)) {
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result)){
                $res.="<option >" . $row[0] . "</option>";
            }
            mysqli_free_result($result);
        }
    }
    echo $res;    
} else if($req == "get_col_list") {
    $sql = "SELECT column_name FROM COLUMNS WHERE table_schema='" . $_GET["db"] . "' AND table_name='" . $_GET["tbl"] . "'";
    if($result = mysqli_query($link, $sql)) {
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result)){
                $res.='<div class="list-group-item"><label><input type="checkbox"><span class="list-group-item-text">&nbsp;&nbsp;&nbsp;' . $row[0] . '</span></label></div>';
            }
            mysqli_free_result($result);
        }
    }
    echo $res;    
} else if($req == "get_charset_list") {
    $sql = "SELECT character_set_name, description FROM character_sets ORDER BY character_set_name";
    if($result = mysqli_query($link, $sql)) {
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result)){
                $res.="<option value='" . $row[0] . "'>" . $row[0] . ": " . $row[1] . "</option>";
            }
            mysqli_free_result($result);
        }
    }
    echo $res;    
} else if($req == "import") {
    $f_term = $_POST["f_term"];  
    $f_enc = $_POST["f_enc"]; 
    $f_esc = $_POST["f_esc"]; 
    $l_term = $_POST["l_term"]; 
    $opt = $_POST["opt"]=="true"?"OPTIONALLY ": " "; 
    $ign = $_POST["ign"];
    $trunc = $_POST["trunc"];
    $opt_ins = $_POST["opt_ins"];
    $db = $_POST["db"]; 
    $tbl = $_POST["tbl"]; 
    $encoding = $_POST["encoding"];
    $f_name = $_POST["f_name"]; 
    $col_list = "(`" . join("`, `", explode("#", $_POST["col_list"])) . "`);";

    while(! file_exists($f_name)) {
        sleep(1);
    }

    $sql = "";
    if ($trunc == "true") {
        $sql = "TRUNCATE TABLE `" . $db . "`.`" . $tbl . "`;
        ";
        if($result = mysqli_query($link, $sql)) {
            echo $sql;
        } else {
            echo mysqli_error($link);
        }
    }
    $sql = "LOAD DATA LOCAL INFILE '". $f_name . "' 
    " . $opt_ins . " INTO TABLE `" . $db . "`.`" . $tbl . "`
    CHARACTER SET ". $encoding . "
    FIELDS ESCAPED BY '" . $f_esc . "' 
    TERMINATED BY '" . $f_term . "' 
    " . $opt . " ENCLOSED BY '" . $f_enc . "' 
    LINES TERMINATED BY '" . $l_term . "'  
    IGNORE " . $ign . " LINES 
    " . $col_list ;
    if($result = mysqli_query($link, $sql)) {
        echo $sql;
    } else {
        echo mysqli_error($link);
    }
} else if($req == "upload") {   

    $currentDirectory = getcwd();
    $errors = []; // Store errors here
    $fileExtensionsAllowed = ['csv','txt']; // These will be the only file extensions allowed 

    $fileName = $_FILES['f_name']['name'];
    $fileTmpName  = $_FILES['f_name']['tmp_name'];
    $fileType = $_FILES['f_name']['type'];
    $fileExtension = strtolower(end(explode('.',$fileName)));

    $uploadPath = $currentDirectory . "/" . basename($fileName); 

    if (! in_array($fileExtension,$fileExtensionsAllowed)) {
        $errors[] = "This file extension is not allowed. Please upload a CSV or TXT file";
    } else {
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
        if ($didUpload) {
            echo "The file " . basename($fileName) . " has been uploaded";
        } else {
            echo "An error occurred. Please contact the administrator.";
        }
    }


    $f_term = $_POST["f_term"];  
    $f_enc = $_POST["f_enc"]; 
    $f_esc = $_POST["f_esc"]; 
    $l_term = $_POST["l_term"]; 
    $opt = isset($_POST["opt"])?"OPTIONALLY ": " "; 
    $ign = $_POST["m_touchspin_5"];
    $opt_ins = $_POST["opt_ins"];
    $db = $_POST["db_list"]; 
    $tbl = $_POST["tbl_list"]; 
    $encoding = $_POST["charset_list"];
    $col_list = "(`" . join("`, `", explode("#", $_POST["field_list"])) . "`);";

    $sql = "";
    if (isset($_POST["trunc"])) {
        $sql = "TRUNCATE TABLE `" . $db . "`.`" . $tbl . "`;
        ";
        if($result = mysqli_query($link, $sql)) {
            echo $sql;
        } else {
            echo mysqli_error($link);
        }
    }
    $sql = "LOAD DATA LOCAL INFILE '". $fileName . "' 
    " . $opt_ins . " INTO TABLE `" . $db . "`.`" . $tbl . "`
    CHARACTER SET ". $encoding . "
    FIELDS ESCAPED BY '" . $f_esc . "' 
    TERMINATED BY '" . $f_term . "' 
    " . $opt . " ENCLOSED BY '" . $f_enc . "' 
    LINES TERMINATED BY '" . $l_term . "'  
    IGNORE " . $ign . " LINES 
    " . $col_list ;

    // echo "<br>". $sql;
    if($result = mysqli_query($link, $sql)) {
        echo $sql;
    } else {
        echo mysqli_error($link);
    }

    header("Location: {$_SERVER['HTTP_REFERER']}");
	exit;
}

mysqli_close($link);
?>