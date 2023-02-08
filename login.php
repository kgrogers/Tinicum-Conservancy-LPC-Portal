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
                   LPC,
                   permission
            from tblLpcMembers
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
            $_SESSION['mLPC'] = $usrData['LPC'];
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
        global $em;
        global $mi;
        global $fn;
        
        if (!in_array(strtolower($email),$em)) {
            $_SESSION['loggedin'] = 'missingEmail';
            $_SESSION['username'] = '';
            $_SESSION['permission'] = '';
            $_SESSION['LpcMemberID'] = '';
            return $_SESSION;
        }
        
        $sql = "
            select FirstName,
                   LastName,
                   lower(eMail) eMail,
                   LpcMemberId LpcMemberID,
                   password,
                   username,
                   permission,
                   MemberInactive
            from tblLpcMembers
            where eMail = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(":email"=>$email));
        $usrData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usrData['MemberInactive'] == 1) {
            $_SESSION['loggedin'] = 'inactive';
            $_SESSION['username'] = '';
            $_SESSION['permission'] = '';
            $_SESSION['LpcMemberID'] = '';
        } elseif ($email == $usrData['eMail']) {
            $userid = fivetwo($usrData['FirstName'], $usrData['LastName']);
            $sql = "
                update tblLpcMembers
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
            $_SESSION['FirstName'] = $fn[array_search(strtolower($email),$em)];
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
    $fn = $em = $mi = $un = array();
    
    // Preload arrays for prechecks
    $sql = "
        select FirstName,
               eMail,
               LPC,
               MemberInactive,
               username
        from tblLpcMembers";
    foreach ($db->query($sql,PDO::FETCH_ASSOC) as $row) {
        $fn[] = $row['FirstName'];
        $em[] = strtolower($row['eMail']);
        $mi[] = $row['MemberInactive'];
        $un[] = $row['username'];
    }

    if ($userid == "") {
        $loggedin = register(strtolower($email),$pwd);
    } else {
        $loggedin = login($userid, $pwd);
    }
    print json_encode($loggedin);
?>
