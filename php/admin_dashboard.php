<?php
require_once "db_connection.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];


$pending_query = "SELECT COUNT(*) as count FROM reservation 
                 WHERE status = 'en attente'";
$pending_result = $conn->query($pending_query);
$pending_count = $pending_result->fetch_assoc()['count'];

$today_query = "SELECT COUNT(*) as count FROM reservation 
                WHERE status = 'confirmer' 
                AND DATE(date_reservation) = CURDATE()";
$today_result = $conn->query($today_query);
$today_approved = $today_result->fetch_assoc()['count'];

$tomorrow_query = "SELECT COUNT(*) as count FROM reservation 
                  WHERE status = 'confirmer' 
                  AND DATE(date_reservation) = CURDATE() + INTERVAL 1 DAY";
$tomorrow_result = $conn->query($tomorrow_query);
$tomorrow_approved = $tomorrow_result->fetch_assoc()['count'];

$clients_query = "SELECT COUNT(*) as count FROM user";
$clients_result = $conn->query($clients_query);
$total_clients = $clients_result->fetch_assoc()['count'];

$next_reservation_query = "SELECT r.*, u.name as client_name, m.menu_name
                         FROM reservation r 
                         JOIN user u ON r.id_client = u.id_client 
                         LEFT JOIN menu m ON r.id_menu = m.id_menu
                         WHERE r.status = 'confirmer' 
                         AND r.date_reservation >= CURDATE() 
                         ORDER BY r.date_reservation ASC
                         LIMIT 1";
