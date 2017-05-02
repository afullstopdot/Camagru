<?php

/*
** This class deals with files uploaded in  form
** Mostly images tho
*/

class ImageUpload
{
	private $file = [];

	/*
	** Set the attribute file (form upload $_FILES)
	*/

	public function set_file($file = [])
	{
		$this->file = empty($file) ? [] : $file;
	}

	/*
	** Returns name of file
	** @params none
	** @return filename (string)
	*/

	public function filename()
	{
		if (!empty($this->file))
		{
			return $this->file['name'];
		}
		return false;
	}

	/*
	** Returns the full path of file
	*/

	public function path()
	{
		if (!empty($this->file))
		{
			return $this->file['tmp_name'];
		}
		return false;
	}

	/*
	** Get extension of file
	*/

	public function type($file = '')
	{
		if (is_string($file) && !empty($file)) {
			return pathinfo($file, PATHINFO_EXTENSION);
		}
		return pathinfo($this->file['name'], PATHINFO_EXTENSION);
	}

	/*
	** Returns base64encode contents of file
	*/

	public function content($raw = false)
	{
		if (!empty($this->file)) {
			$type = $this->type();
			if (!empty($type)) {
				$content = file_get_contents($this->file['tmp_name']);
				if ($raw) {
					return $content;
				}
				return 'data:image/' . $type . ';base64,' . base64_encode($content);
			}
		}
	}

	/*
	** content fo external function (not uploaded)
	*/

	public function ext_content($path)
	{
		if (is_string($path) && !empty($path)) {
			$type = $this->type($path);
			if (!empty($type)) {
				$content = file_get_contents($path);
				return 'data:image/' . $type . ';base64,' . base64_encode($content);
			}
		}
		return false;
	}

	/*
	** Check if image has ext that is passed as argument
	** Default allow png, jpg, jpeg
	** @params $ext (array)
	** @return true/false
	*/

	public function valid_ext($ext = ['png', 'jpg', 'jpeg', 'JPG', 'JPEG', 'PNG'])
	{
		return in_array($this->type(), $ext);
	}

	/*
	** Check if image has a valid file size passed as argumebt
	** Default 5mb allowed per file
	** @params $size (int/double)
	** @return true/false
	*/

	public function valid_size($size = 5000)
	{
		if (!empty($this->file)) {
			return ($this->file['size'] / 1024) > $size ? false : true;
		}
		return false;
	}

	/*
	** Convert absolute path to url path
	*/

	public function absolute_to_url($file_path)
	{
		if (isset($file_path)) {
			return str_replace(ROOT_DIR, '', $file_path);
		}
		return false;
	}

	/*
	** Convert url path to absolute webroot path
	*/

	public function url_to_webroot($url_path)
	{
		if (isset($url_path)) {
			return str_replace(SITE_HOST, '', $url_path);
		}
		return false;
	}

	/*
	** Convert url to absolute path
	*/

	public function url_to_absolute($url_path)
	{
		if (isset($url_path)) {
			return str_replace(SITE_HOST, ROOT_DIR, $url_path);
		}
		return false;
	}
}