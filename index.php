<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        $signedIn['loggedin'] = 'no';
    } else {
        $signedIn = $_SESSION;
        $_SESSION['LandOwnerID'] = '';
    }
    include "sqlPDO.php";
    $db = opendb('MySQL','tinicum');
    foreach ($db->query("select LpcID,LPC from tblLpcType order by 1") as $row) {
        $LpcList[$row['LpcID']] = $row['LPC'];
    }
    unset($db);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Ken Rogers on behalf of Tinicum Conservancy, LLC">
    <meta name="generator" content="Hugo 0.88.1">
    <title>Tinicum Conservancy Land Owners Comittee Portal</title>

    <!-- <link rel="canonical" href="https://getbootstrap.com/docs/5.1/examples/sign-in/"> -->
    <!-- Bootstrap core CSS -->
    <link href="/bootstrap5/css/bootstrap.css" rel="stylesheet">
    <style>
        .input-group-append {
            cursor: pointer;
        }
        #loinfo th {
            width: 200px;
        }
    
        #loinfo td {
            width: 1000px;
        }
    
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
    
        @media (min-width: 375px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
        < !-- ---------------- -->
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        .b-example-divider {
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
        
        .mparcels {
            cursor: pointer;
            text-decoration: underline;
        }
        .rh {
            display:none;
        }
        #newNote {
            display: none;
        }

        #tlogo {
            width: 70px;
            height: 70px;
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="/css/signin.css" rel="stylesheet" id="signin_css">
    <!-- Custom styles for dashboard -->
    <link href="dashboard.css" rel="stylesheet" id="dash_css" disabled="disabled">
    <link href="/bootstrap5/css/bootstrap-icons.css" rel="stylesheet">
    <link href="/bootstrap5/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/js.cookie.js"></script>
    <script>
        $(function(){
            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
        });
        var SIGNEDIN = <?php print json_encode($signedIn); ?>;
        var LOID = '';
        var lcpmemselect = '';
        var testVar = '';
        var LandOwnerData = '';
        var lcpmodeselect = '';
        var LpcList = <?php print json_encode($LpcList); ?>;
        var ThisLandOwner = '';
        
        // $.cookie('TC_uname','rogeke5', {expires: 30})
        // console.log("SIGNEDIN Before doc.ready:", SIGNEDIN);
                
        function loginScreen() {
            $('.register').hide();
            $('.remember').show();
            $('#signin').prop('disabled', false);
            $('#InputEmail').prop('required', false);
            $('#InputPassword1').prop('required', true);
            $('#InputPassword2').prop('required', false);
            $('h1.h3.mb-3.fw-normal').html('Please sign in');
            $('#InputUsername').val($.cookie('TC_uname'));
            if (!$.cookie('TC_uname')) {
                $('#InputUsername').focus();
            } else {
                $('#InputPassword1').css('margin-bottom', '10px').focus();
            }
            $('#signin').html('Sign in');
        }
        
        function registerScreen() {
            $('.register').show();
            $('.remember').hide();
            $('#InputEmail').focus();
            $('#signin').prop('disabled', true);
            $('#InputEmail').prop('required', true);
            $('#InputPassword1').prop('required', true);
            $('#InputPassword2').prop('required', true);
            $('h1.h3.mb-3.fw-normal').html('Please register');
            $('#InputPassword1').css('margin-bottom', '-1px');
            $('#InputPassword2').css('margin-bottom', '10px');
            $('#signin').html('Submit');
        }
        
        function mainScreen() {
            // console.log("executing function mainScreen, SIGNEDIN:", SIGNEDIN);
            $.ajax({
                    url: 'landowners.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        memberID: SIGNEDIN['LpcMemberID'],
                        permission: SIGNEDIN['permission']
                    }
                })
                .always(function(retData) {
                    $('#landOwnerList').html(retData);
                    $('#formSignIn').hide();
                    $('header').show();
                    $('#ContainerDash').show();
                    $('#dash_css').removeAttr('disabled');
                    $('#signin_css').attr('disabled', 'disabled');
                });
            $('#formSignIn').hide();
            $('header').show();
            $('#ContainerDash').show();
            $('#dash_css').removeAttr('disabled');
            $('#signin_css').attr('disabled', 'disabled');
        }
        $(document).ready(function() {
            $('#forgotup').hide();
            if (SIGNEDIN['loggedin'] == 'yes') {
                mainScreen();
            }
            if ($.cookie('TC_uname')) {
                $('#InputUsername').val($.cookie('TC_uname'));
                $('#InputPassword1').focus();
                $('#InputPassword2').removeAttr('required');
                $('#InputEmail').removeAttr('required');
            }
            // $('#savenewnote').on('click', function(e) {
                // e.preventDefault();
            // });
            $('#b_action').on('click', function(e) {
                // console.log(SIGNEDIN);
                switch(SIGNEDIN['permission']) {
                    case "user":
                        $('a#adminpage').hide();
                        break;
                    case "lpchead":
                        $('a#adminpage').hide();
                        $('a#'+LpcList[SIGNEDIN['mLPC']]).show();
                        break;
                    case "root":
                        $('.rh').show();
                        break;
                }
                if (SIGNEDIN['numLandOwners'] > 0) {
                    $('a#propreport').show();
                } else {
                    $('a#propreport').hide();
                }
                if (SIGNEDIN['LandOwnerID'] == 0) {
                    $('a#propreport1').hide();
                    // $('a#propreport').hide();
                }
                $('ul.dropdown-menu').toggle();
            });
            $('a.dropdown-item').on('click', function(e) {
                // console.log($(this).html());
                $('ul.dropdown-menu').toggle();
                switch($(this).html()) {
                    case "Open Admin Page":
                        window.open('/lpc-admin');
                        break;
                    case "Logoff":
                        window.open('logout.php', "_self");
                        break;
                    case "My Landowners Report":
                        window.open('membersReport.php?rpt=MY','_blank');
                        break;
                    case ThisLandOwner:
                        window.open('memberReport.php','_blank');
                        break;
                    case "Bridgeton Landowners Report":
                        SIGNEDIN['reportType'] = 'BR';
                        window.open('membersReport.php?rpt=BR','_blank');
                        break;
                    case "Nockamixon Landowners Report":
                        SIGNEDIN['reportType'] = 'NX';
                        window.open('membersReport.php?rpt=NX','_blank');
                        break;
                    case "Tinicum Landowners Report":
                        SIGNEDIN['reportType'] = 'TT';
                        window.open('membersReport.php?rpt=TT','_blank');
                        break;
                    case "All Landowners Report":
                        window.open('membersReport.php?rpt=ALL','_blank');
                        break;
                }
            });
            $('#LandOwnerListQuestion').hover(function() {
                $(this).prop('title',"Landowners that are colored red\nare are Status inactive. Check the\nLand Owner Notes section\nto determine the disposition of\nthe parcel(s) they own(ed)");
            });
            $('#newnoteform').on('submit', function(e) {
                // console.log(e);
                e.preventDefault();
                // $(this).addClass('was-validated');
                if (typeof $('#contactNextStep').val() == 'undefined') {
                    contactNextStep = '';
                } else {
                    contactNextStep = $('#contactNextStep').val();
                }
                // newNoteData = {
                    // 'LOID': LOID,
                    // 'contactDate': $('#contactDate').val(),
                    // 'contactMode': parseInt($('#contactMode').val()),
                    // 'contactBy': parseInt($('#members').val()),
                    // 'contactNote': $('#contactNote').val(),
                    // 'contactNextStep': contactNextStep
                // }
                // console.log("newNoteData",newNoteData);
                $.ajax({
                    url: 'addnewnote.php',
                    method: 'post',
                    dataType: 'html',
                    // data: { newNoteData: newNoteData }
                    data: {
                        LOID: LOID,
                        contactDate: $('#contactDate').val(),
                        contactMode: parseInt($('#contactMode').val()),
                        contactBy: parseInt($('#members').val()),
                        contactNote: $('#contactNote').val(),
                        contactNextStep: contactNextStep
                    }
                })
                .success(function(retData) {
                    // console.log("Return data from newNodeAdd: ",retData);
                    $('#newNoteModal').modal('hide');
                    $('#t-contactnotes').html(retData);
                    // Reset the AddNote form
                    $('#newnoteform').trigger('reset');
                });
            });
            $('#landOwnerList').on('click', 'a', function() {
                console.log($(this).html().split(',')[0]);
                ThisLandOwner = $(this).html().split(',')[0]+" Landowner Report"
                $('#propreport1').html(ThisLandOwner);
                LOID = parseInt($(this).attr("loid"));
                SIGNEDIN['LandOwnerID'] = LOID;
                $('a#propreport1').show();
                $.ajax({
                        url: 'landownernorth.php',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            loid: $(this).attr("loid")
                        }
                    })
                    .success(function(retData) {
                        LandOwnerData = retData;
                        // console.log(retData);
                        html = '';
                        $.each(retData, function(k, v) {
                            if (k.indexOf("ma_") < 0) {
                                html += '<tr><th class="table-success ps-2">' + k + '</th><td class="ps-2">' + v + '</td></tr>';
                            }
                        });
                        $('#loinfo').html(html);
                    });
                $.ajax({
                        url: 'tabsDataFill.php',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            loid: $(this).attr("loid")
                        }
                })
                .success(function(hretData) {
                    $('#t-contactnotes').html(hretData['contactnoteshtml']);
                    $('#t-parcels').html(hretData['parcelshtml']);
                    $('#t-mailingaddress').html(hretData['mailaddrhtml']);
                    $('#newNote').show();
                    $('#t-contactnotes tr td').hover(function() {
                        if ($(this)[0]['id'] != '' && SIGNEDIN['permission'] == 'root') {
                            // console.log($(this)[0]['id']);
                            $(this).prop('title',"ContactNoteID: "+$(this)[0]['id']);
                        }
                    });

                });
                // console.log("LpcmemberID="+SIGNEDIN['LpcMemberID']);
                $.ajax({
                    url: 'getContactModes.php',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        LpcMemberID: SIGNEDIN['LpcMemberID']
                    }
                })
                .success(function(retData) {
                    lcpmodeselect = retData['contactModes'];
                    lcpmebersselect = retData['members'];
                    $('#contactMode').html(lcpmodeselect);
                    $('#members').html(lcpmebersselect);
                });                    
            });
        
            $('#regCheck').on('click', function() {
                if ($('#regCheck').is(':checked')) {
                    $.removeCookie('TC_uname');
                    $('#InputUsername').val('');
                    // $('#forgotup').hide();
                    registerScreen();
                } else {
                    // $('#forgotup').show();
                    loginScreen();
                }
            });
        
            $('#signin').on('click', function() {
                $.ajax({
                        url: 'login.php',
                        method: 'post',
                        dataType: 'json',
                        data: {
                            InputEmail: $('#InputEmail').val(),
                            InputUsername: $('#InputUsername').val(),
                            InputPassword1: $('#InputPassword1').val()
                        }
                    })
                    .always(function(retData) {
                        // console.log("retData from #signin click", retData);
                        if (retData['loggedin'] == 'yes') {
                            SIGNEDIN = retData;
                            // console.log("loggedin:", retData);
                            // LOGGEDIN = retData;
                            // SIGNEDIN['loggedin'] = 'yes';
                            $.cookie('TC_uname', retData['username'], {
                                expires: 90
                            });
                            // console.log("Get Land Owners list for LpcMemberID " + retData['LpcMemberID']);
                            $.ajax({
                                    url: 'landowners.php',
                                    method: 'post',
                                    dataType: 'json',
                                    data: {
                                        memberID: retData['LpcMemberID'],
                                        permission: retData['permission']
                                    }
                                })
                                .always(function(retData) {
                                    // $('#LOMenu').html(retData);
                                    $('#landOwnerList').html(retData);
                                    $('#formSignIn').hide();
                                    $('header').show();
                                    $('#ContainerDash').show();
                                    $('#dash_css').removeAttr('disabled');
                                    $('#signin_css').attr('disabled', 'disabled');
                                });
                            // $('main').hide();
                            // $('.container-fluid').show();
                        }
                        if (retData['loggedin'] == 'emailCreds') {
                            // console.log("emailCreds:", retData);
                            // $.cookie('TC_uname', retData['username'], {
                                // expires: 30
                            // });
                            $.ajax({
                                url: 'mailto.php',
                                method: 'post',
                                dataType: 'json',
                                data: {
                                    email: $('#InputEmail').val(),
                                    fname: retData['FirstName']
                                }
                            })
                            .always(function() {
                                alert("An email was sent to you that has your username. Once you receieve it you should be able to log in");
                                $('#regCheck').prop('checked', false);
                                $('#InputEmail').val('');
                                $('#InputPassword1').val('');
                                $('#InputPassword2').val('');
                                location.reload();
                            });
                        }
                        if (retData['loggedin'] == 'pwfail') {
                            // console.log("Password is wrong!");
                            alert('Wrong userid or password - Did you already register? If so, try again. If you forgot your password, please register again.');
                            $('#InputPassword1').val('');
                        }
                        if (retData['loggedin'] == 'missingEmail') {
                            alert("You don't have an email address registered with us - Please contact someone at Tinicum Conservancy for assistance.");
                            $('#regCheck').prop('checked', false);
                            window.open('thanks.html','_self');
                        }
                        if (retData['loggedin'] == 'inactive') {
                            alert("You are not currsntly an active member - Please contact someone at Tinicum Conservancy for assistance.");
                            $('#regCheck').prop('checked', false);
                            window.open('thanks.html','_self');
                        }
                    });
            });
            $('#InputPassword1').keyup(function() {
                if ($('#InputPassword1').val().length < 8) {
                    $('#InputPassword1').css('color', 'red').attr('title', 'Password too short');
                    $('#InputPassword2').prop('disabled', true);
                } else {
                    $('#InputPassword1').css('color', 'black').attr('title', 'Length Ok');
                    $('#InputPassword2').prop('disabled', false);
                }
            });
            $('#InputPassword2').keyup(function() {
                $('#signin').prop('disabled', true);
                if ($('#InputPassword1').val() != $('#InputPassword2').val()) {
                    $('#InputPassword2').css("color", "red").attr("title", "Passwords don't match");
                } else {
                    $('#InputPassword2').css("color", "black").attr("title", "Passwords match");
                    $('#signin').prop('disabled', false);
                }
            });
        });
    </script>
