<?php
/**
 * Our very own template engine. - Denver
 * @author Stevan
 *
 */
namespace sys;

class Template
{
	private $_default = "default";
	private $_template_dir;
	private $_template;
	private $_layout;
	private $_force_footer;
	
	/**
	 * Default constructor
	 * 
	 * @param Config $config
	 */
	public function __construct($config = null)
	{
		if ($config != null)
		{
			$this->_template_dir = $config->getSetting("template.template_dir");
			$this->_template     = $config->getSetting("template.template");
			$this->_layout       = $config->getSetting("template.layout");
		}
	}
	
	/**
	 * Sets the top level template directory
	 * 
	 * @param string $template_dir
	 */
	public function setTemplateDir($template_dir)
	{
		$this->_template_dir = $template_dir;
	}
	
	/**
	 * Sets the template to use
	 * 
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		$this->_template = $template;
	}
	
	/**
	 * Sets the default template, in case the requested view or template don't exist
	 * 
	 * @param string $default_template
	 */
	public function setDefaultTemplate($default_template)
	{
		$this->_default = $default_template;
	}
	
	/**
	 * Sets a variable to pass to the template
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function setVar($key, $value)
	{
		$this->{$key} = $value;
	}
	
	/**
	 * Fetches the page content, complete with template
	 * 
	 * @param string $page
	 * @param string $layout
	 */
	public function fetch($page, $layout = null)
	{
		return $this->templateise($this->fetch_content($page), $layout);
	}
	
	/**
	 * Put the requested content into the template
	 * 
	 * @param string $content
	 * @param string $layout
	 */
	public function templateise($content, $layout = null)
	{
		if (is_null($layout)) {
			$layout = $this->_layout;
		}
		
		$this->content_for_template = $content;
		
		ob_start();
		if (file_exists($this->_template_dir. '/' . $this->_template . '/layouts/' . $layout . '.phtml'))
		{
			include_once $this->_template_dir. '/' . $this->_template . '/layouts/' . $layout . '.phtml';
		} else {
			include_once $this->_template_dir. '/' . $this->_default . '/layouts/' . $layout . '.phtml';
		}
		$content = ob_get_clean();
		if (stristr($content, "</body>") > -1) {
			$content = str_replace("</body>", $this->_force_footer."</body>", $content);
		} else if (stristr($content, "</html>") > -1) {
			$content = str_replace("</html>", $this->_force_footer."</html>", $content);
		} else {
			$content .= $this->_force_footer;
		}
		return $content;
	}
	
	/**
	 * Sets the layout to use
	 * 
	 * @param string $layout
	 */
	public function setLayout($layout)
	{
		$this->_layout = $layout;
	}
	
	/**
	 * Displays the output of the template pages
	 * 
	 * @param string $page
	 * @param string $layout
	 */
	public function display($page, $layout = null)
	{
		echo $this->fetch($page, $layout);
	}
	
	/**
	 * Fetches only the content without the layout
	 * 
	 * @param string $page
	 */
	public function fetch_content($page)
	{
		ob_start();
		if (file_exists($this->_template_dir. '/' . $this->_template . '/content/' . $page . '.phtml'))
		{
			include_once $this->_template_dir. '/' . $this->_template . '/content/' . $page . '.phtml';
		} else {
			include_once $this->_template_dir. '/' . $this->_default . '/content/' . $page . '.phtml';
		}
		return ob_get_clean();
	}
	
	/**
	 * Adds a footer which will be put into the page before the body tag before
	 * final output.
	 * 
	 * @param string $footer
	 */
	public function forceFooter($footer)
	{
		$this->_force_footer = $footer;
	}
}

?>