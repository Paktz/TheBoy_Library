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
$bookID = $userID = $dateBorrowed = $dueDate = "";
$successMessage = $errorMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $bookID = $_POST['bookID'];
    $userID = $_POST['userID'];
    $dateBorrowed = $_POST['dateBorrowed'];
    $dueDate = $_POST['dueDate'];

    // Check if the book is available
    $checkAvailability = "SELECT StatusID FROM Books WHERE BookID = $bookID";
    $result = mysqli_query($conn, $checkAvailability);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['StatusID'] == 1) { // Book is available
            // Insert new borrowing record into Borrowings table
            $sql = "INSERT INTO Borrowings (BookID, UserID, DateBorrowed, DueDate)
                    VALUES ($bookID, $userID, '$dateBorrowed', '$dueDate')";

            if (mysqli_query($conn, $sql)) {
                // Update the status of the book to "Unavailable"
                $updateBookStatus = "UPDATE Books SET StatusID = 0 WHERE BookID = $bookID";
                mysqli_query($conn, $updateBookStatus);

                // Set success message and clear form data
                $successMessage = "Book borrowed successfully";
                $bookID = $userID = $dateBorrowed = $dueDate = "";
            } else {
                $errorMessage = "Error: " . mysqli_error($conn);
            }
        } else {
            // Book is not available
            $errorMessage = "The book is currently unavailable.";
        }
    } else {
        $errorMessage = "Invalid Book ID.";
    }
}

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Borrow Book</title>
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
        input, select {
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
        <h2>Borrow Book</h2>
        <?php if ($successMessage): ?>
            <p class="message"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form action="borrow_book.php" method="post">
            <label for="bookID">Book ID:</label>
            <input type="number" id="bookID" name="bookID" value="<?php echo htmlspecialchars($bookID); ?>" required>

            <label for="userID">User ID:</label>
            <input type="number" id="userID" name="userID" value="<?php echo htmlspecialchars($userID); ?>" required>

            <label for="dateBorrowed">Date Borrowed:</label>
            <input type="date" id="dateBorrowed" name="dateBorrowed" value="<?php echo htmlspecialchars($dateBorrowed); ?>" required>

            <label for="dueDate">Due Date:</label>
            <input type="date" id="dueDate" name="dueDate" value="<?php echo htmlspecialchars($dueDate); ?>" required>

            <input type="submit" value="Borrow Book">
        </form>
    </div>
</body>
</html>
