<html>    
  <head> 
    <style>
      *{
        font-family: verdana;
        font-size: 12px;
      }
      body{
        text-align:center;
		background: #E1E1E1;
      }
	  iframe{
		margin: 80px 50px;
	  }
    </style>

  <body>
  
<?php
if(isset($_GET['id'])){
	$id=$_GET['id'];
	if(isset($_GET['table'])){ $tabla =$_GET['table']; } else { $tabla ="cats";} 
	require_once("../../include/core/database.class.php");
	$Obj = new DBase();
	$Comentario = $Obj->GetDataRecordFromSQL("SELECT * FROM ".$tabla."_comments WHERE ID=".$id);
	echo $Comentario['Comment'];
} else {
	echo "No hay ningún comentario vinculado al identidficador seleccionado.";
}

?>

  </body>
</html>