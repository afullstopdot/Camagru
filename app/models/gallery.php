<?php

require_once 'Model.php';

class gallery extends Model
{
	private $db;

	/*
	** when the Controller creates a new instance of this class
	** the pdo object is passed by the constructor and set here.
	*/

	public function __construct($db)
	{
		if (!isset($this->db))
		  $this->db = $db;
	}

	/*
	** Get all uploads, 10 uploads a time
	**
	** Each record will return the username, image path, upload time
	*/

	public function getUploads($page = 0)
	{
		if (isset($this->db))
		{
			try 
			{
				$stmt = $this->db->prepare("
					SELECT i.image_id, u.username, u.picture, i.img_path, i.upload_time
					FROM uploads as i
					INNER JOIN users as u
					ON u.user_id = i.user_id
					ORDER BY i.upload_time DESC
					LIMIT 10
					OFFSET {$page}
				");

				$stmt->execute();
				return $stmt->fetchAll();
			}
			catch (PDOException $e)
			{

				/*
				** Send emails to admin
				*/

				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> getUploads()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> getUploads()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Get all upload by user to create thumbnails
	*/

	public function getThumbnails($id)
	{
		if (isset($this->db))
		{
			try 
			{
				$stmt = $this->db->prepare('
					SELECT i.img_path, i.upload_time
					FROM uploads as i
					WHERE i.user_id = :id
					ORDER BY i.upload_time DESC
				');

				$stmt->execute([
					'id' => $id
				]);
				return $stmt->fetchAll();
			}
			catch (PDOException $e)
			{

				/*
				** Send emails to admin
				*/

				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> getThumbnails()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> getThumbnails()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Add upload
	*/

	public function addUpload($id, $path)
	{
		if (isset($this->db))
		{
			try 
			{
				$stmt = $this->db->prepare('
					INSERT INTO uploads (user_id, img_path)
					VALUES (:id, :img_path)
				');

				return $stmt->execute([
					'id' => $id,
					'img_path' => $path
				]);
			}
			catch (PDOException $e)
			{

				/*
				** Send emails to admin
				*/

				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> addUpload()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> addUpload()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Get list of comments for each upload
	*/

	public function getComments()
	{
		if (isset($this->db))
		{
			try 
			{
				$stmt = $this->db->prepare('
					SELECT c.comment_id as id, c.comment, c.image_id, c.user_id as username
					FROM comments as c
					INNER JOIN uploads as u
					ON c.image_id = u.image_id
					ORDER BY c.comment_time DESC
				');

				$stmt->execute();
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			catch (PDOException $e)
			{

				/*
				** Send emails to admin
				*/

				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> getComments()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> getComments()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Get list of likes for each upload
	*/

	public function getLikes($id = NULL)
	{
		if (isset($this->db))
		{
			try 
			{
				if (isset($id)) {
					$stmt = $this->db->prepare('
						SELECT *
						FROM likes
						WHERE image_id = :id
					');

					$stmt->execute(['id' => $id]);
					return $stmt->rowCount();
				}
				else {
					$stmt = $this->db->prepare('
						SELECT l.like_id, l.image_id
						FROM likes as l
						INNER JOIN uploads as u
						ON l.image_id = u.image_id
					');

					$stmt->execute();
					return $stmt->fetchAll();
				}
			}
			catch (PDOException $e)
			{

				/*
				** Send emails to admin
				*/

				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> getLikes()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> getLikes()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Add a like of a picture
	*/

	public function addLike($user_id, $image_id)
	{
		if (isset($this->db))
		{
			try 
			{

				/*
				** 1 like per user allowed so check | find out if can do in one sql expression also
				*/

				$stmt = $this->db->prepare('
					SELECT * FROM likes WHERE user_id = :id AND image_id = :image
				');
				$stmt->execute(['id' => $user_id, 'image' => $image_id]);

				if ($stmt->rowCount() === 0) {
					$stmt = $this->db->prepare('
						INSERT INTO likes (user_id, image_id)
						VALUES (:user, :image)
					');

					$stmt->execute([
						'user' => $user_id,
						'image' => $image_id
					]);
					return true;
				}
				return false;
			}
			catch (PDOException $e)
			{

				/*
				** Send emails to admin
				*/

				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> addLike()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> addLike()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Add a comment of a picture
	*/

	public function addComment($user_id, $image_id, $comment)
	{
		if (isset($this->db))
		{
			try 
			{
				$stmt = $this->db->prepare('
					INSERT INTO comments (user_id, image_id, comment)
					VALUES (:user, :image, :comment)
				');

				return $stmt->execute([
					'user' => $user_id,
					'image' => $image_id,
					'comment' => $comment
				]);
			}
			catch (PDOException $e)
			{
				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> addComment()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> addComment()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Check if this is a valid image id
	*/

	public function validImage($image_id)
	{
		if (isset($this->db))
		{
			try 
			{
				$stmt = $this->db->prepare('
					SELECT *
					FROM uploads
					WHERE image_id = :image
				');

				$stmt->execute([
					'image' => $image_id,
				]);

				return $stmt->rowCount() === 1 ? true : false;
			}
			catch (PDOException $e)
			{

				/*
				** Send emails to admin
				*/

				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> validImage()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> validImage()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** get info on image owner
	*/

	public function imageOwner($image)
	{
		if (isset($this->db) && isset($image)) {
			try
			{
				$stmt = $this->db->prepare('
					SELECT u.email as email, u.username as name, img_path
					FROM uploads as i
					INNER JOIN users as u
					ON i.user_id = u.user_id
					WHERE i.image_id = :image
				');

				$stmt->execute([
					'image' => $image
				]);

				return $stmt->rowCount() == 1 ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
			}
			catch (PDOException $e)
			{
				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> imageOwner()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> imageOwner()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Using the path of the image, remove the record
	** if it belongs to the user 
	*/

	public function removeUpload($id, $path)
	{
		if (isset($this->db) && isset($id) && isset($path)) {
			try
			{
				$stmt = $this->db->prepare('
					DELETE u, c, l 
					FROM uploads as u
					LEFT JOIN comments as c
					ON u.image_id = c.image_id
					LEFT JOIN likes as l
					ON u.image_id = l.image_id
					WHERE u.user_id = :id
					AND u.img_path = :img_path
				');

				return $stmt->execute([
					'id' => $id,
					'img_path' => $path
				]);
			}
			catch (PDOException $e)
			{
				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> getImageByOwner()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> getImageByName()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}

	/*
	** Find image by name and return image id
	*/

	public function findImageByName($id, $path)
	{
		if (isset($this->db) && isset($id) && isset($path)) {
			try
			{
				$stmt = $this->db->prepare('
					SELECT image_id
					FROM uploads
					WHERE user_id = :id
					AND img_path = :img_path
				');

				$stmt->execute([
					'id' => $id,
					'img_path' => $path
				]);
				return $stmt->fetch(PDO::FETCH_BOTH);
			}
			catch (PDOException $e)
			{
				$this->error_log('
					<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
					<p>Function:<b style="color: green; font-size: 18px;"> findImageByName()</b></p><hr>
					<p>Error:<b style="color: red; font-size: 18px;"> ' . $e->getMessage() . '</b></p><hr>'
				);
				return false;
			}
		}
		else
		{
			$this->error_log('
				<p>Model:<b style="color: cyan; font-size: 18px;"> gallery.php</b></p><hr>
				<p>Function:<b style="color: green; font-size: 18px;"> findImageByName()</b></p><hr>
				<p>Error:<b style="color: red; font-size: 18px;">PDO object not set</b></p><hr>'
			);
			return false;
		}
	}
}
