<!--
*    This is th index page and handles all login/create account requests, it also
*    hosts the admin, staff and customer panels.
*    All content on the page is stored in the $content variable and dispalyed when
*    pagelayout.php is required
-->

<!--
*     This section sets the $title variable and requires the databse connnection
*     file and starts the session
 -->
<?php $Title = 'Ed\'s Electronics';
require 'dbconnect.php';
session_start();
?>


<!--
*     This section is to set the open graph tags for the facebook share scraper
*
-->
<?php
$metaurl = '<meta property="og:url"                content="document.URL;" />';
$metatype='<meta property="og:type"               content="article" />';
$metatitle = '<meta property="og:title"              content="Ed/s Electronics" />';
$metadescription='<meta property="og:description"        content="Cheapest electronics around!" />';
$metaimage ='<meta property="og:image"              content="/images/oled.jpg" />';
?>



<!--
*      This is the main section of the page, here is where the login and panels are layed out
*      More in depth comments will be detailed by the seperate code blocks
*
-->
<?php
/*
*       This hablock handles the information inserted from the login form
*       It quiries the database for the provided username and then compares
*       the provided password to the hashed password stored in the database
*       via the password_verify function.
*       If the result of the verify is true then various session variables are
*       set for later use accross the site.
*       If the verify fails the user is asked to try again or create an account
*       The default logins are:
*       admin:admin
*       staff:staff
*       user:user
*/
if(isset($_POST['login'])){
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
        $criteria = [
        'username' => $_POST['username']
        ];
        $stmt->execute($criteria);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(password_verify($_POST['password'], $result['password'])){
       $_SESSION['loggedin'] = true;
       $_SESSION['username'] = $_POST['username'];
       $_SESSION['is_staff'] = $result['is_staff'];
       $_SESSION['is_admin'] = $result['is_admin'];
       $_SESSION['id'] = $result['idusers'];
       $_SESSION['address'] = $result['address'];
       $_SESSION['phone'] = $result['phone'];
       $_SESSION['email'] = $result['email'];
      // echo "valid";
       //var_dump($_SESSION);
       session_write_close();
     }
        else {
          $content ='
          <h1>Welcome to Ed\'s Electronics</h1>

          <p>We stock a large variety of electrical goods including phones, tvs, computers and games. Everything comes with at least a one year guarantee and free next day delivery.</p>

          <hr />
          <form name="searchform" action="search.php" method="post">
          <input type="text" name="term" placeholder="Enter search term" />
          <input type="submit" name="search" value="Search" />
          </form>
          <hr />

          <h2>Login</h2>
          <p>Incorrect password or no account existed. Please try again or create an account.</p>

          <form action="index.php" method="post">
             <input type="text" name="username" placeholder="enter username" />
             <input type="password" name="password" placeholder="enter password" />
             <input type="submit" name="login" value="Login" />
          </form>
          <hr />
          <p>Don\'t have an account? Use the button below to create one.</p>
          <form action="index.php" method="post">
             <label>Create account:</label><input type="submit" name="create" value="Create" />
          </form>
          ';
        }
    }


/*
*       This block displays the customer panel if the following session variables are set
*       loggedin = true, is_staff = false and is_admin = false
*       Here the customer can choose to edit their stored details, or view the reviews they have left
*/
/*
*   This block handles the logout button
*/
elseif (isset($_POST['Logout'])) {
  session_unset();
  header('Location: index.php');
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true &&   $_SESSION['is_staff'] == false &&$_SESSION['is_admin'] ==false){
  $content = '
  			<h1>Welcome '. $_SESSION['username'] .' </h1>
        <hr />
        <form name="searchform" action="search.php" method="post">
        <input type="text" name="term" placeholder="Enter search term" />
        <input type="submit" name="search" value="Search" />
        </form>
        <hr />
        <p>Account controls</p>
        <form action="reviews.php?user='.$_SESSION['username'].'" method="post">
        <input type="submit" name="reviews" value="My reviews" />
        </form>
        <form action="index.php" method="post">
        <input type="submit" name="Logout" value="Logout"/>
        </form>
        ';

      }

