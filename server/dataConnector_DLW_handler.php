<?php
/***************************************************************
* Part of mS3 Commerce
* Copyright (C) 2019 Goodson GmbH <http://www.goodson.at>
*  All rights reserved
* 
* Dieses Computerprogramm ist urheberrechtlich sowie durch internationale
* Abkommen geschützt. Die unerlaubte Reproduktion oder Weitergabe dieses
* Programms oder von Teilen dieses Programms kann eine zivil- oder
* strafrechtliche Ahndung nach sich ziehen und wird gemäß der geltenden
* Rechtsprechung mit größtmöglicher Härte verfolgt.
* 
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require 'dataConnectorImpl.php';
require_once __DIR__ . '/../binding/dataConnectorTypes.php';
require_once __DIR__ . '/../binding/dataConnectorMessages.php';

//disable cache that cause errors 
ini_set("soap.wsdl_cache_enabled", "0");

/**
 * SOAP Document Literal Wrapped wrapper for mS3 Commerce dataConnectorV2
 *
 * @author marcelo.stucky
 */
class dataConnector_DLW_handler {

	/**
	 *
	 * @var dataConnectorImpl 
	 */
	var $impl = null;
	var $useStage = null;

	public function __construct($useStage) {
		$this->useStage = $useStage;
	}
	
	/**
	 * @return dataConnectorImpl
	 * @throws SoapFault 
	 */
	private function getImpl() {
		if ($this->impl != null) {
			return $this->impl;
		}
		if (is_null($this->useStage)) {
			throw new SoapFault('Client', 'Invalid database access type specified');
		}
		$this->impl = new dataConnectorImpl($this->useStage, new SOAPErrorHandler());
		return $this->impl;
	}

	public function getShopRoot($request) {
		$shopRoot = $this->getImpl()->getShopRoot($request->shopId);
		$re = new getShopRootResponse();
		$re->shopRoot = $shopRoot;
		return $re;
	}

	public function getShopRootForLangAndMarket($request) {

		$shopRoot = $this->getImpl()->getShopRootForLangAndMarket($request->languageId, $request->marketId);
		$re = new getShopRootForLangAndMarketResponse();
		$re->shopRoot=$shopRoot;
		return $re;
	}

	public function getShopRootList($request) {
		$shopRootList = $this->getImpl()->getAllShopRoots();
		$re = new getShopRootListResponse();
		$re->shopRootList->shopRoots = $shopRootList->shops;
		return $re;
	}
	
	public function getGroupChildren($request) {

		$groupChildren = $this->getImpl()->getGroupChildren($request->groupId);
		$re = new getGroupChildrenResponse();
		$re->groupChildren = $groupChildren;
		return $re;
	}

	public function getGroupContent($request) {
		$list= new FeatureList();
		$groupContent = $this->getImpl()->getGroupContent($request->groupId,$list,TRUE);
		$re = new getGroupContentResponse();
		$re->groupContent= $groupContent;
		return $re;
	}

	public function getGroupContentBySMZ($request) {
		$groupContent = $this->getImpl()->getContentBySMZ($request->groupId, $request->smzName);
		$re = new getGroupContentBySMZResponse();
		$re->groupContent = $groupContent;
		return $re;
	}

	public function getGroupContentBySMList($request) {
		$list= new FeatureList();
		$list->feature=$request->features->feature;
		$groupContent = $this->getImpl()->getGroupContent($request->groupId, $list);
		$re = new getGroupContentBySMListResponse();
		$re->groupContent = $groupContent;
		return $re;
	}
	
	public function getGroupTree($request) {

		$groupTree = $this->getImpl()->getGroupTree($request->groupId);
		$re = new getGroupTreeResponse();
		$re->groupTree = $groupTree;
		return $re;
	}

	public function getProductFeature($request) {
		$productFeature = $this->getImpl()->getProductFeature($request->productId,null,TRUE);
		$re = new getProductFeatureResponse();
		$re->product = $productFeature;
		return $re;
	}

	public function getProductFeatureBySMZ($request) {
		$productFeatures = $this->getImpl()->getProductBySMZ($request->productId, $request->smzName);
		$re = new getProductFeatureBySMZResponse();
		$re->product=$productFeatures;
		return $re;
	}
	
	public function getProductFeatureBySMZType($request) {
		$productFeatures = $this->getImpl()->getProductBySMZType($request->productId, $request->smzType);
		$re = new getProductFeatureBySMZTypeResponse();
		$re->product=$productFeatures;
		return $re;
	}

