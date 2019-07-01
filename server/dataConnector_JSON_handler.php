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

header('Content-Type: application/json; charset=utf-8');
require 'dataConnectorImpl.php';
require_once __DIR__ . '/../binding/dataConnectorTypes.php';
require_once __DIR__ . '/../binding/dataConnectorMessages.php';

class dataConnector_JSON_handler {

	var $impl = null;
	var $useStage = false;
	var $ressource = "";
	var $params = array();
	var $get = array();
	var $post = null;
	var $errorHandler;

	public function __construct() {
		if (isset($_GET['stage']) && $_GET['stage'] == 1) {
			$this->useStage = true;
		}
		$this->errorHandler = new JSONErrorHandler();
	}

	public function run() {
		$response = null;
		switch ($this->getRessource()) {
			case 'shoproot':
				$response = $this->shoproot();
				break;
			case 'groupchildren':
				$response = $this->groupchildren();
				break;
			case 'groupcontent':
				$response = $this->groupcontent();
				break;
			case 'grouptree':
				$response = $this->grouptree();
				break;
			case 'product':
				$response = $this->product();
				break;
			case 'document':
				$response = $this->document();
				break;
			case 'group':
				$response = $this->group();
				break;
			case 'smz':
				$response = $this->smz();
				break;
			case 'relation':
				$response = $this->relation();
				break;
			case 'parents':
				$response = $this->parents();
				break;
			case 'dbinfo':
				$response = $this->dbinfo();
				break;
			case 'object':
				$response = $this->object();
				break;
			case 'search':
				$response = $this->search();
				break;
			default:
				$this->errorHandler->sendError('Client', 'Unsupported resource', 'Client');
				return;
				
		}
		echo json_encode($response);
	}

	private function shoproot() {
		if ($this->getParamsValue(0) == "bylangandmarket") {
			return $this->getImpl()->getShopRootForLangAndMarket($this->getGetValue('lang'), $this->getGetValue('market'));
		} else if ($this->getParamsValue(0)) {
			return $this->getImpl()->getShopRoot($this->getParamsValue(0));
		} else {
			return $this->getImpl()->getAllShopRoots();
		}
	}

	private function groupchildren() {
		return $this->getImpl()->getGroupChildren($this->getParamsValue(0));
	}

	private function groupcontent() {
		if ($this->getParamsValue(1) == 'bysmz') {
			return $this->getImpl()->getContentBySMZ($this->getParamsValue(0), $this->getGetValue('smz'));
		} else if ($this->getParamsValue(1) == 'bysmlist') {
			$list = new FeatureList();
			$list->feature = $this->getPostValue();
			return $this->getImpl()->getGroupContent($this->getParamsValue(0), $list);
		} else if ($this->getParamsValue(0)) {
			return $this->getImpl()->getGroupContent($this->getParamsValue(0), new FeatureList(), true);
		} else {
			$this->errorHandler->sendError('Client', 'Parameter missing', 'Client');
		}
	}

	private function grouptree() {
		return $this->getImpl()->getGroupTree($this->getParamsValue(0));
	}

	private function product() {
		if ($this->getParamsValue(0) == 'list') {
			return $this->productlist();
		} else if ($this->getParamsValue(1) == 'bysmz') {
			return $this->getImpl()->getProductBySMZ($this->getParamsValue(0), $this->getGetValue('smz'));
		} else if ($this->getParamsValue(1) == 'bysmztype') {
			return $this->getImpl()->getProductBySMZType($this->getParamsValue(0), $this->getGetValue('type'));
		} else if ($this->getParamsValue(1) == 'bysmlist') {
			$list = new FeatureList();
			$list->feature = $this->getPostValue();
			return $this->getImpl()->getProductFeature($this->getParamsValue(0), $list);
		} else if ($this->getParamsValue(0)) {
			return $this->getImpl()->getProductFeature($this->getParamsValue(0), null, TRUE);
		} else {
			$this->errorHandler->sendError('Client', 'Parameter missing', 'Client');
		}
	}