$next_reservation_result = $conn->query($next_reservation_query);
$next_reservation = $next_reservation_result->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['menu_name'])) {
    $menu_name = htmlspecialchars(trim($_POST['menu_name']));

    if (!empty($menu_name)) {
        $query = "INSERT INTO menu (menu_name, id_client) VALUES (?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("si", $menu_name, $admin_id);

            if ($stmt->execute()) {
                $menu_id = $stmt->insert_id;
                echo "Menu created successfully! Menu ID: " . $menu_id;
            } else {
                echo "Error creating menu: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Menu name cannot be empty";
    }
}

if(isset($_POST["log_out"])) {
    session_unset();
    session_destroy();
    header("Location: index.php");  
    exit(); 
}




$client_query = "SELECT u.*, COALESCE(r.status, 'en attente') as status 
                 FROM user u 
                 LEFT JOIN reservation r ON u.id_client = r.id_client";
$client_stm = $conn->prepare($client_query);
$client_stm->execute();
$client_list = $client_stm->get_result();


$menu_query = "SELECT id_menu, menu_name FROM menu WHERE id_client = ?";
$menu_stmt = $conn->prepare($menu_query);
$menu_stmt->bind_param("i", $admin_id);
$menu_stmt->execute();
$menu_result = $menu_stmt->get_result();


if (isset($_POST["status_change"])) {
    $status = $_POST["status"];
    if ($status == "confirmer_status") {
        $message = "confirmer";
    } else if ($status == "refuser") {
        $message = "annuler";
    } else {
        $message = "en attente";
    }
    $id_client = $_POST["id_client"];

    $check_query = "SELECT id_client FROM reservation WHERE id_client = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $id_client);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $query = "UPDATE reservation SET status = ? WHERE id_client = ?";
    } else {
        $query = "INSERT INTO reservation (status, id_client, date_reservation) VALUES (?, ?, CURDATE())";
    }

    $stm = $conn->prepare($query);
    
    if ($check_result->num_rows > 0) {
        $stm->bind_param("si", $message, $id_client);
    } else {
        $stm->bind_param("si", $message, $id_client);
    }

    if ($stm->execute()) {
        echo "<script>alert('Status updated successfully.');</script>";
        echo "<script>window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Error updating status: " . $stm->error . "');</script>";
    }
    
    $stm->close();
    $check_stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-4xl bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h1 class="text-2xl font-bold text-center">Restaurant Management System</h1>
        
<div class="w-full max-w-4xl mx-auto mb-8 space-y-6">
    <h2 class="text-xl font-semibold mb-4">Dashboard Statistics</h2>
    
  
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
     
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Demandes en Attente</p>
                    <h3 class="text-xl font-bold text-gray-900"><?php echo $pending_count; ?></h3>
                </div>
            </div>
        </div>

      
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Approuvées Aujourd'hui</p>
                    <h3 class="text-xl font-bold text-gray-900"><?php echo $today_approved; ?></h3>
                </div>
            </div>
        </div>

     
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Approuvées Demain</p>
                    <h3 class="text-xl font-bold text-gray-900"><?php echo $tomorrow_approved; ?></h3>
                </div>
            </div>
        </div>

     
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Clients Inscrits</p>
                    <h3 class="text-xl font-bold text-gray-900"><?php echo $total_clients; ?></h3>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold mb-4">Prochaine Réservation</h3>
        <?php if ($next_reservation): ?>
        <div class="space-y-2">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-medium"><?php echo htmlspecialchars($next_reservation['client_name']); ?></span>
            </div>
            <div class="text-sm text-gray-600">
                <p>Date: <?php echo date('d/m/Y', strtotime($next_reservation['date_reservation'])); ?></p>
                <p>Menu: <?php echo htmlspecialchars($next_reservation['menu_name'] ?? 'Non spécifié'); ?></p>
                <p>Nombre de places: <?php echo htmlspecialchars($next_reservation['nombre_place']); ?></p>
            </div>
        </div>
        <?php else: ?>
        <p class="text-gray-500">Aucune réservation à venir</p>
        <?php endif; ?>
    </div>
</div>


        <div class="max-w-md mx-auto">
            <h2 class="text-xl font-semibold mb-3">Create New Menu</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="menu_name" class="block text-sm font-medium text-green-700 mb-1">Menu Name:</label>
                    <input
                        type="text"
                        name="menu_name"
                        id="menu_name"
                        required
                        class="block w-full px-3 py-2 border border-green-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>
                <button
                    type="submit"
                    class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Create Menu
                </button>
            </form>
        </div>


        <div>
            <h2 class="text-xl font-semibold mb-3">Existing Menus</h2>
            <?php if ($menu_result->num_rows > 0): ?>
                <ul class="space-y-2">
                    <?php while ($menu = $menu_result->fetch_assoc()): ?>
                        <li class="bg-gray-100 p-2 rounded">
                            <a href="add_dish.php?menu_id=<?php echo $menu['id_menu']; ?>"
                                class="text-green-600 hover:underline">
                                <?php echo htmlspecialchars($menu['menu_name']); ?>
                                (ID: <?php echo $menu['id_menu']; ?>)
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500">No menus created yet.</p>
            <?php endif; ?>
        </div>


        <div>
            <h2 class="text-xl font-semibold mb-3">Client List</h2>
            <?php if ($client_list->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-green-500 text-white">
                                <th class="px-4 py-2 text-left">Client Name</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Current Status</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($list = $client_list->fetch_assoc()): ?>
                                <tr class="bg-white odd:bg-gray-50 hover:bg-gray-100">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($list["name"]); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($list["email"]); ?></td>
                                    <td class="px-4 py-2">
                                        <?php
                                        $status_text = "";
                                        switch ($list["status"]) {
                                            case "confirmer":
                                                $status_text = "Confirmé";
                                                break;
                                            case "annuler":
                                                $status_text = "Refusé";
                                                break;
                                            default:
                                                $status_text = "En Attente";
                                        }
                                        echo $status_text;
                                        ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <form method="POST" class="flex gap-2">
                                            <input type="hidden" name="id_client" value="<?php echo htmlspecialchars($list["id_client"]); ?>">
                                            <select name="status" class="border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                                                <option value="en_attente" <?php echo ($list["status"] == "en attente") ? "selected" : ""; ?>>En Attente</option>
                                                <option value="refuser" <?php echo ($list["status"] == "annuler") ? "selected" : ""; ?>>Refuser</option>
                                                <option value="confirmer_status" <?php echo ($list["status"] == "confirmer") ? "selected" : ""; ?>>Confirmer</option>
                                            </select>
                                            <button type="submit" name="status_change" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500">No clients in the list.</p>
            <?php endif; ?>
        </div>
    </div>
    <form method="POST">
    <button name="log_out" class="bg-red-600 hover:bg-red-200 w-[20vw]">
        log out
    </button>
    
    </form>
</body>

</html>