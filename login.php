<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="./assets/styles/loginstyles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;700&display=swap">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

</head>
<body>
    
    <div class="login-container">
        <h2>Login</h2>
        <?php
            session_start();
            include('./config/config.php');

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST['username'];
                $enteredPassword = $_POST['password'];

                $sql = "SELECT * FROM users WHERE username = '$username'";
                $result = $conn->query($sql);

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $hashedPassword = $row['password'];

                    if (password_verify($enteredPassword, $hashedPassword)) {
                        $_SESSION["username"] = $username;
                        $_SESSION['user_id'] = $row['user_id'];
                        $_SESSION['role'] = $row['role'];
                        $_SESSION["userfname"] = $row['fname'];
                        $_SESSION["userlname"] = $row['lname'];

                        // Redirect to admin.php if the user is an admin
                        if ($row['role'] === 'admin') {

                            $userID = $row['user_id'];
                            $name = $row['fname'] . ' ' . $row['lname'];
                            $time = gmdate("Y-m-d H:i:s"); // Current timestamp
                            $ipAddress = $_SERVER['REMOTE_ADDR']; // User's current IP address
                            $type = 'Login';

                            // Prepare SQL statement
                            $sql = "INSERT INTO user_login_log (userID, name, time, type, IPAddress) VALUES (?, ?, ?, ?, ?)";
                            $stmt = $conn->prepare($sql);

                            // Bind parameters
                            $stmt->bind_param("issss", $userID, $name, $time, $type, $ipAddress);

                            // Execute statement and handle errors
                            if ($stmt->execute()) {
                                // Close statement and go to admin page
                                $stmt->close();
                                header("Location: ./admin/admin.php");
                                exit();
                            } else {
                                echo '<script>
                                       Swal.fire({
                                           icon: "error",
                                           title: "Error Logging.",
                                           text: "Please retry."
                                         });
                                    </script>';
                            }

                        } else {
                            echo '<script>
                                Swal.fire({
                                    icon: "error",
                                    title: "No Admin Privileges",
                                    text: "Please retry."
                                  });
                             </script>';
                        }
                    } else {
                        echo '<script>
                                Swal.fire({
                                    icon: "error",
                                    title: "Incorrect Username or Password",
                                    text: "Please retry."
                                  });
                             </script>';
                    }
                } else {
                    echo '<script>
                            Swal.fire({
                                icon: "error",
                                title: "User Not Found",
                                text: "Incorrect Username or Password"
                              });
                         </script>';
                }
            }
        ?>
                <form action="#" method="post" class="mb-2">
                    <label for="username">Username:</label>
                    <input type="text" name="username" class="form-control" required>

                    <label for="password">Password:</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <span class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
            
                    <button class="px-3 my-2" type="submit">Login</button>
                </form>
                <a href="javascript:history.go(-1);" class="btn btn-danger">Cancel</a>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var toggleButton = document.querySelector(".toggle-password");
        
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = "password";
                toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }
        
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
