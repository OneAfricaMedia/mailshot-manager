<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href = '<?php echo base_url(); ?>' />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MailsShots Calendar</title>
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="css/round-about.css" rel="stylesheet">
<link href="css/calendar.css" rel="stylesheet">
<?php foreach($js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
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
                            <a href="mailshotmanager/mailshots_schedule">Schedule List</a>
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
	<div style='height:20px;'></div>  
    <div>
		<?php echo $output; ?>
    </div>
</body>
</html>
