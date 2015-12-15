<?php
require_once('phpconf.php');
require_once('phpfunc.php');

session_cache_expire(0);
session_cache_limiter('private_no_expire');
session_start();

// デバッグ用
if (!isset($_SESSION['userid'])) {
  header('Location: '.SITE_URL.'signin.php');
  exit;
}

if (!isset($_SESSION['planid'])) {
  header('Location: '.SITE_URL.'form.php');
  exit;
}

$planid = $_SESSION['planid'];

// デバッグ用変数
//$planid = 22;

// プランに登録されているスポット取得
$sql = <<<EOS
  Select * From Plans as pn
  Inner join course as co
  On pn.PlanID = :planid
  And pn.PlanID = co.PlanID
  Inner join Spots as sp
  On co.SpotID = sp.SpotID
  Order by Schedule ASC;
EOS;

$i = 0;

// データベース接続
$pdo = connectDB();

$stmt = $pdo->prepare($sql);
$stmt->execute(array(':planid' => $planid));
$result = array();
while ($spot = $stmt->fetch(PDO::FETCH_ASSOC)) {
  // var_dump($spot);
  foreach (array('PlanID', 'UserID', 'OpenID', 'SpotID', 'PriceID', 'CategoryCode') as $k) {
    $spot[$k] = (int)($spot[$k]);
  }
  foreach (array('Name', 'Kana', 'Description', 'ImagePath', 'TEL', 'URL') as $k) {
    $spot[$k] = h($spot[$k]);
  }
  $spot['Schedule'] = convertDatetime($spot['Schedule'], 0);
  $result[] = $spot;
}


?>

<!DOCTYPE html>
<html>
  <head>
    <title>GoatSupporter</title>

    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <meta charset = "utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/feedback.css">

    <link rel="stylesheet" type="text/css" href="css/inlineliststyle.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>

    <!-- Using Font Awesome-->
    <link rel="stylesheet" href="css/font-awesome.min.css">

    <!-- ナビゲーション用の スタイル-->
    <link rel="stylesheet" type="text/css" href="css/navigation.css"> 

    <!-- View Tooltip (Using tooltip.js in Bootstrap)--> 
    <script type="text/javascript" src="js/tooltip.js"></script> 
    <!--画面サイズが500px以上のメディア(すまほ)ではツールチップ表示-->
    <script type="text/javascript">
$(document).ready(function(){
  if (window.matchMedia('screen and (min-width:800px)').matches) { 
    //500px以上の処理
  } else {
    $('[data-toggle="tooltip"]').tooltip();
  }
});
    </script>

    <!-- ここまでナビゲーション用の スタイル-->

    <!-- for iCheck -->
    <script type="text/javascript" src="js/icheck.js"></script>
    <link rel="stylesheet" type="text/css" href="css/skins/flat/red.css">

    <script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_flat',
    radioClass: 'iradio_flat',
    //increase checkable area (max:200%)
    increaseArea: '20%'
  });
});
    </script>

    <script>
$(document).ready(function(){
  $('input').iCheck({
    checkboxClass: 'icheckbox_flat-red',
    radioClass: 'iradio_flat-red',
    increaseArea: '20%'
  });
});
    </script>

    <!-- 年齢が正当な値かチェック -->
    <script type="text/javascript">
$(function(){
  $("input[value=submit]").click(function() {
    if ($("input[type=number][id=age]").val() <= 0) {
      res = "負の値です" ;
      $("div.error").text(res);
    }
  })
});
    </script>

    <!-- Feedbackは任意なので入力内容チェックしなくていいかも... -->

    <script type="text/javascript">
$(function(){
  var res = "\n";
});
    </script>

    <!-- For modal.js-->
    <script type="text/javascript" src="js/modal.js"></script>
    <script type="text/javascript">
$('#myModal').on('shown.bs.modal', function () {
  $('#myInput').focus()
})
    </script>
    <script>
function isCheck(id, revid){
  cid = '#' + id;
  rid = '#review_' + revid;
  // チェックされている場合はテキストボックス表示
  // されていない場合はアラート
  if($(cid).is(':checked')) {
    $(rid).show();
  }
  else {
    $(rid).hide();
    alert("チェックされていません。");
  }
}
    </script>

    <!-- テキストボックスのクラスを非表示にしておく -->
    <style type="text/css">
