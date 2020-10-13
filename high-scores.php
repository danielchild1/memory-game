<?php
session_start();
$servername = "localhost";
$username = "xxx";
$password = "xxx";
$dbname = "xxx";
$name = $_SESSION["name"];
$score = $_SESSION["usersScore"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if($conn->connect_error){
    die('Connection failed: ' . $conn->connect_error);
}
$sql = "INSERT INTO highScores(name, score) VALUES ('$name', $score)";
if ($conn->query($sql) === FALSE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <h1 id="title">High Scores</h1>
    <table>
        <thead>
            <tr>
                <th>Name:</th>
                <th>Score:</th>
            </tr>
        </thead>
        <tbody>


    <?php
    $sql = "SELECT * FROM highScores ORDER BY score DESC LIMIT 10";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
    echo " <tr> <td>" . $row["name"] . " </td> <td>" . $row["score"] . "</td></tr>";
    }
    } else {
    echo "0 results found";
    }

    $conn->close();
    ?>
        </tbody>
    </table>
    <br>
    <br>
    <a class="button" href="start.php">back to start</a>
    </body>

    
</html>