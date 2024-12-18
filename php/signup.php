<?php
require_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["full_name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = trim($_POST["password"]);
    $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
    $email_pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $name_pattern = "/^[a-zA-Z\s]+$/";
    $errors = [];

    if (empty($name) || !preg_match($name_pattern, $name)) {
        $errors[] = "Invalid or missing name.";
    }


    if (empty($email) || !preg_match($email_pattern, $email)) {
        $errors[] = "Invalid or missing email.";
    }


    if (empty($errors)) {
       
        $query = "INSERT INTO user (name, email, password) VALUES (?, ?, ?)";
        $stat = $conn->prepare($query);

        if ($stat) {
            $stat->bind_param("sss", $name, $email, $hashed_pass);
        }

        if ($stat->execute()) {
            
            $user_id = $stat->insert_id;

     
            $role = ($user_id == 1) ? 'admin' : 'user';

         
            $role_query = "INSERT INTO role (role, id_client) VALUES (?, ?)";
            $role_stat = $conn->prepare($role_query);

            if ($role_stat) {
                $role_stat->bind_param("si", $role, $user_id);  
            }

            if ($role_stat->execute()) {
                echo "<div>Client added successfully. Redirecting...</div>";
                echo "<script> 
                        setTimeout(function() {
                            window.location.href = 'index.php';
                        }, 2000);
                      </script>";
                      echo "<div class='bg-green-500 text-white font-bold py-2 px-4 rounded shadow-md text-center'>
  Successfully registered
</div>";
            } else {
                echo "Error assigning role.";
            }
        } else {
            echo "Error adding user.";
        }
    } else {
      
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}
?>
<head>
<link href="../src/output.css" rel="stylesheet">
</head>
