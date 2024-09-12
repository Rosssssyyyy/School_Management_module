<?php
include "../database/database.php";

session_start();

// Check if the user is an admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
  $_SESSION['error'] = "You do not have permission to access this page!.";
  header("Location: ../index.php");
  exit();
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from the database
$query = "SELECT id, announcement, view FROM announcement";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../asset/css/account-approval.css">
  <!-- Sweet Alert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Announcement</title>
</head>
<body>

  <div class="navbar">
    <a href="../admin/account-approval.php">Accounts</a>
    <a href="../admin/section.php">Section</a>
    <a href="../admin/announcement.php">Announcement</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
  </div>

  <div class="container">
    <button class="button" id="openModal" style="margin-bottom: 10px;">Add Announcement</button>

    <!---------------ADD MODAL---------------------------->
    <div class="modal" id="accountModal">
      <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Add Announcement</h2>

        <form id="addAccountForm" method="post" action="../controller/AdminController/add-announcement.php">

        <textarea name="announcement" rows="5" cols="65" placeholder="Place your announcement here"></textarea>

        <select name="view" id="view">
          <option value="" disabled selected>Who can view this?</option>
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
          <option value="studentTeacher">Teacher and Student</option>
        </select>

          <button type="submit">Add Announcement</button>
        </form>
      </div>
    </div>
     <!---------------ADD MODAL---------------------------->

     <table>
  <thead>
    <tr>
      <th>Announcement</th>
      <th>Who can see it?</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Check if there are rows
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // Ensure the 'id' key exists
            if (isset($row['id'])) {
                // Map view to readable format
                $viewText = '';
                switch ($row['view']) {
                    case 'student':
                        $viewText = 'Student';
                        break;
                    case 'teacher':
                        $viewText = 'Teacher';
                        break;
                    case 'studentTeacher':
                        $viewText = 'Teacher and Student';
                        break;
                }
                // Ensure proper escaping for attributes
                $announcement = htmlspecialchars($row['announcement']);
                $viewText = htmlspecialchars($viewText);
                $id = urlencode($row['id']);
                ?>
                <tr>
                  <td><?php echo $announcement; ?></td>
                  <td><?php echo $viewText; ?></td>
                  <td>
                    <button 
                      type="button" 
                      onclick="location.href='../controller/AdminController/delete-announcement.php?id=<?php echo $id; ?>'"
                      style="background-color: #f44336; color: white; border: none; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 4px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
                <?php
            } else {
                // If 'id' is not present in the result
                echo '<tr><td colspan="3">Error: Missing ID for announcement</td></tr>';
            }
        }
    } else {
        echo '<tr><td colspan="3">No announcements found</td></tr>';
    }
    ?>
  </tbody>
</table>
  </div>
  
  
  <script>
    document.getElementById('openModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'flex';
    });

    document.getElementById('closeModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'none';
    });

    window.addEventListener('click', function(event) {
      if (event.target === document.getElementById('accountModal')) {
        document.getElementById('accountModal').style.display = 'none';
      }
    });
  </script>

<script>
      document.addEventListener('DOMContentLoaded', function () {
          // Check for success message
          <?php if (isset($_SESSION['success'])): ?>
              Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: '<?php echo $_SESSION['success']; ?>',
                  confirmButtonText: 'OK'
              });
              <?php unset($_SESSION['success']); // Clear the session variable ?>
          <?php endif; ?>

          // Check for error message
          <?php if (isset($_SESSION['error'])): ?>
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: '<?php echo $_SESSION['error']; ?>',
                  confirmButtonText: 'Try Again'
              });
              <?php unset($_SESSION['error']); // Clear the session variable ?>
          <?php endif; ?>
      });
    </script>

</body>
</html>