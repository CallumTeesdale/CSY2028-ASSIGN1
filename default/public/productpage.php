<!--
*     This page handles the individual product pages
*     This is where the in depth description is loaded along with the reviews
-->

<!--
*     Connect to the database
-->
<?php require 'dbconnect.php';
session_start();
?>

<!--
*       Sets the title of the page in the title variable
-->
<?php $Title ='Product Page' ?>

<!--
*       Sets the open graph tags for facebook sharing api
-->
<?php
$metaurl = '<meta property="og:url"                content="document.URL;" />';
$metatype='<meta property="og:type"               content="article" />';
$metatitle = '<meta property="og:title"              content="Ed/s Electronics" />';
$metadescription='<meta property="og:description"        content="Cheapest electronics around!" />';
$metaimage ='<meta property="og:image"              content="/images/oled.jpg" />';?>


<?php

/*
*       This checks if the user has clicked on a thumb icon and if the session voted variable is empty
*       and if the user is logged in, if all true it upates the review score for the selected review
*       by 1 and sets the session voted variable to true so that the user can't vote again
*/
      if(isset($_POST['submit'])&&empty($_SESSION['voted'] && $_SESSION['loggedin']==true)){
        $revCount = $pdo->prepare('UPDATE reviews SET review_score = review_score + 1 WHERE idreviews = '. $_GET['reviewid'] . '');
        $revCount->execute();
        echo "voted";
        $_SESSION['voted'] = true;
        header('Location: productpage.php?id='. $_GET['id'] . '');
       }

/*
*       if the id is set in the URL display the products with that category id and selected
*       all reviews for the product
*/

if (isset($_GET['id'])) {
  $products = $pdo->prepare('SELECT * FROM products WHERE idproducts = '. $_GET['id'] . '');
  $products->execute();
  if(!isset($_POST['ordered'])){
  $reviews = $pdo->prepare('SELECT * FROM reviews WHERE review_moderated =1 AND review_product = '. $_GET['id'] . '');
  $reviews->execute();
}
if(isset($_POST['ordered'])){
$reviews = $pdo->prepare('SELECT * FROM reviews WHERE review_moderated =1 AND review_product = '. $_GET['id'] . ' ORDER BY review_stars '.$_POST['order'].'');
$reviews->execute();
}
  $content = '	<h2>Product Page</h2>';


/*
*       Loop to display the contents of the product attributes
*/
      foreach ($products as $prod) {
        $content .='<h3>'. $prod['product_name'] . '</h3>';
        $content .= '<img src="data:image/jpeg;base64,'.base64_encode( $prod['product_image'] ).'" width="300" height="300"/>';
        $content .='<h4>Product details</h4>';
        $content .='<p>' . $prod['product_description'] . '</p>';
        $content .='<div class="price">' . $prod['product_price'] . '</div>';
      }

/*
*       This is where the code for the reviews is
*/
      $content .='<h4>Product reviews</h4>';
      $content .='<form action="productpage.php?id='. $_GET['id'].'" method="post">
      <select name="order">
      <option value="DESC">Highest starts First</option>
      <option value="ASC">Lowest Stars First</option>
      <input type="submit" name="ordered" value="Order" >
      </select>
      </form>';


/*
*       Loop to display all reviews
*/
      foreach ($reviews as $rev){
        $content .= '<ul class="reviews"><li>';


/*
*       For loop to display the review rating as star images
*/
        for ($i=0; $i < $rev['review_stars']; $i++) {
          $content .= '<img src="images/star.jpg" alt="star" height="20" width="20">';
        }

        $content .='<p>' . $rev['review_description'] . '</p>';
        $content .= '
        <div class="details">
          <strong><a href="reviews.php?user='.$rev['review_customer'].'&product_id='.$rev['review_product'].'">'.$rev['review_customer'].'</a></strong>
          <em>'.$rev['review_date'].'</em>
        </div>';
        $content .= '<hr />';


/*
*       Display the form for the review rating system. Sets the review id in the url
*       So that the above rating statement (lines 67-73) functions
*/

        $content .= '
        <div class="help">
        <form action="productpage.php?id='. $_GET['id'] . '&reviewid='. $rev['idreviews'] . '" method="POST" class="reviewhelpful">
        <input type="submit"  alt="submit" name="submit" value="submit" class="help" />
        </form>
        </div>
        ';
        $content .= '+' . $rev['review_score'];
        $content .= ' Find this review helpful';
        $content .= '<hr />';
        $content .= '<div class="fb-share-button" data-href="v.je/productpage.php" data-layout="button" data-size="small" data-mobile-iframe="false"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fv.je%2Fproductpage.php%3Fid%3D1&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>';
        $content .='</ul></li>';
      }


/*
*       If logged in allow the user to leave a review for the product
*/

}
if(isset($_POST['add_review'])){
  $date = date("Y-m-d");
  echo $date;
  $stmt = $pdo->prepare('INSERT INTO reviews (review_customer, review_desription, review_date, review_moderated, review_stars, review_product)
  VALUES (:review_customer, :review_desription, :review_date, :review_moderated, :review_stars, :review_product)');
  $criteria = [
  'review_customer' => $_SESSION['username'],
  'review_desription' => $_POST['review_description'],
  'review_date' => $date,
  'review_moderated' => $_POST['review_moderated'],
  'review_stars' => $_POST['star'],
  'review_product' => $_POST['review_product']
  ];
  $stmt->execute($criteria);
  header('Location: productpage.php?id='.$_POST['review_product'].'');
  $_SESSION['reviewed$_GET[\'id\']']==true;
}
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']==true&&empty($_SESSION['reviewed$_GET[\'id\']'])) {
  $content.='
  <h4>Leave a review</h4>
  <form action="addreview.php" method="post">
  <label>Select a star rating:</label>
  <select name="star">
  <option value="5">5</option>
  <option value="4">4</option>
  <option value="3">3</option>
  <option value="2">2</option>
  <option value="1">1</option>
  </select>
  <label>Review text:</label>
  <input type="textarea" name="review_description" required="">
  <input type="hidden" name="review_moderated" value="0">
  <input type="hidden" name="review_product" value="'.$_GET['id'].'">
  <input type="submit" name="add_review" value="Add Review" >
  </form>
  ';
}
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']==true&&!empty($_SESSION['reviewed$_GET[\'id\']'])) {
    $content.='
    <h4>Leave a review</h4>
    <p>You have all ready left a review for this product</p>';

  }

?>
<?php require 'layout.php'; ?>
