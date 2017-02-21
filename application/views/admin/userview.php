



<!-- nav bar -->
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container">
    <div class="row">

        <div class="col-sm-9">

            <!-- resumt -->
            <div class="panel panel-default">
                <div class="panel-heading resume-heading">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-xs-12 col-sm-4">
                                <figure>
                                    <img class="img-circle img-responsive" alt="" src="http://placehold.it/150x150">
                                </figure>                          
                            </div>
                            <div class="col-xs-12 col-sm-8">
                                <ul class="list-group">
                                    <li class="list-group-item"><?php echo $users->getName(); ?></li>
                                    <li class="list-group-item"><?php echo $users->getEmail(); ?></li>
                                    <li class="list-group-item"><?php echo $users->getPhone_no(); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bs-callout bs-callout-danger">
                    <div class="col-xs-12 col-sm-6">
                        <h4>Preventives</h4>
                        <ul class="list-group">
                            <?php
                            $prevent = '';
                            foreach ($preventives as $preventive) {
                                $prevent .= $preventive->getName() . "<li class='list-group-item'>";
                            }
                            echo "<li  class='list-group-item ' >" . rtrim($prevent, ",") . "</li>";
                            ?>
                        </ul>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <ul class="list-group">
                            <h4>Family Members</h4>
                            <?php
                            foreach ($members as $member) {
                                echo "<li onclick=showandhide(" . $member['id'] . ") class='list-group-item show' >" . $member['name'] . '<span class="caret"></span></li>';

                                echo "<li style='display:none;' class='allpreventives'  id='" . $member['id'] . "'>";
                                foreach ($member['preventives'] as $preventive) {
                                    echo "<ol>" . $preventive->getName() . '</ol>';
                                }
                                echo "</li>";
                            }
//                            echo "<li  class='list-group-item' ><a href='member_id=".$member->getId()."'>". rtrim($names, ',') . "</a></li>";
//                            echo "<li  class='list-group-item' ><a onclick=updatePreventives('".$member->getId()."')>". rtrim($names, ',') . "</a></li>";
                            ?>
                        </ul>
                    </div>




                </div>


            </div>
        </div>
        <!-- resume -->

    </div>
</div>
</div>

<script>
//$(document).ready(function(){
//     $("ol, p").hide();
//    $("#").click(function(){
//        $(" p,ol").toggle();
//    });
//});
</script>



<script>
    function showandhide(id) {
        if (document.getElementById(id).style.display == 'none') {
//            document.getElementsByClassName('allpreventives').style.display = 'none';
            document.getElementById(id).style.display = 'block';

        } else {
            document.getElementById(id).style.display = 'none';
        }
    }
</script>