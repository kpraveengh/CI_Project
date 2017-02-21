


<div class="container">
    <h2>Hover Rows</h2>           
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                echo "<tr>";
                echo "<td>".$admins->getName()."</td>";
                echo "<td>".$admins->getEmail()."</td>";
                
                echo "</tr>";
            ?>
            
           
        </tbody>
    </table>
</div>
