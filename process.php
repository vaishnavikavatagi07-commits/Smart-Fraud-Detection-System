<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account_no = $conn->real_escape_string($_POST['account_no']);
    $amount = $_POST['amount'];
    $location = $conn->real_escape_string($_POST['location']);

    $user_res = $conn->query("SELECT id FROM users WHERE account_no = '$account_no'");
    
    if ($user_res->num_rows > 0) {
        $user = $user_res->fetch_assoc();
        $user_id = $user['id'];
        $is_fraud = 0;
        $reason = "";

        // RULE 1: Location Check
        $last_res = $conn->query("SELECT location FROM transactions WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
        if ($last_res->num_rows > 0) {
            $last_trans = $last_res->fetch_assoc();
            if ($last_trans['location'] !== $location) {
                $is_fraud = 1;
                $reason = "Location mismatch!";
            }
        }

        // RULE 2: High Amount Check (New Rule)
        if ($amount > 20000) {
            $is_fraud = 1;
            $reason = "Amount exceeds ₹20,000 limit!";
        }

        $stmt = "INSERT INTO transactions (user_id, amount, location, is_fraud) VALUES ('$user_id', '$amount', '$location', '$is_fraud')";
        
        if ($conn->query($stmt)) {
            if ($is_fraud == 1) {
                echo "<div style='color:white; background:red; padding:20px;'>⚠️ FRAUD DETECTED: $reason</div>";
            } else {
                echo "<div style='color:white; background:green; padding:20px;'>✅ Transaction Verified & Successful.</div>";
            }
            echo "<br><a href='admin.php'>View Admin Dashboard</a>";
        }
    } else {
        echo "Account not found.";
    }
}
?>