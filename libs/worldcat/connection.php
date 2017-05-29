<?php
$con_temp = mysqli_connect("localhost","root","hJ7lRObbpk");

//$con_temp = mysqli_connect("localhost","root","");

mysqli_query($con_temp,"CREATE DATABASE IF NOT EXISTS worldcat");
$con = mysqli_connect("localhost","root","hJ7lRObbpk","worldcat");

//$con = mysqli_connect("localhost","root","","worldcat");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
?>