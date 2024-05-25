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
    </style>
</head>
<body>
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
    </div>
</body>
</html>
