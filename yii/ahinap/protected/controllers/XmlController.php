<?php
// http://coffeerings.posterous.com/php-simplexml-and-cdata
class SimpleXMLExtended extends SimpleXMLElement {
  public function addCData($cdata_text) {
    $node = dom_import_simplexml($this); 
    $no   = $node->ownerDocument; 
    $node->appendChild($no->createCDATASection($cdata_text)); 
  } 
}

$xmlFile    = 'config.xml';
// instead of $xml = new SimpleXMLElement('<site/>');
$xml        = new SimpleXMLExtended('<site/>');
$xml->title = NULL; // VERY IMPORTANT! We need a node where to append
$xml->title->addCData('Site Title');
$xml->title->addAttribute('lang', 'en');
$xml->saveXML($xmlFile);
?>
