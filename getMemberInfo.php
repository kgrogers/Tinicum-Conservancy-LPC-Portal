<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        return false;
    }

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    
    include 'sqlPDO.php';
    $db = opendb('MySQL','tinicum');
        
    $sql = "
        select concat(m.FirstName,' ',m.LastName) as Name,
               m.Phone,
               m.eMail,
               t.LpcDescription
        from tblLpcMembers m,
             tblLpcType t
        where m.Lpc = t. LpcID and
              m.FirstName = :fname and
              m.LastName = :lname";
    
    $stmt = $db->prepare($sql);
    $stmt->execute(array(":fname"=>$fname,":lname"=>$lname));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print(json_encode($row));
?>