<?php
// admin.php

// Retrieve the DATABASE_URL environment variable set by Heroku
$dbUrl = getenv("DATABASE_URL");
if (!$dbUrl) {
    die("DATABASE_URL not set. Please ensure your Heroku Postgres add-on is configured.");
}
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
    $stmt = $pdo->prepare("INSERT INTO submissions (student_name, class_period, author) VALUES (:student_name, :class_period, :author)");
    $stmt->bindParam(':student_name', $studentName);
    $stmt->bindParam(':class_period', $classPeriod);
    $stmt->bindParam(':author', $selectedAuthor['name']);
    $stmt->execute();
} catch (PDOException $e) {
    die("Database insert failed: " . $e->getMessage());
}

// Query to retrieve all submissions, ordered by the most recent
$stmt = $pdo->query("SELECT * FROM submissions ORDER BY submission_time DESC");
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - View Submissions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            padding: 20px;
            color: #333;
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
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submissions Admin Page</h1>
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
    </div>
</body>
</html>
