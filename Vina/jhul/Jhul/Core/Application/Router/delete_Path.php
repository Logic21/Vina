<?php namespace Jhul\Core\Application\Router ;

/* @Author : Manish Dhruw [1D3N717Y12@gmail.com]
+=======================================================================================================================
| @Created : Sun 07 Feb 2016 06:45:57 PM IST
|
| Requested URI Node
+---------------------------------------------------------------------------------------------------------------------*/

class Path
{

	private $_breadCrumbs = [];

	private $_value = '' ;

	protected $_pointer = 1;

	public function __construct( $path )
	{
		$this->_value = $path['V'];

		foreach ($path['P'] as $key => $P)
		{
			$this->_breadCrumbs[$key] = urlDecode($P);
		}

	}

	public function current()
	{
		return $this->_breadCrumbs[ $this->_pointer ] ;
	}

	public function next()
	{
		if( isset( $this->_breadCrumbs[ $this->_pointer + 1 ] ) )
		{
			return $this->_breadCrumbs[ $this->_pointer + 1 ] ;
		}
	}

	public function hasNext()
	{
		 return isset( $this->_breadCrumbs[ $this->_pointer + 1 ] );
	}

	public function moveToNext() { ++$this->_pointer; }

	public function value() { return $this->_value; }

	public function last(){ return end($this->_breadCrumbs); }

	public function get( $pointer )
	{
		if( isset( $this->_breadCrumbs[$pointer] ) )
		{
			return $this->_breadCrumbs[$pointer] ;
		}
	}

	public function __toString()
	{
		return $this->value();
	}

	public function map()
	{
		return $this->_breadCrumbs;
	}

	function getFrom( $fromIndex, $preserveKeys = TRUE )
	{
		return array_slice( $this->map(), $fromIndex, NULL, $preserveKeys );
	}
}
