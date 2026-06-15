<?php
session_start();
session_destroy();
header("Location: login.php");
exit; //quebra de sessão, sair
?>