<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize inputs
    $account_no = $conn->real_escape_string($_POST['account_no']);
    $amount = (float)$_POST['amount'];
    $location = $conn->real_escape_string($_POST['location']);

    // 1. Get User ID from Account Number
    $user_res = $conn->query("SELECT id FROM users WHERE account_no = '$account_no'");
    
    if ($user_res->num_rows > 0) {
        $user = $user_res->fetch_assoc();
        $user_id = $user['id'];
        
        // Initialize Fraud Logic Variables
        $is_fraud = 0;
        $reason = "Transaction Verified"; // Default status

        // 2. SMART LOGIC: Check the last known location
        $last_res = $conn->query("SELECT location FROM transactions WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
        
        if ($last_res->num_rows > 0) {
            $last_trans = $last_res->fetch_assoc();
            
            // Rule A: Location mismatch check
            if ($last_trans['location'] !== $location) {
                $is_fraud = 1;
                $reason = "Location Mismatch (Previous: " . $last_trans['location'] . ")";
            }
        }

        // Rule B: High Amount Threshold Check (Flag if > 20,000)
        if ($amount > 20000) {
            if ($is_fraud == 1) {
                $reason .= " & High Amount Threshold Exceeded";
            } else {
                $is_fraud = 1;
                $reason = "High Amount Threshold Exceeded (>₹20,000)";
            }
        }

        // 3. Save the transaction with the detected Reason
        // Note: Ensure you added 'fraud_reason' to your database table first!
        $stmt = "INSERT INTO transactions (user_id, amount, location, is_fraud, fraud_reason) 
                 VALUES ('$user_id', '$amount', '$location', '$is_fraud', '$reason')";
        
        if ($conn->query($stmt)) {
            // UI for the Transaction Result (Matches your screenshots)
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <style>
                    body { font-family: Arial, sans-serif; background: #f4f7fc; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                    .result-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; width: 450px; }
                    .icon { font-size: 50px; margin-bottom: 20px; }
                    .msg { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
                    .reason { color: #666; margin-bottom: 30px; font-size: 16px; }
                    .btn-group a { text-decoration: none; font-weight: bold; color: #2563eb; margin: 0 15px; }
                    .fraud-text { color: #dc2626; }
                    .success-text { color: #059669; }
                </style>
            </head>
            <body>
                <div class="result-card">
                    <?php if ($is_fraud == 1): ?>
                        <div class="icon">⚠️</div>
                        <div class="msg fraud-text">Transaction Flagged</div>
                    <?php else: ?>
                        <div class="icon">✅</div>
                        <div class="msg success-text">Transaction Secure</div>
                    <?php endif; ?>
                    
                    <div class="reason"><strong>Reason:</strong> <?php echo $reason; ?></div>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                    
                    <div class="btn-group">
                        <a href="index.php">← Back</a> | <a href="admin.php">Go to Dashboard →</a>
                    </div>
                </div>
            </body>
            </html>
            <?php
        }
    } else {
        echo "<div style='text-align:center; margin-top:50px;'><h3>Account not found.</h3><a href='index.php'>Try Again</a></div>";
    }
}
?>