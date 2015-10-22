<?php
class Layout{
	public $dialog 		= true;
	private $js 		= array();
	private $css 		= array();
	private $project 	= "Hearthstone Stats";
	private $title 		= "Hearthstone Stats";
	private $includes 	= "/var/www/html/hs_stats/includes";

	public function __construct(){
		// do nothing
	}

	public function __destruct(){
		$this->footer();
	}

	public function title($value){
		$this->title = (!preg_match("/^{$this->project}/i", $value)) ? $this->project." - ".$value : $value; // add the project title to the html title tag
	}

	public function header(){
		include_once($this->includes."/header.php"); // include the default header files and layout
	}

	public function footer(){
		include_once($this->includes."/footer.php"); // include the footer layout
	}

	public function add_js($src){
		array_push($this->js, $src); // add the js source file to the array
	}

	public function add_css($src){
		array_push($this->css, $src); // add the css source file to the array
	}

	public function js(){
		foreach($this->js as $js){
			print "<script src=\"{$js}\"></script>\n"; // loop through all js files added to the array
		}
	}

	public function css(){
		foreach($this->css as $css){
			print "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$css}\" />\n"; // loop through all css files added to the array
		}
	}
}
?>
