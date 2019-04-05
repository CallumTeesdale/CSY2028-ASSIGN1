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
if (isset($_GET['id'])) {
  $products = $pdo->prepare('SELECT * FROM products WHERE product_category = '. $_GET['id'] . ' ORDER BY date_added DESC');
  $products->execute();
  $content = '	<h2>Product list</h2>

    <ul class="products">';
    //Loop through the results of the query as list elements
      foreach ($products as $prod) {
        $content .= '<li>';
        $content .='<h3><a href="productpage.php?id='. $prod['idproducts'] .'">'. $prod['product_name'] . '</a></h3>';
        $content .= '<img src="data:image/jpeg;base64,'.base64_encode( $prod['product_image'] ).'" width="200" height="200"/>';
        $content .='<p>' . $prod['product_description'] . '</p>';
        $content .='<div class="price">' . $prod['product_price'] . '</div>';
        $content .= '</li>';
      }
  '
    </ul>
    <hr />
  ';
}

//if no id is set in url display all products
else {
  $products = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC');
  $products->execute();
  $content = '	<h2>Product list</h2>

    <ul class="products">

';
//Loop through the results of the query as list elements
      foreach ($products as $prod) {
        $content .= '<li>';
        $content .='<h3><a href="productpage.php?id='. $prod['idproducts'] .'">'. $prod['product_name'] . '</a></h3>';
        $content .= '<img src="data:image/jpeg;base64,'.base64_encode( $prod['product_image'] ).'" width="200" height="200"/>';
        $content .='<p>' . $prod['product_description'] . '</p>';
        $content .='<div class="price">' . $prod['product_price'] . '</div>';
        $content .= '</li>';
      }
  '

    </ul>

    <hr />
  ';

}
 ?>

 <?php require 'layout.php';?>