	public function getProductFeatureBySMList($request) {
		$list=new FeatureList();
		$list->feature=$request->features->feature;
		$productFeature = $this->getImpl()->getProductFeature($request->productId, $list);
		$re = new getProductFeatureResponse();
		$re->product= $productFeature;
		return $re;
	}
	
	public function getProductListFeature($request) {
		$ids = new IDList();
		$ids->ids = $request->productIds->ids;
		$products = $this->getImpl()->getProductListFeature($ids,null,TRUE);
		$re = new getProductListFeatureResponse();
		$re->products = new ProductList();
		$re->products->products = $products;
		return $re;
	}
	
	public function getProductListFeatureBySMList($request) {
		$ids = new IDList();
		$ids->ids = $request->productIds->ids;
		$list=new FeatureList();
		$list->feature=$request->features->feature;
		$products = $this->getImpl()->getProductListFeature($ids,$list,FALSE);
		$re = new getProductListFeatureBySMListResponse();
		$re->products = new ProductList();
		$re->products->products = $products;
		return $re;
	}
	
	public function getProductListFeatureBySMZ($request) {
		$ids = new IDList();
		$ids->ids = $request->productIds->ids;
		$products = $this->getImpl()->getProductListBySMZ($ids, $request->smzName);
		$re = new getProductListFeatureBySMZResponse();
		$re->products = new ProductList();
		$re->products->products = $products;
		return $re;
	}

	public function getGroupFeature($request) {
		$groupFeature = $this->getImpl()->getGroupFeature($request->groupId,null,TRUE);
		$re = new getGroupFeatureResponse();
		$re->group= $groupFeature;
		return $re;
	}
	
	public function getGroupFeatureBySMZ($request){
		$groupFeature=$this->getImpl()->getGroupBySMZ($request->groupId, $request->smzName);
		$re= new getGroupFeatureBySMZResponse();
		$re->group=$groupFeature;
		return $re;
	}
	
	public function getGroupFeatureBySMZType($request){
		$groupFeature=$this->getImpl()->getGroupBySMZType($request->groupId, $request->smzType);
		$re= new getGroupFeatureBySMZTypeResponse();
		$re->group=$groupFeature;
		return $re;
	}
	
	public function getGroupFeatureBySMList($request) {
		$list=new FeatureList();
		$list->feature=$request->features->feature;
		$groupFeature = $this->getImpl()->getGroupFeature($request->groupId, $list);
		$re = new getGroupFeatureResponse();
		$re->group= $groupFeature;
		return $re;
	}
	
	public function getGroupListFeature($request) {
		$ids = new IDList();
		$ids->ids = $request->groupIds->ids;
		$groups = $this->getImpl()->getGroupListFeature($ids,null,TRUE);
		$re = new getGroupListFeatureResponse();
		$re->groups = new GroupList();
		$re->groups->groups = $groups;
		return $re;
	}
	
	public function getGroupListFeatureBySMList($request) {
		$ids = new IDList();
		$ids->ids = $request->groupIds->ids;
		$list=new FeatureList();
		$list->feature=$request->features->feature;
		$groups = $this->getImpl()->getGroupListFeature($ids,$list,FALSE);
		$re = new getGroupListFeatureBySMListResponse();
		$re->groups = new GroupList();
		$re->groups->groups = $groups;
		return $re;
	}
	
	public function getGroupListFeatureBySMZ($request) {
		$ids = new IDList();
		$ids->ids = $request->groupIds->ids;
		$groups = $this->getImpl()->getGroupListBySMZ($ids, $request->smzName);
		$re = new getGroupListFeatureBySMZResponse();
		$re->groups = new GroupList();
		$re->groups->groups = $groups;
		return $re;
	}

	public function getDocumentFeature($request) {
		$documentFeature = $this->getImpl()->getDocumentFeature($request->documentId,null,TRUE);
		$re = new getDocumentFeatureResponse();
		$re->document= $documentFeature;
		return $re;
	}
	
	public function getDocumentFeatureBySMZ($request){
		$documentFeatures=$this->getImpl()->getDocumentBySMZ($request->documentId, $request->smzName);
		$re= new getDocumentFeatureBySMZResponse();
		$re->document=$documentFeatures;
		return $re;
	}
	
	public function getDocumentFeatureBySMList($request) {
		$list=new FeatureList();
		$list->feature=$request->features->feature;
		$documentFeature = $this->getImpl()->getDocumentFeature($request->documentId, $list);
		$re = new getDocumentFeatureResponse();
		$re->document= $documentFeature;
		return $re;
	}
	
