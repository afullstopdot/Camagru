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

	public function getUploads()
	{
		if (isset($this->db))
		{
			try 
			{
				$stmt = $this->db->prepare('
					SELECT u.username, u.picture, i.img_path, i.upload_time
					FROM uploads as i
					INNER JOIN users as u
					ON u.user_id = i.user_id
					ORDER BY i.upload_time DESC
				');

				$stmt->execute();
				return $stmt->fetchAll();
			}
			catch (PDOException $e)
			{

				/*
				** Send emails to admin
				*/

				$this->error_log('
					Model: gallery
					Function: getUploads
					Error: ' . $e->getMessage()
				);
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
