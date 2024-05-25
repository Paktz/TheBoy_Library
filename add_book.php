<?php
// Start the session
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "thebooyks_lib");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables
$title = $author = $isbn = $publishedYear = $genre = $shelfLocation = "";
$successMessage = $errorMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $publishedYear = $_POST['publishedYear'];
    $genre = $_POST['genre'];
    $shelfLocation = $_POST['shelfLocation'];

    // Insert new book into Books table
    $sql = "INSERT INTO Books (Title, Author, ISBN, PublishedYear, Genre, ShelfLocation)
            VALUES ('$title', '$author', '$isbn', $publishedYear, '$genre', '$shelfLocation')";

    if (mysqli_query($conn, $sql)) {
        // Set success message and clear form data
        $successMessage = "New book added successfully";
        $title = $author = $isbn = $publishedYear = $genre = $shelfLocation = "";
    } else {
        $errorMessage = "Error: " . mysqli_error($conn);
    }
}

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin: 10px 0 5px;
        }
        input {
            margin: 5px 0 15px;
            padding: 8px;
            width: 100%;
            max-width: 300px;
        }
        input[type="submit"] {
            width: 150px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
        }
        .error {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Book</h2>
        <?php if ($successMessage): ?>
            <p class="message"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form action="add_book.php" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($author); ?>" required>

            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" required>

            <label for="publishedYear">Published Year:</label>
            <input type="number" id="publishedYear" name="publishedYear" value="<?php echo htmlspecialchars($publishedYear); ?>" required>

            <label for="genre">Genre:</label>
            <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($genre); ?>" required>

            <label for="shelfLocation">Shelf Location:</label>
            <input type="text" id="shelfLocation" name="shelfLocation" value="<?php echo htmlspecialchars($shelfLocation); ?>" required>

            <input type="submit" value="Add Book">
        </form>
    </div>
</body>
</html>
