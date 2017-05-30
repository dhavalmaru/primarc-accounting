<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\jui\Autocomplete;

use yii\jui\DatePicker;
use yii\web\JsExpression;
use yii\db\Query;

// use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GrnSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <link href="assets/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
    <link href="assets/css/style.css" media="screen" rel="stylesheet" type="text/css">
    <script src="assets/js/jquery.min.js" type="text/javascript"></script>
    <script src="../vendors/mustache.js" type="text/javascript"></script>
    <script src="../stream_table.js" type="text/javascript"></script>
    <script src="movie_data.js" type="text/javascript"></script>
    <script src="stream.js" type="text/javascript"></script>
  </head>
  <body> -->
    <div class="container">
      <div class='example'>
        <div class="title">
          <h1><!--<span class="glyphicon glyphicon-filter"> --></span>StreamTable.js</h1>
          <p class='lead'> Streaming example</p>
        </div>
        <div class="progress progress-striped active">
          <div id="record_count" class="progress-bar progress-bar-success" style="width: 0%">0</div>
        </div>
        <span id="found" class="label label-info"></span>
        <table id="stream_table" class='table table-striped table-bordered'>
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Director</th>
              <th>Actor</th>
              <th>Rating</th>
              <th>Year</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <div id="summary"><div>
      </div>
    </div>


    <script id="template" type="text/html">
      <tr>
        <td>{{index}}</td>
        <td>{{record.name}}</td>
        <td>{{record.director}}</td>
        <td>{{record.actor}}</td>
        <td>{{record.rating}}</td>
        <td>{{record.year}}</td>
      </tr>
    </script>
    <script>
      var BASE_URL="<?php echo Url::base(); ?>";


    </script>
<?php 
    

    // $this->registerJsFile(
    //     '@web/js/jquery-ui-1.11.2/jquery-ui.min.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );


    // $this->registerJsFile(
    //     '@web/StreamTable/examples/assets/js/jquery.min.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
    // $this->registerJsFile(
    //     '@web/StreamTable/vendors/mustache.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
    // $this->registerJsFile(
    //     '@web/StreamTable/stream_table.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
    // $this->registerJsFile(
    //     '@web/StreamTable/examples/movie_data.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
    // $this->registerJsFile(
    //     '@web/StreamTable/examples/stream.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );


    // $this->registerJsFile(
    //     '@web/js/test.js',
    //     ['depends' => [\yii\web\JqueryAsset::className()]]
    // );
?>


    

  <!-- </body>
</html> -->
