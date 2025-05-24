<?php
include 'header.php';
// print_r($_POST);
function formatNumber($number)
{
  if (abs($number) >= 1_000_000_000) {
    return round($number / 1_000_000_000, 1) . 'B';
  } elseif (abs($number) >= 1_000_000) {
    return round($number / 1_000_000, 1) . 'M';
  } elseif (abs($number) >= 1_000) {
    return round($number / 1_000, 1) . 'K';
  } else {
    return (string)$number;
  }
}
if (isset($_POST['action']) && $_POST['action'] == 'add') {
  $event_years_ago = $_POST['event_years_ago'];
  $event_title = trim(htmlspecialchars($_POST['event_title'], ENT_QUOTES));
  $event_description = trim(htmlspecialchars($_POST['event_description'], ENT_QUOTES));
  $event_cat = isset($_POST['event_cat']) ? $_POST['event_cat'] : 0;
  $sql = "INSERT INTO events (event_years_ago, event_title, event_description, event_cat) 
            VALUES ('$event_years_ago', '$event_title', '$event_description', '$event_cat')";
  if ($conn->query($sql) === TRUE) {
    // echo "<div class='alert alert-success' role='alert'><b>$event_title</b> added.</div>";
  } else {
    print_r($_POST);
    echo "<div class='alert alert-danger' role='alert'>Error updating record: " . $conn->error;
    echo "<br>SQL: " . $sql . "<br></div>";
  }
} else if (isset($_POST['action']) && $_POST['action'] == 'edit') {
  $id = $_POST['id'];
  $event_years_ago = $_POST['event_years_ago'];
  $event_title = trim(htmlspecialchars($_POST['event_title'], ENT_QUOTES));
  $event_description = trim(htmlspecialchars($_POST['event_description'], ENT_QUOTES));
  $event_cat = isset($_POST['event_cat']) ? $_POST['event_cat'] : 0;
  $sql = "UPDATE events SET event_years_ago = '$event_years_ago', event_title = '$event_title', 
          event_description = '$event_description', event_cat = '$event_cat' WHERE event_id = $id";
  if ($conn->query($sql) === TRUE) {
    echo "<div class='alert alert-success' role='alert'><b>$event_title</b> edited.</div>";
  } else {
    print_r($_POST);
    echo "<div class='alert alert-danger' role='alert'>Error updating record: " . $conn->error;
    echo "<br>SQL: " . $sql . "<br></div>";
  }
}
$total_qty_sql = "SELECT COUNT(*) AS qty FROM events";
$total_qty_result = $conn->query($total_qty_sql);
$total_qty_row = $total_qty_result->fetch_assoc();
$total_qty = $total_qty_row['qty'];
?>

