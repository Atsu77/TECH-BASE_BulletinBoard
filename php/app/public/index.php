<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Bulletin board</title>
</head>

<body>
  <div>
    <?php
    include "./operation.php";

    $edit_flag = isset($_POST['edit_flag']) ? true : false;
    $dsn = 'mysql:dbname=bulletin_board;host=db;';
    $user = 'atsu';
    $password = 'password';
    $driver_options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
    ];
    $tbname = 'tbtest';
    $pdo = new PDO($dsn, $user, $password, $driver_options);

    # 新規投稿の場合
    if (
      isset($_POST['name'], $_POST['comment'], $_POST['password'])
      && $_POST['action'] == '投稿'
      && !$edit_flag
    ) {
      try {
        $name = $_POST['name'];
        $comment = $_POST['comment'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO tbtest(name, comment, password) VALUES(:name, :comment, :password)');
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':comment', $comment);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
      } catch (PDOException $Exception) {
        echo_alert("エラー:" . $Exception->getMessage());
      }
    }


    # 投稿を削除する場合
    if (isset($_POST['delete_post_num'], $_POST['password'])) {
      $delete_post_num = $_POST['delete_post_num'];
      $password = $_POST['password'];
      try {
        $stmt = $pdo->prepare('SELECT password FROM tbtest WHERE id = :delete_post_num');
        $stmt->bindValue(':delete_post_num', $delete_post_num);
        $stmt->execute();
        $res = $stmt->fetch();
        $confirm_password = $res['password'];
        if (password_verify($password, $confirm_password)) {
          $stmt = $pdo->prepare('DELETE FROM tbtest WHERE id = :delete_post_num');
          $stmt->bindValue(':delete_post_num', $delete_post_num);
          $stmt->execute();
        } else {
          echo_alert('パスワードが間違っています');
        }
      } catch (PDOException $Exception) {
        echo_alert("エラー:" . $Exception->getMessage());
      }
    }



    //$matched_post_num = false;
    //$stmt->bindValue(':name', $name);
    //$stmt->bindValue(':comment', $comment);
    //$stmt->bindValue(':password', $password);
    //if (!$matched_post_num) echo_alert('投稿番号' . $delete_post_num . 'は存在しません');
    //}

    //# 編集する投稿番号を指定する場合
    //if (
    //  isset($_POST['edit_num'], $_POST['password'])
    //  && $_POST['action'] == '編集'
    //) {
    //  $edit_num = $_POST['edit_num'];
    //  $confirm_password = $_POST['password'];
    //  $matched_post_num = false;
    //  foreach ($lines as $line) {
    //    $post_elements = explode("<>", $line);
    //    if ($post_elements[0] == $edit_num) {
    //      $matched_post_num = true;
    //      if ($confirm_password == end($post_elements)) {
    //        $edit_flag = true;
    //        $edit_num = $_POST['edit_num'];
    //        list($name, $comment) = get_edit_post($lines, $edit_num);
    //      } else {
    //        echo_alert('パスワードが間違っています');
    //      }
    //    }
    //  }
    //  if (!$matched_post_num) echo_alert('投稿番号' . $edit_num . 'は存在しません');
    //}

    //# 投稿を編集する場合
    //if (
    //  isset($_POST['name'], $_POST['comment'])
    //  && $_POST['action'] == '投稿'
    //  && $edit_flag
    //) {
    //  $edit_post_num = $_POST['edit_post_num'];
    //  if ($edit_post_num) {
    //    $post_elements = set_post_elements($edit_post_num);
    //    edit_post($lines, $edit_post_num, $post_elements);
    //    $lines = get_file_lines($filename);
    //  } else {
    //    echo_alert('投稿の編集に失敗しました');
    //  }
    //}
    //

    // 投稿を表示
    function indicate_post($pdo){
      try{
        $stmt = $pdo->query('SELECT * FROM tbtest');
        foreach($stmt as $row){
            echo '<-------------------------------------------><br>';
            echo '投稿番号'. $row['id']. '<br>';
            echo '名前'. $row['name']. '<br>';
            echo 'コメント'. $row['comment']. '<br>';
            echo '投稿日時'. $row['updated_at']. '<br>';
        }
      } catch(PDOException $Exception){
        echo_alert("エラー:". $Exception->getMessage());
      }
    }
    ?>
  </div>
  <form method="post">
    <label>名前: <input type="text" name="name" placeholder="名前" required value="<?php if (isset($name)) echo $name; ?>"></label>
    <label>コメント: <input type="text" name="comment" placeholder="コメント" required value="<?php if (isset($name)) echo $comment; ?>"></label>
    <label>パスワード: <input type="password" name="password" placeholder="パスワード" required></label>
    <input type="submit" name="action" value="投稿">
  </form>

  <form method="post">
    <label>削除: <input type="number" name="delete_post_num" min="0" placeholder="投稿番号を記入" required></label>
    <label>パスワード: <input type="password" name="password" placeholder="パスワード" required></label>
    <input type="submit" name='action' value="削除">
  </form>
  <?php indicate_post($pdo)?>

</body>

</html>