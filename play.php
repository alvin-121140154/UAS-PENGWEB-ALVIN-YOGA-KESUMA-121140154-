<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "flashcard";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

$sqlFetchDecks = "SELECT * FROM decks";
$resultDecks = $conn->query($sqlFetchDecks);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Flashcards</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Play Flashcards</h1>

    <form action="" method="post">
        <label for="selectDeck">Select Deck:</label>
        <select id="selectDeck" name="selectDeck" required>
            <?php foreach ($resultDecks as $row): ?>
                <option value="<?= $row['deck_name'] ?>"><?= $row['deck_name'] ?></option>
            <?php endforeach; ?>
        </select>
        <br />
        <button type="submit" name="submit_play">Play Flashcards</button>
        <a href="index.php">Back</a>

    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_play"])) {
        $selectedDeck = $conn->real_escape_string($_POST["selectDeck"]);
        $sqlFetchPlayFlashcards = "SELECT * FROM flashcards WHERE deck = '$selectedDeck'";
        $resultPlayFlashcards = $conn->query($sqlFetchPlayFlashcards);
    ?>
        <table>
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Toggle Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultPlayFlashcards as $row): ?>
                    <tr>
                        <td><?= $row['pertanyaan'] ?></td>
                        <td>
                            <span id="answer_<?= $row['id'] ?>" class="answer" style="display: none;"><?= $row['jawaban'] ?></span>
                        </td>
                        <td>
                            <button onclick="toggleAnswer('answer_<?= $row['id'] ?>')">Toggle Answer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php
    }
    ?>

    <script>
        function toggleAnswer(answerId) {
            var answer = document.getElementById(answerId);
            if (answer.style.display === 'none') {
                answer.style.display = 'inline-block';
            } else {
                answer.style.display = 'none';
            }
        }
    </script>
</body>
</html>