	public function getDocumentListFeature($request) {
		$ids = new IDList();
		$ids->ids = $request->documentIds->ids;
		$documents = $this->getImpl()->getDocumentListFeature($ids,null,TRUE);
		$re = new getDocumentListFeatureResponse();
		$re->documents = new DocumentList();
		$re->documents->documents = $documents;
		return $re;
	}
	
	public function getDocumentListFeatureBySMList($request) {
		$ids = new IDList();
		$ids->ids = $request->documentIds->ids;
		$list=new FeatureList();
		$list->feature=$request->features->feature;
		$documents = $this->getImpl()->getDocumentListFeature($ids,$list,FALSE);
		$re = new getDocumentListFeatureBySMListResponse();
		$re->documents = new DocumentList();
		$re->documents->documents = $documents;
		return $re;
	}
	
	public function getDocumentListFeatureBySMZ($request) {
		$ids = new IDList();
		$ids->ids = $request->documentIds->ids;
		$documents = $this->getImpl()->getDocumentListBySMZ($ids, $request->smzName);
		$re = new getDocumentListFeatureBySMZResponse();
		$re->documents = new DocumentList();
		$re->documents->documents = $documents;
		return $re;
	}

	public function getGroupRelations($request) {
		$relations = $this->getImpl()->getRelations('group', $request->groupId);
		$re = new getGroupRelationsResponse();
		$re->relationList = $relations;
		return $re;
	}

	public function getGroupRelationsForName($request) {
		$relations = $this->getImpl()->getRelations('group', $request->groupId, $request->relationName);
		$re = new getGroupRelationsForNameResponse();
		$re->relationList = $relations;
		return $re;
	}

	public function getProductRelations($request) {
		$relations = $this->getImpl()->getRelations('product', $request->productId);
		$re = new getProductRelationsResponse();
		$re->relationList = $relations;
		return $re;
	}

	public function getProductRelationsForName($request) {
		$relations = $this->getImpl()->getRelations('product', $request->productId, $request->relationName);
		$re = new getProductRelationsForNameResponse();
		$re->relationList = $relations;
		return $re;
	}

	public function getDocumentRelations($request) {
		$relations = $this->getImpl()->getRelations('document', $request->documentId);
		$re = new getDocumentRelationsResponse();
		$re->relationList = $relations;
		return $re;
	}

	public function getDocumentRelationsForName($request) {
		$relations = $this->getImpl()->getRelations('document', $request->documentId, $request->relationName);
		$re = new getDocumentRelationsForNameResponse();
		$re->relationList = $relations;
		return $re;
	}

	public function searchProduct($request) {
		$list= new SearchCriteriaList();
		$list->searchCriteria=$request->searchCriterias->searchCriteria;
		$prodIds = $this->getImpl()->searchProduct($list, $request->shopId, $request->type);
		$re = new searchProductResponse(); 
	 
		$re->productIds = $prodIds;
		return $re;
	}

	public function getProductParents($request) {	
		$listOfParentIds = $this->getImpl()->getParents('product', $request->productId);
		$p = new ElementParents();
		$p->elementId = $request->productId;
		$p->parents = $listOfParentIds;
		$re = new getProductParentsResponse();
		$re->parents = $p;
		return $re;
	}
	
	public function getProductListParents($request) {	
		$ids = new IDList();
		$ids->ids = $request->productIds->ids;
		$listOfParentIds = $this->getImpl()->getParentsList('product', $ids);
		$re = new getProductParentsResponse();
		$re->parents = array();
		foreach ($listOfParentIds as $elementId => $parentIds) {
			$p = new ElementParents();
			$p->elementId = $elementId;
			$p->parents = new IDList();
			$p->parents->ids = $parentIds;
			$re->parents[] = $p;
		}
		return $re;
	}

	public function getGroupParents($request) {
		$listOfParentIds = $this->getImpl()->getParents('group', $request->groupId);
		$p = new ElementParents();
		$p->elementId = $request->groupId;
		$p->parents = $listOfParentIds;
		$re = new getGroupParentsResponse();
		$re->parents = $p;
		return $re;
	}
	
	public function getGroupListParents($request) {	
		$ids = new IDList();
		$ids->ids = $request->groupIds->ids;
		$listOfParentIds = $this->getImpl()->getParentsList('group', $ids);
		$re = new getGroupParentsResponse();
		$re->parents = array();
		foreach ($listOfParentIds as $elementId => $parentIds) {
			$p = new ElementParents();
			$p->elementId = $elementId;
			$p->parents = new IDList();
			$p->parents->ids = $parentIds;
			$re->parents[] = $p;
		}
		return $re;
	}

