<?php
//creating a database connection - $link is a variable use for just connection class
$link=mysqli_connect("localhost","root","") or die(mysqli_connect_error());
mysqli_select_db($link,"book_management") or die(mysqli_error($link));
?>

<!-- CREATE DATABASE book_management;
USE book_management;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100),
  username VARCHAR(50) UNIQUE,
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255)
);

CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100),
  author VARCHAR(100),
  category VARCHAR(50),
  year INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); -->
