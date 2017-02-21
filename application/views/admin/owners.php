          
 <ol class="breadcrumb" >
     <a class="btn btn-primary" style="float: right" href="../admin/createnew">Create new</a>
</ol>
<div class="container">
    <h2>Admins</h2>           
    <table class="table table-hover">
        <thead>
            <tr>
                <th>S.no</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i=1;
            
            foreach ($owners as $owner) {
                echo "<tr>";
                echo "<td>".$i++."</td>";
                echo "<td>".$owner->getName()."</td>";
                echo "<td>".$owner->getEmail()."</td>";
                echo "<td><a href='./ownerprofile/".$owner->getId()."'>View</a>&nbsp;<a href='./editowner/".$owner->getId()."'>Edit</a>&nbsp;<a href='./deleteowner/".$owner->getId()."'>Delete</a></td>";
                echo "</tr>";
            }
            ?>
            
           
        </tbody>
    </table>
</div>
