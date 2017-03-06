<?php
namespace Org\Editor;
class Editor {
	/**
	 * 编辑器
	 * @param int $textareaid
	 * @param string $height 编辑器高度
	 */
	public static function editor($textareaid = 'content',$height = 200) {
		$str ='';
		$str = '<script type="text/javascript" src="'.C("PLUGIN_PATH").'/ckeditor/ckeditor.js"></script>';
		$str .= "<script type=\"text/javascript\">\r\n";
		$str .= "var _ck_$textareaid = CKEDITOR.replace( '$textareaid',{";
		$str .= "height:{$height},";
		$str .="flashupload:true,\r\n";
		$str .= "filebrowserImageUploadUrl : '/index.php?m=Attachment&c=Index&a=editorUpload',\r\n";
		$str .= "});\r\n";
		$str .= '</script>';
		return $str;
	}
}