<?php
    session_start();

    require_once "sqlPDO.php";
    $db = opendb("MySQL","tinicum");
    $sql = "insert into tblMemberActivity (LpcMemberID,activity,occurredAt, result)
            values (:LpcMemberID,:activity,:now,:result)";
    $actvy = $db->prepare($sql);
    $actvy->execute(array(":LpcMemberID"=>$_SESSION['LpcMemberID'],"activity"=>"logoff",":now"=>date('Y-m-d H:i:s'),":result"=>"success"));
    unset($_SESSION);
    session_destroy();
    header("location: /thanks.html");
?>    
