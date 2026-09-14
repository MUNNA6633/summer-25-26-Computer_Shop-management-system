<?php
// Destroys the login session and sends the user back to the login page.
// Note: this does NOT clear the "remember_username" cookie, so their
// username will still be pre-filled next time - only the active login
// session ends.
session_start();
session_destroy();
header('Location: ../index.php');
exit;
