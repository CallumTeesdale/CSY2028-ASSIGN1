<?php require 'dbconnect.php';
session_start(); ?>
<!--
*       Sets the title of the page in the title variable
-->
<?php $Title ='Reviews' ?>
<?php
$metaurl = '<meta property="og:url"                content="document.URL;" />';
$metatype='<meta property="og:type"               content="article" />';
$metatitle = '<meta property="og:title"              content="Ed/s Electronics" />';
$metadescription='<meta property="og:description"        content="Cheapest electronics around!" />';
$metaimage ='<meta property="og:image"              content="/images/oled.jpg" />';?>
<?php

//displays administartive controls if the logged in user is an admin or staff
if($_SESSION['is_admin'] || $_SESSION['is_staff'] ==true){

  //show reviews needing moderation
  $reviews = $pdo->prepare('SELECT * FROM reviews WHERE review_moderated =0');
  $reviews->execute();

  //if reviews need moderating display them
if ($reviews->rowCount() > 0) {
        $content ='<h4>Product reviews needing moderation</h4>';


    /*
    *       Loop to display all reviews
    */
        foreach ($reviews as $rev){
          $products = $pdo->prepare('SELECT * FROM products WHERE idproducts="'.$rev['review_product'].'"');
          $products->execute();
          $product = $products->fetchAll();
          $content .= '<ul class="reviews"><li>';
          foreach ($product as $prod ) {
          $content .= '<p>Review for <strong><a href="productpage.php?id='. $rev['review_product'] .'">'.$prod['product_name']. '</a></strong></p>';
          }
    /*
    *       For loop to display the review rating as star images
    */
          for ($i=0; $i < $rev['review_stars']; $i++) {
            $content .= '<img src="images/star.jpg" alt="star" height="20" width="20">';
          }

          $content .='<p>' . $rev['review_description'] . '</p>';
          $content .= '
          <div class="details">
            <strong><a href="reviews.php?user='.$rev['review_customer'].'">'.$rev['review_customer'].'</a></strong>
            <em>'.$rev['review_date'].'</em>
          </div>';
          $content .= '<hr />';
          $content .= '<form action="reviews.php" method="post">
          <input type="hidden" name="idreviews" value="'.$rev['idreviews'].'" />
          <input type="submit" name="show_review" value="Allow" />
          <input type="submit" name="delete_review" value="Delete" />
          </form>';
          $content .='</ul></li>';
        }

        //if a review is allowed, show it on the product page and remove from moderation queue
        if(isset($_POST['show_review'])) {
          try {
            $review = $pdo->prepare('UPDATE reviews
              SET review_moderated= 1 WHERE idreviews=:idreviews');
              $criteria = [
              'idreviews' => $_POST['idreviews']
            ];
            $review->execute($criteria);
            echo "<script type='text/javascript'>alert('Review added');</script>";
          } catch (\Exception $e) {

          }

        }

        //if review is not allowed delete the review from database and moderation queue
        if(isset($_POST['delete_review'])) {
          try {
            $stmt = $pdo->prepare('DELETE FROM reviews WHERE idreviews = :idreviews');
            $stmt->execute(['idreviews' => $_POST['idreviews']]);
            echo "<script type='text/javascript'>alert('Review deleted');</script>";
          } catch (\Exception $e) {

          }


        }
      }

      //if no reviews need moderating tell the user
      elseif ($reviews->rowCount() == 0) {
          $content='No reviews need moderating';

      }
    }

//handles the logic if the user got to this page via clicking a username
elseif(isset($_GET['user'])) {

  $reviews = $pdo->prepare('SELECT * FROM reviews WHERE review_customer = "'.$_GET['user'].'"');
  $reviews->execute();
/*
*       This is where the code for the reviews is
*/
      $content ='<h4>Product reviews by customer '.$_GET['user'].'</h4>';


/*
*       Loop to display all reviews
*/
      foreach ($reviews as $rev){
        $products = $pdo->prepare('SELECT * FROM products WHERE idproducts="'.$rev['review_product'].'"');
        $products->execute();
        $product = $products->fetchAll();
        $content .= '<ul class="reviews"><li>';
        foreach ($product as $prod ) {
        $content .= '<p>Review for <strong><a href="productpage.php?id='. $rev['review_product'] .'">'.$prod['product_name']. '</a></strong></p>';
        }
/*
*       For loop to display the review rating as star images
*/
        for ($i=0; $i < $rev['review_stars']; $i++) {
          $content .= '<img src="images/star.jpg" alt="star" height="20" width="20">';
        }

        $content .='<p>' . $rev['review_description'] . '</p>';
        $content .= '
        <div class="details">
          <strong><a href="reviews.php?user='.$rev['review_customer'].'">'.$rev['review_customer'].'</a></strong>
          <em>'.$rev['review_date'].'</em>
        </div>';
        $content .= '<hr />';


/*
*       Display the form for the review rating system. Sets the review id in the url
*       So that the above rating statement (lines 67-73) functions
*/

        $content .= '
        <div class="help">
        <form action="productpage.php?id='. $rev['review_product'] . '&reviewid='. $rev['idreviews'] . '" method="POST" class="reviewhelpful">
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

 ?>
 <?php require 'layout.php'; ?>
