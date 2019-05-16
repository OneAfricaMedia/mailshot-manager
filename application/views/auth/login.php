<!DOCTYPE html>
<html lang="en">

<head>
    <base href = '<?php echo base_url(); ?>' />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Sales Tool</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/round-about.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img src="https://www.zoomtanzania.com/assets/zo-admin/img/logo-colour-d727efbfd2.svg" height="30" ></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                         <?php if($this->ion_auth->logged_in()): ?>
                        <li>
                            <a href="auth/logout">Logout</a>
                        </li>
                        <li>
                            <a href="auth/users/add">Register</a>
                        </li>
                        <li>
                            <a href="mailshotmanager/calendar">Admin Calendar</a>
                        </li> 
                        <li>
                            <a href="mailshotmanager/company_credits">Companies</a>
                        </li>                    
                    <?php endif; ?>


                    <li>
                        <a href="mailshotmanager/customer_calendar">Customer Calendar</a>
                    </li> 
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">

        <!-- Introduction Row -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Login
                    
                </h1>
                
            </div>
        </div>

        <!-- Team Members Row -->
        <div class="row">
            <?php echo form_open("auth/login");?>

            <div id="infoMessage"><?php echo $message;?></div>

            <div class="form-group">
              <label for="identity">
                <?php echo lang('login_identity_label', 'identity');?>
              </label>
                <?php echo form_input($identity);?>
            </div>
            <div class="form-group">
              <p>
                <?php echo lang('login_password_label', 'password');?>
                <?php echo form_input($password);?>
              </p>
            </div>
            <div class="form-group">
              <p>
                <?php echo lang('login_remember_label', 'remember');?>
                <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
              </p>
            </div>


              <p><?php echo form_submit('submit', lang('login_submit_btn'));?></p>

            <?php echo form_close();?>

            <!-- <p><a href="forgot_password"><?php //echo lang('login_forgot_password');?></a></p> -->
           
        </div>

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <!-- <p>Copyright &copy; Zoom Voting System 2017</p> -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->



    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
