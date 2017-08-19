<?php
namespace Tamce;

use Exception;

class Renderer
{
	static $path;
	static $defaults = [
		'data' => [],
		'statusCode' => null,
		'echo' => true,
		'mimeType' => null
	];
	static public function render($viewFile, array $data = [])
	{
		$data = $data + self::$defaults;
		try {
			$page = new Renderer\Page(self::$path.$viewFile, $data['data']);
		} catch (Exception $e) {
			throw $e;
		}

		if (!empty($data['statusCode'])) {
			http_response_code($data['statusCode']);
		}
		if (!empty($data['mimeType'])) {
			header('Content-Type: '.$data['mimeType']);
		}
		return $page->show($data['echo']);
	}

	static public function path($path)
	{
		return self::$path = rtrim($path, '\\/').'/';
	}
}

namespace Tamce\Renderer;

use Exception;

class Page
{
	protected $file;
	protected $data;
	public function __construct($viewFile, array $data)
	{
		$this->file = file_exists($viewFile) ? $viewFile : $viewFile . '.php';
		if (!file_exists($this->file)) {
			throw new Exception("Failed to load file: `$this->file`, file not exist!");
		}
		$this->data = $data;
	}

	public function show($echo = true)
	{
		ob_start();
		extract($this->data);
		include($this->file);
		$output = ob_get_contents();
		ob_end_clean();
		if ($echo) {
			echo $output;
		}
		return $output;
	}

	// ------- Helper Functions below are for view files

	protected function css($path, $echo = true)
	{
		$buf = '<link href="'.$path.'" rel="stylesheet" />';
		if ($echo) {
			echo $buf;
		}
		return $buf;
	}

	protected function js($path, $echo = true)
	{
		$buf = '<script src="'.$path.'"></script>';
		if ($echo) {
			echo $buf;
		}
		return $buf;
	}
}
