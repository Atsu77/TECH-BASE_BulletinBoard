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
    $tb_name = 'tbtest';
    $post = new Post($dsn, $user, $password, $tb_name);


    # 新規投稿の場合
    if (
      isset($_POST['name'], $_POST['comment'], $_POST['password'])
      && $_POST['action'] == '投稿'
      && !$edit_flag
    ) {
      $name = $_POST['name'];
      $comment = $_POST['comment'];
      $password = $_POST['password'];
      $post->new_post($name, $comment, $password);
    }


    # 投稿を削除する場合
    if (isset($_POST['delete_post_num'], $_POST['password'])) {
      $delete_post_num = $_POST['delete_post_num'];
      $password = $_POST['password'];
      $post->delete_post($delete_post_num, $password);
    }

    # 編集する投稿番号を指定する場合
    if (isset($_POST['edit_post_num'], $_POST['password'])
      && $_POST['action'] == '編集'
    ) {
      $edit_post_num = $_POST['edit_post_num'];
      $password = $_POST['password'];
      $res = $post->get_edit_element($edit_post_num, $password);
      list($target_name, $target_comment) = $res;
    }

    # 投稿を編集する場合
    if (
      isset($_POST['name'], $_POST['comment'], $_POST['edit_num'])
      && $_POST['action'] == '投稿'
    ) {
      $edit_post_num = $_POST['edit_num'];
      $name = $_POST['name'];
      $comment = $_POST['comment'];
      $post->edit_post($edit_post_num, $name, $comment);
      $edit_flag = false;
    }
    


    ?>
  </div>
  <form method="post">
    <label>名前: <input type="text" name="name" placeholder="名前" required value="<?php if (isset($target_name)) echo $target_name; ?>"></label>
    <label>コメント: <input type="text" name="comment" placeholder="コメント" required value="<?php if (isset($target_comment)) echo $target_comment; ?>"></label>
    <?php
    if ($edit_flag) {
      echo '<input type="hidden" name="edit_num" value=' . $edit_post_num . '>';
      //echo '<input type="hidden" name="edit_flag" value="true">';
    } else {
      echo '<label>パスワード: <input type="password" name="password" placeholder="パスワード" required></label>';
    }
    ?>
    <input type="submit" name="action" value="投稿">
  </form>

  <form method="post">
    <label>削除: <input type="number" name="delete_post_num" min="0" placeholder="投稿番号を記入" required></label>
    <label>パスワード: <input type="password" name="password" placeholder="パスワード" required></label>
    <input type="submit" name='action' value="削除">
  </form>
  <form method="post">
    <label>編集番号指定: <input type="number" name="edit_post_num" min="0" placeholder="投稿番号を記入" required></label>
    <label>パスワード: <input type="password" name="password" placeholder="パスワード" required></label>
    <input type="hidden" name="edit_flag" value='true'>
    <input type="submit" name="action" value="編集">
  </form>
  <?php $post->show_post($pdo) ?>

</body>

</html>