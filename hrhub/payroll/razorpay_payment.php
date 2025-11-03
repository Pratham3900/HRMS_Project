<?php
require("src/Razorpay.php");
use Razorpay\Api\Api;

// Razorpay API Credentials
$api_key = "rzp_test_3FUccOHkA69Y6O";
$api_secret = "9r8mHneAzJXt2q8E3MvI30pI";

// Check if required fields are set
if (!isset($_POST['salary']) || !isset($_POST['employeeId'])) {
    die(json_encode(["error" => "Missing salary or employeeId"]));
}

// Retrieve data
$salary = $_POST['salary'];
$employeeId = $_POST['employeeId'];

try {
    $api = new Api($api_key, $api_secret);
    $order = $api->order->create([
        'receipt' => "pay_$employeeId",
        'amount' => $salary, // Amount should be in paisa (already multiplied by 100 in frontend)
        'currency' => 'INR'
    ]);

    $order_id = $order['id'];
    $amount = $order['amount'];

    echo json_encode(["order_id" => $order_id, "amount" => $amount]);
} catch (Exception $e) {
    die(json_encode(["error" => $e->getMessage()]));
}
?>
