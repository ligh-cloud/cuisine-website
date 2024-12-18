<?php 
require_once "db_connection.php"; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_date = $_POST['reservation_date'];
    $nb_client = $_POST['nb_client'];
    $login_id = $_SESSION['user_id'];
    $current_time = new DateTime();
    $reservation_datetime = new DateTime($reservation_date);

    if($reservation_datetime < $current_time){
        die("You cannot enter an old date.");
    }



    $query = "INSERT INTO reservation (id_client, date_reservation, nombre_place) VALUES (?, ?, ?)";
    $stm = $conn->prepare($query);
    $stm->bind_param("isi", $login_id, $reservation_date, $nb_client);
    $stm->execute();
    

    if ($stm->affected_rows == 1) {
        echo "Reservation made successfully!";
        echo "<meta http-equiv='refresh' content='2;url=user_dashboard.php'>"
        ;
    } else {
        echo "Error making reservation." ;
    }
} else {
    echo "Invalid request.";
}
?>
