<?php
  // Database access logic
  // Only use paramterized queries

  class DB {

    // Connection variable used for interacting with database
    private $dbh;

    // Constructor to create connection to database
    function __construct() {
        try {
          $this->dbh = new PDO("mysql:host={$_SERVER['DB_SERVER']};dbname={$_SERVER['DB']}",
            $_SERVER['DB_USER'],
            $_SERVER['DB_PASSWORD']);

          // Change error reporting
          $this->dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        } // try
        catch (PDOException $e) {
          echo 'DB Error';
        } // catch

    } //contructor

    // Function to get all sale items in the sales table
    // Returns array of sale items array of information
    function getAllSales() {

      try {
        $data = array();
        $stmt = $this->dbh->prepare("select Name, Image, Description, Price, SalePrice, Quantity, CatalogID from Sales");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
          $data[] = $row;
        } // while

        return $data;

      } // try
      catch (PDOException $e) {
        echo "Failure Getting Sales";
        die();
      } // catch
    } // getAllSales

    function getCart() {

      try {
        $data = array();
        $stmt = $this->dbh->prepare("select Name, Description, Quantity, Cost from Cart");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
          $data[] = $row;
        } // while

        return $data;

      } // try
      catch (PDOException $e) {
        echo "Failure Getting Cart";
        die();
      } // catch
    }

    function updateCart($id, $item) {
      $exists = false;
      try {
        $data = array();
        $stmt = $this->dbh->prepare("select Quantity, CatalogID from Cart");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
          $data[] = $row;
        } // while

        foreach ($data as $dataItem) {
          if ($dataItem['CatalogID'] == $id) {
            $exists = true;
            break;
          }
        }

        if ($exists) {
          try {
            $cost = "";
            if ($item[3] !== '0.00') {
              $cost = $item[3] * $dataItem['Quantity'];
            }
            else {
              $cost = $item[2] * $dataItem['Quantity'];
            }

            $stmt = $this->dbh->prepare("update Cart set Name=:name, Description=:desc, Cost=:price where CatalogID = :id");
            $stmt->bindParam(":name",$item[0],PDO::PARAM_STR);
            $stmt->bindParam(":desc",$item[1],PDO::PARAM_STR);
            $stmt->bindParam(":price",$cost,PDO::PARAM_STR);
            $stmt->bindParam(":id",$id,PDO::PARAM_STR);
            $stmt->execute();
          } // try
          catch (PDOException $e) {
            echo "Failure Updating Cart";
            die();
          } // catch
        }

      } // try
      catch (PDOException $e) {
        echo "Failure Getting Cart";
        die();
      } // catch
    }

    // Function to get the number of items in the catalog that are not on sale
    // Returns the number of non sale items in the catalog table
    function getCatalogCount() {

      try {
        $total = "";
        $stmt = $this->dbh->prepare("select COUNT(*) from Catalog where SalePrice = '0.00'");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
          $total = $row;
        } // while

        return $total[0];

      } // try
      catch (PDOException $e) {
        echo "Failure Getting Catalog Count";
        die();
      } // catch
    }

    // Function to get the number of items in the sale table
    // Returns the number of sale items
    function getSaleCount() {
      try {
        $total = "";
        $stmt = $this->dbh->prepare("select COUNT(*) from Sales");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
          $total = $row;
        } // while

        return $total[0];

      } // try
      catch (PDOException $e) {
        echo "Failure Getting Sale Count";
        die();
      } // catch
    }

    // Function to get all the information from the catalog table for non sale items
    // Input the limit and the offset for use with paging
    //Limit is the number of items per page, offset is the item to start on for the page
    // Returns array of items array of information
    function pageQuery($limit, $offset) {

      try {
        $data = array();
        $stmt = $this->dbh->prepare("select Name, Image, Description, Price, Quantity, SalePrice from Catalog where SalePrice = '0.000' order by Name limit :limit offset :offset");
        $stmt->bindParam(":limit",$limit,PDO::PARAM_INT);
        $stmt->bindParam(":offset",$offset,PDO::PARAM_INT);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
          $data[] = $row;
        } // while

        return $data;

      } // try
      catch (PDOException $e) {
        echo "Failure Performing Page Query";
        die();
      } // catch
    }

    // Function to get all details of an item from the Catalog table
    // Returns an array of all the info related to the item requested
    function getItemDetails($image) {

      try {
        $data = array();
        $stmt = $this->dbh->prepare("select Name, Image, Description, Price, Quantity, SalePrice from Catalog where Image = :image");
        $stmt->bindParam(":image",$image,PDO::PARAM_STR);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
          $data[] = $row;
        } // while

        return $data;

      } // try
      catch (PDOException $e) {
        echo "Failure Getting Item Details";
        die();
      } // catch
    }

    // Function to do all the updating of the cart
    // If the item is already in the cart, the cart item is incremented
    // Otherwise, a new cart item is created
    // 1 is removed from quantity every time an item is added
    // If item is on sale, removes 1 from both sale and catalog tables
    function pushToCart($item, $id) {

      $cartQuantity = "";

      try {
        $data = array();
        $stmt = $this->dbh->prepare("select Quantity from Cart where Name = :name");
        $stmt->bindParam(":name",$item['Name'],PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
          while ($row = $stmt->fetch()) {
            $data[] = $row;
          } // while

          $cartQuantity = (int)$data[0]['Quantity'] + 1;
          $totalCost = $cartQuantity * $item['Price'];

          try {
            $stmt = $this->dbh->prepare("update Cart set Quantity=:quantity, Cost=:cost where Name = :name");
            $stmt->bindParam(":quantity",$cartQuantity,PDO::PARAM_INT);
            $stmt->bindParam(":cost",$totalCost,PDO::PARAM_STR);
            $stmt->bindParam(":name",$item['Name'],PDO::PARAM_STR);
            $stmt->execute();

          } // try
          catch (PDOException $e) {
            echo "Failure Pushing to Cart 2";
            die();
          } // catch

        } // if
        else {
          $totalCost = '';
          $one = 1;
          if ($item['SalePrice'] == '0.00') {
            $totalCost = 1 * (int)$item['Price'];
          }
          else {
            $totalCost = 1 * (int)$item['SalePrice'];
          }

          try {
            $stmt = $this->dbh->prepare("insert into Cart (Name, Description, Quantity, Cost, CatalogID) values (:name, :desc, :quantity, :cost, :id)");
            $stmt->bindParam(":name",$item['Name'],PDO::PARAM_STR);
            $stmt->bindParam(":desc",$item['Description'],PDO::PARAM_STR);
            $stmt->bindParam(":quantity",$one,PDO::PARAM_INT);
            $stmt->bindParam(":cost",$totalCost,PDO::PARAM_STR);
            $stmt->bindParam(":id",$id,PDO::PARAM_INT);
            $stmt->execute();
          } // try
          catch (PDOException $e) {
            echo "Failure Pushing to Cart 3";
            die();
          } // catch

        } // else

      } // try
      catch (PDOException $e) {
        echo "Failure Pushing to Cart 1";
        die();
      } // catch

      $newQuant = $item['Quantity'] - 1;
      if ($item['SalePrice'] !== '0.00') {
        try {
          $stmt = $this->dbh->prepare("update Sales set Quantity=:quantity where Name = :name");
          $stmt->bindParam(":quantity",$newQuant,PDO::PARAM_INT);
          $stmt->bindParam(":name",$item['Name'],PDO::PARAM_STR);
          $stmt->execute();
        } // try
        catch (PDOException $e) {
          echo "Failure Pushing to Cart 4";
          die();
        } // catch

        try {
          $stmt = $this->dbh->prepare("update Catalog set Quantity=:quantity where Name = :name");
          $stmt->bindParam(":quantity",$newQuant,PDO::PARAM_INT);
          $stmt->bindParam(":name",$item['Name'],PDO::PARAM_STR);
          $stmt->execute();
        } // try
        catch (PDOException $e) {
          echo "Failure Pushing to Cart 5";
          die();
        } // catch
      }
      else {
        try {
          $stmt = $this->dbh->prepare("update Catalog set Quantity=:quantity where Name = :name");
          $stmt->bindParam(":quantity",$newQuant,PDO::PARAM_INT);
          $stmt->bindParam(":name",$item['Name'],PDO::PARAM_STR);
          $stmt->execute();
        } // try
        catch (PDOException $e) {
          echo "Failure Pushing to Cart 6";
          die();
        } // catch
      }

    }

    // Function to empty the cart
    // Uses simple delete query to empty entire cart table
    function emptyCartDB() {
      try {
        $stmt = $this->dbh->prepare("delete from Cart");
        $stmt->execute();
      } // try
      catch (PDOException $e) {
        echo "Failure Delete Cart";
        die();
      } // catch
    }

    // Function to select all infomation of each item in the catalog table
    // Returns a 2d array of each item and its info
    function getAllItems() {
      try {
        $data = array();
        $stmt = $this->dbh->prepare("select Name, Image, Description, Price, Quantity, SalePrice from Catalog");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
          $data[] = $row;
        } // while

        return $data;

      } // try
      catch (PDOException $e) {
        echo "Failure Getting All Items";
        die();
      } // catch
    }

    // Function to get the id of an item in the catalog
    // returns and array containing the id of the item's image name passed in
    function getCatalogID($item) {
      try {
        $data = "";
        $stmt = $this->dbh->prepare("select ID from Catalog where Image=:image");
        $stmt->bindParam(":image",$item,PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        $data = $row;

        return $data;

      } // try
      catch (PDOException $e) {
        echo "Failure Getting All Items";
        die();
      } // catch
    }

    // Function to update an item in the catalog table
    // Takes in an items information and the id associated with that item
    function updateCatalogItem($id, $item) {
      try {
        $stmt = $this->dbh->prepare("update Catalog set Name=:name, Description=:desc, Price=:price, SalePrice=:sale, Quantity=:quantity, Image=:image where ID = :id");
        $stmt->bindParam(":name",$item[0],PDO::PARAM_STR);
        $stmt->bindParam(":desc",$item[1],PDO::PARAM_STR);
        $stmt->bindParam(":price",$item[2],PDO::PARAM_STR);
        $stmt->bindParam(":sale",$item[3],PDO::PARAM_STR);
        $stmt->bindParam(":quantity",$item[4],PDO::PARAM_INT);
        $stmt->bindParam(":image",$item[5],PDO::PARAM_STR);
        $stmt->bindParam(":id",$id,PDO::PARAM_STR);
        $stmt->execute();
      } // try
      catch (PDOException $e) {
        echo "Failure Updating Catalog Item";
        die();
      } // catch
    }

    // Function to update an item in the sales table
    // Takes in an items information and the id associated with that item
    function updateSaleItem($id, $item) {
      try {
        $stmt = $this->dbh->prepare("update Sales set Name=:name, Description=:desc, Price=:price, SalePrice=:sale, Quantity=:quantity, Image=:image where CatalogID=:id");
        $stmt->bindParam(":name",$item[0],PDO::PARAM_STR);
        $stmt->bindParam(":desc",$item[1],PDO::PARAM_STR);
        $stmt->bindParam(":price",$item[2],PDO::PARAM_STR);
        $stmt->bindParam(":sale",$item[3],PDO::PARAM_STR);
        $stmt->bindParam(":quantity",$item[4],PDO::PARAM_INT);
        $stmt->bindParam(":image",$item[5],PDO::PARAM_STR);
        $stmt->bindParam(":id",$id,PDO::PARAM_STR);
        $stmt->execute();
      } // try
      catch (PDOException $e) {
        echo "Failure Updating Sale Item";
        die();
      } // catch
    }

    // Function to add an item to the sales table
    // Takes in the item info to be added and the id associated with the item
    function addSaleItem($id, $item) {
      try {
        $stmt = $this->dbh->prepare("insert into Sales (Name, Description, Price, SalePrice, Quantity, Image, CatalogID) values (:name, :desc, :price, :sale, :quantity, :image, :id)");
        $stmt->bindParam(":name",$item[0],PDO::PARAM_STR);
        $stmt->bindParam(":desc",$item[1],PDO::PARAM_STR);
        $stmt->bindParam(":price",$item[2],PDO::PARAM_STR);
        $stmt->bindParam(":sale",$item[3],PDO::PARAM_STR);
        $stmt->bindParam(":quantity",$item[4],PDO::PARAM_INT);
        $stmt->bindParam(":image",$item[5],PDO::PARAM_STR);
        $stmt->bindParam(":id",$id,PDO::PARAM_STR);
        $stmt->execute();
      } // try
      catch (PDOException $e) {
        echo "Failure Adding Sale Item";
        die();
      } // catch
    }

    // Function to remove an item from the sales table
    // Takes in the id of the item to remove
    function removeSaleItem($id) {
      try {
        $stmt = $this->dbh->prepare("delete from Sales where CatalogID=:id");
        $stmt->bindParam(":id",$id,PDO::PARAM_STR);
        $stmt->execute();
      } // try
      catch (PDOException $e) {
        echo "Failure Removing Sale Item";
        die();
      } // catch
    }

    // Function to add an item to the catalog
    // Takes in an array of the items info
    function addCatalogItem($item) {
      $saleDefault = '0.00';
      try {
        $stmt = $this->dbh->prepare("insert into Catalog (Name, Description, Price, SalePrice, Quantity, Image) values (:name, :desc, :price, :sale, :quantity, :image)");
        $stmt->bindParam(":name",$item[0],PDO::PARAM_STR);
        $stmt->bindParam(":desc",$item[1],PDO::PARAM_STR);
        $stmt->bindParam(":price",$item[2],PDO::PARAM_STR);
        $stmt->bindParam(":sale",$saleDefault,PDO::PARAM_STR);
        $stmt->bindParam(":quantity",$item[3],PDO::PARAM_INT);
        $stmt->bindParam(":image",$item[4],PDO::PARAM_STR);
        $stmt->execute();
      } // try
      catch (PDOException $e) {
        echo "Failure Adding Catalog Item";
        die();
      } // catch
    }

  }



 ?>
