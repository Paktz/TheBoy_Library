<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "thebooyks_lib");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to get the most active borrowers with their credit points and borrowed books
$sql = "SELECT u.UserID, u.FirstName, u.LastName, u.LibraryCreditPoints, COUNT(br.BorrowingID) AS BorrowCount,
               GROUP_CONCAT(DISTINCT b.Title SEPARATOR ', ') AS Books
        FROM Users u
        JOIN Borrowings br ON u.UserID = br.UserID
        JOIN Books b ON br.BookID = b.BookID
        GROUP BY u.UserID
        ORDER BY BorrowCount DESC
        LIMIT 10";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Most Active Borrowers</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Most Active Borrowers</h2>
        <table>
            <tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Library Credit Points</th>
                <th>Borrow Count</th>
                <th>Books</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['UserID']) . "</td>
                            <td>" . htmlspecialchars($row['FirstName']) . "</td>
                            <td>" . htmlspecialchars($row['LastName']) . "</td>
                            <td>" . htmlspecialchars($row['LibraryCreditPoints']) . "</td>
                            <td>" . $row['BorrowCount'] . "</td>
                            <td>" . htmlspecialchars($row['Books']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No data available</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
