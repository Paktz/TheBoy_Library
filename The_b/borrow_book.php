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
$bookID = $userID = $dateBorrowed = $dueDate = $searchTitle = $searchISBN = "";
$successMessage = $errorMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow'])) {
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

// Check if search form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['searchTitle']) || isset($_POST['searchISBN']))) {
    $searchTitle = $_POST['searchTitle'];
    $searchISBN = $_POST['searchISBN'];
}

// Get list of all books
$allBooksQuery = "SELECT BookID, Title, Author, ISBN, (CASE WHEN StatusID = 1 THEN 'Available' ELSE 'Unavailable' END) AS Status FROM Books";
if (!empty($searchTitle)) {
    $allBooksQuery .= " WHERE Title LIKE '%" . mysqli_real_escape_string($conn, $searchTitle) . "%'";
}
if (!empty($searchISBN)) {
    if (!empty($searchTitle)) {
        $allBooksQuery .= " AND ISBN LIKE '%" . mysqli_real_escape_string($conn, $searchISBN) . "%'";
    } else {
        $allBooksQuery .= " WHERE ISBN LIKE '%" . mysqli_real_escape_string($conn, $searchISBN) . "%'";
    }
}
$allBooksResult = mysqli_query($conn, $allBooksQuery);

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
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .all-books {
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

            <input type="submit" name="borrow" value="Borrow Book">
        </form>

        <div class="search-bar">
            <h3>Search Books</h3>
            <form action="borrow_book.php" method="post">
                <input type="text" name="searchTitle" placeholder="Enter book title" value="<?php echo htmlspecialchars($searchTitle); ?>">
                <input type="text" name="searchISBN" placeholder="Enter ISBN" value="<?php echo htmlspecialchars($searchISBN); ?>">
                <input type="submit" name="search" value="Search">
            </form>
        </div>

        <div class="all-books">
            <h3>All Books</h3>
            <table>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Status</th>
                </tr>
                <?php
                if ($allBooksResult && mysqli_num_rows($allBooksResult) > 0) {
                    while ($book = mysqli_fetch_assoc($allBooksResult)) {
                        echo "<tr>
                                <td>" . htmlspecialchars($book['BookID']) . "</td>
                                <td>" . htmlspecialchars($book['Title']) . "</td>
                                <td>" . htmlspecialchars($book['Author']) . "</td>
                                <td>" . htmlspecialchars($book['ISBN']) . "</td>
                                <td>" . htmlspecialchars($book['Status']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No books found</td></tr>";
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
