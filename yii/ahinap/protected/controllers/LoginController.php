<?php

class LoginController extends Controller
{
	public function actionIndex()
	{	
// 		$arrGoogle=array('google','Gmail','Chrome','Android');
// //第一次使用each取得当前键值对，并且将指针移到下一个位置
// $arrG=each($arrGoogle);
// print_r($arrG);
// $arrGmail=each($arrGoogle);
// print_r($arrGmail);
// $arrChrome=each($arrGoogle);
// print_r($arrChrome);
// $arrAndroid=each($arrGoogle);
// print_r($arrAndroid);
//当指针位于数组末尾再次执行函数each，如果是这样再次执行结果返回false
// $empty=each($arrGoogle);die();
		$xml=new SimpleXMLElement('<?xml version="1.0" encoding="GBK"?><DOCUMENT />');
		header("Content-type: text/html; charset=utf-8"); 
		include "simple_html_dom.php" ;
  		$html = file_get_html('http://www.ttpet.com/quanzhong/jinmao.html');
  		$item=$xml->addchild("item");
// Find all images 

		foreach($html->find('dl.qz_inft ') as $element)/*echo $element;die();*//*echo $element->children(2);echo $element->children(3);die()*/
			// $num1 = $item->addchild("name",$element->children(1)->plaintext);
			$num1 = $item->addchild("alias",$element->children(2)->plaintext);
			$num1 = $item->addchild("ename",$element->children(3)->plaintext);
			// $num1 = $item->addchild("weight",$element->children(8)->plaintext);
			// $num1 = $item->addchild("years",$element->children(9)->plaintext);
			// $num1 = $item->addchild("place",$element->children(10)->plaintext);
			// $num1 = $item->addchild("shape",$element->children(4)->plaintext);
			// $num1 = $item->addchild("wool",$element->children(6)->plaintext);
			$num1 = $item->addchild("function",$element->children(5)->plaintext);
			 // $num1 = $item->addchild("name",$element->plaintext);
			 // unset($num1);continue;
		 // $item->addchild("age",$element-next_sibling()->plaintext();

  	
  
  			
  
  
		
		header("Content-type: text/xml");
  		echo $xml->asXml();
  		$xml->asXml("student.xml");
			
// die();
 




	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}