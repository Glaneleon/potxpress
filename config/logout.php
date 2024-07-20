<?php
include('./config.php');

if (session_start()) {

    $userID = $_SESSION["user_id"];
    $name = $_SESSION["userfname"] . ' ' . $_SESSION["userlname"];
    $time = gmdate("Y-m-d H:i:s"); // Current timestamp
    $ipAddress = $_SERVER['REMOTE_ADDR']; // User's current IP address
    $type = 'Logout';

    // Prepare SQL statement
    $sql = "INSERT INTO user_login_log (userID, name, time, type, IPAddress) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("issss", $userID, $name, $time, $type, $ipAddress);

    // Execute statement and handle errors
    if ($stmt->execute()) {
        // Close statement and go to admin page
        $stmt->close();
        $_SESSION = array();
        session_destroy();
        header("Location: /");
        exit();
    } else {
        echo '<script>
               Swal.fire({
                   icon: "error",
                   title: "Error Logging out.",
                   text: "Please retry."
                 });
            </script>';
    }

} else {
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "You are not logged in.",
            text: "Wha-What is happening??"
          });
     </script>';
}

exit();
?>
