<?php
session_start();
if (isset($_SESSION['id_utilisateur'])) {
    echo 'connected';
} else {
    echo 'not_connected';
}
?>
