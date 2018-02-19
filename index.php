<!-- Sales section (Min: 3 Max: 5)
  Name
  Image
  Description
  Original price
  Sale price
  Quantity available

Catalog section (Min: 15)
  Name
  Image
  Description
  Price
  Quanitity available
  5 Items per page
  Do not display sale items here**

All items have "buy"/"add to cart" button
  Increases number in cart
  Decreases number available -->

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Home</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/style.php">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="assets/ajaxCalls.js"></script>
  </head>
  <body>
    <?php require 'LIB_project1.php';
      // echo colorChanger();
    ?>
    <div class="container pb-3" style="overflow: auto; min-height: 100%; box-shadow: 0px 0 5px 0 rgba(0, 0, 0, 0.2); background-color: #F0F8FF">
      <?php
      echo navigation("index");
      ?>
    </div>
    <div class="container mt-4" style="overflow: auto; min-height: 100%; box-shadow: 0px 0 5px 0 rgba(0, 0, 0, 0.2); background-color: #F0F8FF">
      <hr>
      <h1>Today's Sales</h1>
      <hr>
      <?php
      echo sales();
      ?>
      <hr>
    </div>
    <div id="startPaging" class="container mt-4" style="overflow: auto; min-height: 100%; box-shadow: 0px 0 5px 0 rgba(0, 0, 0, 0.2); background-color: #F0F8FF">
      <hr>
      <h1>Catalog</h1>
      <hr>
      <?php
      echo catalog();
      ?>
      <hr>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
  </body>
</html>
