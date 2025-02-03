<?php
// --- Process Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    // Debug: Log the raw POST data
    error_log("POST Data: " . print_r($_POST, true));

    // Retrieve form values (ensure the field names match your HTML)
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $classPeriod = trim($_POST['classPeriod'] ?? '');
    $selectedAuthorId = trim($_POST['author'] ?? '');

    // Debug: Log each retrieved value
    error_log("firstName: '$firstName'");
    error_log("lastName: '$lastName'");
    error_log("classPeriod: '$classPeriod'");
    error_log("selectedAuthorId: '$selectedAuthorId'");

    if (empty($firstName) || empty($lastName) || empty($classPeriod) || empty($selectedAuthorId)) {
         die("Please fill in all required fields.");
    }

    // Build the full student name.
    $studentName = $firstName . " " . $lastName;
    error_log("studentName: '$studentName'");

    // Load the authors from JSON.
    $authorsFile = 'authors.json';
    if (!file_exists($authorsFile)) {
         die("Authors file not found.");
    }
    $authorsData = json_decode(file_get_contents($authorsFile), true);

    // Retrieve the selected author from the JSON array.
    $selectedAuthor = null;
    foreach ($authorsData as $index => $author) {
         if ($author['id'] === $selectedAuthorId) {
             $selectedAuthor = $author;
             // Debug: Log the retrieved author details
             error_log("Selected Author: " . print_r($selectedAuthor, true));
             // Remove the author so that no one else can choose them.
             unset($authorsData[$index]);
             break;
         }
    }

    if ($selectedAuthor === null) {
         error_log("No author found for ID: $selectedAuthorId");
         die("The selected author is no longer available. Please choose another.");
    }

    // Re-index the array so that JSON is written as a proper array.
    $authorsData = array_values($authorsData);
    // Update the JSON file.
    file_put_contents($authorsFile, json_encode($authorsData, JSON_PRETTY_PRINT));

    // --- Connect to the Database and Insert the Submission ---
    $dbUrl = getenv("DATABASE_URL");
    if (!$dbUrl) {
        die("DATABASE_URL not set. Please ensure your Heroku Postgres add-on is configured.");
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

    // Insert the submission into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO submissions (student_name, class_period, author) VALUES (:student_name, :class_period, :author)");
        $stmt->bindParam(':student_name', $studentName);
        $stmt->bindParam(':class_period', $classPeriod);
        $stmt->bindParam(':author', $selectedAuthor['name']);
        $stmt->execute();
        error_log("Submission inserted successfully for student '$studentName'");
    } catch (PDOException $e) {
        die("Database insert failed: " . $e->getMessage());
    }

    // Redirect to avoid form resubmission.
    header("Location: index.php?success=1");
    exit();
}

