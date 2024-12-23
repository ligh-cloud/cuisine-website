

<?php
require_once "db_connection.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$login_id = $_SESSION['user_id'];

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
        if ($reservation_datetime < $current_time) {
            throw new Exception("You cannot enter an old date.");
        }
        $conn->begin_transaction();
        $query = "INSERT INTO reservation (date_reservation, id_client, nombre_place, status, id_menu) 
        VALUES (?, ?, ?, 'en attente', ?)";
        $stm = $conn->prepare($query);
        $stm->bind_param("siii", $reservation_date, $login_id, $nb_client, $menu_id);
        $execute = $stm->execute();
        if ($stm->affected_rows == 1) {
            $conn->commit();
            echo '<div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"> Reservation made successfully! </div>';
            header('Location: user_dashboard.php?success=1');
            exit;
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

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Reservation</title>
       <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen p-4">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Make a New Reservation</h2>
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="reservation_date" class="block text-sm font-medium text-gray-700">Reservation Date and Time</label>
                    <input type="datetime-local" name="reservation_date" id="reservation_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label for="nb_client" class="block text-sm font-medium text-gray-700">Number of Guests</label>
                    <input type="number" name="nb_client" id="nb_client" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>

            <div class="space-y-8">
                <?php foreach ($menus as $menu_id => $menu): ?>
                <div class="bg-gray-50 rounded-lg p-6 shadow-md">
                    <div class="flex items-center mb-4">
                        <input type="radio" name="menu_id" value="<?php echo $menu_id; ?>" id="menu_<?php echo $menu_id; ?>" required class="h-4 w-4 text-green-600 focus:ring-green-500">
                        <label for="menu_<?php echo $menu_id; ?>" class="ml-3 block text-lg font-medium text-gray-700"><?php echo htmlspecialchars($menu['menu_name']); ?></label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($menu['dishes'] as $dish): ?>
                        <div class="bg-white rounded-lg overflow-hidden shadow-md">
                            <div class="h-48 overflow-hidden">
                                <?php if ($dish['image_url']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($dish['image_url']); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No image</span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($dish['name']); ?></h3>
                                <p class="mt-1 text-sm text-gray-500"><?php echo htmlspecialchars($dish['ingrediant']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-between">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Make Reservation</button>
                <a href="user_dashboard.php" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
