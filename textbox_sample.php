<!DOCTYPE html>
<html>
<head><title>GoatSupporter</title>

<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta charset = "utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


<script type="text/javascript" src="js/jquery.min.js"></script>
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
      <li class="feedback">
      <!-- スポット名称はプラン内に含まれている名称,DBから取得-->
        <div style="text-decoration:underline;">
        以下のスポットには訪問しましたか?<br></div>
        <div class="spot">
          <form class="feedbackforms" method = "post" action = "" target = "questionnaire.html">
            <label for "2-1"><input name="isVisit" type= "checkbox" id="2-1" value = "1"  >
              スポット名称1</label>
            <div>
              <input class="review" id="review_1" type="text"></input>
            </div>
          </form>
          <button class="btn btn-salmon" id ="b2-1"
              onClick="javascript: isCheck('2-1', '1');">Review</button>
          <br>
        </div>
      </li>
</body>

</html>
