<?php $urlArchivo = $_GET['directorio']."/".$_GET['archivo'] ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/intranet/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <style>
             html, body {
                height: 100%;
                margin: 0px;
            }
        </style>

    </head>
    <body >
            <embed src="http://servermatrixxxb.ddns.net:8181/intranet/documentos/sgc/<?= $urlArchivo ?>#toolbar=0&navpanes=0&scrollbar=0" class="col-md-12" style="height:100%;width:100%;">
            <script src="/intranet/assets/js/jquery-1.11.2.min.js" ></script>
            <script src="/intranet/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
        <script>
                
    //Disable part of page
    $("embed").on("contextmenu",function(e){
        return false;
    });
</script>


    </body>
</html>