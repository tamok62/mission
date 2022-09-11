<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
    <h1>Web掲示板</h1>
    <form action = "" method = "POST">
      <h2>コメント投稿</h2>
        <input type = "text" placeholder = "名前" name = "name"><br>
        <input type = "text" placeholder = "コメント" name = "comment">
        <input type = "submit" value = "投稿" name = "submit">
      <h2>コメント削除</h2>
        <input type = "number" placeholder = "削除対象番号" name = "delete">
        <input type = "submit" value = "削除" name = "submit2">
      <h2>コメント編集</h2>
        <input type = "number" placeholder = "編集対象番号" name = "edit_number"><br>
        <input type = "text" placeholder = "新しいコメント" name = "new_comment">
        <input type = "submit" value = "編集" name = "submit3">
    </form>
    <hr>
    <h1>コメント一覧</h1>

    <?php
  //データベース情報
  $hostname = 'localhost';
  $dbname = 'データベース名';
  $user = 'ユーザー名';
  $password = 'パスワード';

  //データソース
  $dbs = "mysql:host=$hostname;dbname=$dbname;charset=utf8";

  //データベースに接続
  $pdo = new PDO($dbs, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

  //入力ミスがあるかどうか調べるための変数
  $errmsg = "";

  //投稿番号を取得
  $sql = "SELECT MAX(id) FROM comment_table";
  $result = $pdo->query($sql);
  foreach($result as $row){
    $last_id = $row[0];
  }

  //投稿機能
  if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $comment = trim($_POST['comment']);

  //名前とコメントが入力されているかどうかをチェック
  if($name == "" || $comment == ""){
    $errmsg = "投稿者名またはコメントが入力されていません";

  //エラーメッセージを表示
    echo "<p><font color='red'>$errmsg</font></p>";
  }

  //入力ミスがなければ以下の処理を行う
    if($errmsg == ""){

        //データベース内にテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS comment_table"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "cdate datetime"
        .");";
        $stm = $pdo->query($sql);

        
  //データ追加のinsert文を作成
        $sql = "insert into comment_table (name, comment, cdate) value (:name, :comment, now())";

  //insert文の実行を準備
        $stm = $pdo -> prepare($sql);

  //プレースホルダに値を設定
        $stm -> bindParam(':name', $name, PDO::PARAM_STR);
        $stm -> bindParam(':comment', $comment, PDO::PARAM_STR);

  //insert文を実行
        $stm -> execute();
    }
  }

  //削除機能
  if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit2'])){
    $delete = @trim($_POST["delete"]);

  //削除対象番号が入力されているかどうかをチェック
  if($delete == ""){
    $errmsg = "削除対象番号が入力されていません";

  //エラーメッセージを表示
    echo "<p><font color='red'>$errmsg</font></p>";

  //投稿番号が存在しない場合
  } else if($delete > $last_id){
    $errmsg = "投稿が存在しません";

  //エラーメッセージを表示
    echo "<p><font color='red'>$errmsg</font></p>";
  }

  //入力ミスがなければ以下の処理を行う
    if($errmsg == ""){

  //データ削除のdelete文を作成
      $sql = "delete from comment_table WHERE id = '" . $delete . "' ";

  //delete文の実行を準備
      $stm = $pdo -> prepare($sql);

  //delete文を実行
      $stm -> execute();
    }
  }

  //編集機能
  if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit3'])){
    $edit_number = @trim($_POST["edit_number"]);
    $new_comment = @trim($_POST["new_comment"]);

  //編集対象番号と新しいコメントが入力されているかどうかをチェック
  if($edit_number == "" || $new_comment == ""){
    $errmsg = "編集対象番号または新しいコメントが入力されていません";

  //エラーメッセージを表示
    echo "<p><font color='red'>$errmsg</font></p>";

  //投稿が存在しない場合
  } else if($edit_number > $last_id){
    $errmsg = "投稿が存在しません";

  //エラーメッセージを表示
    echo "<p><font color='red'>$errmsg</font></p>";
  }

  //入力ミスがなければ以下の処理を行う
    if($errmsg == ""){

  //データ更新のupdate文を作成
      $sql = "update comment_table set comment = '" . $new_comment . "' WHERE id = '" . $edit_number . "' ";

  //update文の実行を準備
      $stm = $pdo -> prepare($sql);

  //update文を実行
      $stm -> execute();
    }
  }

  //テーブルの内容を表示
  $sql = 'SELECT * FROM comment_table order by id DESC';
  $stmt = $pdo->query($sql);
  $results = $stmt->fetchAll();
  foreach ($results as $row){
  //$rowの中にはテーブルのカラム名が入る
  echo $row['id'].'<>';
  echo $row['name'].'<>';
  echo $row['comment'].'<>';
  echo $row['cdate'].'<br>';

  //データベースの接続を解除
  $pdo = null;
  }
?>
<div><a href = "#">トップへ戻る</a></div>
</body>
</html>