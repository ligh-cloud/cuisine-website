<?php
require_once "db_connection.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$query = "SELECT id_client, name, email FROM user WHERE id_client = ?";
$stat = $conn->prepare($query);
$stat->bind_param("i", $admin_id);
$stat->execute();
$result = $stat->get_result();

if ($result->num_rows > 0) {
    $admin_data = $result->fetch_assoc();
} else {
    echo "Admin data not found.";
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dish_name = htmlspecialchars(trim($_POST["dish_name"]));
    $dish_ingrediant = htmlspecialchars(trim($_POST["dish_ingrediant"]));
    $dish_image = $_POST["image"];
    if(empty($dish_name) || empty($dish_ingrediant)){
        echo "fill the input ";
        exit();
    }
    else{
        $query = "INSERT INTO dishes (dish_name , ingrediant , image_url) VALUE(? , ? , ? )";
        $stm = $conn->prepare($query);
        if($stm){
            $stm->bind_param("ssb", $dish_name , $dish_ingrediant , $dish_image);
            $stm->execute();
        }
        else{
            echo "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 space-y-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Admin Dashboard</h1>
            <p class="text-gray-600">Welcome, <?php echo htmlspecialchars($admin_data['name']); ?>!</p>
            <p class="text-sm text-gray-500">Email: <?php echo htmlspecialchars($admin_data['email']); ?></p>
        </div>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="image" class="block text-sm font-medium text-green-700 mb-1">Choose Image:</label>
                <input 
                    type="file" 
                    name="image" 
                    id="image" 
                    accept="image/*" 
                    class="block w-full px-3 py-2 border border-green-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            </div>
            <div>
                <label for="dish_name" class="block text-sm font-medium text-green-700 mb-1">Dish Name:</label>
                <input 
                    type="text" 
                    name="dish_name" 
                    id="dish_name" 
                    class="block w-full px-3 py-2 border border-green-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            </div>
            <div>
                <label for="dish_ingrediant" class="block text-sm font-medium text-green-700 mb-1">Dish Ingredients:</label>
                <input 
                    type="text" 
                    name="dish_ingrediant" 
                    id="dish_ingrediant" 
                    class="block w-full px-3 py-2 border border-green-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            </div>
            <div>
                <button 
                    type="submit" 
                    class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Upload
                </button>
            </div>
        </form>
    </div>
</body>
</html>
