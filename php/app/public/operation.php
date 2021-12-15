<?php
class Post
{
	private static $driver_options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
	];
	private $dsn;
	private $db_user;
	private $db_password;
	private $tb_name;
	private $pdo;

	public function __construct($dns, $db_user, $db_password, $tb_name)
	{
		$this->dsn = $dns;
		$this->db_user = $db_user;
		$this->db_password = $db_password;
		$this->tb_name = $tb_name;
		$this->pdo = new PDO($this->dsn, $this->db_user, $this->db_password, $this->driver_options);
	}

	public function new_post($name, $comment, $password)
	{
		try {
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$stmt = $this->pdo->prepare('INSERT INTO tbtest(name, comment, password) VALUES(:name, :comment, :password)');
			$stmt->bindValue(':name', $name);
			$stmt->bindValue(':comment', $comment);
			$stmt->bindValue(':password', $hashed_password);
			$stmt->execute();
		} catch (PDOException $Exception) {
			$this->echo_alert("エラー:" . $Exception->getMessage());
		}
	}

	public function delete_post($delete_post_num, $password)
	{
		try {
			$authenticated = $this->authentication_password($password, $delete_post_num);
			if ($authenticated) {
				$stmt = $this->pdo->prepare('DELETE FROM tbtest WHERE id = :delete_post_num');
				$stmt->bindValue(':delete_post_num', $delete_post_num);
				$stmt->execute();
			} else {
				$this->echo_alert('パスワードが間違っています');
			}
		} catch (PDOException $Exception) {
			$this->echo_alert("エラー:" . $Exception->getMessage());
		}
	}

	/**
	 * @brief 編集対象の投稿のnameとcommentを取得
	 * @returns 
	 *       編集対象が存在した場合: List[名前, コメント]
	 *       編集対象が存在しない場合: false
	 */
	public function get_edit_element($edit_post_num, $password)
	{
		try {
			$authenticated = $this->authentication_password($password, $edit_post_num);
			if ($authenticated) {
				$_stmt = $this->pdo->prepare('SELECT name, comment FROM tbtest WHERE id = :edit_num');
				$_stmt->bindValue(':edit_num', $edit_post_num);
				$_stmt->execute();
				$_res = $_stmt->fetch();
				return array($_res['name'], $_res['comment']);
			} else {
				$this->echo_alert('パスワードが間違っています');
				return false;
			}
		} catch (PDOException $Exception) {
			$this->echo_alert("エラー:" . $Exception->getMessage());
			return false;
		}
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
	function edit_post($edit_post_num, $name, $comment)
	{
		try {
			$stmt = $this->pdo->prepare('UPDATE tbtest SET name = ?, comment = ? WHERE id = ?');
			$stmt->bindValue(1, $name);
			$stmt->bindValue(2, $comment);
			$stmt->bindValue(3, $edit_post_num);
			$stmt->execute();
		} catch (PDOException $Exception) {
			$this->echo_alert("エラー:" . $Exception->getMessage());
		}
	}

	// 投稿を表示
	public function show_post()
	{
		try {
			$stmt = $this->pdo->query('SELECT * FROM tbtest');
			foreach ($stmt as $row) {
				echo '<-------------------------------------------><br>';
				echo '投稿番号' . $row['id'] . '<br>';
				echo '名前' . $row['name'] . '<br>';
				echo 'コメント' . $row['comment'] . '<br>';
				echo '投稿日時' . $row['updated_at'] . '<br>';
			}
		} catch (PDOException $Exception) {
			$this->echo_alert("エラー:" . $Exception->getMessage());
		}
	}

	/**
	 * @brief パスワードをハッシュ化
	 * @params
	 *       $password: パスワード
	 * @returns ハッシュ化されたパスワード 
	 */
	// パスワードを確認
	private function authentication_password($password, $post_num)
	{
		$stmt = $this->pdo->prepare('SELECT password FROM tbtest WHERE id = :post_num');
		$stmt->bindValue(':post_num', $post_num);
		$stmt->execute();
		$res = $stmt->fetch();
		$confirm_password = $res['password'];
		return password_verify($password, $confirm_password);
	}


	/**
	 * @brief アラートを表示
	 * @params
	 *       $message: 表示させるアラート
	 */
	public static function echo_alert($message)
	{
		$alert = "<script type='text/javascript'>alert('" . $message . "');</script>";
		echo $alert;
	}
	public static function echo_log($message)
	{
		$alert = "<script type='text/javascript'>console.log('" . $message . "');</script>";
		echo $alert;
	}
}
