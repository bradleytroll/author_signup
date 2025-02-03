<?php
// --- Process Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize input values.
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName'] ?? '');
    $classPeriod = trim($_POST['classPeriod'] ?? '');
    $selectedAuthorId = trim($_POST['author'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($classPeriod) || empty($selectedAuthorId)) {
         die("Please fill in all required fields.");
    }

    // Load the authors from JSON.
    $authorsFile = 'authors.json';
    if (!file_exists($authorsFile)) {
         die("Authors file not found.");
    }
    $authorsData = json_decode(file_get_contents($authorsFile), true);

    // Look for the selected author.
    $selectedAuthor = null;
    foreach ($authorsData as $index => $author) {
         if ($author['id'] === $selectedAuthorId) {
             $selectedAuthor = $author;
             // Remove the author so that no one else can choose them.
             unset($authorsData[$index]);
             break;
         }
    }

    if ($selectedAuthor === null) {
         die("The selected author is no longer available. Please choose another.");
    }

    // Re-index the array so that JSON is written as a proper array.
    $authorsData = array_values($authorsData);
    // Write the updated authors list back to the JSON file.
    file_put_contents($authorsFile, json_encode($authorsData, JSON_PRETTY_PRINT));

    // Log the submission details.
    $studentName = $firstName . " " . $lastName;
    $logEntry = date('Y-m-d H:i:s') . " - Student: " . $studentName . ", Class Period: " . $classPeriod . ", Author: " . $selectedAuthor['name'] . "\n";
    file_put_contents("submissions.log", $logEntry, FILE_APPEND);

    // Redirect back to avoid form resubmission.
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
  <title>Author Signup</title>
  <style>
    /* Overall page styling */
    body {
       font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
       background: #f0f4f8;
       margin: 0;
       padding: 0;
       color: #333;
    }
    .container {
       max-width: 600px;
       margin: 30px auto;
       background: #fff;
       padding: 20px;
       border-radius: 8px;
       box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    h1 {
       text-align: center;
       color: #005f73;
    }
    /* Warning message styling */
    p.warning {
       background: #ffcccb;
       color: #900;
       padding: 10px;
       border-radius: 5px;
       text-align: center;
       margin-bottom: 20px;
    }
    /* Instructions styling */
    .instructions {
       font-size: 16px;
       margin-bottom: 20px;
       line-height: 1.5;
       color: #555;
    }
    .instructions p {
      margin-bottom: 12px;
    }
    .instructions strong {
      display: block;
      margin-bottom: 4px;
    }
    .form-field {
       margin-bottom: 1em;
    }
    label {
       display: block;
       margin-bottom: 5px;
       font-weight: bold;
    }
    input[type="text"], select {
       width: 100%;
       padding: 10px;
       border: 1px solid #ccc;
       border-radius: 4px;
       box-sizing: border-box;
    }
    input[type="submit"] {
       background-color: #0077b6;
       color: white;
       border: none;
       padding: 10px 20px;
       border-radius: 4px;
       cursor: pointer;
       font-size: 16px;
    }
    input[type="submit"]:hover {
       background-color: #005f73;
    }
    /* Styling for the author details display */
    #details {
       margin-top: 10px;
       padding: 10px;
       background: #e0fbfc;
       border: 1px solid #ccc;
       border-radius: 4px;
    }
    .detail-box {
       margin-bottom: 10px;
       padding: 8px;
       border: 1px solid #ccc;
       border-radius: 4px;
       background: #fff;
    }
    .detail-title {
       font-weight: bold;
       margin-bottom: 4px;
       color: #0077b6;
    }
    .success {
       color: green;
       text-align: center;
       font-weight: bold;
       margin-bottom: 20px;
    }
  </style>
  <script type="text/javascript">
    // Build an object mapping each author id to its details (bio, genre, themes, and sample).
    var authorDetails = {
      <?php
      if (!empty($authors)) {
          foreach ($authors as $author) {
              $sample = isset($author['sample']) ? $author['sample'] : "";
              echo json_encode($author['id']) . ": { " .
                   "\"bio\": " . json_encode($author['bio']) . ", " .
                   "\"genre\": " . json_encode($author['genre']) . ", " .
                   "\"themes\": " . json_encode($author['themes']) . ", " .
                   "\"sample\": " . json_encode($sample) .
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
          "<div class='detail-box'><div class='detail-title'>Quote from the Author:</div><p>" + details.sample + "</p></div>";
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
      <strong>Warning:</strong> Once you submit your response, you cannot change it.<br>
      <strong>Advertencia:</strong> Una vez que envíe su respuesta, no podrá cambiarla.<br>
      <strong>Aviso:</strong> Uma vez que você enviar sua resposta, não será possível alterá-la.
    </p>
    <div class="instructions">
      <p><strong>Instructions:</strong> Please fill in your first name and last name, select your class period, and choose the author you'd like to write about from the drop-down menu. When you select an author, details about their biography, genre, common themes, and a sample of their work will appear. Make sure your choices are final before submitting.</p>
      <p><strong>Instrucciones:</strong> Por favor, rellene su nombre y apellido, seleccione su período de clase y elija el autor sobre el que desea escribir del menú desplegable. Al seleccionar un autor, se mostrarán detalles sobre su biografía, género, temas comunes y una muestra de su obra. Asegúrese de que sus elecciones sean definitivas antes de enviar.</p>
      <p><strong>Instruções:</strong> Por favor, preencha seu nome e sobrenome, selecione seu período de aula e escolha o autor sobre o qual deseja escrever a partir do menu suspenso. Ao selecionar um autor, serão exibidos detalhes sobre sua biografia, gênero, temas comuns e uma amostra de sua obra. Certifique-se de que suas escolhas estejam definitivas antes de enviar.</p>
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
      <!-- This div will display the author details (bio, genre, themes, sample) -->
      <div id="details"></div>
      <div class="form-field">
        <input type="submit" name="submit" value="Sign Up">
      </div>
    </form>
  </div>
</body>
</html>