/*
*   This creates the add category form for the admin and staff panels
*/
elseif (isset($_POST['add_category'])) {
  $content ='
  <form action="index.php" method="post">
  <input type="text" name="category" required="" placeholder="enter category name">
  <input type="submit" name="add_category_handler" value="Add Category">
  </form>
  ';

}

/*
*       This handles the logic for the add category form
*/
elseif (isset($_POST['add_category_handler'])) {
  try {
    $category = $pdo->prepare('INSERT INTO categories (category_name) VALUES (:category_name)');
    $criteria = [
    'category_name' => $_POST['category']
  ];
    $category->execute($criteria);
    $content ='Succesfully added category
    <button type="button" onclick="window.location=\'index.php\';">Back</button>
    ';

  } catch (\Exception $e) {
    $category->execute($criteria);
    $content ='Error adding category, Check connection and inputs.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>
    ';
  }
}


/*
*   This creates category selection form to edit
*/
elseif (isset($_POST['edit_category'])) {
  $categories = $pdo->prepare('SELECT * FROM categories');
  $categories ->execute();
  $content ='
  <button type="button" onclick="window.location=\'index.php\';">Back</button>
  <form action="index.php" method="post">
  <select name="category">
  ';
  foreach ($categories as $cat ) {
    $content.= '<option value="'.$cat['idcategories'].'" required="">' . $cat['category_name'] .'</option>';
  }
  $content .='
  </select>
  <input type="submit" name="edit_category_form" value="submit" />
  </form>
  ';

}

/*
*       This creates the from to edit the category and save
*/
elseif (isset($_POST['edit_category_form'])) {

  $categories = $pdo->prepare('SELECT * FROM categories WHERE idcategories = '.$_POST['category'].'');
  $categories ->execute();
  $cat = $categories->fetch(PDO::FETCH_ASSOC);
  $content ='
  <button type="button" onclick="window.location=\'index.php\';">Back</button>
  <form action="index.php" method="post">
  <input type="hidden" name="idcategories" value="'.$cat['idcategories'].'" />
  <input type="text" required="" name="category_name" value="'.$cat['category_name'].'" />
  <input type="submit" name="edit_category_handler" value="Save" />
  <input type="submit" name="delete_category" value="Delete" />
  </form>
  ';

}

