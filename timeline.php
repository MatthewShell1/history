<?php
function formatNumber($number)
{
  if (abs($number) >= 1_000_000_000) {
    return round($number / 1_000_000_000, 1) . ' Billion Years Ago';
  } elseif (abs($number) >= 1_000_000) {
    return round($number / 1_000_000, 1) . ' Million Years Ago';
  } elseif (abs($number) >= 1_000) {
    return number_format($number) . ' Years Ago';
  } else {
    return (string)$number . ' Years Ago';
  }
}
include 'header.php';

// Fetch all categories for checkbox generation
$catSql = "SELECT * FROM cat";
$catResult = $conn->query($catSql);
?>

<style>
  .filter-options {
    text-align: center;
    margin: 20px 0;
  }

  .filter-options button {
    padding: 8px 16px;
    margin: 0 5px;
    background-color: #E4FDE1;
    color: #2e364a;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
  }

  .filter-options button:hover {
    background-color: #C4E8C1;
  }

  .checkbox-group {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 15px;
  }

  .checkbox-group label {
    margin: 5px 10px;
    color: #ddd;
    font-weight: 400;
  }

  .checkbox-group input[type="checkbox"] {
    margin-right: 5px;
    vertical-align: middle;
  }

  .toggle-description {
    display: inline-block;
    margin-top: 5px;
    color: #0066cc;
    cursor: pointer;
    text-decoration: underline;
  }
</style>

<body id='tLbody'>
  <div class='filter-options'>
    <button id="showAll" type="button">Show All</button>
    <button id="removeAll" type="button">Remove All</button>
    <div class='checkbox-group'>
      <?php while ($catRow = $catResult->fetch_assoc()) { ?>
        <label>
          <input type="checkbox" class="category-filter" value="<?php echo $catRow['cat_id']; ?>" checked>
          <span><?php echo $catRow['cat_name']; ?></span>
        </label>
      <?php } ?>
    </div>
  </div>

  <div class='timeline'>
    <?php
    $counter = 0;
    $sql = "SELECT * FROM events LEFT JOIN cat ON events.event_cat = cat.cat_id
            ORDER BY event_years_ago DESC LIMIT 0,100";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
      $containerClass = ($counter % 2 === 0) ? 'left-container' : 'right-container';
      $contArrowClass = ($counter % 2 === 0) ? 'left-container-arrow' : 'right-container-arrow';
      $id = $row['event_id'];
      // Get the first 160 characters of the description
      $fullDescription = $row['event_description'];
      $isTextTruncated = strlen($fullDescription) > 160;
      $shortDescription = $isTextTruncated ? substr($fullDescription, 0, 160) . '...' : $fullDescription;

      echo "<div class='tLcontainer $containerClass' data-category='{$row['event_cat']}'>";
      echo "<img src='" . $row['cat_image'] . "' alt='" . $row['cat_name'] . "'>";
      echo "<div class='text-box'>";
      echo "<h4>" . $row['event_title'] . "</h4>";
      echo "<small>" . formatNumber($row['event_years_ago']) . "</small>";
      echo "<p>";
      echo "<span class='short-description'>$shortDescription</span>";
      echo "<span class='full-description' style='display: none;'>$fullDescription</span>";
      if ($isTextTruncated) {
        echo "<br><a href='javascript:void(0);' class='toggle-description'>More</a>";
      }
      echo "</p>";
      echo "<span class='$contArrowClass'></span>";
      echo "</div>"; // text-box
      echo "</div>"; // tLcontainer
      $counter++;
    }
    ?>
  </div>

  <script>
    // Handle animation delay for timeline containers
    const containers = document.querySelectorAll('.tLcontainer');
    containers.forEach((container, index) => {
      container.style.animationDelay = `${index}s`;
    });

    // Handle "More/Less" toggle
    document.addEventListener('DOMContentLoaded', () => {
      const toggleLinks = document.querySelectorAll('.toggle-description');
      toggleLinks.forEach(link => {
        link.addEventListener('click', function() {
          const shortDesc = this.parentNode.querySelector('.short-description');
          const fullDesc = this.parentNode.querySelector('.full-description');

          if (this.textContent === 'More') {
            fullDesc.style.display = 'inline';
            shortDesc.style.display = 'none';
            this.textContent = 'Less';
          } else {
            fullDesc.style.display = 'none';
            shortDesc.style.display = 'inline';
            this.textContent = 'More';
          }
        });
      });

      // Handle checkbox filtering
      const checkboxes = document.querySelectorAll('.category-filter');
      const showAllBtn = document.getElementById('showAll');
      const removeAllBtn = document.getElementById('removeAll');
      const timelineContainers = document.querySelectorAll('.tLcontainer');

      // Update visibility based on selected categories
      function updateFilters() {
        const selectedCategories = Array.from(checkboxes)
          .filter(checkbox => checkbox.checked)
          .map(checkbox => checkbox.value);

        // First determine which containers will be visible
        let visibleContainers = [];
        timelineContainers.forEach(container => {
          const category = container.getAttribute('data-category');
          if (selectedCategories.includes(category)) {
            container.style.display = 'block';
            visibleContainers.push(container);
          } else {
            container.style.display = 'none';
          }
        });
        
        // Then update the left/right classes for visible containers
        visibleContainers.forEach((container, index) => {
          // Remove existing position classes
          container.classList.remove('left-container', 'right-container');
          
          // Find and remove arrow classes
          const arrow = container.querySelector('[class$="-container-arrow"]');
          if (arrow) {
            arrow.classList.remove('left-container-arrow', 'right-container-arrow');
          }
          
          // Add new position classes based on index
          if (index % 2 === 0) {
            container.classList.add('left-container');
            if (arrow) arrow.classList.add('left-container-arrow');
          } else {
            container.classList.add('right-container');
            if (arrow) arrow.classList.add('right-container-arrow');
          }
        });
      }

      // Add event listeners to checkboxes
      checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateFilters);
      });

      // Show all button functionality
      showAllBtn.addEventListener('click', () => {
        checkboxes.forEach(checkbox => checkbox.checked = true);
        updateFilters();
      });

      // Remove all button functionality
      removeAllBtn.addEventListener('click', () => {
        checkboxes.forEach(checkbox => checkbox.checked = false);
        updateFilters();
      });
    });
  </script>
</body>

<?php include 'footer.php'; ?>