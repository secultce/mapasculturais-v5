</body>

<script>
    $(document).ready(function() {
        window.print();
        $("#btn-print-report").hide();
        setTimeout(() => {
            $("#btn-print-report").show();
        }, 500);
        $("#btn-print-report").click(function(e) {
            e.preventDefault();
            setTimeout(() => {
                $("#btn-print-report").hide();
                window.print();
            }, 500);
            setTimeout(() => {
                $("#btn-print-report").show();
            }, 500);
        });

    });
</script>

</html>
