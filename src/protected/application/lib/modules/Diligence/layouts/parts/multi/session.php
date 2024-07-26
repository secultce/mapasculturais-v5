<?php
   if(isset($_SESSION['error']))
   {
    echo '<div class="alert danger">'.$_SESSION['error'].'</div>';
   }
?>

