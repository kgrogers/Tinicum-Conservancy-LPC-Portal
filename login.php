<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        $_SESSION['loggedin'] = "no";
    } else {
        $_SESSION['loggedin'] = "yes";
        return false;
    }
    // $_SESSION['username'] = '';
    $_SESSION['permission'] = '';
    $_SESSION['LpcMemberID'] = '';
    $_SESSION['pwdfailcnt'] = 0;

    $email = $_POST['InputEmail'];
    $userid = $_POST['InputUsername'];
    $pwd = $_POST['InputPassword1'];

    function login($userid, $pwd) {
        global $db;
        $sql = "
            select username,
                   password,
                   LpcMemberID,
                   permission
            from tblMemberCreds
            where username = :uname";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(":uname"=>$userid));
        $usrData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($pwd, $usrData['password']) && $userid == $usrData['username']) {
            $_SESSION['loggedin'] = 'yes';
            $_SESSION['username'] = $usrData['username'];
            $_SESSION['permission'] = $usrData['permission'];
            $_SESSION['LpcMemberID'] = $usrData['LpcMemberID'];
            $_SESSION['LandOwnerID'] = 0;
        } else {
            $_SESSION['loggedin'] = 'pwfail';
            $_SESSION['username'] = $usrData['username'];
            $_SESSION['permission'] = $usrData['permission'];
            $_SESSION['LpcMemberID'] = $usrData['LpcMemberID'];
            $_SESSION['pwfailcnt'] += 1;
            if ($_SESSION['pwfailcnt'] > 3) {
                $_SESSION['loggedin'] = 'too many bad';
            }
        }
        return $_SESSION;
    }
    
    function register($email,$pwd) {
        global $db;
        $sql = "
            select m.FirstName,
                   m.LastName,
                   lower(m.eMail) eMail,
                   m.LpcMemberId LpcMemberID,
                   c.password,
                   c.username,
                   c.permission
            from tblLpcMembers m, tblMemberCreds c
            where m.LpcMemberID = c.LpcMemberID and
                  m.eMail = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(":email"=>$email));
        $usrData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($email == $usrData['eMail']) {
            $userid = fivetwo($usrData['FirstName'], $usrData['LastName']);
            $sql = "
                update tblMemberCreds
                set password = :pwd,
                    username = :username
                where LpcMemberID = :memid";
            $stmt = $db->prepare($sql);
            $stmt->execute(array(
                ":pwd"=>password_hash($pwd, PASSWORD_DEFAULT),
                ":username"=>$userid,
                ":memid"=>$usrData['LpcMemberID']
            ));
            $_SESSION['loggedin'] = 'emailCreds';
            $_SESSION['username'] = $userid;
            $_SESSION['permission'] = $usrData['permission'];
            $_SESSION['LpcMemberID'] = $usrData['LpcMemberID'];
        } else {
            // Email not found
            $_SESSION['loggedin'] = 'contact admin';
            $_SESSION['username'] = '';
            $_SESSION['permission'] = '';
            $_SESSION['LpcMemberID'] = '';
        }            
        return $_SESSION;
    }
    
    function password_generate($chars) {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, $chars);
    }
    
    function fivetwo($fname,$lname) {
        if (strlen($lname) < 5) {
            $ft = $lname.substr($fname,0,7 - strlen($lname));
        } else {
            $ft = substr($lname,0,5).substr($fname,0,2);
        }
        return strtolower($ft);
    }

    
    require_once "sqlPDO.php";
    
    $db = opendb("MySQL","tinicum");

    if ($userid == "") {
        $loggedin = register(strtolower($email),$pwd);
    } else {
        $loggedin = login($userid, $pwd);
    }
    print json_encode($loggedin);
?>
