<?php

/**
 * @brief フォームから受け取ったデータをファイルに書き込み
 * @params 投稿番号
 * @memo フォーマット: 投稿番号<>名前<>コメント<>Y/m/d H:i:s<>パスワード
 */
function set_post_elements($post_num)
{
	$_name = $_POST['name'];
	$_comment = $_POST['comment'];
	$_date = date("Y/m/d H:i:s");
	$_password = $_POST['comment'];
	$_post_result = $post_num . "<>" . $_name . "<>" . $_comment . "<>" . $_date . "<>" . $_password;
	return $_post_result;
}

/**
 * @brief 指定のファイルから各行を読み取る
 * @returns 
 *       ファイルが存在した場合: List[string]
 *       ファイルが存在しない場合: false
 */
function get_file_lines()
{
	global $filename;
	if (file_exists($filename)) {
		$_lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		return $_lines;
	}
	return false;
}


/**
 * @brief 次の投稿番号を取得
 * @params 
 *       $post_lines:ファイルの各行
 * @returns 次の投稿番号
 */
function get_next_post_num($post_lines)
{
	if (empty($post_lines)) return 1;
	return array_pop($post_lines)[0] + 1;;
}

/**
 * @brief ファイルから投稿番号, 名前, コメント, 投稿日時を読み取り出力 
 * @params 
 *       $post_lines:ファイルの各行
 * @returns None
 */
function echo_post_lines($post_lines)
{
	foreach ($post_lines as $_line) {
		$_post_elements = explode("<>", $_line);
		echo "<----------------------------------------------><br>";
		echo "投稿番号: " . $_post_elements[0] . "<br>";
		echo "名前: " . $_post_elements[1] . "<br>";
		echo "コメント: " . $_post_elements[2] . "<br>";
		echo "投稿日時: " . $_post_elements[3] . "<br>";
	}
}

/**
 * @brief 指定された投稿番号の投稿を削除
 * @params 
 *       $post_lines:ファイルの各行
 *       $delete_post_num: 削除する投稿番号
 * @returns None
 */

function delete_post($post_lines, $delete_post_num)
{
	global $filename;
	$_deleted = false;
	$_handle = fopen($filename, 'w');
	foreach ($post_lines as $_line) {
		if ($_line[0] == $delete_post_num) {
			$deleted = true;
			continue;
		}
		fwrite($_handle, $_line . PHP_EOL);
	}
	fclose($_handle);
	return $_deleted;
}

/**
 * @brief 編集対象の投稿のnameとcommentを取得
 * @returns 
 *       編集対象が存在した場合: List[名前, コメント]
 *       編集対象が存在しない場合: false
 */
function get_edit_post($post_lines, $edit_num)
{
	global $filename;
	$_post_exist = false;
	foreach ($post_lines as $_line) {
		if ($_line[0] == $edit_num) {
			$_post_elements = explode("<>", $_line);
			$_name = $_post_elements[1];
			$_comment = $_post_elements[2];
			return array($_name, $_comment);
		}
	}
	return $_post_exist;
}

/**
 * @brief 指定した投稿番号を受け取った投稿で編集
 * @params
 *       $post_line: ファイルの中身 
 *       $edit_post_num: 編集対象の投稿番号
 *       $post_element: 受け取った投稿
 * @returns 
 *       編集対象が存在した場合: List[名前, コメント]
 *       編集対象が存在しない場合: false
 */
function edit_post($post_line, $edit_post_num, $post_element)
{
	global $filename;
	$_post_exist = false;
	$_handle = fopen($filename, 'w');
	foreach ($post_line as $_line) {
		if ($_line[0] == $edit_post_num) {
			fwrite($_handle, $post_element);
			$_post_exist = true;
			continue;
		}
		fwrite($_handle, $_line . PHP_EOL);
	}
	fclose($_handle);
	return $_post_exist;
}

/**
 * @brief アラートを表示
 * @params
 *       $message: 表示させるアラート
 */
function echo_alert($message)
{
	$alert = "<script type='text/javascript'>alert('" . $message . "');</script>";
	echo $alert;
}

/**
 * @brief パスワードをハッシュ化
 * @params
 *       $password: パスワード
 * @returns ハッシュ化されたパスワード 
 */

