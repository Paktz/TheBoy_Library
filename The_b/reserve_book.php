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
$bookID = $userID = $dateReserved = $expirationDate = "";
$successMessage = $errorMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $bookID = $_POST['bookID'];
    $userID = $_POST['userID'];
    $dateReserved = $_POST['dateReserved'];
    $expirationDate = $_POST['expirationDate'];

    // Insert new reservation record into Reservations table
    $sql = "INSERT INTO Reservations (BookID, UserID, DateReserved, ExpirationDate)
            VALUES ($bookID, $userID, '$dateReserved', '$expirationDate')";

    if (mysqli_query($conn, $sql)) {
        // Set success message and clear form data
        $successMessage = "Book reserved successfully";
        $bookID = $userID = $dateReserved = $expirationDate = "";
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
    <title>Reserve Book</title>
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
        <h2>Reserve Book</h2>
        <?php if ($successMessage): ?>
            <p class="message"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form action="reserve_book.php" method="post">
            <label for="bookID">Book ID:</label>
            <input type="number" id="bookID" name="bookID" value="<?php echo htmlspecialchars($bookID); ?>" required>

            <label for="userID">User ID:</label>
            <input type="number" id="userID" name="userID" value="<?php echo htmlspecialchars($userID); ?>" required>

            <label for="dateReserved">Date Reserved:</label>
            <input type="date" id="dateReserved" name="dateReserved" value="<?php echo htmlspecialchars($dateReserved); ?>" required>

            <label for="expirationDate">Expiration Date:</label>
            <input type="date" id="expirationDate" name="expirationDate" value="<?php echo htmlspecialchars($expirationDate); ?>" required>

            <input type="submit" value="Reserve Book">
        </form>
    </div>
</body>
</html>
