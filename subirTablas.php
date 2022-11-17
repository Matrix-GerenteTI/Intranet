<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Subir Amortizacion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-2">
                    <form method="POST" action="controladores/amortizacion.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="tabla-amortizacion">Tipo de tabla de amortización:</label>
                        <select name="cuenta" class="form-control" id="tabla-amortizacion">

                        </select>
                    </div>
                    <div class="form-group">
                        <input type="file" name="tabla" id="" class="form-control">
                    </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary"value="Registrar">
                        </div>
                    </form>
            </div>
            </div>
        </div>
    </div>
    <script  src="https://code.jquery.com/jquery-3.3.1.min.js"  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="  crossorigin="anonymous"></script>
    <script src="assets/js/amortizacion.js"></script>
</body>
</html>