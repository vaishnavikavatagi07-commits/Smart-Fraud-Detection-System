<?php
include 'db.php'; // Connects to your database

// SQL to fetch transactions joined with user names
$sql = "SELECT transactions.*, users.name 
        FROM transactions 
        JOIN users ON transactions.user_id = users.id 
        ORDER BY transactions.trans_time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bank Admin Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #f4f7fc; padding: 20px; }
        .box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #1e3a8a; color: white; }
        /* Fraud rows will turn red */
        .fraud { background: #ffdada; color: #cc0000; font-weight: bold; }
        .safe { background: #e7f9ed; }
    </style>
</head>
<body>
    <div class="box">
        <h2>🏦 Bank Admin Monitoring</h2>
        <a href="transection.html">← Back to Transaction Form</a>
        <table>
            <tr>
                <th>Customer</th>
                <th>Amount</th>
                <th>Location</th>
                <th>Status</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr class="<?php echo $row['is_fraud'] ? 'fraud' : 'safe'; ?>">
                    <td><?php echo $row['name']; ?></td>
                    <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td><?php echo $row['is_fraud'] ? '🚩 FRAUD FLAG' : '✅ Verified'; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>