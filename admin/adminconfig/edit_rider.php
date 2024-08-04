<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


if (!isset($_POST['riderId'])){
    echo 'error';
    exit();
} else {
    $riderId = $_POST['riderId'];
}
$first_name = isset($_POST['first_name']) ? sanitize_input($_POST['first_name']) : null;
$last_name = isset($_POST['last_name']) ? sanitize_input($_POST['last_name']) : null;
$middle_name = isset($_POST['middle_name']) ? sanitize_input($_POST['middle_name']) : null;
$date_of_birth = isset($_POST['date_of_birth']) ? sanitize_input($_POST['date_of_birth']) : null;
$address = isset($_POST['address']) ? sanitize_input($_POST['address']) : null;
$contact_number = isset($_POST['contact_number']) ? sanitize_input($_POST['contact_number']) : null;
$email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : null;
$license_number = isset($_POST['license_number']) ? sanitize_input($_POST['license_number']) : null;
$license_expiry_date = isset($_POST['license_expiry_date']) ? sanitize_input($_POST['license_expiry_date']) : null;
$vehicle_type = isset($_POST['vehicle_type']) ? sanitize_input($_POST['vehicle_type']) : null;
$vehicle_plate_number = isset($_POST['vehicle_plate_number']) ? sanitize_input($_POST['vehicle_plate_number']) : null;
$status = isset($_POST['status']) ? sanitize_input($_POST['status']) : null;
$date_hired = isset($_POST['date_hired']) ? sanitize_input($_POST['date_hired']) : null;
$bank_account_number = isset($_POST['bank_account_number']) ? sanitize_input($_POST['bank_account_number']) : null;
$bank_name = isset($_POST['bank_name']) ? sanitize_input($_POST['bank_name']) : null;
$emergency_contact_name = isset($_POST['emergency_contact_name']) ? sanitize_input($_POST['emergency_contact_name']) : null;
$emergency_contact_number = isset($_POST['emergency_contact_number']) ? sanitize_input($_POST['emergency_contact_number']) : null;
$notes = isset($_POST['notes']) ? sanitize_input($_POST['notes']) : null;

$cstmt = $conn->prepare("SELECT * FROM delivery_riders WHERE contact_number = ? AND id != ?");
$cstmt->bind_param("ss", $contact_number, $riderId);
$cstmt->execute();
$result = $cstmt->get_result();
if ($result->fetch_assoc() > 0){
    echo 'contact_number_exists';
    $conn->close();
    $cstmt->close();
    exit();
}

$estmt = $conn->prepare("SELECT * FROM delivery_riders WHERE email = ? AND id != ?");
$estmt->bind_param("ss", $email, $riderId);
$estmt->execute();
$result = $estmt->get_result();
if ($result->fetch_assoc() > 0){
    echo 'email_exists';
    $conn->close();
    $estmt->close();
    exit();
}

$lstmt = $conn->prepare("SELECT * FROM delivery_riders WHERE license_number = ? AND id != ?");
$lstmt->bind_param("ss", $license_number, $riderId);
$lstmt->execute();
$result = $lstmt->get_result();
if ($result->fetch_assoc() > 0){
    echo 'license_exists';
    $conn->close();
    $lstmt->close();
    exit();
}

$stmt = $conn->prepare("UPDATE delivery_riders
                        SET first_name = ?,
                            last_name = ?,
                            middle_name = ?,
                            date_of_birth = ?,
                            address = ?,
                            contact_number = ?,
                            email = ?,
                            license_number = ?,
                            license_expiry_date = ?,
                            vehicle_type = ?,
                            vehicle_plate_number = ?,
                            status = ?,
                            date_hired = ?,
                            bank_account_number = ?,
                            bank_name = ?,
                            emergency_contact_name = ?,
                            emergency_contact_number = ?,
                            notes = ?
                        WHERE id = ?");


$stmt->bind_param("sssssssssssssssssss",
    $first_name,
    $last_name,
    $middle_name,
    $date_of_birth,
    $address,
    $contact_number,
    $email,
    $license_number,
    $license_expiry_date,
    $vehicle_type,
    $vehicle_plate_number,
    $status,
    $date_hired,
    $bank_account_number,
    $bank_name,
    $emergency_contact_name,
    $emergency_contact_number,
    $notes,
    $riderId
);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'error';
}

$conn->close();
$stmt->close();
exit();