<body>
  <div class="container">
    <p><a href="index.html">Home</a>; total events: <?php echo $total_qty; ?></p>
    <div class="content">
      <h4>Add Events</h4>
      <table class="table table-bordered table-hover mb-5">
        <tr>
          <th>Years Ago</th>
          <th>Title / Category</th>
          <th>Event Description</th>
        </tr>
        <?php
        if (isset($_GET['edit']) && $_GET['edit'] == 1) {
          $id = $_GET['id'];
          $sql = "SELECT * FROM events WHERE event_id = $id";
          $result = $conn->query($sql);
          $row = $result->fetch_assoc();
          echo "<tr>";
          echo "<form action='add.php' method='post' oninput='validateInput(this)'>";
          echo "<input type='hidden' name='action' value='edit'>";
          echo "<input type='hidden' name='id' value='$id'>";
          echo "<td><input type='text' name='event_years_ago' class='form-control width8ch' value='" . $row['event_years_ago'] . "'><button class='btn btn-sm btn-secondary mt-2' id='addBtn'>Save</button></td>";
          echo "<td><input type='text' name='event_title' class='form-control width16ch' 
              value='" . $row['event_title'] . "'>";
          echo "<select name='event_cat' class='form-select'>";
          $sql2 = "SELECT * FROM cat";
          $result2 = $conn->query($sql2);
          while ($cat = $result2->fetch_assoc()) {
            $selected = ($cat['cat_id'] == $row['event_cat']) ? 'selected' : '';
            echo "<option value='" . $cat['cat_id'] . "' $selected>" . $cat['cat_name'] . "</option>";
          }
          echo "</select></td>";
          echo "<td><textarea class='form-control' name='event_description' rows='4' style='width: 80ch;'>";
          echo $row['event_description'] . "</textarea></td>";
          echo "</tr>";
        } else {
        ?>
          <tr>
            <form action="add.php" method="post" oninput="validateInput(this)">
              <input type="hidden" name="action" value="add">
              <td><input type="text" name="event_years_ago" class="form-control width8ch">
                <button class='btn btn-sm btn-secondary mt-2' id='addBtn'>Save</button>
              </td>
              <td><input type="text" name="event_title" class="form-control width16ch">
                <select name="event_cat" class="form-select">
                  <?php
                  $sql = "SELECT * FROM cat";
                  $result = $conn->query($sql);
                  while ($cat = $result->fetch_assoc()) {
                    echo "<option value='" . $cat['cat_id'] . "'>" . $cat['cat_name'] . "</option>";
                  }
                  ?>
                </select>
              </td>
              <td><textarea class="form-control" name="event_description" rows="4" style="width: 80ch;"></textarea></td>
            </form>
          </tr>
        <?php
          $sql = "SELECT * FROM events LEFT JOIN cat ON (events.event_cat = cat.cat_id)
                  ORDER BY event_years_ago DESC";
          $result = $conn->query($sql);
          while ($row = $result->fetch_assoc()) {
            $id = $row['event_id'];
            echo "<tr>";
            echo "<td>" . formatNumber($row['event_years_ago']) . "</td>";
            echo "<td><a href='add.php?edit=1&id=$id'>" . $row['event_title'] . "</a>";
            echo "<br><span class='catName'>" . $row['cat_name'] . "</span></td>";
            echo "<td>" . $row['event_description'] . "</td>";
            echo "</tr>";
          }
        } // END if ($_GET['edit'] == 1)
        ?>
      </table>
      <hr class='my-4'>
      <h4>Categories</h4>
      <table class="table table-bordered table-hover">
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Image</th>
          <th>Qty</th>
        </tr>
        <?php
        $sql = "SELECT * FROM cat";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
          $cat_id = $row['cat_id'];
          $sql2 = "SELECT COUNT(*) AS qty FROM events WHERE event_cat = $cat_id";
          $result2 = $conn->query($sql2);
          $row2 = $result2->fetch_assoc();
          echo "<tr>";
          echo "<td>" . $row['cat_name'] . "</td>";
          echo "<td>" . $row['cat_description'] . "</td>";
          echo "<td class='imageTD'><img src='" . $row['cat_image'] . "' alt='" . $row['cat_name'] . "'></td>";
          echo "<td class='text-center'>" . $row2['qty'] . "</td>";
          echo "</tr>";
        }
        ?>

    </div> <!-- END content -->
  </div> <!-- END container -->
  <script>
    // Validation function
    function validateInput(form) {
      const eventTitle = form.querySelector('[name="event_title"]');
      const eventYearsAgo = form.querySelector('[name="event_years_ago"]');
      const addBtn = form.querySelector('#addBtn');

      // Function to validate integer input
      function isInteger(value) {
        return /^\d+$/.test(value.trim());
      }

      // Check validity of inputs
      const isTitleValid = eventTitle.value.trim().length > 0 && eventTitle.value.length <= 63;
      const isYearValid = eventYearsAgo.value.trim().length > 0 && isInteger(eventYearsAgo.value);

      // Enable/disable button based on validity
      addBtn.disabled = !(isTitleValid && isYearValid);

      // Apply validation feedback
      eventTitle.classList.toggle('is-invalid', !isTitleValid);
      eventYearsAgo.classList.toggle('is-invalid', !isYearValid);
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

<?php
include 'footer.php';
?>