	private function productlist() {
		$ids = new IDList();
		if ($this->getParamsValue(1) == 'bysmz') {
			$ids->ids = $this->getPostValue();
			return $this->getImpl()->getProductListBySMZ($ids, $this->getGetValue('smz'));
		} else if ($this->getParamsValue(1) == 'bysmlist') {
			$ids->ids = $this->getPostValue('ids');
			$list = new FeatureList();
			$list->feature = $this->getPostValue('sms');
			return $this->getImpl()->getProductListFeature($ids, $list, FALSE);
		} else {
			$ids->ids = $this->getPostValue();
			return $this->getImpl()->getProductListFeature($ids, null, TRUE);
		}
	}

	private function document() {
		if ($this->getParamsValue(0) == 'list') {
			return $this->documentlist();
		} else if ($this->getParamsValue(1) == 'bysmz') {
			return $this->getImpl()->getDocumentBySMZ($this->getParamsValue(0), $this->getGetValue('smz'));
		} else if ($this->getParamsValue(1) == 'bysmlist') {
			$list = new FeatureList();
			$list->feature = $this->getPostValue();
			return $this->getImpl()->getDocumentFeature($this->getParamsValue(0), $list);
		} else if ($this->getParamsValue(0)) {
			return $this->getImpl()->getDocumentFeature($this->getParamsValue(0), null, TRUE);
		} else {
			$this->errorHandler->sendError('Client', 'Parameter missing', 'Client');
		}
	}

	private function documentlist() {
		$ids = new IDList();
		if ($this->getParamsValue(1) == 'bysmz') {
			$ids->ids = $this->getPostValue();
			return $this->getImpl()->getDocumentListBySMZ($ids, $this->getGetValue('smz'));
		} else if ($this->getParamsValue(1) == 'bysmlist') {
			$ids->ids = $this->getPostValue('ids');
			$list = new FeatureList();
			$list->feature = $this->getPostValue('sms');
			return $this->getImpl()->getDocumentListFeature($ids, $list, FALSE);
		} else {
			$ids->ids = $this->getPostValue();
			return $this->getImpl()->getDocumentListFeature($ids, null, TRUE);
		}
	}

	private function group() {
		if ($this->getParamsValue(0) == 'list') {
			return $this->grouplist();
		} else if ($this->getParamsValue(1) == 'bysmz') {
			return $this->getImpl()->getGroupBySMZ($this->getParamsValue(0), $this->getGetValue('smz'));
		} else if ($this->getParamsValue(1) == 'bysmztype') {
			return $this->getImpl()->getGroupBySMZType($this->getParamsValue(0), $this->getGetValue('type'));
		} else if ($this->getParamsValue(1) == 'bysmlist') {
			$list = new FeatureList();
			$list->feature = $this->getPostValue();
			return $this->getImpl()->getGroupFeature($this->getParamsValue(0), $list);
		} else if ($this->getParamsValue(0)) {
			return $this->getImpl()->getGroupFeature($this->getParamsValue(0), null, TRUE);
		} else {
			$this->errorHandler->sendError('Client', 'Parameter missing', 'Client');
		}
	}

	private function grouplist() {
		$ids = new IDList();
		if ($this->getParamsValue(1) == 'bysmz') {
			$ids->ids = $this->getPostValue();
			return $this->getImpl()->getGroupListBySMZ($ids, $this->getGetValue('smz'));
		} else if ($this->getParamsValue(1) == 'bysmlist') {
			$ids->ids = $this->getPostValue('ids');
			$list = new FeatureList();
			$list->feature = $this->getPostValue('sms');
			return $this->getImpl()->getGroupListFeature($ids, $list, FALSE);
		} else {
			$ids->ids = $this->getPostValue();
			return $this->getImpl()->getGroupListFeature($ids, null, TRUE);
		}
	}

	private function smz() {
		if ($this->getParamsValue(0) == "bytype") {
			$group = $this->getGetValue('group');
			$prod = $this->getGetValue('product');
			if (!empty($group)) {
				$t = 'G';
				$id = $group;
			}
			if (!empty($prod)) {
				$t = 'P';
				$id = $prod;
			}
			
			if ($this->getGetValue('nameonly')) {
				return $this->getImpl()->getSMZNameByType($id, $t, $this->getGetValue('type'));
			}
			return $this->getImpl()->getSMZByType($id, $t, $this->getGetValue('type'));
		} else if ($this->getParamsValue(0)) {
			return $this->getImpl()->getSMZ($this->getParamsValue(0), $this->getGetValue('shopid'));
		} else {
			$this->errorHandler->sendError('Client', 'Parameter missing', 'Client');
		}
	}

