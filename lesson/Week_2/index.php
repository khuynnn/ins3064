<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
    <?php
    //http://ins3064.test/?x=5&y=8 -> pass references
    $x = $_GET['x'];
    $y = $_GET['y'];
    echo "x: ".$x."<br/>";
    echo "y: ".$y."<br/>";
    echo "$x/$y: ".($x/$y)."<br/>";
    echo "x%y: ".($x%$y)."<br/>";
    echo "x++: ".($x++)."<br/>";
    echo "++y: ".(++$y)."<br/>";
    ?> 
</body>
</html>