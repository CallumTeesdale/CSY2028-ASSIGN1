<?php
require 'dbconnect.php';
require 'layout.php';
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

if(isset($_POST['addstaffform'])){
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
  require 'layout.php';
  ?>
