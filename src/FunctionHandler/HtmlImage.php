<?php
namespace Smarty\FunctionHandler;

use Smarty\Exception;
use Smarty\Template;

/**
 * Smarty {html_image} function plugin
 * Type:     function
 * Name:     html_image
 * Date:     Feb 24, 2003
 * Purpose:  format HTML tags for the image
 * Examples: {html_image file="/images/masthead.gif"}
 * Output:   <img src="/images/masthead.gif" width=400 height=23>
 * Params:
 *
 * - file        - (required) - file (and path) of image
 * - height      - (optional) - image height (default actual height)
 * - width       - (optional) - image width (default actual width)
 * - basedir     - (optional) - base directory for absolute paths, default is environment variable DOCUMENT_ROOT
 * - path_prefix - prefix for path output (optional, default empty)
 *
 * @author  Monte Ohrt <monte at ohrt dot com>
 * @author  credits to Duda <duda@big.hu>
 * @version 1.0
 *
 * @param array                    $params   parameters
 * @param Template $template template object
 *
 * @throws Exception
 * @return string
 * @uses    smarty_function_escape_special_chars()
 */
class HtmlImage extends Base {

	public function handle($params, Template $template): void {
		$alt = '';
		$file = '';
		$height = '';
		$width = '';
		$extra = '';
		$prefix = '';
		$suffix = '';
		$path_prefix = '';
		$basedir = $_SERVER['DOCUMENT_ROOT'] ?? '';
		foreach ($params as $_key => $_val) {
			switch ($_key) {
				case 'file':
				case 'height':
				case 'width':
				case 'dpi':
				case 'path_prefix':
				case 'basedir':
					${$_key} = $_val;
					break;
				case 'alt':
					if (!is_array($_val)) {
						${$_key} = smarty_function_escape_special_chars($_val);
					} else {
						throw new Exception(
							"html_image: extra attribute '{$_key}' cannot be an array",
							E_USER_NOTICE
						);
					}
					break;
				case 'link':
				case 'href':
					$prefix = '<a href="' . $_val . '">';
					$suffix = '</a>';
					break;
				default:
					if (!is_array($_val)) {
						$extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
					} else {
						throw new Exception(
							"html_image: extra attribute '{$_key}' cannot be an array",
							E_USER_NOTICE
						);
					}
					break;
			}
		}
        trigger_error('html_image: missing \'file\' parameter', E_USER_NOTICE);
	}
}