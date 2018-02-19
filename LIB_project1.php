<?php
  // Reusable functions called by other pages
  // Comments to describe input, outputs, and purpose
  // Included by all other pages

  // Function to create the navigation
  // Used on all pages of the site
  function navigation($page) {
    echo '<div class="container">';
      echo '<img class="float-left" src="assets/images/teacup.png" alt="Teacup" width="90px" height="90px"><h1 class="display-3 m-4">Teatopia</h1>';
      echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">';
          echo '<ul class="navbar-nav">';
              $active = ($page == 'index') ? 'nav-item active' : 'nav-item';
              echo '<li class="' . $active . '"><a class="nav-link" href="index.php">Home</a></li>';

              $active = ($page == 'cart') ? 'nav-item active' : 'nav-item';
              echo '<li class="' . $active . '"><a class="nav-link" href="cart.php">Cart</a></li>';

              $active = ($page == 'admin') ? 'nav-item active' : 'nav-item';
              echo '<li class="' . $active . '"><a class="nav-link" href="admin.php">Admin</a></li>';
          echo '</ul>';
      echo '</nav>';
    echo '</div>';
  }

  // Function to get all sale items and print them in the sale section
  function sales() {

    require_once("DB.class.php");

    $db = new DB();

    $salesArr = $db->getAllSales();

    foreach ($salesArr as $sale) {
      itemPrint($sale);
    }
  }

  // Function to print all catalog items in a paging scheme
  function catalog(){

    require_once("DB.class.php");

    $db = new DB();

    try {

      $getPage = getPage();

      $page = $getPage[0];
      $pages = $getPage[1];
      $limit = $getPage[2];
      $total = $getPage[3];

      // Calculate the offset for the query
      $offset = ($page - 1)  * $limit;

      // Some information to display to the user
      $start = $offset + 1;
      $end = min(($offset + $limit), $total);

      // The "back" link
      /////////// EDIT Maybe change icons to be easier to see/click
      $prevlink = ($page > 1) ? '<li class="page-item"><a href="?page=1#startPaging" class="page-link" title="First page">&laquo;</a></li> <li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '#startPaging" title="Previous page">&lsaquo;</a></li>' : '<li class="disabled page-link">&laquo;</li> <li class="disabled page-link">&lsaquo;</li>';

      // The "forward" link
      /////////// EDIT Maybe change icons to be easier to see/click
      $nextlink = ($page < $pages) ? '<li class="page-item"><a href="?page=' . ($page + 1) . '#startPaging" class="page-link" title="Next page">&rsaquo;</a></li> <li class="page-item"><a href="?page=' . $pages . '#startPaging"class="page-link" title="Last page">&raquo;</a></li>' : '<li class="disabled page-link">&rsaquo;</li> <li class="disabled page-link">&raquo;</li>';

      // Display the paging information
      echo '<ul class="pagination justify-content-center">', $prevlink, '<li class="page-link disabled"> Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' results </li>', $nextlink, ' </ul>';

      // Get data for current page
      $data = $db->pageQuery($limit, $offset);

      // Echo out the info of each item
      foreach ($data as $row) {
        itemPrint($row);
      }

      // Display the paging information again
      echo '<ul class="pagination justify-content-center">', $prevlink, '<li class="page-link disabled"> Page ', $page, ' of ', $pages, ' pages, displaying ', $start, '-', $end, ' of ', $total, ' results </li>', $nextlink, ' </ul>';

    } catch (Exception $e) {
        echo '<p>', $e->getMessage(), '</p>';
    }
  }

  // Function to print out all items in the cart and the total cost of the cart
  function cart() {
    require_once("DB.class.php");

    $db = new DB();

    $cart = $db->getCart();
    $totalCost = 0;

    foreach ($cart as $item) {
      $totalCost += doubleval($item['Cost']);
      echo '<div class="card row m-3 bg-light">';
        echo '<div class="card-block col-md-12">';
          echo '<h2>' . $item['Name'] . '</h2>';
          echo '<p>' . $item['Description'] . '</p>';
          echo '<p><h5 class="float-left mr-2">Total Price: </h5>$' . $item['Cost'] . '</p>';
          echo '<p><h5 class="float-left mr-2">Quantity: </h5>' . $item['Quantity'] . '</p>';
        echo '</div>';
      echo '</div>';
    }
    echo '<hr />';
    echo '<h1 class="float-right">Total Cost: $' . number_format($totalCost, 2, '.', '') . '</h1>';
    echo '<input class="btn btn-info mb-2 align-center" style="margin-left: 46%;margin-right: 46%;" type="submit" value="Empty Cart" onclick="empty()"/>';

  }

  // Function to generate the forms and info on the admin page
  function adminEdit() {
    require_once("DB.class.php");

    $db = new DB();

    $items = $db->getAllItems();

    echo '<select class="custom-select" onchange="populateEdit(this)">';
      echo '<option selected>Open this select menu</option>';
      foreach ($items as $item) {
        echo '<option value="'.$item['Image'].'">'.$item['Name'].'</option>';
      }
    echo '</select>';
  }

  // Function used to create and populate the form for editing an item
  function adminEditForm($itemName) {

    require_once("DB.class.php");

    $db = new DB();

    $item = '';

    if ($db->getItemDetails($itemName)) {
      $item = $db->getItemDetails($itemName);
    }
    else {
      return;
    }
    $id = $db->getCatalogID($itemName);

    echo '<form>';
      echo '<input type="hidden" id="itemID" value="'.$id[0].'" />';
      echo '<div class="form-group">';
        echo '<label for="Name">Name:</label>';
        echo '<input type="text" class="form-control" id="Name" value="'.$item[0]['Name'].'">';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label for="Description">Description:</label>';
        echo '<input type="text" class="form-control" id="Description" value="'.$item[0]['Description'].'">';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label for="Price">Price:</label>';
        echo '<div class="input-group">';
          echo '<span class="input-group-addon">$</span>';
          echo '<input type="text" class="form-control" id="Price" value="'.$item[0]['Price'].'">';
        echo '</div>';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label for="salePrice">Sale Price:</label>';
        echo '<div class="input-group">';
          echo '<span class="input-group-addon">$</span>';
          echo '<input type="text" class="form-control" id="salePrice"value="'.$item[0]['SalePrice'].'">';
        echo '</div>';
        echo '<small id="salePriceText" class="form-text text-muted">Enter "0" if item is not on sale</small>';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label for="Quantity">Quantity:</label>';
        echo '<input type="text" class="form-control" id="Quantity" value="'.$item[0]['Quantity'].'">';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label>Image Name:</label>';
        echo '<div class="input-group">';
          echo '<input type="text" class="form-control" id="Image" value="'.$item[0]['Image'].'">';
          echo '<span class="input-group-addon">.jpg</span>';
        echo '</div>';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label for="Password">Password:</label>';
        echo '<input type="password" class="form-control" id="Password" placeholder="Enter password">';
      echo '</div>';
    echo '</form>';
    echo '<input class="btn btn-info mt-3 mb-2" style="margin-left: 46%;margin-right: 46%;" type="submit" value="Edit Item" onclick="callEditItem(document.forms[0].Name.value, document.forms[0].Description.value, document.forms[0].Price.value, document.forms[0].salePrice.value, document.forms[0].Quantity.value, document.forms[0].Image.value, document.forms[0].Password.value, document.forms[0].itemID.value)"/>';
    echo '<hr />';
  }

  // Function to update an item in the DB
  // Takes in item info consisting of name, description, price, saleprice, quantity, image, and password
  // Passes each item through a check to make sure it contains valid input
  // Calls the update function to update the item in the DB
  function editItem($itemInfo) {
    require_once("DB.class.php");

    $db = new DB();

    $name = $itemInfo[0];
    $desc = $itemInfo[1];
    $price = $itemInfo[2];
    $sale = $itemInfo[3];
    $quantity = $itemInfo[4];
    $image = $itemInfo[5];
    $pass = $itemInfo[6];
    $id = $itemInfo[7];
    $saleCount = $db->getSaleCount();
    $saleItems = $db->getAllSales();
    $onSale = false;
    $salePass = false;

    foreach($saleItems as $item) {
      if ($item['CatalogID'] == $id) {
        $onSale = true;
      }
    }

    if ($pass == 'project1H') {
      if ($onSale) {
        if ($sale == '0.00' && $saleCount <= 3) {
          echo 'Error: There cannot be less than 3 items on sale.';
        }
        else {
          $salePass = true;
        }
      }
      else {
        if ($sale !== '0.00' && $saleCount >= 5) {
          echo 'Error: There cannot be more than 5 items on sale.';
        }
        else {
          $salePass = true;
        }
      }
    }
    else {
      echo 'Error: Invalid Password';
    }

    if ($salePass) {
      if (!ctype_alpha(str_replace(' ', '', $name))) {
        echo 'Error: Name can only contain letters and spaces';
      }
      elseif (!preg_match('/^[a-zA-Z0-9-,.!? ]*$/', $desc)) {
        echo 'Error: Description can only contain alphanumeric characters, punctuation, and hyphens';
      }
      elseif (!preg_match('/^[0-9]+(?:\.[0-9]{2}){0,1}$/', $price)) {
        echo 'Error: Price must be a number with two decimal places.';
      }
      elseif (!preg_match('/^[0-9]+(?:\.[0-9]{2}){0,1}$/', $sale)) {
        echo 'Error: Sale price must be a number with two decimal places.';
      }
      elseif (!ctype_digit($quantity)) {
        echo 'Error: Quantity must be an integer.';
      }
      elseif (!ctype_alpha($image)) {
        echo 'Error: Image name must only contain letters.';
      }
      else {
        $db->updateCatalogItem($id, $itemInfo);
        $db->updateCart($id, $itemInfo);
        if ($onSale && $sale !== '0.00') {
          $db->updateSaleItem($id, $itemInfo);
        }
        elseif ($onSale && $sale == '0.00') {
          $db->removeSaleItem($id);
        }
        elseif (!$onSale && $sale !== '0.00') {
          $db->addSaleItem($id, $itemInfo);
        }
        echo "Item Updated";
      }
    }
  }

  // Function to create the form used to add an item to the catalog
  function adminAddForm(){
    echo '<form>';
      echo '<div class="form-group">';
        echo '<label>Name:</label>';
        echo '<input type="text" class="form-control" id="NameAdd" placeholder="Enter name">';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label>Description:</label>';
        echo '<input type="text" class="form-control" id="DescriptionAdd" placeholder="Enter description">';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label>Price:</label>';
        echo '<div class="input-group">';
          echo '<span class="input-group-addon">$</span>';
          echo '<input type="text" class="form-control" id="PriceAdd" placeholder="Enter price (Ex: 8.50)">';
        echo '</div>';
        echo '<small class="form-text text-muted">If you would like to put this item on sale, add it as not on sale, then you may update it with the sale price.</small>';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label>Quantity:</label>';
        echo '<input type="text" class="form-control" id="QuantityAdd" placeholder="Enter quantity">';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label>Image Name:</label>';
        echo '<div class="input-group">';
          echo '<input type="text" class="form-control" id="ImageAdd" placeholder="Enter image name">';
          echo '<span class="input-group-addon">.jpg</span>';
        echo '</div>';
      echo '</div>';
      echo '<div class="form-group">';
        echo '<label>Password:</label>';
        echo '<input type="password" class="form-control" id="PasswordAdd" placeholder="Enter password">';
      echo '</div>';
    echo '</form>';
    echo '<input class="btn btn-info mt-3 mb-2" style="margin-left: 46%;margin-right: 46%;" type="submit" value="Add Item" onclick="callAddItem(document.forms[0].NameAdd.value, document.forms[0].DescriptionAdd.value, document.forms[0].PriceAdd.value, document.forms[0].QuantityAdd.value, document.forms[0].ImageAdd.value, document.forms[0].PasswordAdd.value)"/>';
  }

  // Function to add an item into the catalog or catalog and sales table
  // takes in an array of the items information to be added
  function addItem($itemInfo) {
    require_once("DB.class.php");

    $db = new DB();

    $name = $itemInfo[0];
    $desc = $itemInfo[1];
    $price = $itemInfo[2];
    $quantity = $itemInfo[3];
    $image = $itemInfo[4];
    $pass = $itemInfo[5];
    $allItems = $db->getAllItems();
    $exists = false;

    foreach($allItems as $item) {
      if ($item['Name'] == $name) {
        $exists = "name";
      }
      elseif ($item['Description'] == $desc) {
        $exists = "description";
      }
      elseif ($item['Image'] == $desc) {
        $exists = "image";
      }
    }

    if ($pass == 'project1H') {
      if ($exists == "name") {
        echo 'Error: An item with this name already exists';
      }
      elseif ($exists == "description") {
        echo 'Error: An item with this description already exists';
      }
      elseif ($exists == "image") {
        echo 'Error: An item with this image already exists';
      }
      else {
        if (!ctype_alpha(str_replace(' ', '', $name))) {
          echo 'Error: Name can only contain letters and spaces';
        }
        elseif (!preg_match('/^[a-zA-Z0-9-,.!? ]*$/', $desc)) {
          echo 'Error: Description can only contain alphanumeric characters, punctuation, and hyphens';
        }
        elseif (!preg_match('/^[0-9]+(?:\.[0-9]{2}){0,1}$/', $price)) {
          echo 'Error: Price must be a number with two decimal places.';
        }
        elseif (!ctype_digit($quantity)) {
          echo 'Error: Quantity must be an integer.';
        }
        elseif (!ctype_alpha($image)) {
          echo 'Error: Image name must only contain letters.';
        }
        else {
          $db->addCatalogItem($itemInfo);
          echo "Item Added";
        }
      }
    }
    else {
      echo 'Error: Invalid Password';
    }
  }

  // Function to print out items with all their info
  // Checks to see if an item is on sale or not
  function itemPrint($item) {
    $page = getPage()[0];
    echo '<div class="card row m-3 bg-white">';
      echo '<div id="'.$item['Image'].'Div" class="card-block col-md-12">';
        echo '<img class="float-left mr-4" height="200px" width="200px" src="assets/images/' . $item['Image'] . '.jpg" alt="' . $item['Image'] . '">';
        echo '<h1 class="card-title">' . $item['Name'] . '</h1>';
        echo '<input class="btn btn-info float-right" style="margin-top: 80px;" type="submit" name="' . $item['Image'] . 'Btn" value="Add to Cart" onclick="clicked(this)"/>';
        echo '<h5>' . $item['Description'] . '</h5>';
        if ($item['SalePrice'] !== '0.00') {
          echo '<p><h5 class="float-left mr-2">Price: </h5><strike>$' . $item['Price'] . '</strike>  $' . $item['SalePrice'] . '</p>';
        }
        else {
          echo '<p><h5 class="float-left mr-2">Price(2 oz.): </h5>$' . $item['Price'] . '</p>';
        }
        echo '<p><h5 class="float-left mr-2">Available: </h5>' . $item['Quantity'] . '</p>';
      echo '</div>';
    echo '</div>';
  }

  // Function to add an item to your cart
  function addCart($name) {
    require_once("DB.class.php");

    $db = new DB();

    $item = $db->getItemDetails($name);
    if ($item[0]['Quantity'] == 0) {
      echo 'There are no more of this item left.';
    }
    else {
      $id = $db->getCatalogID($item[0]['Image']);
      $db->pushToCart($item[0], $id[0]);
    }

  }

  // Function to get info about current paging scheme
  // Returns current page, total pages, page limit, and total # of non sale items in catalog table
  function getPage() {

    require_once("DB.class.php");

    $db = new DB();

    $total = $db->getCatalogCount();

    // How many items to list per page
    $limit = 5;

    // How many pages will there be
    $pages = ceil($total / $limit);

    // What page are we currently on?
    $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
        'options' => array(
            'default'   => 1,
            'min_range' => 1,
        ),
    )));

    $data = [$page, $pages, $limit, $total];

    return $data;
  }

  // Function to empty the cart
  function emptyCart() {
    require_once("DB.class.php");

    $db = new DB();

    $db->emptyCartDB();
  }

  // Function to allow the user to change between light and dark mode
  function colorChanger() {
    echo '<div class="btn-group btn-toggle float-left fixed-top">';
      echo '<button class="btn btn-sm btn-default" style="cursor: pointer;" onclick="callChangeColor(\'light\')">Light</button>';
      echo '<button class="btn btn-sm btn-default" style="cursor: pointer;" onclick="callChangeColor(\'dark\')">Dark</button>';
    echo '</div>';
  }

 ?>
