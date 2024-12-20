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
<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
  
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  
    <div id="sweetAlert" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-sm mx-auto shadow-lg transform transition-all">
            <div class="text-center">
              
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2" id="alertTitle">Success!</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500" id="alertMessage">Registration completed successfully!</p>
                </div>
                
                <div class="mt-5">
                    <button type="button" onclick="closeSweetAlert()" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSweetAlert(title, message, success = true) {
            const alertElement = document.getElementById('sweetAlert');
            const titleElement = document.getElementById('alertTitle');
            const messageElement = document.getElementById('alertMessage');
            
            titleElement.textContent = title;
            messageElement.textContent = message;
            
          
            alertElement.classList.remove('hidden');
            
           
            if (success) {
                setTimeout(() => {
                    closeSweetAlert();
                }, 3000);
            }
        }

        function closeSweetAlert() {
            const alertElement = document.getElementById('sweetAlert');
            alertElement.classList.add('hidden');
        }

 
    </script>
</body>
</html>
