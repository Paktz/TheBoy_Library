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

// Fetch unavailable books and their return dates
$unavailableBooksQuery = "SELECT b.BookID, b.Title, br.DueDate 
                          FROM Books b
                          JOIN Borrowings br ON b.BookID = br.BookID
                          WHERE b.StatusID = 0 AND br.DateReturned IS NULL";
$unavailableBooksResult = mysqli_query($conn, $unavailableBooksQuery);

// Fetch currently reserved books and their durations
$reservedBooksQuery = "SELECT r.ReservationID, r.BookID, b.Title, r.DateReserved, r.ExpirationDate, 
                              DATEDIFF(r.ExpirationDate, r.DateReserved) AS Duration
                       FROM Reservations r
                       JOIN Books b ON r.BookID = b.BookID
                       WHERE r.ExpirationDate >= CURDATE()";
$reservedBooksResult = mysqli_query($conn, $reservedBooksQuery);

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
        h2, h3 {
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
        .unavailable-books, .reserved-books {
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
            <a href="reserve_book.php">Reserve Book</a>
        </div>
    </div>
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
        <div class="unavailable-books">
            <h3>Currently Unavailable Books</h3>
            <table>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Return Date</th>
                </tr>
                <?php
                if ($unavailableBooksResult && mysqli_num_rows($unavailableBooksResult) > 0) {
                    while ($book = mysqli_fetch_assoc($unavailableBooksResult)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($book['BookID']) . "</td>
                                <td>" . htmlspecialchars($book['Title']) . "</td>
                                <td>" . htmlspecialchars($book['DueDate']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No unavailable books</td></tr>";
                }
                ?>
            </table>
        </div>
        <div class="reserved-books">
            <h3>Currently Reserved Books</h3>
            <table>
                <tr>
                    <th>Reservation ID</th>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Date Reserved</th>
                    <th>Expiration Date</th>
                    <th>Duration (days)</th>
                </tr>
                <?php
                if ($reservedBooksResult && mysqli_num_rows($reservedBooksResult) > 0) {
                    while ($book = mysqli_fetch_assoc($reservedBooksResult)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($book['ReservationID']) . "</td>
                                <td>" . htmlspecialchars($book['BookID']) . "</td>
                                <td>" . htmlspecialchars($book['Title']) . "</td>
                                <td>" . htmlspecialchars($book['DateReserved']) . "</td>
                                <td>" . htmlspecialchars($book['ExpirationDate']) . "</td>
                                <td>" . htmlspecialchars($book['Duration']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No reserved books</td></tr>";
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
