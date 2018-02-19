function clicked(button) {
  $.ajax({
      url: "ajax.php" ,
      type: "post",
      data: { item: button.name.substring(0, button.name.length-3) },
      success : function(result) {
        console.log(result);
        location.reload();
      }
  });
}
// Unused attempt to create light/dark mode button
function callChangeColor(color) {
  // $.ajax({
  //     url: "assets/style.php" ,
  //     type: "post",
  //     data: { color: color },
  //     success : function(result) {
  //       console.log(result);
  //       // location.reload();
  //     }
  // });
  switch (color) {
    case "dark":
      document.body.style.backgroundColor = "#343A40";

    case "light":
      document.body.style.backgroundColor = "#F8F9FA";

  }
}
function empty() {
  $.ajax({
      url: "ajax.php" ,
      type: "post",
      data: { empty: 'empty'},
      success : function() {
        location.reload();
      }
  });
}
function populateEdit(item) {
  $.ajax({
      url: "ajax.php" ,
      type: "post",
      data: { edit: item[item.selectedIndex].value},
      success : function(result) {
        $('#adminEditForm').html(result);
      }
  });
}
function callEditItem(name, desc, price, sale, quantity, image, pass, id) {
  var itemInfo = [name, desc, price, sale, quantity, image, pass, id];
  $.ajax({
      url: "ajax.php" ,
      type: "post",
      data: { editItem: itemInfo},
      success : function(result) {
        var substr = result.substring(0, 5);
        if (substr == "Error") {
          var html = '<div class="container alert alert-danger mt-4">'+result+'</div>';
          $('#successFailEdit').html(html);
        }
        else {
          $("#resetterEdit").load(" #editForm");
          var html = '<div class="container alert alert-success mt-4">'+result+'</div>';
          $('#successFailEdit').html(html);
        }
      }
  });
}
function callAddItem(name, desc, price, quantity, image, pass) {
  var itemInfoAdd = [name, desc, price, quantity, image, pass];
  $.ajax({
      url: "ajax.php" ,
      type: "post",
      data: { addItem: itemInfoAdd},
      success : function(result) {
        var substr = result.substring(0, 5);
        if (substr == "Error") {
          var html = '<div class="container alert alert-danger mt-4">'+result+'</div>';
          $('#successFailAdd').html(html);
        }
        else {
          $("#resetter").load(" #addForm");
          var html = '<div class="container alert alert-success mt-4">'+result+'</div>';
          $('#successFailAdd').html(html);
        }
      }
  });
}
