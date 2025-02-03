<?php
// admin.php

// Retrieve the DATABASE_URL from Heroku config vars
$dbUrl = getenv("DATABASE_URL");
if (!$dbUrl) {
    die("DATABASE_URL is not set. Please ensure your Heroku Postgres add-on is configured.");
}

// Parse the connection details from DATABASE_URL.
$dbopts = parse_url($dbUrl);
$host = $dbopts["host"];
$port = $dbopts["port"];
$user = $dbopts["user"];
$pass = $dbopts["pass"];
$dbname = ltrim($dbopts["path"], '/');

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

try {
    // Retrieve all submissions, ordered by submission_time descending
    $stmt = $pdo->query("SELECT * FROM submissions ORDER BY submission_time DESC");
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Submissions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #0077b6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background: #0077b6;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #e8f0fe;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submissions Admin Page</h1>
        <p>Number of submissions found: <?php echo count($submissions); ?></p>
        <?php if (count($submissions) > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Class Period</th>
                    <th>Author</th>
                    <th>Submission Time</th>
                </tr>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($submission['id']); ?></td>
                        <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['class_period']); ?></td>
                        <td><?php echo htmlspecialchars($submission['author']); ?></td>
                        <td><?php echo htmlspecialchars($submission['submission_time']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No submissions found.</p>
        <?php endif; ?>
        <!-- Optional: Debug output (uncomment to display raw array data)
        <pre><?php //print_r($submissions); ?></pre>
        -->
    </div>
</body>
</html>
