<?php 
Header("Content-type: text/xml; charset=GBK"); 

class SimpleXMLExtended extends SimpleXMLElement 
{
  	public function addCData($cdata_text) 
  	{
    	$node = dom_import_simplexml($this); 
    	$no   = $node->ownerDocument; 
    	$node->appendChild($no->createCDATASection($cdata_text)); 
  	}	 
}
include "simple_html_dom.php" ;
// instead of $xml = new SimpleXMLElement('<site/>');
class MysqlToXmlController extends CController
{	

	public function __construct()
	{

	}
	public function Index($rows)
	{	
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="GBK"?><DOCUMENT/>');
		foreach ($rows as $keys => $values) {
			
		
			foreach ($values as $key => $value) 
			{$i=-8;
				$num_item = $xml->addchild($key);
				$num_key = $num_item->addchild('key');

				$num_display = $num_item->addchild('display');
				$n=1;
				foreach ($value as $k => $val) 
				{	

					$n++;
					if($n<3){
					$num_key->addCData($val);}
				
					
					
				else{
					$i++;
					if($i<14){

						$num_display->$k = NULL;
						$num_display->$k->addCData($val);
					continue;
					}
					elseif($i==14)
					{
						$num_moremore = $num_display->addchild('moremore');
						$num_moremore->$k = NULL;
						$num_moremore->$k->addCData($val);
					}$num_moremore->$k = NULL;
						$num_moremore->$k->addCData($val);
}
				}	
			}
		}
		echo $xml->asXml();
	}
	public function actionIndex()
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition("cateid=1"); 
		$model = Breed::model()->findAll($criteria);
		$parseRows = Breed::model()->parse($model);
		return $parseRows;
	}

	public function actionIndexCattie()
	{
		$criteria = new CDbCriteria();
		$criteria->addCondition("cateid=2"); 
		$model = Breed::model()->findAll($criteria);
		$parseRows = Breed::model()->parseCattie($model);
		return $parseRows;
	}	

	public function actionIndexCutie()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('catid',array(3,13,14,15,16,17));
		$model = SupeBreeditems::model()->findAll($criteria);
		$parseRows = SupeBreeditems::model()->parseCutie($model);
		return $parseRows;
	}

	public function actionIndexAqanimal()
	{
		$criteria = new CDbCriteria;
		$criteria->addInCondition('catid',array(4,26,27,28,29));
		$model = SupeBreeditems::model()->findAll($criteria);
		$parseRows = SupeBreeditems::model()->parseAqanimal($model);
		return $parseRows;
	}
}
// $rows = array('item0'=>array('name'=>1,'function'=>2,'wool'=>3,'more'=>4),
// 	'item1'=>array('name'=>11,'function'=>22,'wool'=>33,'more'=>44));

$new = new MysqlToXmlController;
$rows = $new->actionIndex();
// $rows = $new->actionIndexCattie();
// $rows = $new->actionIndexCutie();
// $rows = $new->actionIndexAqanimal();
$new->index($rows);

