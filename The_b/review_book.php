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
$bookID = $userID = $reviewText = $rating = $reviewDate = "";
$successMessage = $errorMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $bookID = $_POST['bookID'];
    $userID = $_POST['userID'];
    $reviewText = $_POST['reviewText'];
    $rating = $_POST['rating'];
    $reviewDate = date("Y-m-d H:i:s"); // Current date and time

    // Insert new review record into Reviews table
    $sql = "INSERT INTO Reviews (BookID, UserID, ReviewText, Rating, ReviewDate)
            VALUES ($bookID, $userID, '$reviewText', $rating, '$reviewDate')";

    if (mysqli_query($conn, $sql)) {
        // Set success message and clear form data
        $successMessage = "Review submitted successfully";
        $bookID = $userID = $reviewText = $rating = "";
    } else {
        $errorMessage = "Error: " . mysqli_error($conn);
    }
}

// Fetch all books
$booksQuery = "SELECT BookID, Title FROM Books";
$booksResult = mysqli_query($conn, $booksQuery);

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .navbar {
            overflow: hidden;
            background-color: #333;
        }
        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .navbar-right {
            float: right;
        }
        .container {
            width: 80%;
            margin: 20px auto;
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
        input, textarea, select {
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
        .books-list {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .button-container button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .button-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <div class="navbar-right">
            <a href="add_book.php">Add Book</a>
            <a href="borrow_book.php">Borrow Book</a>
            <a href="return_book.php">Return Book</a>
            <a href="pay_fine.php">Pay Fine</a>
            <a href="review_book.php">Review Book</a>
        </div>
    </div>
    <div class="container">
        <h2>Submit Review</h2>
        <?php if ($successMessage): ?>
            <p class="message"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form action="review_book.php" method="post">
            <label for="bookID">Book ID:</label>
            <input type="number" id="bookID" name="bookID" value="<?php echo htmlspecialchars($bookID); ?>" required>

            <label for="userID">User ID:</label>
            <input type="number" id="userID" name="userID" value="<?php echo htmlspecialchars($userID); ?>" required>

            <label for="reviewText">Review Text:</label>
            <textarea id="reviewText" name="reviewText" rows="4" required><?php echo htmlspecialchars($reviewText); ?></textarea>

            <label for="rating">Rating:</label>
            <select id="rating" name="rating" required>
                <option value="" disabled selected>Select a rating</option>
                <option value="1" <?php if ($rating == 1) echo 'selected'; ?>>1</option>
                <option value="2" <?php if ($rating == 2) echo 'selected'; ?>>2</option>
                <option value="3" <?php if ($rating == 3) echo 'selected'; ?>>3</option>
                <option value="4" <?php if ($rating == 4) echo 'selected'; ?>>4</option>
                <option value="5" <?php if ($rating == 5) echo 'selected'; ?>>5</option>
            </select>

            <input type="submit" value="Submit Review">
        </form>
        <div class="books-list">
            <h3>Books List</h3>
            <table>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                </tr>
                <?php
                if ($booksResult && mysqli_num_rows($booksResult) > 0) {
                    while ($book = mysqli_fetch_assoc($booksResult)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($book['BookID']) . "</td>
                                <td>" . htmlspecialchars($book['Title']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No books available</td></tr>";
                }
                ?>
            </table>
        </div>
        <div class="button-container">
            <button onclick="window.location.href='index.php'">Return to Home Page</button>
        </div>
    </div>
</body>
</html>
