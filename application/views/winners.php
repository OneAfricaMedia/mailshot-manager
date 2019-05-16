<!DOCTYPE html>
<html lang="en">

<head>
    <base href = '<?php echo base_url(); ?>' />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Employee of the Month Voting System</title>

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
                    <?php endif; ?>
                    <?php if(!$this->ion_auth->logged_in()): ?>
                        <li>
                            <a href="auth/users/add">Register</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="welcome/results">Results</a>
                    </li>                    
                    <?php if ($this->ion_auth->is_admin()): ?>
                        <li>
                            <a href="welcome/admin_results">Approve Results</a>
                        </li>
                    <?php endif; ?>

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
                <h1 class="page-header">Employee of the Month - Results
                    
                </h1>


                    <?php foreach($winners->result() as $winner): ?>
                    
                        <?php if(date("m-Y", strtotime($winner->win_date)) != date("m-Y")) continue; ?>

                        <div class="col-lg-12 col-sm-12 text-center">

                            <?php if($winner->photo): ?>
                                <img class="img-circle img-responsive img-center" style="width: 150px" src="assets/uploads/files/<?php echo $winner->photo ?>" alt="">
                            <?php else: ?>
                                <img class="img-circle img-responsive img-center" style="width: 150px" src="assets/uploads/files/user-icon.png" alt="">
                            <?php endif; ?>
                            <h3><?php echo $winner->first_name . " " . $winner->last_name ?><br />
                                <small>In <?php echo date("F Y", strtotime($winner->win_date)); ?></small><br>
                                <small>Won by <?php echo $winner->num_votes ?> Votes</small>
                            </h3>

                        </div>
                    
                <?php endforeach; ?>

                
            </div>
        </div>

        <!-- Team Members Row -->

        <?php if($winners->num_rows() > 1): ?>
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">Previous Winners</h2>
                </div>

                <?php foreach($winners->result() as $winner): ?>
                    
                        <?php if(date("m-Y", strtotime($winner->win_date)) == date("m-Y")) continue; ?>


                        <div class="col-lg-4 col-sm-6 text-center">

                            <?php if($winner->photo): ?>
                                <img class="img-circle img-responsive img-center" style="width: 150px" src="assets/uploads/files/<?php echo $winner->photo ?>" alt="">
                            <?php else: ?>
                                <img class="img-circle img-responsive img-center" style="width: 150px" src="assets/uploads/files/user-icon.png" alt="">
                            <?php endif; ?>
                            <h3><?php echo $winner->first_name . " " . $winner->last_name ?><br />
                                <small>In <?php echo date("F Y", strtotime($winner->win_date)); ?></small><br>
                                <small>Won by <?php echo $winner->num_votes ?> Votes</small>
                            </h3>

                        </div>
                    
                <?php endforeach; ?>
               
            </div>
        <?php endif; ?>

        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; Zoom Voting System 2017</p>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <div id="confirm" class="modal fade admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" style="display:block;">
            <div class="modal-content">
            
                <div class="modal-body" align="center">
                    <h2>Why are you voting for <span id="name"></span>?</h2><br><br> 
                    <form method="post" action = "welcome/submitvote">
                        <div class="form-group">
                            <textarea name = "reasons" class="form-control"  rows="6"></textarea>
                        </div>
                        <input type="hidden" name="candidate_id" id="candidate_id" value="">
                        <button   type="submit" class="btn btn-primary">Vote</button>
                        <button   type="button" class="btn btn-dark" data-toggle="modal" data-target="#confirm">Cancel</button>
                    </form>


                </div>
            </div>
        </div>
    </div>

    <div id="voted" class="modal fade admin-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" style="display:block;">
            <div class="modal-content">
            
                <div class="modal-body" align="center">

                    <?php if($this->session->flashdata('Voted')): ?>
                        <h2>Thank you for Voting!</h2><br>
                    <?php else: ?>
                        <h2>You already voted. No need to vote again.</h2><br>
                    <?php endif; ?>

                    <button   type="button" class="btn btn-dark" data-toggle="modal" data-target="#voted">Close</button>

                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
      function showModal(name,id){
        $('#candidate_id').val(id);
        $('#name').html(name);
        // $('#name').innerHTML = name;
        $('#confirm').modal('show');
      }
    </script>

    <?php if($this->session->flashdata('Voted') OR $this->session->flashdata('AlreadyVoted')): ?>
        <script type="text/javascript">
          showVoted();
          function showVoted(){
            $('#voted').modal('show');
          }
        </script>
    <?php endif; ?>
</body>

</html>
