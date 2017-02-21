          
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
            $i = 1;
            foreach ($admins as $admin) {
                echo "<tr>";
                echo "<td>" . $i++ . "</td>";
                echo "<td>" . $admin->getName() . "</td>";
                echo "<td>" . $admin->getEmail() . "</td>";
                echo "<td><a href='./adminprofile/" . $admin->getId() . "'>View</a>&nbsp;<a href='./editadmin/" . $admin->getId() . "'>Edit</a>&nbsp;<a href='./deleteadmin/" . $admin->getId() . "'>Delete</a></td>";
                echo "</tr>";
            }
            ?>


        </tbody>
    </table>
</div>
