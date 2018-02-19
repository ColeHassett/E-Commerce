<?php

  header("Content-type: text/css");

  $bgColor = '#F8F9FA';

  // if (isset($_POST['color'])) {
  //   switch ($_POST['color']) {
  //     case "light":
  //       $bgColor = '#F8F9FA';
  //       break;
  //     case "dark":
  //       $bgColor = '#343A40';
  //       break;
  //   }
  // }
  // else {
  //   die("fail");
  // }

?>

body {
  background-color: <?=$bgColor ?>;
}