	private function relation() {
		$element = $this->getParamsValue(0);
		if ($element == "group" || $element == "product" || $element == "document") {
			if ($this->getParamsValue(2) == "byname") {
				return $this->getImpl()->getRelations($element, $this->getParamsValue(1), $this->getGetValue('name'));
			} else if ($this->getParamsValue(1)) {
				return $this->getImpl()->getRelations($element, $this->getParamsValue(1));
			}
		} else {
			$this->errorHandler->sendError('Client', 'Unsupported endpoint', 'Client');
		}
	}

	private function parents() {
		$element = $this->getParamsValue(0);
		if ($element == "group" || $element == "product" || $element == "document") {
			return $this->getImpl()->getParents($element, $this->getParamsValue(1));
		} else if ($element == "groups" || $element == "products" || $element == "documents") {
			$e = substr($element, 0, strlen($element)-1);
			$lst = new IDList();
			$lst->ids = $this->getPostValue();
			return $this->getImpl()->getParentsList($e, $lst);
		} else {
			$this->errorHandler->sendError('Client', 'Unsupported endpoint', 'Client');
		}
	}

	private function dbinfo() {
		return $this->getImpl()->getDBInfo();
	}

	private function object() {
		return $this->getImpl()->getAsimObjectByOid($this->getParamsValue(0), $this->getGetValue('shop'));
	}

	private function search() {
		$list = new SearchCriteriaList();
		$list->searchCriteria = $this->getPostValue();
		return $this->getImpl()->searchProduct($list, $this->getGetValue('shopid'), $this->getGetValue('type'));
	}

	private function getParamsValue($pos) {
		$params = $this->getParams();
		if (isset($params[$pos])) {
			return is_numeric($params[$pos]) ? intval($params[$pos]) : $params[$pos];
		}
		return "";
	}

	private function getGetValue($key) {
		$get = $this->getGet();
		if (isset($get[$key])) {
			return is_numeric($get[$key]) ? intval($get[$key]) : $get[$key];
		}
		return "";
	}

	private function getPostValue($key = null) {
		$post = $this->getPost();
		if ($key == null) {
			return $post;
		}
		if (isset($post->$key)) {
			return $post->$key;
		}
		return "";
	}

	private function getImpl() {
		if ($this->impl != null) {
			return $this->impl;
		}
		$this->impl = new dataConnectorImpl($this->useStage, $this->errorHandler);
		return $this->impl;
	}

	public function getRequestValues() {
		$ressource = "";
		$params = array();
		$get = array();
		$post = null;
		if (isset($_GET)) {
			$get = $_GET;
		}
		$post = file_get_contents("php://input");
		$parmsstring = explode('?', $_SERVER['REQUEST_URI']);
		
		$path = $parmsstring[0];
		$path = preg_replace('#^.*/dataConnectorV2/rest/#', '', $path);
		$parts = explode('/', $path);
		$parts = array_map('urldecode', $parts);
		$ressource = array_shift($parts);
		$params = $parts;
		
		$this->setRessource($ressource);
		$this->setParams($params);
		$this->setGet($get);
		$this->setPost($post);
	}

	public function getRessource() {
		return $this->ressource;
	}

	public function getParams() {
		return $this->params;
	}

	public function getGet() {
		return $this->get;
	}

	public function getPost() {
		return $this->post;
	}

	public function setRessource($ressource) {
		$this->ressource = $ressource;
	}

	public function setParams($params) {
		$this->params = $params;
	}

	public function setGet($get) {
		$this->get = $get;
	}

	public function setPost($post) {
		if ($post) {
			$this->post = json_decode($post);
		}
	}

}

class JSONErrorHandler implements dataConnectorErrorHandler {

	public function sendError($code, $string, $actor) {
		header("HTTP/1.1 400: Bad Request");
		echo json_encode(array("code" => $code, "message" => $string, "actor" => $actor));
		exit();
	}

}

$handler = new dataConnector_JSON_handler();
$handler->getRequestValues();
$handler->run();
?>