<?php
  session_start();

    if(isset($_POST["name"])){
        $_SESSION["name"] = $_POST["name"];
    }

    // gameState can be "Preview", "Playing", "Compare"
    if (!isset($_SESSION["gameState"])) {
        $_SESSION["gameState"] = "Preview";
    }

    if(!isset($_SESSION["currentRound"]) || ($_SESSION["currentRound"] > 3)) {
        $_SESSION["currentRound"] = "0";
        $_SESSION["usersScore"] = 0;
    }

    if(isset($_POST["userInput"])){
        $_SESSION['userInput'] = json_decode($_POST["userInput"]); 
    }


    function checkAnswers(){
        //one point for every button that is supposed to be selected
        //one point for every button that is not supposed to be selected that is not selected
        
        //keeps track of the points for this round
        $_SESSION["roundScore"] = 0;
        
        for($i = 0; $i < 36; $i++){
            echo "<script>console.log('The user input is {$_SESSION['userInput'][$i]} and the colored buttons is {$_SESSION["coloredButtons"][$i]}')</script>";
            if($_SESSION["userInput"][$i] == $_SESSION["coloredButtons"][$i]){
                $_SESSION["usersScore"]++;
                $_SESSION["roundScore"]++;
                echo "<script>console.log('Adding to score: " . $_SESSION["usersScore"] . "')</script>";
            }
        }
    }

    function randomizeButtons(){

        $coloredButtons = array();

        // Add random true/false to button array
        for($i = 0; $i < 36; $i++){
            if (rand(0,1) == 0)
                array_push($coloredButtons, false);
            else
                array_push($coloredButtons, true);
        }
        return $coloredButtons;
    }

    function createBoard($description = '', $isButtonVisible = False, $isBarVisible = True, $isGameBoard = True){
        $redirectUrl = '';
        if($_SESSION["currentRound"] == 3 && $_SESSION["gameState"] == "Compare") {
            $redirectUrl = "high-scores.php";
        }

        echo "<form id='gameBoard' action='{$redirectUrl}' method='post'>";

        // Determine the colored buttons
        $coloredButtons = array();
        switch ($_SESSION["gameState"]) {
            case "Preview":
                $coloredButtons = randomizeButtons();
                $_SESSION["coloredButtons"] = $coloredButtons;
                break;
            case "Playing":
                for ($i = 0; $i < 36; $i++){
                    array_push($coloredButtons, false);
                }
                break;
            case "Compare":
                if ($isGameBoard){
                    $coloredButtons = $_SESSION["coloredButtons"];
                }
                else {
                    $coloredButtons = $_SESSION["userInput"];
                }
        }

        //Keeps track of the id for the next button
        $id = 0;
        
        for($r = 0; $r < 6; $r++){
            for($c = 0; $c < 6; $c++){
                // We change type="button" so it is not the default of type="submit"
                $colored = "";
                if ($coloredButtons[$id] == true) {
                    $colored = "color";
                }
                else if ($coloredButtons[$id] == false) {
                    $colored = "blank";
                }
                echo '<button id="' . $id . '" type="button" class="' . $colored . '" onClick="buttonClick(' . $id . ')"></button>';
                $id++;
            }
            echo '<br>';
        }

        echo '<p>' . $description . '</p>';

        if ($isButtonVisible) {
            echo '<button>Continue</button>';
        }
        if ($isBarVisible) { ?>
            <svg width="100%" height="30px">
                <rect x="0" y="0" width="100%" height="30" style="fill:Gainsboro; stroke:black; stroke-width:3px">
                </rect>
                <rect x="0" y="0" width="0" height="30" style="fill:green;">
                    <animate attributeName="width" from="0" to="100%" begin="0s" dur="15s"/>
                </rect>
            </svg>
        <input type="hidden" id="userInput" name="userInput" value="">
            <?php }
        echo '</form>';
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <title>Game Board</title>
    </head>
    
    <script>
            buttonsSelected = Array(36)
            for(let i = 0; i < buttonsSelected.length; i++){
                buttonsSelected[i] = false
            }

            function buttonClick(id){
                <?php if($_SESSION["gameState"] == "Playing") { ?>

                    if(buttonsSelected[id]){
                        document.getElementById(id.toString()).style.backgroundColor = "Gainsboro"
                        buttonsSelected[id] = false
                    }
                    else{
                        document.getElementById(id.toString()).style.backgroundColor = "green"
                        buttonsSelected[id] = true
                    }
                
                    document.getElementById("userInput").value = JSON.stringify(buttonsSelected);
                    console.log(document.getElementById("userInput").value);
                <?php } ?>
            }

            <?php if ($_SESSION["gameState"] == "Playing" || $_SESSION["gameState"] == "Preview") { ?>
                console.log("I am starting countdown");
                window.setTimeout(() => {
                    document.getElementById('gameBoard').submit();
                }, 15000);;
            <?php } ?>

        </script>

    <body>
        <h1 id="title">Memory Game</h1>
        <div id="gameboard">
        <?php
            switch ($_SESSION["gameState"]) { 
                case "Preview":
                    $_SESSION["currentRound"]++;
                    createBoard('This is the Preview state. Try to remember as much of the pattern as you can in the next 15 seconds.');
                    $_SESSION["gameState"] = "Playing";
                    break; 

                case "Playing": 
                    createBoard('This is the Playing state. Try to recreate the pattern from the preview state.',False,True, );
                    $_SESSION["gameState"] = "Compare";
                    break; 
            
                case "Compare": 
                    echo "<p>Expected Board</p>";
                    createBoard('', False, False, True);
                    echo '<hr>';
                    echo "<p>Player Board</p>";
                    createBoard('This is the Compare state. Here you can see how you did. Each correctly filled space is worth 1 point, and each correct empty space is worth 1 point.', True, False, False);
                    $_SESSION["gameState"] = "Preview";
                    checkAnswers();
                    echo "<h2>Score for this round: {$_SESSION['roundScore']}<h2>";
                    break;
                }
                echo "<h2 class='score'>Current Score: {$_SESSION["usersScore"]}</h2>";
                ?>

            <h2 class="round">Round <?php echo $_SESSION["currentRound"];?></h2>
        </div>
    </body>
</html>