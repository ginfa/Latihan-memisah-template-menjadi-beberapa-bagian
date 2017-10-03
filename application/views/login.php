<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' type='image/ico' href='<?php echo base_url();?>assets/images/favicon.ico'>
	<meta property="og:image" content="<?php echo base_url();?>assets/images/logo.png">

    <title>GIMPIS - Gema Insani Manufacture Printing Information System</title>

    <!-- Bootstrap -->
    <link href="<?php echo base_url();?>assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo base_url();?>assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="<?php echo base_url();?>assets/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="<?php echo base_url();?>assets/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo base_url();?>assets/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <div class="login_wrapper">
        <div class="animate form login_form">
			<div align="center">
			  <img src="<?php echo base_url()?>/assets/images/logo.png" alt="Logo" width="75px" height="75px">
			  <h2>Manufacture Printing Information System</h2>
			</div>
			<section class="login_content">
			<form action="<?php echo base_url()?>loginauth/login/submit" method="post">
			  <h1>GIMPIS</h1>
              <h1>Login Form</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" required="" name="username" id="username"/>
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" name="userpass" id="userpass"/>
              </div>
              <div align="center">
			    <input name="Log in" type="submit" class="btn btn-success" value="Login"/>
        </div>
				<?php
				if($this->session->flashdata('msg'))
				$msg=$this->session->flashdata('msg');

				if(isset($msg)):?>
                	<div style="margin-top:20px"></div>
					<div class="nNote <?php echo $msg['type']?>">
						<?php echo $msg['msg']?>
					</div>
				<?php endif;?>

              <div class="clearfix"></div>

              <div class="separator">
                <div class="clearfix"></div>
                <br />
                <div>
                  <p>©2017 All Rights Reserved. Gema Insani. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
