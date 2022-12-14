<html>
<head>
<!-- Bootstrap 4.5 CSS-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

<!-- Bootstrap JS Requirements -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-4">
                <label for="ruta" class="form-label">Route</label>
                <input type="text" class="form-control" id="ruta" aria-describedby="rutaHelp">
                <div id="rutaHelp" class="form-text">Incluir ROOT PATH.</div>
            </div>
            <div class="col-5">
                <label for="postval" class="form-label">POST</label>
                <input type="text" class="form-control" id="postval" aria-describedby="postvalHelp">
                <div id="postvalHelp" class="form-text">String del POST vía Javascript.</div>
            </div>
            <div class="col-3">
                <br/>
                <button type="button" class="btn btn-primary" onclick="sendPost()">Submit</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12" id="response">
            
            </div>
        </div>
    </div>
    <script>

    function sendPost(){
        var ruta = $("#ruta").val();
        var postval = $("#postval").val();
        $.post('http://servermatrixxxb.ddns.net:8181/intranet/'+ruta, postval, function(resp){
            $("#response").html(resp)
        });
    }

    </script>
</body>
</html>