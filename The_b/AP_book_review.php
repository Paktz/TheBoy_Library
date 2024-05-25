<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "thebooyks_lib");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to get book review analysis
$sql = "SELECT b.BookID, b.Title, AVG(r.Rating) AS AverageRating, COUNT(r.ReviewID) AS ReviewCount,
               GROUP_CONCAT(DISTINCT CONCAT(u.FirstName, ' ', u.LastName, ': ', r.ReviewText) SEPARATOR '; ') AS Reviews
        FROM Books b
        JOIN Reviews r ON b.BookID = r.BookID
        JOIN Users u ON r.UserID = u.UserID
        GROUP BY b.BookID
        ORDER BY AverageRating DESC, ReviewCount DESC";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Review Analysis</title>
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
        <h2>Book Review Analysis</h2>
        <table>
            <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>Average Rating</th>
                <th>Review Count</th>
                <th>Reviews</th>
            </tr>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['BookID']) . "</td>
                            <td>" . htmlspecialchars($row['Title']) . "</td>
                            <td>" . number_format($row['AverageRating'], 2) . "</td>
                            <td>" . $row['ReviewCount'] . "</td>
                            <td>" . htmlspecialchars($row['Reviews']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No data available</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
