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

// Get the list of current borrowings
$borrowingsQuery = "SELECT b.BorrowingID, b.BookID, b.UserID, b.DueDate, u.FirstName, u.LastName, bk.Title
                    FROM Borrowings b
                    JOIN Users u ON b.UserID = u.UserID
                    JOIN Books bk ON b.BookID = bk.BookID
                    WHERE b.DateReturned IS NULL";
$borrowingsResult = mysqli_query($conn, $borrowingsQuery);

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
        .current-borrowings {
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
        <div class="current-borrowings">
            <h3>Current Borrowings</h3>
            <table>
                <tr>
                    <th>Borrowing ID</th>
                    <th>Book Title</th>
                    <th>User</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
                <?php
                if ($borrowingsResult && mysqli_num_rows($borrowingsResult) > 0) {
                    while ($borrowing = mysqli_fetch_assoc($borrowingsResult)) {
                        $dueDate = new DateTime($borrowing['DueDate']);
                        $currentDate = new DateTime();
                        $status = $currentDate > $dueDate ? 'Late' : 'Not Late';
                        echo "<tr>
                                <td>" . htmlspecialchars($borrowing['BorrowingID']) . "</td>
                                <td>" . htmlspecialchars($borrowing['Title']) . "</td>
                                <td>" . htmlspecialchars($borrowing['FirstName']) . " " . htmlspecialchars($borrowing['LastName']) . "</td>
                                <td>" . htmlspecialchars($borrowing['DueDate']) . "</td>
                                <td>" . $status . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No current borrowings</td></tr>";
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
