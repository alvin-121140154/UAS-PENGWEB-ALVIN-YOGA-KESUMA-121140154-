<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "flashcard";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

$sqlFetchCategories = "SELECT * FROM categories";
$resultCategories = $conn->query($sqlFetchCategories);

$sqlFetchDecks = "SELECT * FROM decks";
$resultDecks = $conn->query($sqlFetchDecks);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_flashcard"])) {
    $flashcard_id = $_POST["flashcard_id"];
    $pertanyaan = $conn->real_escape_string($_POST["pertanyaan"]);
    $jawaban = $conn->real_escape_string($_POST["jawaban"]);
    $kategori = $conn->real_escape_string($_POST["kategori"]);
    $deck = $conn->real_escape_string($_POST["deck"]);

    $updateQuery = "UPDATE flashcards SET pertanyaan='$pertanyaan', jawaban='$jawaban', kategori='$kategori', deck='$deck' WHERE id=$flashcard_id";
    
    if ($conn->query($updateQuery) === TRUE) {
        header('Location: index.php');
        exit();
    } else {
        echo '<script>alert("Error updating flashcard: ' . $conn->error . '");</script>';
    }
}

if (isset($_GET['id'])) {
    $flashcard_id = $_GET['id'];
    $editQuery = "SELECT * FROM flashcards WHERE id = $flashcard_id";
    $editResult = $conn->query($editQuery);

    if ($editResult->num_rows > 0) {
        $editData = $editResult->fetch_assoc();
    } else {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Flashcard</title>
</head>
<body>

    <form action="" method="post" id="flashCard">
        <input type="hidden" name="flashcard_id" value="<?= $editData['id'] ?>">
        
        <label for="pertanyaan">Pertanyaan:</label>
        <input type="text" id="pertanyaan" name="pertanyaan" value="<?= $editData['pertanyaan'] ?>" required />
        <br />

        <label for="jawaban">Jawaban:</label>
        <input type="text" id="jawaban" name="jawaban" value="<?= $editData['jawaban'] ?>" required />
        <br />

        <label for="kategori">Kategori:</label>
        <select id="kategori" name="kategori" required>
            <?php foreach ($resultCategories as $row): ?>
                <?php $selected = ($row['category_name'] == $editData['kategori']) ? 'selected' : ''; ?>
                <option value="<?= $row['category_name'] ?>" <?= $selected ?>><?= $row['category_name'] ?></option>
            <?php endforeach; ?>
        </select>
        <br />

        <label for="deck">Deck:</label>
        <select id="deck" name="deck" required>
            <?php foreach ($resultDecks as $row): ?>
                <?php $selected = ($row['deck_name'] == $editData['deck']) ? 'selected' : ''; ?>
                <option value="<?= $row['deck_name'] ?>" <?= $selected ?>><?= $row['deck_name'] ?></option>
            <?php endforeach; ?>
        </select>
        <br />

        <button type="submit" name="update_flashcard">Update Flashcard</button>
    </form>

</body>
</html>
