<?php
require_once "db_connection.php";
session_start();


if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

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
       
        $query = "INSERT INTO reservation (status, id_client) VALUES (?, ?)";
    }

    $stm = $conn->prepare($query);
    $stm->bind_param("si", $message, $id_client);
    
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
                                        switch($list["status"]) {
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
</body>

</html>