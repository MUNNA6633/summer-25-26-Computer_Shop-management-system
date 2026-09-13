<?php
session_start();
session_destroy();
header('Location: ../../views/customer/login.php');
exit;