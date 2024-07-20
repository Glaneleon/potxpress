<?php
include '../../config/config.php';
$user_email = $_POST['user_email'];
$user_password = md5($_POST['user_password']);

// $user_email = 'zoilojun38@gmail.com';
// $user_password = md5('Pasado@123456');

//  $user_email = 'zoilojun38@gmail.com';

//  $sqlQuery = "SELECT user_id, firstname, lastname, email, passwords, status FROM uers_test WHERE email ='$user_email' AND passwords = '$user_password'";
  $sqlQuery = "SELECT user_id, user_id_no, firstname, lastname, age, birthdate, gender, username, email, phone_no, status FROM uers_test WHERE email ='$user_email' AND passwords = '$user_password' ";

$resultOfQuery = $conn->query($sqlQuery);


if($resultOfQuery->num_rows > 0){
    $userRecord = array();
      
    while($rowFound = $resultOfQuery->fetch_assoc()){
        $userRecord = $rowFound;
    }

        echo json_encode(array(
            "loginSuccess" => true,
            "userData" => $userRecord,
        ));
  
    // echo json_encode(array(
    //             "loginSuccess" => true,
    //             "userData" => $userRecord,
    //         ));

}
else{
    echo json_encode(array("loginSuccess" => false));
}
?>