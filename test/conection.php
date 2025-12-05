<?php
//creating a database connection - $link is a variable use for just connection class
$link=mysqli_connect("sql100.infinityfree.com","if0_40506385","cQS41DJilwFGr", "if0_40506385_book_management") or die(mysqli_connect_error());
mysqli_select_db($link,"if0_40506385_book_management") or die(mysqli_error($link));
?>


