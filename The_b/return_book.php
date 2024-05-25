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
$borrowingID = $returnDate = "";
$successMessage = $errorMessage = "";
$fineAmount = 0; // Fine amount

// Define the fine rate (e.g., $1 per day late)
define("FINE_RATE", 1.00);
define("CREDIT_INCREMENT", 1); // Points to increment for on-time returns

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $borrowingID = $_POST['borrowingID'];
    $returnDate = $_POST['returnDate'];

    // Get the BookID, UserID, and DueDate for the borrowing record
    $query = "SELECT BookID, UserID, DueDate FROM Borrowings WHERE BorrowingID = $borrowingID";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $bookID = $row['BookID'];
        $userID = $row['UserID'];
        $dueDate = $row['DueDate'];

        // Calculate the fine if the book is returned late
        $dueDateTime = new DateTime($dueDate);
        $returnDateTime = new DateTime($returnDate);
        if ($returnDateTime > $dueDateTime) {
            $interval = $dueDateTime->diff($returnDateTime);
            $daysLate = $interval->days;
            $fineAmount = $daysLate * FINE_RATE;
        } else {
            // Increment the user's library credit points if returned on time
            $incrementCreditPoints = "UPDATE Users SET LibraryCreditPoints = LibraryCreditPoints + " . CREDIT_INCREMENT . " WHERE UserID = $userID";
            mysqli_query($conn, $incrementCreditPoints);
        }

        // Update the borrowing record with the return date
        $sql = "UPDATE Borrowings SET DateReturned = '$returnDate' WHERE BorrowingID = $borrowingID";

        if (mysqli_query($conn, $sql)) {
            // Update the status of the book to "Available"
            $updateBookStatus = "UPDATE Books SET StatusID = 1 WHERE BookID = $bookID";
            mysqli_query($conn, $updateBookStatus);

            // Record the fine if applicable
            if ($fineAmount > 0) {
                $fineSql = "INSERT INTO Fines (UserID, BorrowingID, Amount, DateIssued, PayStatus)
                            VALUES ($userID, $borrowingID, $fineAmount, '$returnDate', 'Unpaid')";
                mysqli_query($conn, $fineSql);
            }

            // Set success message and clear form data
            $successMessage = "Book returned successfully";
            if ($fineAmount > 0) {
                $successMessage .= " with a fine of $$fineAmount.";
            } else {
                $successMessage .= " Library credit points have been increased.";
            }
            $borrowingID = $returnDate = "";
        } else {
            $errorMessage = "Error: " . mysqli_error($conn);
        }
    } else {
        $errorMessage = "Invalid Borrowing ID";
    }
}

// Close the connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Return Book</title>
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
        <h2>Return Book</h2>
        <?php if ($successMessage): ?>
            <p class="message"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form action="return_book.php" method="post">
            <label for="borrowingID">Borrowing ID:</label>
            <input type="number" id="borrowingID" name="borrowingID" value="<?php echo htmlspecialchars($borrowingID); ?>" required>

            <label for="returnDate">Return Date:</label>
            <input type="date" id="returnDate" name="returnDate" value="<?php echo htmlspecialchars($returnDate); ?>" required>

            <input type="submit" value="Return Book">
        </form>
    </div>
</body>
</html>
