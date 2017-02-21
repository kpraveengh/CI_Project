
<div class="container">
    <div class="row main">
        <div class="panel-heading">
            <div class="panel-title text-center">
                <h1 class="title">MAA-APP</h1>
                <hr />
            </div>
        </div> 
        <div class="main-login main-center">
            <?php echo form_open('admin/updateadmin/'); ?>

            <div class="form-group">
                <label for="name" class="cols-sm-2 control-label">Name</label>
                <div class="cols-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user fa" aria-hidden="true"></i></span>
                        <input type="text" class="form-control" name="name" id="name"  value="<?php echo $admin->getName(); ?>"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="cols-sm-2 control-label">Email</label>
                <div class="cols-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
                        <input type="text" class="form-control" name="email" id="email"  value="<?php echo $admin->getEmail(); ?>"/>
                    </div>
                </div>
            </div>

            <input type="hidden" class="form-control" name="id" id="email"  value="<?php echo $admin->getId(); ?>"/>


        <div class="form-group ">
            <button type="submit"  class="btn btn-primary btn-lg btn-block login-button">Update</button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
</div>