</head>

<body>
    <!-- Modal -->
    <div class="modal fade modal-lg" id="newNoteModal" tabindex="-1" aria-labelledby="LOModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="LOModalLabel">Land Owner Contact Note</h5>
                <!--    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body">
                    <form class="row" id="newnoteform" action="return">
                        <label for="contactDate" class="col-1 col-form-label">Date</label>
                        <div class="col-12">
                            <div class="input-group date mb-3" id="datepicker">
                                <input type="text" class="form-control" id="contactDate" required />
                                <span class="input-group-append">
                                    <span class="input-group-text bg-light d-block">
                                        <i class="bi bi-calendar3"></i>
                                    </span>
                                </span>
                            </div>
                            <div class="mb-3">
                                <label for="contactMode">Contact Mode</label>
                                <select class="form-select" aria-label="contactMode" id="contactMode" required></select>
                            </div>
                            <div class="mb-3">
                                <label for="members">Contacted By</label>
                                <select class="form-select" aria-label="members" id="members"></select>
                            </div>
                            <div class="mb-3">
                                <label for="Note">Note</label>
                                <textarea rows="8" class="form-control" id="contactNote" required="required"></textarea>
                            </div>
                        </div>
                        <div>
                            <label for="nextStep">Next Step</label>
                            <textarea rows="3" class="form-control" id="contactNextStep"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="savenewnote">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal End-->
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow" style="display:none">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6" href="#">Land Owners&nbsp;&nbsp;
            <i id="LandOwnerListQuestion" class="bi bi-question-circle"></i>
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <!-- <a class="nav-link px-3" href="#" loid=-10>Sign out</a> -->
            </div>
        </div>
    </header>
    <div class="container" id="formSignIn">
        <form class="form-signin" onsubmit="return false;">
            <img class="mb-4" src="/images/tinicum-logo-encircled-TRANSPARENT.png" alt="" width="150" height="150">
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

            <div class="form-floating register">
                <input type="email" class="form-control" id="InputEmail" placeholder="name@example.com" name="InputEmail" required>
                <label for="InputEmail">Email address</label>
            </div>
            <div class="form-floating remember">
                <input type="text" class="form-control" id="InputUsername" name="InputUsername" autofocus>
                <label for="InputUsername">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="InputPassword1" placeholder="Password" name="InputPassword1" required>
                <label for="InputPassword1">Password</label>
            </div>
            <div class="form-floating register">
                <input type="password" class="form-control" id="InputPassword2" placeholder="Re-Enter Password" name="InputPassword2" required=true>
                <label for="InputPassword2">Re-Enter Password</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary" id="signin" type="submit">Sign in</button>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" value="" id="regCheck">
                <label class="form-check-label" for="regCheck">Register</label>
            </div>
            <div class="form-check form-check-inline" id="forgotup">
                <input class="form-check-input" type="checkbox" value="" id="forgotCheck" disabled>
                <label class="form-check-label" for="forgotCheck" title="Future feature">Forgot userid/pwd</label>
            </div>

        </form>
    </div>

    <div class="container-fluid" id="ContainerDash" style="display:none;">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3 sidebar-sticky">
                    <ul id="landOwnerList" class="nav flex-column">
                    </ul>
                </div>
            </nav>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><img src="/images/tinicum-logo-encircled-TRANSPARENT.png" id="tlogo" class="pe-1">Land Owner Information</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button id="b_action" type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">Actions</button>
                            <ul class="dropdown-menu" style="transform: translate(-186px, 38px)">
                                <li><a id="adminpage" class="dropdown-item" href="#">Open Admin Page</a></li>
                                <li><a id="propreport" class="dropdown-item" href="#">My Landowners Report</a></li>
                                <li><a id="propreport1" class="dropdown-item" href="#">This Landowner Report</a></li>
                                <li><a id="BR" class="dropdown-item rh" href="#">Bridgeton Landowners Report</a></li>
                                <li><a id="NX" class="dropdown-item rh" href="#">Nockamixon Landowners Report</a></li>
                                <li><a id="TT" class="dropdown-item rh" href="#">Tinicum Landowners Report</a></li>
                                <li><a id="allReport" class="dropdown-item rh" href="#">All Landowners Report</a></li>
                                <li><a id="logoff" class="dropdown-item" href="#">Logoff</a></li>
                            </ul>

                    </div>
                </div>
                <table class="table-responsive table-bordered" id="loinfo">
                    <tbody>
                        <tr class="table-success">
                            <th>Land Owner</th>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <th class="table-success">Assigned To</th>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <th class="table-success">Status</th>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <th class="table-success">How To Contact</th>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <th class="table-success">Land Owner Notes</th>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="row">
                    <div class="col-lg-12">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#contactnotes">Contact Notes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#parcels">Parcels</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#mailingaddress">Mailing Address</a>
                            </li>
                        </ul>
                        <!-- Tab Panes -->
                        <div class="tab-content">
                            <div id="contactnotes" class="container tab-pane active"><br />
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#newNoteModal" id="newNote">New Contact
                                    Note</button><br />
                                <div class="table-responsive pt-2">
                                    <table class="table table-striped table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>Contact Date</th>
                                                <th>Contacted By</th>
                                                <th>Contact Mode</th>
                                                <th>Contact Note</th>
                                                <th>Next Step</th>
                                            </tr>
                                        </thead>
                                        <tbody id="t-contactnotes"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="parcels" class="container tab-pane fade"><br />
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>Parcel Number</th>
                                                <th>Acres</th>
                                                <th>Deeded To</th>
                                                <th>Road Num</th>
                                                <th>Road</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Zip</th>
                                                <th>Land Use</th>
                                                <th>WaterShed</th>
                                                <th>Contiguous Parcels</th>
                                                <th>Gas Lease</th>
                                                <th>Disqualifying Uses</th>
                                                <th>LPC</th>
                                            </tr>
                                        </thead>
                                        <tbody id="t-parcels"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="mailingaddress" class="container tab-pane fade"><br />
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th>Addressed To</th>
                                                <th>Address 1</th>
                                                <th>Address 2</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Zip</th>
                                                <th>Full Address</th>
                                            </tr>
                                        </thead>
                                        <tbody id="t-mailingaddress"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="/bootstrap5/assets/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/bootstrap5/js/bootstrap-datepicker.min.js"></script>
</body>

</html>