	public function getDocumentParents($request) {
		$listOfParentIds = $this->getImpl()->getParents('document', $request->documentId);
		$p = new ElementParents();
		$p->elementId = $request->documentId;
		$p->parents = $listOfParentIds;
		$re = new getDocumentParentsResponse();
		$re->parents = $p;
		return $re;
	}
	
	public function getDocumentListParents($request) {	
		$ids = new IDList();
		$ids->ids = $request->documentIds->ids;
		$listOfParentIds = $this->getImpl()->getParentsList('document', $ids);
		$re = new getDocumentParentsResponse();
		$re->parents = array();
		foreach ($listOfParentIds as $elementId => $parentIds) {
			$p = new ElementParents();
			$p->elementId = $elementId;
			$p->parents = new IDList();
			$p->parents->ids = $parentIds;
			$re->parents[] = $p;
		}
		return $re;
	}
	
	public function getObjectByAsimOid($request) {
		$asimOid = $request->asimOid;
		$shopId = $request->shopId;
		$obj = $this->getImpl()->getAsimObjectByOid($asimOid, $shopId);
		$re = new getObjectByAsimOidResponse();
		$re->object = $obj;
		return $re;
	}

	public function getDBInfo() {
		$DBId = $this->getImpl()->getDBInfo();
		$re = new getDBInfoResponse();
		$re->dbInfo = $DBId;
		return $re;
	}
	
	public function getSMZ($request){
		$smzStructure=$this->getImpl()->getSMZ($request->smzName, $request->shopId);
		$re= new getSMZResponse();
		$re->smz=$smzStructure;
		return	$re;
	}
	
	public function getSMZNameByType($request){
		if (!empty($request->productId)) {
			$id = $request->productId;
			$t = 'P';
		} else {
			$id = $request->groupId;
			$t = 'G';
		}
		$smzName=$this->getImpl()->getSMZNameByType($id, $t, $request->smzType);
		$ret= new getSMZNameByTypeResponse;
		$ret->smzName=$smzName;
		return $ret;
	}
	
	public function getSMZByType($request){
		if (!empty($request->productId)) {
			$id = $request->productId;
			$t = 'P';
		} else {
			$id = $request->groupId;
			$t = 'G';
		}
		$SMZ=$this->getImpl()->getSMZByType($id, $t, $request->smzType);
		$ret=new getSMZByTypeResponse();
		$ret->smz=$SMZ;
		return $ret;
	}

}

class SOAPErrorHandler implements dataConnectorErrorHandler {

	public function sendError($code, $string, $actor) {
		throw new SoapFault($code, $string, $actor, '');
	}

}

$useStage = null;
if (defined('USE_STAGE')) {
	$useStage = USE_STAGE;
} else {
	if (is_array($_GET) && array_key_exists("db", $_GET)) {
		$db = $_GET['db'];
		switch ($db) {
			case 'production':
				$useStage = false;
				break;
			case 'staging':
				$useStage = true;
				break;
		}
	}
}

if (defined('MS3C_WSDL_LOCATION')) {
    $wsdl = MS3C_WSDL_LOCATION;
} else {
    $wsdl = __DIR__ . "/../schema/dataConnector.wsdl";
}

$version = SERVICE_VERSION_STRING;
$server = new SoapServer($wsdl, array("uri" => "http://www.ms3commerce.at/API/V2/service/".SERVICE_VERSION_STRING));

// Get requested version
$req = file_get_contents("php://input");
if ($req) {
	preg_match_all('/xmlns(?::[^=]*)?="([^"]+)"/i', $req, $matches);
	if (count($matches)) {
		$namespaces = $matches[1];
		foreach ($namespaces as $ns) {
			if (strpos($ns, "http://www.ms3commerce.at/API/V2/service/") === 0) {
				$lastsl = strrpos($ns, "/");
				$version = substr($ns, $lastsl+1);
				break;
			}
		}
	}
}

// Create handler depending on requested version
switch ($version) {
	case 'V4.8.0':
		$handler = new dataConnector_DLW_handler($useStage);
		break;
	default:
		$server->fault(
				'UNKNOWN VERSION', 
				'The Request Version could not be determined',
				'Client',
				'Server Version '.SERVICE_VERSION_STRING);
		break;
}


$server->setObject($handler);
$server->handle();

?>
