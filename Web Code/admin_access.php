<?php

    $admin_code_correct = "1234";

    
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        $admin_code = $_POST['admin_code'];

        if ($admin_code === $admin_code_correct) 
        {
            // Démarre la session et redirige vers la page admin
            session_start();
            $_SESSION['is_admin'] = true;
            header("Location: admin_interface.php");
            exit();
        } 
        else 
        {
            echo "Code admin incorrect.";
        }
    }
?>