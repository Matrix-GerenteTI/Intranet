/**
 * @Package: Ultra Admin HTML Theme
 * @Since: Ultra 1.0
 * This file is part of Ultra Admin Theme HTML package.
 */






    /******************************
     initialize respective scripts 
     *****************************/
    $(document).ready(function() {
        /*Line Graph*/
        /* data stolen from http://howmanyleft.co.uk/vehicle/jaguar_'e'_type */
        var day_data = [{
            "period": "2012-10-01",
            "licensed": 3407,
            "sorned": 660
        }, {
            "period": "2012-09-30",
            "licensed": 3351,
            "sorned": 629
        }, {
            "period": "2012-09-29",
            "licensed": 3269,
            "sorned": 618
        }, {
            "period": "2012-09-20",
            "licensed": 3246,
            "sorned": 661
        }, {
            "period": "2012-09-19",
            "licensed": 3257,
            "sorned": 667
        }, {
            "period": "2012-09-18",
            "licensed": 3248,
            "sorned": 627
        }, {
            "period": "2012-09-17",
            "licensed": 3171,
            "sorned": 660
        }, {
            "period": "2012-09-16",
            "licensed": 3171,
            "sorned": 676
        }, {
            "period": "2012-09-15",
            "licensed": 3201,
            "sorned": 656
        }, {
            "period": "2012-09-10",
            "licensed": 3215,
            "sorned": 622
        }];
        Morris.Line({
            element: 'morris_line_graph',
            data: day_data,
            resize: true,
            xkey: 'period',
            ykeys: ['licensed', 'sorned'],
            labels: ['Licensed', 'SORN'],
            lineColors: ['#9972b5', '#1fb5ac'],
            pointFillColors: ['#fa8564']

        });
    });

    $(window).resize(function() {});

    $(window).load(function() {});

});
