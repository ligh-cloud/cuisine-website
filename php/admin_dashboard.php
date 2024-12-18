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


$menu_query = "SELECT id_menu, menu_name FROM menu WHERE id_client = ?";
$menu_stmt = $conn->prepare($menu_query);
$menu_stmt->bind_param("i", $admin_id);
$menu_stmt->execute();
$menu_result = $menu_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h1 class="text-2xl font-bold text-center">Create New Menu</h1>
        
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
    </div>
</body>
</html>