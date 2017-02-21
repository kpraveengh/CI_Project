

<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- Website Font style -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">

        <!-- Google Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Passion+One' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet' type='text/css'>

        <title>Admin</title>
    </head>
    <body>
        <div class="container">
            <div class="row main">
                <div class="panel-heading">
                    <div class="panel-title text-center">
                        <h1 class="title">MAA-APP</h1>
                        <hr />
                    </div>
                </div> 
                <div class="main-login main-center">
                    <?php echo form_open_multipart('user/createpreventive'); ?>

                    <div class="form-group">
                        <label for="name" class="cols-sm-2 control-label">Name</label>
                        <div class="cols-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
                                <input type="text" class="form-control" name="name" id="name"  placeholder="Enter your Name"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dob" class="cols-sm-2 control-label">User_Id</label>
                        <div class="cols-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calender fa-lg" aria-hidden="true"></i></span>
                                <input type="number" class="form-control" name="user_id" id="password"  placeholder="User-Id"/>
                            </div>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="dob" class="cols-sm-2 control-label">Member_id</label>
                        <div class="cols-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calender fa-lg" aria-hidden="true"></i></span>
                                <input type="number" class="form-control" name="member_id" id="password"  placeholder="member-Id"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <button type="submit"  class="btn btn-primary btn-lg btn-block login-button">Register</button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>

    </body>
</html>