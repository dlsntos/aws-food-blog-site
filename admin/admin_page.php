<?php
session_start();
include __DIR__ . '/../includes/check_login.php';

if (!isset($_SESSION["user_id"]) || !$_SESSION["is_admin"]) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "sampleDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total users
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()["count"];
$total_posts = $conn->query("SELECT COUNT(*) AS count FROM posts")->fetch_assoc()["count"];
$total_logins = $conn->query("SELECT COUNT(*) AS count FROM audit_logs WHERE action = 'Login'")->fetch_assoc()["count"];

// Get recent activity logs
$logs = $conn->query("SELECT username, action, timestamp FROM audit_logs ORDER BY timestamp DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f0f2f5;
            padding: 20px;
        }

        .container {
            max-width: 960px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2,
        h3 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .stat-box {
            background: linear-gradient(135deg, #007BFF, #0056b3);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 30%;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-box h3 {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .stat-box p {
            font-size: 24px;
            font-weight: bold;
        }

        canvas {
            display: block;
            margin: 20px auto;
            max-width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007BFF;
            color: white;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .logout-btn {
            display: block;
            width: 140px;
            text-align: center;
            margin: 30px auto 0;
            padding: 12px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .stats {
                flex-direction: column;
                gap: 15px;
            }

            .stat-box {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Admin Dashboard</h2>

        <div class="stats">
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?= $total_users ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Posts</h3>
                <p><?= $total_posts ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Logins</h3>
                <p><?= $total_logins ?></p>
            </div>
        </div>

        <h3>User Login Trends</h3>
        <canvas id="loginChart"></canvas>

        <h3>Recent User Activity</h3>
        <table>
            <tr>
                <th>Username</th>
                <th>Action</th>
                <th>Timestamp</th>
            </tr>
            <?php while ($row = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['action']) ?></td>
                    <td><?= htmlspecialchars($row['timestamp']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <a href="../includes/logout.php" class="logout-btn">Logout</a>
    </div>

    <script>
        fetch('../includes/get_login_data.php') // Fixed path
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error("Error fetching data:", data.error);
                    return;
                }

                const ctx = document.getElementById('loginChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.dates,
                        datasets: [{
                            label: 'Logins per Day',
                            data: data.counts,
                            backgroundColor: 'rgba(0, 123, 255, 0.2)',
                            borderColor: '#007BFF',
                            borderWidth: 2
                        }]
                    }
                });
            })
            .catch(error => console.error("Fetch error:", error));
    </script>

</body>

</html>

<?php $conn->close(); ?>