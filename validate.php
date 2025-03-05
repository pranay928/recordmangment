<?php 
session_start();
include("connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>

<script src="jquery3-7-1.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <script>
        $(document).ready(function() {
            // Check if session message exists
            if($('.sessionMessage').length > 0) {
               
                setTimeout(function() {
                    $('.sessionMessage').fadeOut('slow');
                }, 3000); 
            }
        });

    </script>
    

<?php

// for Insert
if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Basic validation
    $usernameErr = $passwordErr = "";
    $Validate = true;
    
    if(empty($username)) {
        $usernameErr = "Username is required";
        $Validate = false;
    }
    
    if(empty($password)) {
        $passwordErr = "Password is required";
        $Validate = false;
    }
    
    if($Validate) {
        $sql = "INSERT INTO mysecdtable (username, password) VALUES ('$username', '$password')";

        
        if($conn->query($sql) === TRUE) {
            $_SESSION['message'] =  '<h2 style="color: green;">Record Added Successfully</h2>';

            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
if(isset($_SESSION['message'])) {
   echo '<div class="sessionMessage">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}





// for Delete
if(isset($_GET['delete']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM mysecdtable WHERE id='$id'";
    
    if($conn->query($sql) === TRUE) {
        $_SESSION['message'] =  '<h2 style="color: green;">Record Deleted Successfully</h2>';

        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}


if(isset($_SESSION['message'])) {
    echo '<div class="sessionMessage">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}
 

// for Update
if(isset($_POST['update'])) {
    $id = $_POST['edit_id'];
    $username = $_POST['edit_username'];
    $password = $_POST['edit_password'];
    
    $sql = "UPDATE mysecdtable SET username='$username', password='$password' WHERE id='$id'";

    if($conn->query($sql) === TRUE) {

        $_SESSION['message']= '<h2 style="color: green;">Record Updated Successfully</h2>';

        header("Location: ".$_SERVER['PHP_SELF']);
        exit();

    } else {
        echo "Error updating record: " . $conn->error;
    }
}
if(isset($_SESSION['message'])) {
    echo '<div class="sessionMessage">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
} 

// Get record for editing
$edit_row = null;
if(isset($_GET['edit']) && isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    $sql = "SELECT * FROM mysecdtable WHERE id='$edit_id'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        $edit_row = $result->fetch_assoc();
    }
}
?>

<style>
    
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
}

td, th {
    border: 1px solid black;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}

form {
    margin: 20px 0;
}

input[type="text"] {
    padding: 5px;
    margin: 5px;
}
input[type="password"] {
    padding: 5px;
    margin: 6px;

}

input[type="submit"] {
    padding: 5px 10px;
    background: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}

a {
    text-decoration: none;
    padding: 5px 10px;
    margin: 0 5px;
    color: #000;
    background: #f0f0f0;
    border: 1px solid #ccc;
}

a:hover {
    background: #ddd;
}



</style>

<!-- Form for new records -->
<h2>Add New Record</h2>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <label for="username">Username</label>
    <input type="text" name="username" id="username"><?php echo isset($usernameErr) ? $usernameErr : ''; ?><br>
    <label for="password">Password</label>
    <input type="password" name="password" id="password"><?php echo isset($passwordErr) ? $passwordErr : ''; ?><br>
    <input type="submit" name="submit" value="Add New"> 
</form>

<!-- Table  -->
<h2>Records</h2>
<table>
    <tr>
        <th>Sr No</th>
        <th>Id</th>
        <th>Username</th>
        <th>Password</th>
        <th>Action</th>
    </tr>
    <?php 
    $sql = "SELECT id, username, password FROM mysecdtable order by id desc";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        //for sr. no.
         $num =0;
        while($row = $result->fetch_assoc()) { 
            if(isset($edit_row) && $edit_row['id'] == $row['id']) {
                // Show edit form in table
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td colspan="2">
                        <form method="post" action="">
                            <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="edit_username" value="<?php echo $row['username']; ?>">
                            <input type="text" name="edit_password" value="<?php echo $row['password']; ?>">
                            <input type="submit" name="update" value="Update">
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Cancel</a>
                         
                        </form>
                    </td>
                    <td></td>
                </tr>
                <?php
            } else {
                // Show normal row
                ?>
                <tr>
                    <td> <?php echo ++$num; ?></td>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['password']; ?></td>
                    <td>
                        <a href="?edit=true&id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="?delete=true&id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php
            }
        }
    } else {
        echo "<tr><td colspan='4'>0 results</td></tr>";
    }
    ?>
</table></body>
</html>