.review {
  display: none;
}
    </style>
  </head>

  <body>
    <!-- ナビゲーション部分 -->
    <!-- TODO:選択されているページ部分の色を変更する-->
    <!-- Static navbar -->
    <div class="navbar navbar-default" role="navigation">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
      </div> <!-- navbar-header -->

      <!-- ナビゲーションバー -->
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li >
            <a href="dashboard.html" class="nav-href" data-toggle="tooltip" data-placement="bottom"  title="Dashboard">
              <i class="fa fa-home"></i> 
              <span class="navname">Dashboard</span>
              <div class="tooltip">Dashboard</div>
            </a>
          </li>
          <li class="active">
            <a href="form.html" class="nav-href" data-toggle="tooltip" data-placement="bottom"  title="Form">
              <i class="fa fa-pencil-square"></i>
              <span class="navname">Form</span>
              <div class="tooltip"><span>Form</span></div>
            </a>
          </li>
          <li>
            <a href="questionnaire.html" class="nav-href" data-toggle="tooltip" data-placement="bottom"  title="Questionnaire">
              <i class="fa fa-dot-circle-o"></i>
              <span class="navname">Questionnaire</span>
              <div class="tooltip"><span>Questionnaire</span></div>
            </a>
          </li>
          <li>
            <a href="inline_spotlist_iframe.html" class="nav-href" data-toggle="tooltip" data-placement="bottom"  title="Spot List">
              <i class="fa fa-list-alt"></i>
              <span class="navname">Spot List</span>
              <div class="tooltip"><span>Spot List</span></div>
            </a>
          </li>
          <li>
            <a href="timeline.html" class="nav-href" data-toggle="tooltip" data-placement="bottom"  title="Timeline">
              <i class="fa fa-calendar"></i>
              <span class="navname">Timeline</span>
              <div class="tooltip"><span>Timeline</span></div>
            </a>
          </li>
        </ul>
      </div><!--/.nav-collapse -->
    </div><!-- navbar-default -->
    <!-- ここまでナビゲーション部分 -->

    <!-- フォーム群  -->
    <div class="header">
      <span>Please Feedback for plan </span>
    </div>

    <div class="container">
      <ul class="feedbackforms">
        <!-- アクション先phpファイルを入れる-->
        <form class="feedbackforms" method = "POST" action = "Feedback_send.php">
          <fieldset>
            <li class="feedback" requied>
              <div style="text-decoration:underline;">
                プランは役に立ちましたか?<br>
                (5段階評価)<br>
              </div>
              <label for="1-1"><input type="radio" name="satisfy" id="1-1" value="1">1</label>
              <label for="1-2"><input type="radio" name="satisfy" id="1-2" value="2">2</label> 
              <label for="1-3"><input type="radio" name="satisfy" id="1-3" value="3">3</label>
              <label for="1-4"><input type="radio" name="satisfy" id="1-4" value="4">4</label> 
              <label for="1-5"><input type="radio" name="satisfy" id="1-5" value="5">5</label> 
              <!-- </li> -->
              <!-- <li class="feedback"> -->
              <!--   <!&#45;&#45; <form class="feedbackforms" method = "post" action = "" target = "questionnaire.html"> -->
              <!--     <fieldset> &#45;&#45;> -->
              <div style="text-decoration:underline;">
                その他, アプリに対する感想や改善要求がありましたらご記入ください。<br></div>
              <label for="impression"><input name="impression" type="text"></label>
              <br>
                <!-- 結果を送信 -->
                <input type="submit"  class="btn btn-salmon" value="submit" />
                </li>
          </fieldset>
        </form> 

        <!-- </fieldset>
          </form> -->
          <li class="feedback">
            <!-- スポット名称はプラン内に含まれている名称,DBから取得-->
            <div style="text-decoration:underline;">
              以下のスポットには訪問しましたか?<br></div>
            <?php
            for ($i = 1; $i < count($result)+1; $i++) {
            // スポット表示を変数化
            echo <<<_SPOT_LIST_
            <div class="spot">
              <!-- <form class="feedbackforms" method ="post" action ="" target ="questionnaire.html"> -->
            <label for="2-$i"><input name="isVisit" type="checkbox" id="2-$i" value = "1" >{$result[$i-1]['Name']}</label>
            <div>
              <input class="review" id="review_$i" type="text"></input>
            </div>
            <!-- </form> -->
            <button class="btn btn-salmon" id = "b2-$i" onClick = "javascript: isCheck('2-$i', '$i');">Review</button><br>
            <!-- <button class="btn btn-salmon" data-toggle="modal" data-target="#Modal_$i">Review</button> -->
            </div>
_SPOT_LIST_;
                }
                ?>
              </li>
              <!-- </form> -->
      </ul>
      <div class="error"></div>
      <!--<input type="submit" id="submit_button" >-->
      </fieldset>
      </form>
    </div>
    <?php
        for ($i = 1; $i < count($result) + 1; $i++) {
        // Modalの表示を変数化
        echo <<<_MODAL_
        <!-- Modal fade $i -->
        <div id="Modal_$i" class="modal fade reviewmodal">
            <div class="modal-dialog">
                <div class="modal-content" style="text-align: left;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><span class="spotname">{$result[$i - 1]['Name']} のレビュー</span> </h4>
                      </div> <!-- modal header -->
                    <div class="modal-body" align="center">
                        <form class="review" method = "post" action="">
                            <div style="text-decoration:underline;">
                                このスポットはどうでしたか?<br>
                                (5段階評価)<br>
                              </div> <!-- ext-decoration -->
                            <label for="Modal_$i-1"><input name="Modal_{$i}_satisfy" type="radio" id="Modal_$i-1" value = "1">
                                1</label>
                            <label for="Modal_$i-2"><input name="Modal_{$i}_satisfy" type="radio" id="Modal_$i-2" value = "2">
                                2</label>
                            <label for="Modal_$i-3"><input name="Modal_{$i}_satisfy" type="radio" id="Modal_$i-3" value = "3">
                                3</label>
                            <label for="Modal_$i-4"><input name="Modal_{$i}_satisfy" type="radio" id="Modal_$i-4" value = "4">
                                4</label> 
                            <label for="Modal_$i-5"><input name="Modal_{$i}_satisfy" type="radio" id="Modal_$i-5" value = "5">
                                5</label>
                            <br>
                            <div style="text-decoration:underline;">
                                レビュー入力(255文字以内)
                              </div><br> <!-- text decoration -->
                            <input name="reviewsentence" type= "textarea" maxlength="255" rows="2">
                          </form>
                      </div> <!-- modal body -->
                    <div class="modal-footer">
                        <div class="row">
                            <form class="review" method = "post" action="">
                                <input type="submit" class="btn btn-salmon" value="save"> </input>
                                <input type="button" data-dismiss="modal" class="btn btn-close" value="close"></input>
                              </form>
                          </div>
                      </div> <!-- modal footer -->
                  </div> <!-- modal content -->
              </div> <!-- modal dialog -->
          </div>
_MODAL_;
       }
    ?>
  </body>
  <?php
  // データベース切断
  $pdo = null;
  ?>
</html>
