<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $error = "Your session has expired. Please log in again.";
}
?>
<html>
<head>
    <title>Session Expired</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php if (isset($error)) { ?>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="sessionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Session Expired</h5>
          </div>
          <div class="modal-body">
            <?php echo $error; ?>
          </div>
          <div class="modal-footer">
            <a href="login.php" class="btn btn-primary">Login Again</a>
          </div>
        </div>
      </div>
    </div>
<?php } ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    var sessionModal = new bootstrap.Modal(document.getElementById('sessionModal'), {});
    sessionModal.show(); // Show the modal on page load
</script>

</body>
</html>
