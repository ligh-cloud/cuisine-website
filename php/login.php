<?php
require_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = trim($_POST["password"]);

    $email_pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $errors = [];

    if (empty($email) || !preg_match($email_pattern, $email)) {
        $errors[] = "Invalid or missing email.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $query = "SELECT id_client, name, password, email FROM user WHERE email = ?";
        $stat = $conn->prepare($query);
        $stat->bind_param("s", $email);
        $stat->execute();
        $result = $stat->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                session_start();
                $user_id = $row['id_client'];

                $_SESSION['user_id'] = $user_id;

              
                $role_query = "SELECT role FROM role WHERE id_client = ?";
                $role_stat = $conn->prepare($role_query);
                $role_stat->bind_param("i", $user_id);
                $role_stat->execute();
                $role_result = $role_stat->get_result();

                if ($role_result->num_rows > 0) {
                    $role_row = $role_result->fetch_assoc();
                    $role = $role_row['role'];

                   
                    if ($role == 'admin') {
                        $_SESSION['admin_id'] = $user_id; 
                        header("Location: admin_dashboard.php");
                        exit();
                    } elseif ($role == 'user') {
                        header("Location: user_dashboard.php");
                        exit();
                    } else {
                        echo "Unknown role.";
                    }
                } else {
                    echo "Role not found.";
                }
            } else {
                echo "Incorrect password.";
            }
        } else {
            echo "User not found.";
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
