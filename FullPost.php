<?php require_once("Includes/DB.php");?>

 <?php require_once("Includes/Functions.php");?>

 <?php require_once("Includes/Sessions.php");?>

<?php $SearchQueryParameter = $_GET["id"];?>



<?php

if (isset($_POST["submit"])) {

  $Name = $_POST["CommenterName"];
  $Email = $_POST["CommenterEmail"];
  $Comment = $_POST["CommenterThoughts"];
  

    date_default_timezone_set("Asia/Dhaka");
    $CurrentTime=time();

    $DateTime=strftime("%B-%d-%Y %H:%M:%S",$CurrentTime);
    
    if (empty($Name)||empty($Email)||empty($Comment)) {
      
      $_SESSION["ErrorMessage"] = "All fields must be filled out" ;
      Redirect_to("FullPost.php?id={$SearchQueryParameter}");
    }
   elseif (strlen($Comment)>500) {
    $_SESSION["ErrorMessage"] = "Comment length should be less than 500 characters";
      Redirect_to("FullPost.php?id={$SearchQueryParameter}");
   }

  
  else{
    //Query to insert comment to DB when everything fine

     global $ConnectingDB;

    $sql = "INSERT INTO comments(datetime,name,email,comment,approvedby,status)";
    $sql.= "VALUES(:dateTime,:name,:email,:comment,'Pending','OFF')";
    $stmt= $ConnectingDB->prepare($sql); 
    $stmt->bindValue(':dateTime',$DateTime);
    $stmt->bindValue(':name',$Name);
    $stmt->bindValue(':email',$Email);
    $stmt->bindValue(':comment',$Comment);  
    $Execute=$stmt->execute();

    if ($Execute) {
      $_SESSION["SuccessMessage"]="Comment Submitted Successfully";
      Redirect_to("FullPost.php?id={$SearchQueryParameter}");
    }
    else{
      $_SESSION["ErrorMessage"]="Comment Submitted Failed.";
      Redirect_to("FullPost.php?id={$SearchQueryParameter}");
    }
  }
}
?>



<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
	<title>
		Blog Page
	</title>
	<link rel="stylesheet" type="text/css" href="Css/Styles.css">
</head>
<body>

  <!--Navbar Start-->

  <div style="height: 10px; background: #27aae1"></div>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" >
	<div class="container">
	<a href="" class="navbar-brand">Forid.com</a>

    <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarcollapseCMS">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarcollapseCMS">

    <ul class="navbar-nav ml-auto">
          
          <li class="nav-item">
          	<a href="Blog.php" class="nav-link">Home</a>
          </li>
          <li class="nav-item">
          	<a href="#" class="nav-link">About Us</a>
          </li>
          <li class="nav-item">
          	<a href="Blog.php" class="nav-link">Blog</a>
          </li>
          <li class="nav-item">
          	<a href="#" class="nav-link">Contact Us</a>
          </li>
          <li class="nav-item">
          	<a href="#" class="nav-link">Features</a>
          </li>
             
    </ul>
     <ul class="navbar-nav ml-auto">
            <form class="form-inline d-none d-sm-block" action="Blog.php">
              <div class="form-group">
                <input class="form-control mr-2" type="text" name="Search" placeholder="Search here">
                <button  class="btn btn-primary" name="SearchButton">Go</button>

              </div>
              
            </form>
    </ul>
      </div>
    </div>
    

</nav>
  <div style="height: 10px; background: #27aae1"></div>

<!--Navbar End-->

<!--Header-->
<div class="container">
  <div class="row mt-4" >

    <!--Main area start-->

    <div class="col-sm-8" >
      <h1>the complete responsive cms blog</h1>
      <h1 class="lead">the complete blog using php by mohammad forid</h1>
       <?php 
           echo ErrorMessage();
           echo SuccessMessage();
         ?>
      <?php
         global $ConnectingDB;
         //SQL query when search button is active
          if(isset($_GET["SearchButton"])){
             $Search= $_GET["Search"];
             $sql= "SELECT * FROM posts 
             WHERE datetime LIKE :search 
             OR title LIKE :search 
             OR category LIKE :search
             OR POST LIKE :search ";
             $stmt = $ConnectingDB->prepare($sql);
             $stmt->bindvalue(':search','%'.$Search.'%');
             $stmt->execute();
           } 
            
         //default sql query
         else{
          $PostIdFromURL= $_GET["id"];
          if (!isset($PostIdFromURL)) {
            $_SESSION["ErrorMessage"]="Bad Request happened!";
            Redirect_to("Blog.php");
          }
         $sql  = "SELECT * FROM posts WHERE id='$PostIdFromURL' ";
         $stmt = $ConnectingDB->query($sql);
             }
         
         $sr=0;
         while ($DataRows = $stmt->fetch()) {
           $PostId    = $DataRows["id"];
           $DateTime  = $DataRows["datetime"];
           $PostTitle = $DataRows["title"];
           $Category  = $DataRows["category"];
           $Admin     = $DataRows["author"];
           $Image     = $DataRows["image"];
           $PostDescription = $DataRows["post"];
           $sr++;
         
       ?>

       <div class="card">
        <img src="Uploads/<?php echo htmlentities($Image) ;?>" style="max-height: 350px;" class="img-fluid card-img-top" />
         <div class="card-body">
           <h4 class="card-title">
            
            <?php echo htmlentities($PostTitle)  ; ?>
              
            </h4>
           <small class="text-muted">Written by <?php echo htmlentities($Admin); ?> on <?php echo htmlentities($DateTime) ; ?></small>
           <span style="float: right;" class="badge badge-dark text-white">Comments 20</span>
           <hr>
           <p class="card-text">
            
            <?php echo htmlentities($PostDescription); ?>
              
            </p>
           
         </div>
       </div>
     <?php } ?>
          <!--Comment Area-->
       <div class="">
      <form class="" action="FullPost.php?id=<?php echo $SearchQueryParameter;?>" method="post">
        <div class="card mb-3">
          <div class="card-header">
            <h5 class="FieldInfo">Share Your Thoughts about this post</h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <div class="input-group">
                <div>
                  <span class="input-group-text"><i class="fas fa-user pb-2"></i></span>
                </div>
              <input class="form-control" type="text" name="CommenterName" placeholder="Name">
              </div>
            </div>

            <div class="form-group">
              <div class="input-group">
                <div>
                  <span class="input-group-text"><i class="fas fa-envelope pb-2"></i></span>
                </div>
              <input class="form-control" type="email" name="CommenterEmail" placeholder="Email">
              </div>
            </div>

            <div class="form-group">
              <textarea name="CommenterThoughts" class="form-control" rows="6" cols="80">
                
              </textarea>
            </div>

            <div class="">
              <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            </div>

          </div>
        </div>
      </form>
    </div>
       <!--Comment Area End-->
    </div>

    <!--Main area end-->

    <!--Side area start-->
    <div class="col-sm-4" style="min-height: 40px; background: green;">
      
    </div>
    <!--Side area end-->
  </div>
</div>



<!--Header End-->
<br>
<!--Footer-->
<footer class="bg-dark text-white">
  <div class="container">
    <p class="lead text-center">Theme By | learners flame | <span id="demo"></span> &copy; ----All rights reserved.</p>
  </div>
  <div style="height: 10px; background: #27aae1"></div>
</footer>

<!--Footer End-->


<script>
  const d = new Date();
  document.getElementById("demo").innerHTML = d.getFullYear();
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html> 