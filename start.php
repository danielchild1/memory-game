<?php

session_start();
session_unset();

?>
<!DOCTYPE>
<html>
<head>
    <title>Assignment 1 start screen</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<form  action="board.php" method="post">
    <label for="name">Enter Name:</label>
<input type="text" name="name"/>
<input type="submit" value="Go" />
</form>


</body>
</html>