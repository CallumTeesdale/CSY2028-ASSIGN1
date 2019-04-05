<?php require 'dbconnect.php'; ?>
<?php
session_start();
//set the title of the page
$Title = 'Products' ?>

<?php
$metaurl = '<meta property="og:url"                content="document.URL;" />';
$metatype='<meta property="og:type"               content="article" />';
$metatitle = '<meta property="og:title"              content="Ed/s Electronics" />';
$metadescription='<meta property="og:description"        content="Cheapest electronics around!" />';
$metaimage ='<meta property="og:image"              content="/images/oled.jpg" />'; ?>
<?php
//if the id is set in the URL display the products with that category id
$search = str_replace(array('%','_'),'',$_POST['term']);

//handles the logic for searching the database of products names like the search term
$query = "SELECT * FROM products WHERE product_name LIKE :searchcriteria";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':searchcriteria', '%' . $search . '%', PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll();

if($stmt->rowCount() > 0){

/* Fetch all of the remaining rows in the result set */
$content = '	<h2>Product list</h2>

  <ul class="products">';

foreach( $results as $result) {
  $content .= '<li>';
  $content .='<h3><a href="productpage.php?id='. $result['idproducts'] .'">'. $result['product_name'] . '</a></h3>';
  $content .= '<img src="data:image/jpeg;base64,'.base64_encode( $result['product_image'] ).'" width="200" height="200"/>';
  $content .='<p>' . $result['product_description'] . '</p>';
  $content .='<div class="price">' . $result['product_price'] . '</div>';
  $content .= '</li>';

}
'
  </ul>
  <hr />
';
}

//if no results found tell the user
elseif($stmt->rowCount() <= 0){
  $content='No product found for '.$search.'';
}

?>
<!--Require the layout of the page -->
 <?php require 'layout.php';?>
