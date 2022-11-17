<!DOCTYPE html>
<html lang="en">
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
        .mosaic-wrap { width: 700px; }
        .tile, .col { float: left; }

        .blue { background: #2C82C9; }
        .red { background: #FC6042; }
        .green { background: #2CC990; }
        .yellow { background: #EEE657; }
        .orange { background: #FCB941; }
        .grey { background: #545454; }

        .w100 { width: 100px; }
        .w200 { width: 200px; }
        .w300 { width: 300px; }
        .w400 { width: 400px; }
        .h100 { height: 100px; }
        .h200 { height: 200px; }
        </style>    
    </head>
    <body>
    <div class="mosaic-wrap">
    <div class="col w100">
        <div class="tile w100 h100 blue">1</div>
        <div class="tile w100 h100 red">2</div>
        <div class="tile w100 h100 orange">3</div>
        <div class="tile w100 h100 green">4</div>
        <div class="tile w100 h100 yellow">5</div>
    </div>
    
    <div class="col w400">
        <div class="tile w200 h200 green">6</div>
        <div class="col w200">
            <div class="tile w100 h100 yellow">7</div>
            <div class="tile w100 h100 red">8</div>
            <div class="tile w100 h100 orange">9</div>
            <div class="tile w100 h100 blue">10</div>
        </div>
        <div class="col">
            <div class="tile w100 h100 red">11</div>
            <div class="tile w300 h100 grey">12</div>
        </div>
    </div>
    <div class="col w200">
        <div class="tile w100 h100 orange">13</div>
        <div class="tile w100 h100 green">14</div>
        <div class="tile w200 h200 red">15</div>
    </div>
    <div class="col w100">
        <div class="tile w100 h100 orange">16</div>
        <div class="tile w100 h100 blue">17</div>
    </div>
    <div class="tile w100 h200 yellow">18</div>
    <div class="tile w300 h200 orange">19</div>
    <div class="tile w100 h100 green">20</div>
    <div class="tile w100 h100 red">21</div>
</div>
    </body>
</html>