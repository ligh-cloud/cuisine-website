<?php 
require_once "db_connection.php"; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if(isset(($_POST['log_out']))){
    session_unset();
    session_destroy();
    
}
$login_id = $_SESSION['user_id'];

$query = "SELECT id_client, name, email FROM user WHERE id_client = ?";
$stm = $conn->prepare($query);
$stm->bind_param("i", $login_id);
$stm->execute();
$result = $stm->get_result();
$row_user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md bg-white shadow-md rounded-lg p-6 space-y-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">User Dashboard</h1>
            <p class="text-gray-600">Welcome, <?= $row_user['name']; ?>!</p>
            <p class="text-sm text-gray-500">Email: <?= $row_user['email']; ?></p>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Make a Reservation</h2>
            <form action="make_reservation.php" method="post" class="space-y-4">
                <div>
                    <label for="nb_client" class="block text-sm font-medium text-gray-700 mb-1">
                        Number of people
                    </label>
                    <input 
                        type="number" 
                        name="nb_client" 
                        id="nb_client"
                        min="1" 
                        max="10" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div>
                    <label for="reservation_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Reservation date
                    </label>
                    <input 
                        type="date" 
                        id="reservation_date" 
                        name="reservation_date" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Make Reservation
                </button>
                <button 
                    name="log_out"
                    class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    log out
                </button>
            </form>
        </div>
    </div>
</body>
</html>