// --- Read Authors for the Form ---
$authorsFile = 'authors.json';
if (file_exists($authorsFile)) {
    $authors = json_decode(file_get_contents($authorsFile), true);
} else {
    $authors = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Author Signup for Research Projects</title>
  <!-- Google Font for a modern, playful look -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    /* Reset some default styles */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
       font-family: 'Poppins', sans-serif;
       background: linear-gradient(135deg, #ff9a9e, #fad0c4);
       padding: 20px;
       color: #333;
    }
    .container {
       max-width: 700px;
       margin: 0 auto;
       background: #ffffff;
       padding: 30px;
       border-radius: 12px;
       box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
       animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    h1 {
       text-align: center;
       color: #ff6f61;
       margin-bottom: 20px;
    }
    /* Warning message styling */
    p.warning {
       background: #ffe066;
       color: #c0392b;
       padding: 12px;
       border-radius: 6px;
       text-align: center;
       margin-bottom: 20px;
       font-weight: 600;
       line-height: 1.4;
    }
    /* Instructions styling */
    .instructions {
       font-size: 16px;
       margin-bottom: 20px;
       line-height: 1.6;
       color: #555;
    }
    .instructions p {
      margin-bottom: 12px;
    }
    .instructions strong {
      display: block;
      margin-bottom: 4px;
      color: #ff6f61;
    }
    .form-field {
       margin-bottom: 18px;
    }
    label {
       display: block;
       margin-bottom: 8px;
       font-weight: 600;
       color: #333;
    }
    input[type="text"], select {
       width: 100%;
       padding: 12px;
       border: 2px solid #ddd;
       border-radius: 6px;
       font-size: 16px;
       transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    input[type="text"]:focus, select:focus {
       border-color: #ff6f61;
       box-shadow: 0 0 8px rgba(255, 111, 97, 0.5);
       outline: none;
    }
    input[type="submit"] {
       background-color: #ff6f61;
       color: #fff;
       border: none;
       padding: 12px 24px;
       border-radius: 6px;
       cursor: pointer;
       font-size: 18px;
       transition: background-color 0.3s ease;
       width: 100%;
    }
    input[type="submit"]:hover {
       background-color: #e85a4f;
    }
    /* Styling for the author details display */
    #details {
       margin-top: 20px;
       padding: 15px;
       background: #f7f9fc;
       border: 1px solid #ddd;
       border-radius: 6px;
    }
    .detail-box {
       margin-bottom: 12px;
       padding: 10px;
       border: 1px solid #ccc;
       border-radius: 4px;
       background: #fff;
       transition: transform 0.2s ease;
    }
    .detail-box:hover {
       transform: scale(1.02);
    }
    .detail-title {
       font-weight: 600;
       margin-bottom: 6px;
       color: #ff6f61;
    }
    .success {
       color: #27ae60;
       text-align: center;
       font-weight: 600;
       margin-bottom: 20px;
       font-size: 18px;
    }
  </style>
  <script type="text/javascript">
    // Build an object mapping each author id to its details (bio, genre, themes, and quote)
    var authorDetails = {
      <?php
      if (!empty($authors)) {
          foreach ($authors as $author) {
              $quote = isset($author['sample']) ? $author['sample'] : "";
              echo json_encode($author['id']) . ": { " .
                   "\"bio\": " . json_encode($author['bio']) . ", " .
                   "\"genre\": " . json_encode($author['genre']) . ", " .
                   "\"themes\": " . json_encode($author['themes']) . ", " .
                   "\"quote\": " . json_encode($quote) .
                   " },\n";
          }
      }
      ?>
    };

    // When the author drop-down changes, update the details display.
    function updateDetails() {
      var select = document.getElementById("author");
      var detailsDiv = document.getElementById("details");
      var selected = select.options[select.selectedIndex].value;
      if (selected && authorDetails[selected]) {
        var details = authorDetails[selected];
        detailsDiv.innerHTML =
          "<div class='detail-box'><div class='detail-title'>Biography:</div><p>" + details.bio + "</p></div>" +
          "<div class='detail-box'><div class='detail-title'>Genre:</div><p>" + details.genre + "</p></div>" +
          "<div class='detail-box'><div class='detail-title'>Themes:</div><p>" + details.themes + "</p></div>" +
          "<div class='detail-box'><div class='detail-title'>Quote from the Author:</div><p>" + details.quote + "</p></div>";
      } else {
        detailsDiv.innerHTML = "";
      }
    }
  </script>
</head>
<body>
  <div class="container">
    <h1>Author Signup for Research Projects</h1>
    <?php if (isset($_GET['success'])): ?>
      <p class="success">Your selection has been recorded. Thank you!</p>
    <?php endif; ?>
    <p class="warning">
      <strong>Warning (English):</strong> Once you submit your response, you cannot change it.<br>
      <strong>Advertencia (Español):</strong> Una vez que envíe su respuesta, no podrá cambiarla.<br>
      <strong>Aviso (Português):</strong> Uma vez que você enviar sua resposta, não será possível alterá-la.
    </p>
    <div class="instructions">
      <p><strong>Instructions (English):</strong> Please fill in your first name and last name, select your class period, and choose the author you'd like to write about from the drop-down menu. When you select an author, details about their biography, genre, common themes, and a quote from the author will appear. Make sure your choices are final before submitting.</p>
      <p><strong>Instrucciones (Español):</strong> Por favor, rellene su nombre y apellido, seleccione su período de clase y elija el autor sobre el que desea escribir del menú desplegable. Al seleccionar un autor, se mostrarán detalles sobre su biografía, género, temas comunes y una cita del autor. Asegúrese de que sus elecciones sean definitivas antes de enviar.</p>
      <p><strong>Instruções (Português):</strong> Por favor, preencha seu nome e sobrenome, selecione seu período de aula e escolha o autor sobre o qual deseja escrever a partir do menu suspenso. Ao selecionar um autor, serão exibidos detalhes sobre sua biografia, gênero, temas comuns e uma citação do autor. Certifique-se de que suas escolhas estejam definitivas antes de enviar.</p>
    </div>
    <form method="post" action="index.php">
      <div class="form-field">
        <label for="firstName">First Name:</label>
        <input type="text" name="firstName" id="firstName" required>
      </div>
      <div class="form-field">
        <label for="lastName">Last Name:</label>
        <input type="text" name="lastName" id="lastName" required>
      </div>
      <div class="form-field">
        <label for="classPeriod">Class Period:</label>
        <select name="classPeriod" id="classPeriod" required>
          <option value="">-- Select Period --</option>
          <option value="Period 1">Period 1</option>
          <option value="Period 2">Period 2</option>
          <option value="Period 3">Period 3</option>
          <option value="Period 5">Period 5</option>
          <option value="Period 6">Period 6</option>
          <option value="Period 7">Period 7</option>
        </select>
      </div>
      <div class="form-field">
        <label for="author">Select Author:</label>
        <select name="author" id="author" onchange="updateDetails()" required>
          <option value="">-- Select Author --</option>
          <?php foreach ($authors as $author): ?>
            <option value="<?php echo htmlspecialchars($author['id']); ?>">
              <?php echo htmlspecialchars($author['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <!-- This div will display the author details (bio, genre, themes, quote) -->
      <div id="details"></div>
      <div class="form-field">
        <input type="submit" name="submit" value="Sign Up">
      </div>
    </form>
  </div>
</body>
</html>
