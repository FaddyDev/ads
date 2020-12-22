<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Comms | Sign in &mdash; ADS Mt. Kenya</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php
include_once("partials/head.php");
?>

    <div class="site-blocks-cover inner-page overlay" style="background-image: url(images/bg_sign.jpg);" data-aos="fade" data-stellar-background-ratio="0.1">
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7 text-center">
          <?php if(isset($_SESSION["is_logged"])){ ?>
            <h1 class="mb-5">You're already signed in <br><strong><?php echo $_SESSION["fname"]; ?></strong></h1> 
          <?php } else { ?>  <h1 class="mb-5">Sign <strong>In</strong></h1>
            <?php } ?>            
          </div>
        </div>
      </div>
    </div>  

    <div class="site-section bg-light">
      <div class="container">
        <div class="row">
       
          <div class="col-md-12 col-lg-8 mb-5">
          
          <?php if(!isset($_SESSION["is_logged"])){?>          
            <form class="p-5 bg-white" id="signform">

              <div class="row">
                <div class="col-md-12 mb-3 mb-md-0" id="response">
                  
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="phone">Phone</label>
                  <input type="text" id="phone" name="phone"  onKeyPress="return numbersonly(event)" class="input form-control" placeholder="Your phone number 07.." required>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-12">
                  <label class="font-weight-bold" for="password">Password</label>
                  <input type="password" id="password" name="password" class="input form-control" placeholder="Your Password" required>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12">
                  <input type="submit" value="Go" class="btn btn-primary">
                </div>
              </div>

  
            </form>
          <?php } ?>
          </div>

          <div class="col-lg-4">
            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">Note:</h3>
              <p class="mb-0 font-weight-bold">Phone</p>
              <p class="mb-4">Your mobile phone number starting with 07...</p>
              
              <p class="mb-0 font-weight-bold">Password</p>
              <p class="mb-4">Your password</p>
              
              <p class="mb-0 font-weight-bold">Help!</p>
              <p class="mb-4">Contact the admin</p>

            </div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="promo py-5 bg-primary" data-aos="fade">
      <div class="container text-center">
        <h2 class="d-block mb-0 font-weight-light text-white">        
        <a href="https://ads-mtkenya.or.ke/" target="_blank" class="text-white d-block">Visit The Main Website</a>
        </h2>
      </div>
    </div>
    
    <footer class="site-footer">
      <div class="container">
        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <p>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            Copyright &copy; <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>document.write(new Date().getFullYear());</script> All Rights Reserved | <a href="https://ads-mtkenya.or.ke//" target="_blank" >ACK St. Peters Cathedral, Nyeri</a>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </p>
          </div>
          
        </div>
      </div>
    </footer>
  </div>

  <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/jquery-ui.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/jquery.countdown.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/bootstrap-datepicker.min.js"></script>
  <script src="js/aos.js"></script>

  <script src="js/main.js"></script>
  <script src="js/myscripts.js"></script>
    
<script>
$(document).ready(function(){

$('.input').keydown(function(){
  $('#response').empty();
});


$('#signform').on("submit", function(e){ //or $(�form�).submit(function(e){
e.preventDefault();

var phone = $('#phone').val();
var password = $('#password').val();

//show and clear response field
$('#response').show().html("");
if(phone=='' || password==''){
$('#response').html('<p class="alert alert-warning">  Fill in all fields! </p>');
return false;
}

$.ajax({
url: 'scripts/signin.php',
method: 'post',
dataType    : 'json',
data: { phone:phone, password:password},
success: function(response){
if(response['status'] == 'success'){
$('#response').html('<p class="alert alert-success">  '+ response.message +' </p>');
$('form')[0].reset(); //rest form
//lose form
  setTimeout(function() {
    $("#signform").fadeOut(1500);
    $("html, body").animate({ scrollTop: 0 }, "slow");
  },3000);
}
else{
$('#response').html('<p class="alert alert-warning">'+ response.message +'</p>');
}
}
,error: function(data){
$('#response').html('<p class="alert alert-warning">  Error: Contact the admin! </p>');
//console.log(data);
}
});

});

});
</script>
  </body>
</html>