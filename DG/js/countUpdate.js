
        function loadData() {
            $.ajax({
                url: "dashboard.php",
                type: "GET",
                success: function(response){
                    $("#nou").html(response);
                }
            });
        }