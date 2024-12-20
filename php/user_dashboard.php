
<?php 
require_once "db_connection.php"; 
session_start(); 

if (!isset($_SESSION['user_id'])) { 
    header('Location: index.php'); 
    exit; 
}

if(isset($_POST['log_out'])){
    session_unset(); 
    session_destroy(); 
    echo "<div class='text-center text-green-600 font-bold p-4'>You have been successfully logged out. Redirecting in 2 seconds...</div>"; 
    echo "<meta http-equiv='refresh' content='2; url=index.php'>" ; 
    exit;
} 

$login_id = $_SESSION['user_id']; 
$row_user = null; 

$query = "SELECT id_client, name, email FROM user WHERE id_client = ?"; 
$stm = $conn->prepare($query); 
$stm->bind_param("i", $login_id); 
$stm->execute(); 
$result = $stm->get_result(); 
if ($result) { 
    $row_user = $result->fetch_assoc(); 
} else { 
    error_log("Query execution failed: " . $stm->error); 
}

$query = "SELECT m.id_menu, m.menu_name, d.dish_name, d.ingrediant, d.image_url 
FROM menu m 
LEFT JOIN dishes d ON m.id_menu = d.id_menu"; 
$menu_stm = $conn->prepare($query); 
$menu_stm->execute(); 
$result = $menu_stm->get_result(); 
$menus = []; 
while ($row = $result->fetch_assoc()) { 
    $menu_id = $row['id_menu']; 
    if (!isset($menus[$menu_id])) { 
        $menus[$menu_id] = [ 
            'menu_name' => $row['menu_name'], 
            'dishes' => [] 
        ]; 
    } 
    if ($row['dish_name']) { 
        $menus[$menu_id]['dishes'][] = [ 
            'name' => $row['dish_name'], 
            'ingrediant' => $row['ingrediant'], 
            'image_url' => $row['image_url'] 
        ]; 
    } 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservation_date'])) { 
    try { 
        $reservation_date = $_POST['reservation_date']; 
        $nb_client = (int)$_POST['nb_client']; 
        $menu_id = (int)$_POST['menu_id']; 
        $current_time = new DateTime(); 
        $reservation_datetime = new DateTime($reservation_date); 
        if($reservation_datetime < $current_time) { 
            throw new Exception("You cannot enter an old date."); 
        } 
        $conn->begin_transaction(); 
        $query = "INSERT INTO reservation (date_reservation, id_client, nombre_place, status, id_menu) 
        VALUES (?, ?, ?, 'en attente', ?)"; 
        $stm = $conn->prepare($query); 
        if (!$stm) { 
            throw new Exception("Prepare failed: " . $conn->error); 
        } 
        $bind = $stm->bind_param("siii", $reservation_date, $login_id, $nb_client, $menu_id); 
        if (!$bind) { 
            throw new Exception("Binding parameters failed: " . $stm->error); 
        } 
        $execute = $stm->execute(); 
        if (!$execute) { 
            throw new Exception("Execute failed: " . $stm->error); 
        } 
        if ($stm->affected_rows == 1) { 
            $conn->commit(); 
            echo '<div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"> Reservation made successfully! </div>'; 
        } else { 
            throw new Exception("No rows were inserted"); 
        } 
    } catch (Exception $e) { 
        if (isset($conn)) { 
            $conn->rollback(); 
        } 
        echo '<div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"> Error making reservation: ' . htmlspecialchars($e->getMessage()) . ' </div>'; 
        error_log("Reservation Error: " . $e->getMessage()); 
    } 
}

$query = "SELECT r.reservation_id, r.date_reservation, r.nombre_place, r.status, m.menu_name 
FROM reservation r 
JOIN menu m ON r.id_menu = m.id_menu 
WHERE r.id_client = ?"; 
$stm = $conn->prepare($query); 
$stm->bind_param("i", $login_id); 
$stm->execute(); 
$result = $stm->get_result(); 
$reservations = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>User Dashboard</title> 
       <script src="https://cdn.tailwindcss.com"></script>
</head> 
<body class="bg-gray-100 min-h-screen p-4"> 
    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6 mb-6"> 
            <div class="text-center"> 
                <h1 class="text-3xl font-bold text-gray-800 mb-2">User Dashboard</h1> 
                <p class="text-gray-600">Welcome, <?php echo htmlspecialchars($row_user['name']); ?>!</p> 
                <p class="text-sm text-gray-500">Email: <?php echo htmlspecialchars($row_user['email']); ?></p> 
            </div> 
        </div> 

        <!-- Display User Reservations -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6"> 
            <h2 class="text-2xl font-bold mb-6">Your Reservations</h2> 
            <?php if ($reservations): ?> 
                <table class="min-w-full divide-y divide-gray-200"> 
                    <thead class="bg-green-500 text-white"> 
                        <tr> 
                            <th class="px-4 py-2 text-left">Reservation ID</th> 
                            <th>Date</th> 
                            <th>Guests</th> 
                            <th>Status</th> 
                            <th>Menu</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <?php foreach ($reservations as $reservation): ?> 
                            <tr class="bg-white odd:bg-gray-50 hover:bg-gray-100"> 
                                <td class="px-4 py-2"><?php echo $reservation['reservation_id']; ?></td> 
                                <td class="px-4 py-2"><?php echo date('d/m/Y', strtotime($reservation['date_reservation'])); ?></td> 
                                <td class="px-4 py-2"><?php echo $reservation['nombre_place']; ?></td> 
                                <td class="px-4 py-2"><?php echo $reservation['status']; ?></td> 
                                <td class="px-4 py-2"><?php echo $reservation['menu_name']; ?></td> 
                            </tr> 
                        <?php endforeach; ?> 
                    </tbody> 
                </table> 
            <?php else: ?> 
                <p class="text-gray-500">No reservations found.</p> 
            <?php endif; ?> 
        </div> 

        
        <div class="bg-white shadow-md rounded-lg p-6"> 
            <h2 class="text-2xl font-bold mb-6">Make a Reservation</h2> 
            <form method="POST" class="space-y-6"> 
                
                
                <a href="new_reservation.php" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Make Another Reservation</a>
            </form> 
        </div> 

        <form method="POST"> 
            <button type="submit" name="log_out" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Log Out</button> 
        </form> 
    </div> 
</body> 
</html>

