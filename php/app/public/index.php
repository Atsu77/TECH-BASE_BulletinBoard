<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bulletin board</title>
</head>

<body>
  <div>
    <?php
    include "./operation.php";

    $filename = "mission_3-5.txt";
    $lines = get_file_lines();
    $edit_flag = isset($_POST['edit_flag']) ? true : false;


    # 新規投稿の場合
    if (
      isset($_POST['name'], $_POST['comment'], $_POST['password'])
      && $_POST['action'] == '投稿'
      && !$edit_flag
    ) {
      $post_num = get_next_post_num($lines);
      if ($post_num) {
        $handle = fopen($filename, 'a');
        $post_elements = set_post_elements($post_num);
        fwrite($handle, $post_elements . PHP_EOL);
        fclose($handle);
        $lines = get_file_lines($filename);
      } else {
        echo_alert('投稿に失敗しました');
      }
    }

    # 投稿を削除する場合
    if (isset($_POST['delete_post_num'], $_POST['password'])) {
      $delete_post_num = $_POST['delete_post_num'];
      $confirm_password = $_POST['password'];
      $matched_post_num = false;
      foreach ($lines as $line) {
        $post_elements = explode("<>", $line);
        if ($delete_post_num == $post_elements[0]) {
          $matched_post_num = true;
          if ($confirm_password == end($post_elements)) {
            delete_post($lines, $delete_post_num);
            $lines = get_file_lines($filename);
          } else {
            echo_alert('パスワードが間違っています');
          }
        }
      }
      if (!$matched_post_num) echo_alert('投稿番号' . $delete_post_num . 'は存在しません');
    }

    # 編集する投稿番号を指定する場合
    if (
      isset($_POST['edit_num'], $_POST['password'])
      && $_POST['action'] == '編集'
    ) {
      $edit_num = $_POST['edit_num'];
      $confirm_password = $_POST['password'];
      $matched_post_num = false;
      foreach ($lines as $line) {
        $post_elements = explode("<>", $line);
        if ($post_elements[0] == $edit_num) {
          $matched_post_num = true;
          if ($confirm_password == end($post_elements)) {
            $edit_flag = true;
            $edit_num = $_POST['edit_num'];
            list($name, $comment) = get_edit_post($lines, $edit_num);
          } else {
            echo_alert('パスワードが間違っています');
          }
        }
      }
      if (!$matched_post_num) echo_alert('投稿番号' . $edit_num . 'は存在しません');
    }

    # 投稿を編集する場合
    if (
      isset($_POST['name'], $_POST['comment'])
      && $_POST['action'] == '投稿'
      && $edit_flag
    ) {
      $edit_post_num = $_POST['edit_post_num'];
      if ($edit_post_num) {
        $post_elements = set_post_elements($edit_post_num);
        edit_post($lines, $edit_post_num, $post_elements);
        $lines = get_file_lines($filename);
      } else {
        echo_alert('投稿の編集に失敗しました');
      }
    }
    ?>
  </div>

  <form method="post">
    <label>名前: <input type="text" name="name" placeholder="名前" required value="<?php if (isset($name)) echo $name; ?>"></label>
    <label>コメント: <input type="text" name="comment" placeholder="コメント" required value="<?php if (isset($name)) echo $comment; ?>"></label>

    <?php
    if ($edit_flag && isset($edit_num)) {
      echo '<input type="hidden" name="edit_post_num" value=' . $edit_num . '>';
      echo '<input type="hidden" name="edit_flag" value="true">';
    } else {
      echo '<label>パスワード: <input type="password" name="password" placeholder="パスワード" required></label>';
    }
    ?>
    <input type="submit" name="action" value="投稿">
  </form>
  <form method="post">
    <label>編集番号指定: <input type="number" name="edit_num" min="0" placeholder="投稿番号を記入" required></label>
    <label>パスワード: <input type="password" name="password" placeholder="パスワード" required></label>
    <input type="submit" name="action" value="編集">
  </form>
  <form method="post">
    <label>削除: <input type="number" name="delete_post_num" min="0" placeholder="投稿番号を記入" required></label>
    <label>パスワード: <input type="password" name="password" placeholder="パスワード" required></label>
    <input type="submit" name='action' value="削除">
  </form>
  <br>
  <?php echo_post_lines($lines); ?>
</body>

</html>