/*
*       This handles the logic for the edit category form
*/
elseif (isset($_POST['edit_category_handler'])) {
  try {
    $category = $pdo->prepare('UPDATE categories
      SET category_name = :category_name WHERE idcategories=:idcategories');
      $criteria = [
      'category_name' => $_POST['category_name'],
      'idcategories' => $_POST['idcategories']
    ];

    $category->execute($criteria);
    $content ='Category edit succesfull.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';

  } catch (\Exception $e) {
    $content='Editing category failed. Check connection and inputs.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  }


}

/*
*       This handles the logic for the delete category form
*/
elseif (isset($_POST['delete_category'])) {
  try {
    $stmt = $pdo->prepare('DELETE FROM categories WHERE idcategories = :idcategories');
    $stmt->execute(['idcategories' => $_POST['idcategories']]);
    $content ='Category delete succesfull.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  } catch (\Exception $e) {
    $content='Deleting category failed. Check connection and inputs.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  }

}

/*
*       This creates the add product form
*/

elseif (isset($_POST['addprod'])){
  $category = $pdo->prepare('SELECT * FROM categories');
  $category->execute();
  $content ='
  <button type="button" onclick="window.location=\'index.php\';">Back</button>
  <hr />
  <form action="index.php" method="post" enctype="multipart/form-data">
     <label>Product Name:</label>
     <input type="text" name="prodname" placeholder="enter product name" required="" />
     <label>Product Description:</label>
     <input type="textarea" name="proddesc" placeholder="enter description" required="" />
     <label>Price:</label>
     <input type="text" name="prodprice" placeholder="enter price" required="" />
     <label>Featured:</label>
     <select name="featured">
     <option value="0">No</option>
     <option value="1">Yes</option>
     </select>
     <label>Category:</label>
     <select name="cat">
     ';
     foreach ($category as $cat ) {
       $content.= '<option value="'.$cat['idcategories'].'" required="">' . $cat['category_name'] .'</option>';
     }
     $content .='
     </select>
     <label>Product Image:</label>
     <input type="file" name="prodimage" required="" accept="*/image" />
     <input type="hidden" name="added_by" value="'.$_SESSION['username'].'"  />
     <input type="submit" name="submitprod" value="Add product" />
  </form>
  ';
}

/*
*   This is the code to add the new product from the add product form to the database
*/
elseif (isset($_POST['submitprod'])) {
  try {
    $date = date("Y-m-d");
    $tmpName  = $_FILES['prodimage']['tmp_name'];
    $fp = fopen($tmpName, 'rb'); // read binary
    $stmt = $pdo->prepare("INSERT INTO products (product_name, product_description,
      product_price, product_category, product_image, date_added, added_by, featured) VALUES ( ?,?,?,?,?,?,?,? )");
    $stmt->bindParam(1, $_POST['prodname']);
    $stmt->bindParam(2, $_POST['proddesc']);
    $stmt->bindParam(3, $_POST['prodprice']);
    $stmt->bindParam(4, $_POST['cat']);
    $stmt->bindParam(5, $fp, PDO::PARAM_LOB);
    $stmt->bindParam(6, $date);
    $stmt->bindParam(7, $_POST['added_by']);
    $stmt->bindParam(8, $_POST['featured']);
    $pdo->errorInfo();
    $stmt->execute();
    $content ='Adding Product succesful.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  } catch (\Exception $e) {
    $content ='Adding Product failed. Check connection and inputs.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  }

}

/*
*   This creates the add staff form for the admin panel
*/
elseif (isset($_POST['addstaff'])){
  $content ='
  <button type="button" onclick="window.location=\'index.php\';">Back</button>
  <hr />
  <form action="index.php" method="post">
     <label>User name:</label>
     <input type="text" name="username" placeholder="enter username" required="" />
     <label>Password:</label>
     <input type="password" name="password" placeholder="enter password" required="" />
     <label>Email:</label>
     <input type="email" name="email" placeholder="enter email"/>
     <label>Address:</label>
     <input type="textarea" name="address" placeholder="enter address" required="" />
     <label>Phone:</label>
     <input type="text" name="phone" placeholder="enter phone number"/>
     <input type="hidden" name="is_staff" value="1" />
     <label>Select if admin:</label>
     <select name="is_admin">
     <option value="0">No</option>
     <option value="1">Yes</option>
     </select>
     <input type="submit" name="addstaffform" value="submit" />
  </form>
  ';
}

/*
*   This block creates the account in the database once the user has clicked submit on the create
*   account form
*/
elseif(isset($_POST['addstaffform'])){
  try {
    $password = $_POST['password'];
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password, email, address, phone, is_staff, is_admin)
    VALUES (:username, :password, :email, :address, :phone, :is_staff, :is_admin)');
    $criteria = [
    'username' => $_POST['username'],
    'password' => $hashed,
    'email' => $_POST['email'],
    'address' => $_POST['address'],
    'phone' => $_POST['phone'],
    'is_staff' => $_POST['is_staff'],
    'is_admin' => $_POST['is_admin']
    ];
    $stmt->execute($criteria);
    $content ='Adding staff member succesful.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';

  } catch (\Exception $e) {
    $content ='Adding staff member failed.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';

  }

}


/*
*     This allows the admin or staff to select a product to edit
*/
elseif (isset($_POST['editprod'])) {
  $products = $pdo->prepare('SELECT * FROM products');
  $products ->execute();
  $content ='
  <form action="index.php" method="post">
  <select name="product">
  ';
  foreach ($products as $prod ) {
    $content.= '<option value="'.$prod['idproducts'].'" required="">' . $prod['product_name'] .'</option>';
  }
  $content .='
  </select>
  <input type="submit" name="edit_product_form" value="submit" />
  </form>
  ';
}

/*
*       This creates the edit product form and prepoputlates it with the information all ready in the database
*/
elseif (isset($_POST['edit_product_form'])) {
  //$content = var_dump($_POST['product']);
  $products = $pdo->prepare('SELECT * FROM products WHERE idproducts ='.$_POST['product'].'');
  $products->execute();
  $prod = $products->fetch(PDO::FETCH_ASSOC);
  $category = $pdo->prepare('SELECT * FROM categories');
  $category->execute();
  $content ='
  <button type="button" onclick="window.location=\'index.php\';">Back</button>
  <hr />
  <form action="index.php" method="post" enctype="multipart/form-data">
     <input type="hidden" name="idproducts" value="'. $prod['idproducts'].'" required="" />
     <label>Product Name:</label>
     <input type="text" name="prodname" value="'. $prod['product_name'].'" required="" />
     <label>Product Description:</label>
     <input type="textarea" name="proddesc" value="'. $prod['product_description'].'" required="" />
     <label>Price:</label>
     <input type="text" name="prodprice" value="'. $prod['product_price'].'" required="" />
     <label>Category:</label>
     <select name="cat">
     ';
     foreach ($category as $cat ) {
       $content.= '<option value="'.$cat['idcategories'].'" required="">' . $cat['category_name'] .'</option>';
     }
     $content .='
     </select>
     <label>Featured:</label>
     <select name="featured">
     <option value="0">No</option>
     <option value="1">Yes</option>
     </select>
     <label>Date Added:</label>
     <input type="text" name="date_added" value="'. $prod['date_added'].'" required="" readonly/>
     <label>Added By:</label>
     <input type="text" name="added_by" value="'. $prod['added_by'].'" required="" readonly/>
     <input type="submit" name="delete_prod" value="Delete" />
     <input type="submit" name="save_changes" value="Save changes" />
  </form>
  ';
}


/*
*       This handles the logic for deleting the selected product
*/
elseif (isset($_POST['delete_prod'])) {
  try {
    $stmt = $pdo->prepare('DELETE FROM products WHERE idproducts = :idproducts');
    $criteria =[
    'idproducts' => $_POST['idproducts']
  ];
  $stmt->execute($criteria);
  $content ='Deleting product succesful.
  <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  } catch (\Exception $e) {
    $content ='Deleting product failed. Check Connection.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  }
}


/*
*       This saves the changes from the edit product form
*/
elseif (isset($_POST['save_changes'])) {
  try {
    $stmt = $pdo->prepare('UPDATE products
      SET product_name = :product_name,
        product_description = :product_description,
        product_price=:product_price,
        product_category = :product_category,
        featured= :featured
      WHERE idproducts = :idproducts');
      $criteria = [
      'product_name' => $_POST['prodname'],
      'product_description' => $_POST['proddesc'],
      'product_price' => $_POST['prodprice'],
      'product_category' => $_POST['cat'],
      'featured' => $_POST['featured'],
      'idproducts' => $_POST['idproducts']
      ];
    $stmt->execute($criteria);
    $content ='Changes saved.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';

  } catch (\Exception $e) {
    $content ='Changes failed. Check connection and inputs.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  }
}


/*
*       This allows you to select a staff to edit
*/
elseif (isset($_POST['editstaff'])) {
  $users = $pdo->prepare('SELECT * FROM users WHERE is_staff =1');
  $users ->execute();
  $content ='
  <form action="index.php" method="post">
  <select name="user">
  ';
  foreach ($users as $user) {
    $content.= '<option value="'.$user['idusers'].'" required="">' . $user['username'] .'</option>';
  }
  $content .='
  </select>
  <input type="submit" name="edit_staff_form" value="submit" />
  </form>
  ';
}

/*
*       This displays the edit staff form that is prepopulated with the stored data
*/
elseif (isset($_POST['edit_staff_form'])) {
  $users= $pdo->prepare('SELECT * FROM users WHERE idusers ='.$_POST['user'].'');
  $users->execute();
  foreach ($users as $user) {

  $content ='
  <button type="button" onclick="window.location=\'index.php\';">Back</button>
  <hr />
  <form action="index.php" method="post">
     <input type="hidden" name="idusers" value="'.$user['idusers'].'" required="" />
     <label>User name:</label>
     <input type="text" name="username" value="'.$user['username'].'" required="" />
     <label>Email:</label>
     <input type="email" name="email" value="'.$user['email'].'" required=""/>
     <label>Address:</label>
     <input type="textarea" name="address" value="'.$user['address'].'" required="" />
     <label>Phone:</label>
     <input type="text" name="phone" value="'.$user['phone'].'" required=""/>
     <label>Select if staff:</label>
     <select name="is_staff">
     <option value="1">Yes</option>
     <option value="0">No</option>
     </select>
     <label>Select if admin:</label>
     <select name="is_admin">
     <option value="0">No</option>
     <option value="1">Yes</option>
     </select>
     <input type="submit" name="delete_user" value="Delete" />
     <input type="submit" name="save_changes_staff" value="submit" />
  </form>
  ';
}
}

/*
*       This handles the logic for the deleting of users
*/
elseif (isset($_POST['delete_user'])) {
  try {
    $stmt = $pdo->prepare('DELETE FROM users WHERE idusers = :idusers');
    $stmt->execute(['idusers' => $_POST['idusers']]);
    $content ='Staff member deleted.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  } catch (\Exception $e) {
    $content ='Deleteing staff member failed. Check connection.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  }
}

/*
*       This handles the logic for updating staff data from the edit staff form
*/
elseif (isset($_POST['save_changes_staff'])) {
  try {
    $stmt = $pdo->prepare('UPDATE users
      SET username = :username,
        email = :email,
        address =:address,
        phone =:phone,
        is_staff = :is_staff,
        is_admin = :is_admin
      WHERE idusers =:idusers');
      $criteria = [
      'username' => $_POST['username'],
      'email' => $_POST['email'],
      'address' => $_POST['address'],
      'phone' => $_POST['phone'],
      'is_staff' => $_POST['is_staff'],
      'is_admin' => $_POST['is_admin'],
      'idusers' => $_POST['idusers']
      ];
    $stmt->execute($criteria);
    $content ='Changes saved succesfully.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';

  } catch (\Exception $e) {
    $content ='Errors in saving changes. Check connection and inputs.
    <button type="button" onclick="window.location=\'index.php\';">Back</button>';
  }


}


/*
*       This takes the user to the review page
*/
elseif(isset($_POST['modrev'])){
header('Location: reviews.php ');
}



/*
*       This block displays the admin panel if the following session variables are set
*       loggedin = true, is_staff = true and is_admin = true
*       Here the admin can choose to:
*       Add a product
*       Edit a current product
*       Add a category
*       Change the featured product
*       Add a member of staff
*       Remove/Edit a staff
*       Moderate Reviews
*/
elseif (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['is_staff'] == true && $_SESSION['is_admin']==true) {
  $content = '
  			<h1>Admin Panel</h1>
  			<hr />
        <p>What would you like to do today?</p>
        <form action="index.php" method="post">
          <input type="submit" name="addprod" value="Add Product"/>
          <input type="submit" name="editprod" value="Edit Product/Delete"/>
          <input type="submit" name="addstaff" value="Add Staff"/>
          <input type="submit" name="editstaff" value="Edit Staff/Delete"/>
          <input type="submit" name="add_category" value="Add Category"/>
          <input type="submit" name="edit_category" value="Edit/Delete Category"/>
          <input type="submit" name="modrev" value="Moderate Reviews" />
          <input type="submit" name="Logout" value="Logout"/>
        </form>
            ';
}


/*
*       This block displays the staff panel if the following session variables are set
*       loggedin = true, is_staff = true and is_admin = false
*       Here the staff member can choose to
*       Add a product
*       Edit a current product
*       Add a category
*       Change the featured product
*       Moderate Reviews
*/

elseif (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['is_staff'] == true && $_SESSION['is_admin']==false) {
  $content = '
  			<h1>Staff Panel</h1>
  			<hr />
        <p>What would you like to do today?</p>
        <form action="index.php" method="post">
          <input type="submit" name="addprod" value="Add Product"/>
          <input type="submit" name="editprod" value="Edit Product/Delete"/>
          <input type="submit" name="add_category" value="Add Category"/>
          <input type="submit" name="edit_category" value="Edit/Delete Category"/>
          <input type="submit" name="modrev" value="Moderate Review"/>
          <input type="submit" name="Logout" value="Logout"/>
        </form>
        ';
}


/*
*   This block creates the create account form if the button to create an account has been clicked
*/

elseif (isset($_POST['create'])){
  $content ='
  <h1>Create Account</h1>
  <p>Fill in the form to create an account. All fields are mandatory</p>
  <hr />
  <form action="index.php" method="post">
     <label>Username:</label>
     <input type="text" name="username" placeholder="enter username" />
     <label>Password:</label>
     <input type="password" name="password" placeholder="enter password" />
     <label>Email:</label>
     <input type="text" name="email" placeholder="enter phone number" />
     <label>Address:</label>
     <input type="text" name="address" placeholder="Address" />
     <label>Phone:</label>
     <input type="text" name="phone" placeholder="enter phone number" />
     <input type="hidden" name="is_staff" value"0" />
     <input type="hidden" name="is_admin" value"0" />
     <input type="submit" name="createform" value="submit" />
  </form>
  <hr />
  ';
}

/*
*   This block creates the acount in the database once the user has clicked submit on the create
*   account form
*/
elseif(isset($_POST['createform'])){
        $password = $_POST['password'];
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, email, address, phone, is_staff, is_admin)
        VALUES (:username, :password, :email, :address, :phone, :is_staff, :is_admin)');
        $criteria = [
        'username' => $_POST['username'],
        'password' => $hashed,
        'email' => $_POST['email'],
        'address' => $_POST['address'],
        'phone' => $_POST['phone'],
        'is_staff' => $_POST['is_staff'],
        'is_admin' => $_POST['is_admin']
        ];
        $stmt->execute($criteria);
        header('Location: index.php ');
}
/*
*     If no session variables are set it means no one is logged in so display the login form and
*     the option to create an account.
*/
    elseif(!isset($_POST['login'])){
      $content ='
      <h1>Welcome to Ed\'s Electronics</h1>

      <p>We stock a large variety of electrical goods including phones, tvs, computers and games. Everything comes with at least a one year guarantee and free next day delivery.</p>

      <hr />
      <form name="searchform" action="search.php" method="post">
      <input type="text" name="term" placeholder="Enter search term" />
      <input type="submit" name="search" value="Search" />
      </form>
      <hr />

      <h2>Login</h2>
      <p>Have an account? Log in here.</p>

      <form action="index.php" method="post">
         <input type="text" name="username" placeholder="enter username" />
         <input type="password" name="password" placeholder="enter password" />
         <input type="submit" name="login" value="Login" />
      </form>
      <hr />
      <p>Don\'t have an account? Use the button below to create one.</p>
      <form action="index.php" method="post">
         <label>Create account:</label><input type="submit" name="create" value="Create" />
      </form>
      ';
    }

?>


<!--
*     Require the layout of the page
-->
<?php require 'layout.php'; ?>
