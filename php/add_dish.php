<?php
require_once "db_connection.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['menu_id'])) {
    echo "No menu selected.";
    exit();
}
if (isset($_POST["go_back"])) {
    header("Location: admin_dashboard.php");  
    exit(); 
}

$menu_id = intval($_GET['menu_id']);
$admin_id = $_SESSION['admin_id'];

$menu_check_query = "SELECT menu_name FROM menu WHERE id_menu = ? AND id_client = ?";
$menu_check_stmt = $conn->prepare($menu_check_query);
$menu_check_stmt->bind_param("ii", $menu_id, $admin_id);
$menu_check_stmt->execute();
$menu_check_result = $menu_check_stmt->get_result();

if ($menu_check_result->num_rows == 0) {
    echo "Invalid menu or unauthorized access.";
    exit();
}

$menu_name = $menu_check_result->fetch_assoc()['menu_name'];

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dish_name = htmlspecialchars(trim($_POST["dish_name"]));
    $dish_ingrediant = htmlspecialchars(trim($_POST["dish_ingrediant"]));

    if (empty($dish_name) || empty($dish_ingrediant)) {
        echo "Please fill all fields.";
        exit();
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_data = file_get_contents($image_tmp_name);
        $image_type = mime_content_type($image_tmp_name);

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image_type, $allowed_types)) {
            echo "Invalid image type. Only JPEG, PNG, and GIF are allowed.";
            exit();
        }

        $query = "INSERT INTO dishes (id_menu, dish_name, ingrediant, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("isss", $menu_id, $dish_name, $dish_ingrediant, $image_data);

            if ($stmt->execute()) {
                $success_message = "Dish added successfully to menu: " . htmlspecialchars($menu_name);
            } else {
                echo "Error adding dish: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Please upload an image.";
    }
}

$dishes_query = "SELECT dish_name, ingrediant FROM dishes WHERE id_menu = ?";
$dishes_stmt = $conn->prepare($dishes_query);
$dishes_stmt->bind_param("i", $menu_id);
$dishes_stmt->execute();
$dishes_result = $dishes_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Dishes to <?php echo htmlspecialchars($menu_name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="relative w-full max-w-md bg-white shadow-lg rounded-lg p-6 space-y-6">
        <h1 class="text-2xl font-bold text-center">Add Dishes to <?php echo htmlspecialchars($menu_name); ?></h1>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="image" class="block text-sm font-medium text-green-700 mb-1">Choose Image:</label>
                <input
                    type="file"
                    name="image"
                    id="image"
                    accept="image/*"
                    required
                    class="block w-full px-3 py-2 border border-green-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            </div>
            <div>
                <label for="dish_name" class="block text-sm font-medium text-green-700 mb-1">Dish Name:</label>
                <input
                    type="text"
                    name="dish_name"
                    id="dish_name"
                    required
                    class="block w-full px-3 py-2 border border-green-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            </div>
            <div>
                <label for="dish_ingrediant" class="block text-sm font-medium text-green-700 mb-1">Dish Ingredients:</label>
                <input
                    type="text"
                    name="dish_ingrediant"
                    id="dish_ingrediant"
                    required
                    class="block w-full px-3 py-2 border border-green-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
            </div>
            <button
                type="submit"
                class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Add Dish
            </button>
        </form>

        <div>
        <button class="absolute left-0 top-0 text-sm text-gray-600  bg-green-500 py-2 px-4 rounded hover:bg-green-100 focus:outline-none" onclick="goBack()">Go Back</button>
            <h2 class="text-xl font-semibold mb-3">Existing Dishes in this Menu</h2>
            <div class="overflow-y-auto h-[20vh]">
                <?php if ($dishes_result->num_rows > 0): ?>
                    <ul class="space-y-2">
                        <?php while ($dish = $dishes_result->fetch_assoc()): ?>
                            <li class="bg-gray-100 p-2 rounded">
                                <strong><?php echo htmlspecialchars($dish['dish_name']); ?></strong>
                                <p class="text-sm text-gray-600">Ingredients: <?php echo htmlspecialchars($dish['ingrediant']); ?></p>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">No dishes in this menu yet.</p>
            </div>
        <?php endif; ?>
        
        </div>
    </div>
    <form method="POST" >
    <button name="go_back">
        go back
    </button>
    </form>

</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if (!empty($success_message)): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $success_message; ?>',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600'
            }
        });
    <?php endif; ?>

    function goBack() {
        window.location.href = "admin_dashboard.php";
    }
</script>
