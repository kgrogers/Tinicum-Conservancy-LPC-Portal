<?php 
require_once '../../../php/demo/tabs.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Guriddo TreeGrid PHP Demo</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../../../css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="../../../css/trirand/ui.jqgrid.css" />
    <style type="text">
        html, body {
        margin: 0;			/* Remove body margin/padding */
    	padding: 0;
        overflow: hidden;	/* Remove scroll bars on browser window */
        font-size: 75%;
        }
		
    </style>
    <script src="../../../js/jquery.min.js" type="text/javascript"></script>
    <script src="../../../js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="../../../js/jquery-ui.min.js" type="text/javascript"></script>
	
     <!-- This is the Javascript file of jqGrid -->   
    <script type="text/ecmascript" src="js/grid.base.js"></script>
    <script type="text/ecmascript" src="js/grid.common.js"></script>
    <script type="text/ecmascript" src="js/jquery.fmatter.js"></script>
    <script type="text/ecmascript" src="js/grid.treegrid.js"></script>
`	<script type="text/javascript">   	  
		$.jgrid.no_legacy_api = true;
		$.jgrid.useJSON = true;
		$.jgrid.defaults.width = "700";
	</script>
  </head>
  <body>
      <div>
          <?php include ("treegrid.php");?>
      </div>
      <br/>
      <?php tabs(array("treegrid.php"));?>
   </body>
</html>
