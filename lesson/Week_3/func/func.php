<?php
function sum($a, $b){
    return $a+$b;
}

function minOfThree($a, $b, $c) {
    $smallest = $a;
    if ($b < $smallest) {
        $smallest = $b;
    }
    if ($c < $smallest) {
        $smallest = $c;
    }
    return $smallest;
}
?>