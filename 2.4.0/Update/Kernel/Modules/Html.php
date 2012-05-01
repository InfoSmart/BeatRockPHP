<?php
#####################################################
## 					 BeatRock				   	   ##
#####################################################
## Framework avanzado de procesamiento para PHP.   ##
#####################################################
## InfoSmart � 2012 Todos los derechos reservados. ##
## http://www.infosmart.mx/						   ##
#####################################################
## http://beatrock.infosmart.mx/				   ##
#####################################################

// Acci�n ilegal.
if(!defined('BEATROCK'))
	exit;

/*
	Basado en la publicaci�n de:	
	http://davidwalsh.name/create-html-elements-php-htmlelement-class
	Muchas gracias.
*/

class Html
{
	private $element = '';
	private $attrs = Array();
	private $param = '';

	public function __construct($element, $param = '')
	{
		$this->element = strtolower($element);
		$this->param = $param;
	}

	public function Set($param, $value = '')
	{
		if(!is_array($param))
			$this->attrs[$param] = $value;
		else
			$this->attrs = array_merge($this->attrs, $param);
	}

	public function Remove($param)
	{
		if(!isset($this->attrs[$param]))
			return;

		unset($this->attrs[$param]);
	}

	public function Clear()
	{
		$this->attrs = Array();
	}

	public function Add($object)
	{
		if(@get_class($object) == __CLASS__)
			$this->attrs['text'] .= $object->Build();
	}

	public function Build()
	{
		$html = '<' . $this->element;
		$atts = $this->attrs;

		if(!empty($atts))
		{
			foreach($atts as $param => $value)
			{
				if($param == 'text' AND $param !== 0)
					continue;

				if(is_numeric($param))
				{
					$param = $value;
					$value = '';
				}

				$html .= ' '.$param;

				if(!empty($value))
					$html .= '="'.$value.'"';
			}
		}

		$close = Array('input', 'img', 'hr', 'br', 'meta', 'link');

		if(in_array($this->element, $close))
			$html .= ' />';
		else
			$html .= '>'.$this->attrs['text'].'</'.$this->element.'>';

		if(!empty($param))
			Tpl::Set($param, $html);

		return $html;
	}
}
?>