<?php
include 'db.php'; // Includes your database connection

// SQL query to join transactions with user names
$sql = "SELECT transactions.*, users.name 
        FROM transactions 
        JOIN users ON transactions.user_id = users.id 
        ORDER BY transactions.trans_time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bank Admin - Fraud Monitor</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7fc; padding: 40px; }
        .dashboard-container { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8fafc; color: #64748b; text-transform: uppercase; font-size: 12px; }
        
        /* Highlight Fraud Rows */
        .fraud-alert { background-color: #fee2e2; color: #b91c1c; font-weight: bold; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; color: white; }
        .badge-red { background: #ef4444; }
        .badge-green { background: #10b981; }
        
        .nav-link { display: inline-block; margin-bottom: 20px; color: #2563eb; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <a href="index.php" class="nav-link">← Back to Transaction Form</a>
        <h2>🏦 Transaction Monitoring Dashboard</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Amount</th>
                    <th>Location</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="<?php echo $row['is_fraud'] ? 'fraud-alert' : ''; ?>">
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td><?php echo $row['fraud_reason'] ?? 'N/A'; ?></td>
                        <td>
                            <?php if($row['is_fraud']): ?>
                                <span class="status-badge badge-red">🚩 FRAUD</span>
                            <?php else: ?>
                                <span class="status-badge badge-green">✅ SAFE</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>