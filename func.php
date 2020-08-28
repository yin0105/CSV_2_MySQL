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
    echo $_POST["f_term"];
    $f_term = $_POST["f_term"];  
    $f_enc = $_POST["f_enc"]; 
    $f_esc = $_POST["f_esc"]; 
    $l_term = $_POST["l_term"]; 
    // $f_term = str_replace("\\", "\\\\", $_POST["f_term"]);  
    // $f_enc = str_replace("\\", "\\\\", $_POST["f_enc"]); 
    // $f_esc = str_replace("\\", "\\\\", $_POST["f_esc"]); 
    // $l_term = str_replace("\\", "\\\\", $_POST["l_term"]); 
    $opt = $_POST["opt"]=="on"?" OPTIONALLY ": " "; 
    $ign = $_POST["ign"];
    $trunc = $_POST["trunc"];
    $opt_ins = $_POST["opt_ins"];
    $db = $_POST["db"]; 
    $tbl = $_POST["tbl"]; 
    $col_list = "(`" . join("`, `", explode("#", $_POST["col_list"])) . "`);";

    echo "<script>alert('f_term = '" . $_POST['f_term'] . ");</script>";

    $sql = "LOAD DATA LOCAL INFILE 'D:\\\\xampp\\\\mysql\\\\data\\\\yoyo\\\\sample.csv' 
    INTO TABLE `" . $db . "`.`" . $tbl . "`
    FIELDS ESCAPED BY '" . $f_esc . "' 
    TERMINATED BY '" . $f_term . "' 
    " . $opt . " ENCLOSED BY '" . $f_enc . "' 
    LINES TERMINATED BY '" . $l_term . "' 
    " . $col_list ;

    if($result = mysqli_query($link, $sql)) {
        echo $sql;
    } else {
        echo mysqli_error($link);
    }
}



//     $sql = "LOAD DATA LOCAL INFILE 'D:\\\\xampp\\\\mysql\\\\data\\\\yoyo\\\\sample.csv' 
//     INTO TABLE yoyo.sample
//     FIELDS ESCAPED BY '\\\\' 
//     TERMINATED BY ',' 
//     OPTIONALLY ENCLOSED BY '\"' 
//     LINES TERMINATED BY '\\r\\n' 
//     (`country`, `country_code`, `state`, `state_code`);
// ";

//     if($result = mysqli_query($link, $sql)) {
//         echo "executed";
//     } else {
//         echo mysqli_error($link);
//     }
mysqli_close($link);
?>