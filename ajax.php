<?php

  require_once("LIB_project1.php");

  if (isset($_POST['item'])) {
    addCart($_POST['item']);
  }
  else if (isset($_POST['empty'])) {
    emptyCart();
  }
  else if (isset($_POST['edit'])) {
    adminEditForm($_POST['edit']);
  }
  else if (isset($_POST['editItem'])) {
    editItem($_POST['editItem']);
  }
  else if (isset($_POST['addItem'])) {
    addItem($_POST['addItem']);
  }
  else {
    die("fail");
  }

 ?>
