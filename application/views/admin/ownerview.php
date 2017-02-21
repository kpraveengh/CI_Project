



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
                                    <li class="list-group-item"><?php echo $owner->getName(); ?></li>
                                    <li class="list-group-item"><?php echo $owner->getEmail(); ?></li>
                                    <li class="list-group-item"><?php echo $owner->getPhone_no(); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bs-callout bs-callout-danger">
                    <div class="col-xs-12 col-sm-6">
                        <h4>Chefs</h4>
                        <ul class="list-group">
                            <?php
                            $chefname = '';
                            foreach ($chefs as $chef) {
                                echo "<li  class='list-group-item '  >" . $chef->getName() . "<ul class='list-group'>";
                                foreach ($products as $product) {
                                    if ($product->getChef_id() != NULL) {
                                        if ($product->getChef_id()->getId() == $chef->getId()) {
                                            echo "<li  class='list-group-item ' onclick=showandhide(" . $product->getId() . ") >" . $product->getName() . '<span class="caret"></span>';;
                                            echo "<ul class='list-group' style='display:none;' id='" . $product->getId() . "'>";
                                            foreach ($productIngredients as $productIngredient) {
                                                if ($productIngredient->getProduct_id()->getId() == $product->getId()) {
                                                    echo "<li  class='list-group-item ' >" . $productIngredient->getIngredient_id()->getIngredient_name() . "</li>";
                                                }
                                            }
                                            echo "</ul></li>";
                                        }
                                    }
                                }
                                echo "</ul></li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <h4>Products</h4>
                        <ul class="list-group">
                            <?php
                            foreach ($products as $product) {
                                if ($product->getChef_id() == NULL) {
                                    echo "<li onclick=showandhide(" . $product->getId() . ") class='list-group-item show' >" . $product->getName() . '<span class="caret"></span>';
                                    echo "<ul class='list-group' style='display:none;' id='" . $product->getId() . "'>";
                                    foreach ($productIngredients as $productIngredient) {
                                        if ($productIngredient->getProduct_id()->getId() == $product->getId()) {
                                            echo "<li  class='list-group-item ' >" . $productIngredient->getIngredient_id()->getIngredient_name() . "</li>";
                                        }
                                    }
                                    echo "</ul></li>";
                                }
                            }
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
            document.getElementById(id).style.display = 'block';
        } else {
            document.getElementById(id).style.display = 'none';
        }
    }
</script>

<!--<div class="leftsidebar_templ1">
                    <ul id="nav">
                        <li class="expanded"><a class="on">Form a Compalny</a>
                                <ul class="submuneu">
                                    <li><a>United Kingdom (UK)</a></li>
                                    <li><a>United States of America (USA)</a></li>
                                    <li><a>Classic Offshore</a></li>
                                    <li><a>Alternative offshore Companies</a></li>
                                </ul>
                            </div>-->
<!--<script>
    $(document).ready(function(){   
    $('ul li.expanded > a')
    .attr('data-active','0')
    .click(function(event){
       $('.submuneu').hide();    
        if($(this).attr('data-active')==0){
            $(this).parent().find('ul').slideToggle('slow');
            $(this).attr('data-active','1');
        }
        else
          $(this).attr('data-active','0');        
    });
        $('a.on').click(function(){
        $('a.on').removeClass("active");
        $(this).addClass("active");
    });

});
    </script>-->