<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "flashcard";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["submit_flashcard"])) {
      $pertanyaan = $conn->real_escape_string($_POST["pertanyaan"]);
      $jawaban = $conn->real_escape_string($_POST["jawaban"]);
      $kategori = $conn->real_escape_string($_POST["kategori"]);
      $deck = $conn->real_escape_string($_POST["deck"]);

      $sql = "INSERT INTO flashcards (pertanyaan, jawaban, kategori, deck) VALUES ('$pertanyaan', '$jawaban', '$kategori', '$deck')";
      performDatabaseQuery($sql);
  } elseif (isset($_POST["submit_category"])) {
      $newCategory = $conn->real_escape_string($_POST["newCategory"]);

      $checkCategoryQuery = "SELECT * FROM categories WHERE category_name = '$newCategory'";
      $checkCategoryResult = $conn->query($checkCategoryQuery);

      if ($checkCategoryResult->num_rows > 0) {
          echo '<script>alert("Error: Category already exists.");</script>';
      } else {
          $sql = "INSERT INTO categories (category_name) VALUES ('$newCategory')";
          performDatabaseQuery($sql);
      }
  } elseif (isset($_POST["submit_deck"])) {
      $newDeck = $conn->real_escape_string($_POST["newDeck"]);

      $checkDeckQuery = "SELECT * FROM decks WHERE deck_name = '$newDeck'";
      $checkDeckResult = $conn->query($checkDeckQuery);

      if ($checkDeckResult->num_rows > 0) {
          echo '<script>alert("Error: Deck already exists.");</script>';
      } else {
          $sql = "INSERT INTO decks (deck_name) VALUES ('$newDeck')";
          performDatabaseQuery($sql);
      }
  }
}

function performDatabaseQuery($sql, $successMessage = "Record added successfully!") {
  global $conn;

  if ($conn->query($sql) === TRUE) {
      echo '<script>alert("' . $successMessage . '");</script>';
  } else {
      echo '<script>alert("Error: ' . $conn->error . '");</script>';
  }
}

if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
  $flashcardIdToDelete = $conn->real_escape_string($_GET["id"]);
  $deleteFlashcardQuery = "DELETE FROM flashcards WHERE id = '$flashcardIdToDelete'";
  performDatabaseQuery($deleteFlashcardQuery, "Record deleted successfully!");
}

$sqlFetchFlashcards = "SELECT * FROM flashcards";
$resultFlashcards = $conn->query($sqlFetchFlashcards);

$sqlFetchCategories = "SELECT * FROM categories";
$resultCategories = $conn->query($sqlFetchCategories);

$sqlFetchDecks = "SELECT * FROM decks";
$resultDecks = $conn->query($sqlFetchDecks);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
      #forms form {
        display: none;
      }
    </style>
    <link rel="stylesheet" href="index-style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  </head>
  <body>
    <section id="title">
      <h1>Flash Card Quiz</h1>
    </section>
    <section id="navigation">
      <button onclick="toggleForm('flashCard')">Add Flash Card</button>
      <button onclick="toggleForm('category')">Add Category</button>
      <button onclick="toggleForm('deckForm')">Add Deck</button>
      <a href="play.php">Play Flashcards</a>
    </section>
    <section id="forms">
      <form action="" method="post" id="flashCard">
        <label for="pertanyaan">Pertanyaan:</label>
        <input type="text" id="pertanyaan" name="pertanyaan" required />
        <br />

        <label for="jawaban">Jawaban:</label>
        <input type="text" id="jawaban" name="jawaban" required />
        <br />

        <label for="kategori">Kategori:</label>
        <select id="kategori" name="kategori" required>
          <?php foreach ($resultCategories as $row): ?>
            <option value="<?= $row['category_name'] ?>"><?= $row['category_name'] ?></option>
          <?php endforeach; ?>
        </select>
        <br />

        <label for="deck">Deck:</label>
        <select id="deck" name="deck" required>
          <?php foreach ($resultDecks as $row): ?>
            <option value="<?= $row['deck_name'] ?>"><?= $row['deck_name'] ?></option>
          <?php endforeach; ?>
        </select>
        <br />

        <button type="submit" name="submit_flashcard">Add Flashcard</button>
      </form>
      <form action="" method="post" id="category">
        <label for="newCategory">New Category:</label>
        <input type="text" id="newCategory" name="newCategory" required />
        <br />

        <button type="submit" name="submit_category">Add Category</button>
      </form>
      <form action="" method="post" id="deckForm">
        <label for="newDeck">New Deck:</label>
        <input type="text" id="newDeck" name="newDeck" required />
        <br />

        <button type="submit" name="submit_deck">Add Deck</button>
      </form>
    </section>
    <section>
      <h2>Data from Database</h2>

      <h3>Flashcards</h3>
      <table>
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Pertanyaan</th>
                  <th>Jawaban</th>
                  <th>Kategori</th>
                  <th>Deck</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($resultFlashcards as $row): ?>
                  <tr>
                      <td><?= $row['id'] ?></td>
                      <td><?= $row['pertanyaan'] ?></td>
                      <td><?= $row['jawaban'] ?></td>
                      <td><?= $row['kategori'] ?></td>
                      <td><?= $row['deck'] ?></td>
                      <td>
                        <a href="update.php?id=<?= $row['id'] ?>">Edit</a>
                        <a href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this flashcard?')">Delete</a>
                      </td>
                  </tr>
              <?php endforeach; ?>
          </tbody>
      </table>
    </section>

    <script>
      function toggleForm(formId) {
        var forms = document.querySelectorAll("#forms form");
        forms.forEach((form) => (form.style.display = "none"));

        var selectedForm = document.getElementById(formId);
        if (selectedForm) {
          selectedForm.style.display = "block";
        }
      }

      function validateDeckForm() {
        var newDeckInput = document.getElementById('newDeck');
        if (newDeckInput.value.trim() === '') {
          alert('Please enter a deck name.');
          return false;
        }
        return true;
      }
    </script>


  </body>
</html>

