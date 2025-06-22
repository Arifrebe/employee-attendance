</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- Datatable -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"></script>

<script>
    new DataTable('#example');

    window.onload = function() {
        var flashMessage = document.getElementById("flashMessage");
        if (flashMessage) {
            setTimeout(function() {
                flashMessage.classList.remove("show");
                flashMessage.classList.add("fade");
                setTimeout(function() {
                    flashMessage.remove();
                }, 500);
            }, 3000);
        }
    };
</script>
</body>

</html>