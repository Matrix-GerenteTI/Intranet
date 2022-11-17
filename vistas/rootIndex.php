<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->e( $titulo )  ?></title>
    <!-- DEFAULT SCRIPTS  -->

        <!-- CORE CSS FRAMEWORK - START -->
        <link href="/intranet/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/intranet/assets/plugins/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
        <link href="/intranet/assets/fonts/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="/intranet/assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
        <link href="/intranet/assets/css/bootstrapCheckbox.css" rel="stylesheet" type="text/css"/>
        <link href="/intranet/assets/plugins/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" type="text/css"/>
        
        <!-- CORE CSS FRAMEWORK - END -->
        
                <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 
        <link href="/intranet/assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <!-- <link href="/intranet/assets/plugins/datepicker/css/datepicker.css" rel="stylesheet" type="text/css" media="screen"/> -->
        <!-- <link href="/intranet/assets/plugins/daterangepicker/css/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen"/> -->
        <!-- <link href="/intranet/assets/plugins/timepicker/css/timepicker.css" rel="stylesheet" type="text/css" media="screen"/> -->
        <!-- <link href="/intranet/assets/plugins/datetimepicker/css/datetimepicker.min.css" rel="stylesheet" type="text/css" media="screen"/> -->
        <link href="/intranet/assets/plugins/colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/ios-switch/css/switch.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/tagsinput/css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/typeahead/css/typeahead.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/multi-select/css/multi-select.css" rel="stylesheet" type="text/css" media="screen"/>        <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
        <link href="/intranet/assets/plugins/morris-chart/css/morris.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/rickshaw-chart/css/graph.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/rickshaw-chart/css/detail.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/rickshaw-chart/css/legend.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/rickshaw-chart/css/extensions.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/rickshaw-chart/css/rickshaw.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/rickshaw-chart/css/lines.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/jvectormap/jquery-jvectormap-2.0.1.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/icheck/skins/minimal/white.css" rel="stylesheet" type="text/css" media="screen"/>        <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
        <link href="/intranet/assets/plugins/datatables/css/jquery.dataTables.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/datatables/extensions/Responsive/css/dataTables.responsive.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="/intranet/assets/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" type="text/css" media="screen"/>        <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
        
        <!-- CORE CSS TEMPLATE - START -->
        <link href="/intranet/assets/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/intranet/assets/css/responsive.css" rel="stylesheet" type="text/css"/>
        <link href="/intranet/assets/css/loading.css" rel="stylesheet" type="text/css"/>
        <!-- CORE CSS TEMPLATE - END -->
        
        <link rel="stylesheet" href="/intranet/assets/css/animate.min.css">

        <!-- DEFAULT CSS -->
            <!-- CORE CSS TEMPLATE - START -->
            <link href="/intranet/assets/css/style.css" rel="stylesheet" type="text/css"/>
            <link href="/intranet/assets/css/responsive.css" rel="stylesheet" type="text/css"/>
            <link href="/intranet/assets/css/loading.css" rel="stylesheet" type="text/css"/>
            <!-- CORE CSS TEMPLATE - END -->

            <?= $this->section("styles") ?>

            <style>
                
            </style>
</head>
<body>
<div class="cargaSeccion" style="background:rgba(1,0,0,0.1); z-index:1; position:absolute; width:100%;height:10000px;overflow:hidden;display:none">
            <div class="container">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                        <div class="loading">
                            <div>
                                <div class="c1"></div>
                                <div class="c2"></div>
                                <div class="c3"></div>
                                <div class="c4"></div>
                            </div>
                            <span>Cargando datos</span>
                        </div>      
                </div>
                <div class="col-md-4"></div>
            </div>
    </div>

    <?php $this->insert("/sections/topBar") ?>

    <?php $this->insert("/sections/menu")  ?>

    <?= $this->section("maincontent") ?>

    <?php $this->insert("/sections/chat") ?>


    <!-- CORE JS FRAMEWORK - START --> 
    <script src="/intranet/assets/js/jquery-1.11.2.min.js" type="text/javascript"></script> 
    <script src="/intranet/assets/js/jquery.easing.min.js" type="text/javascript"></script> 
    <script src="/intranet/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
    <script src="/intranet/assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
    <script src="/intranet/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js" type="text/javascript"></script> 
    <script src="/intranet/assets/plugins/viewport/viewportchecker.js" type="text/javascript"></script>  
    <!-- CORE JS FRAMEWORK - END --> 

    <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 
		
        <script src="/intranet/assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script>
        <!-- <script src="/intranet/assets/plugins/datepicker/js/datepicker.js" type="text/javascript"></script>  -->
        <!-- <script src="/intranet/assets/plugins/daterangepicker/js/moment.min.js" type="text/javascript"></script> -->
        <!-- <script src="/intranet/assets/plugins/daterangepicker/js/daterangepicker.js" type="text/javascript"></script> -->
        <!-- <script src="/intranet/assets/plugins/timepicker/js/timepicker.min.js" type="text/javascript"></script> -->
        <!-- <script src="/intranet/assets/plugins/datetimepicker/js/datetimepicker.min.js" type="text/javascript"></script> -->
        <!-- <script src="/intranet/assets/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.fr.js" type="text/javascript"></script> -->
        <!-- <script src="/intranet/assets/plugins/colorpicker/js/bootstrap-colorpicker.min.js" type="text/javascript"></script> -->
        <script src="/intranet/assets/plugins/tagsinput/js/bootstrap-tagsinput.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/select2/select2.min.js" type="text/javascript"></script> 
        <script src="/intranet/assets/plugins/typeahead/typeahead.bundle.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/typeahead/handlebars.min.js" type="text/javascript"></script> 
        <script src="/intranet/assets/plugins/multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/multi-select/js/jquery.quicksearch.js" type="text/javascript"></script> <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
        <script src="/intranet/assets/plugins/autosize/autosize.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script><!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
        <script src="/intranet/assets/plugins/inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/autonumeric/autoNumeric.js" type="text/javascript"></script><!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
        <script src="/intranet/assets/plugins/datatables/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.js" type="text/javascript"></script><!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 
        <script src="/intranet/assets/plugins/rickshaw-chart/vendor/d3.v3.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/rickshaw-chart/js/Rickshaw.All.js"></script>
        <script src="/intranet/assets/plugins/sparkline-chart/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/morris-chart/js/raphael-min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/morris-chart/js/morris.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/jvectormap/jquery-jvectormap-2.0.1.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/gauge/gauge.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        
        <!-- CORE TEMPLATE JS - START --> 
        <script src="/intranet/assets/js/scripts.js" type="text/javascript"></script>  
        <!-- END CORE TEMPLATE JS - END --> 

        <!-- Sidebar Graph - START --> 
        <script src="/intranet/assets/plugins/sparkline-chart/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="/intranet/assets/js/chart-sparkline.js" type="text/javascript"></script>
        <script src="/intranet/assets/js/displayRecursosDepartamentos.js" type="text/javascript"></script>
        <!-- Sidebar Graph - END --> 

    <?= $this->section("scripts") ?>

</body>
</html>