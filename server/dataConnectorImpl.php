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

require_once __DIR__ . '/../binding/dataConnectorTypes.php';
if (file_exists(__DIR__ . '/../../dataTransfer/dataTransfer_config.php')) {
	// asimCommerce V3+
	require_once(__DIR__ . '/../../dataTransfer/dataTransfer_config.php');
} else {
	require_once(__DIR__ . "/../../dataTransfer/runtime_config.php");
}
require_once(__DIR__ . "/../../dataTransfer/mS3Commerce_db.php");

define('SERVICE_VERSION_STRING', 'V4.8.0');

/**
 * Description of dataConnectorImpl
 *
 * @author marcelo.stucky
 */
class dataConnectorImpl {

	/**
	 * @$var tx_ms3commerce_db_factory
	 */
	var $db;
	var $useStage;
	var $errorHandler;

	public function __construct($useStage, dataConnectorErrorHandler $errorHandler) {
		$this->useStage = $useStage;
		$this->db = tx_ms3commerce_db_factory::buildDatabase(false, $useStage);
		$this->errorHandler = $errorHandler;
	}

	public function getShopRootForLangAndMarket($langId, $marketId) {
		if (!is_int($langId) | !is_int($marketId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getShopRootForLangAndMarket', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getShopRootForLangAndMarket', 'dataConnector.php');
		} else {
			$ret = new ShopRoot();
			$res = $this->db->exec_SELECTquery("*", "ShopInfo", "LanguageId =" . $langId . " AND MarketId=" . $marketId);
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			$row = $this->db->sql_fetch_assoc($res);
			$this->db->sql_free_result($res);
			if (!$row) {
				return NULL;
			}
			$ret->shopId = $row['ShopId'];
			$ret->languageId = $row['LanguageId'];
			$ret->marketId = $row['MarketId'];
			$ret->rootGroupId = $row['RootGroupId'];
			$ret->exportDate = $row['BaseExportDate'];
			$ret->importDate = $row['ImportDate'];
			$ret->uploadDate = $row['UploadDate'];
			return $ret;
		}
	}

	public function getShopRoot($shopId) {
		if (!is_int($shopId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getShopId', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getShopId', 'dataConnector.php');
		} else {
			$ret = new ShopRoot();
			$res = $this->db->exec_SELECTquery("*", "ShopInfo", "shopId=" . $shopId);
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			$row = $this->db->sql_fetch_assoc($res);
			$this->db->sql_free_result($res);
			if (!$row) {
				return NULL;
			}

			$ret->shopId = $row['ShopId'];
			$ret->languageId = $row['LanguageId'];
			$ret->marketId = $row['MarketId'];
			$ret->rootGroupId = $row['RootGroupId'];
			$ret->exportDate = $row['BaseExportDate'];
			$ret->importDate = $row['ImportDate'];
			$ret->uploadDate = $row['UploadDate'];
			return $ret;
		}
	}

	public function getAllShopRoots() {
		$retList = new stdClass();
		$retList->shops = array();
		$res = $this->db->exec_SELECTquery("*", "ShopInfo", "");
		$this->checkDBRes($res, __FUNCTION__, __LINE__);
		while ($row = $this->db->sql_fetch_assoc($res)) {
			$ret = new ShopRoot();
			$ret->shopId = $row['ShopId'];
			$ret->languageId = $row['LanguageId'];
			$ret->marketId = $row['MarketId'];
			$ret->rootGroupId = $row['RootGroupId'];
			$ret->exportDate = $row['BaseExportDate'];
			$ret->importDate = $row['ImportDate'];
			$ret->uploadDate = $row['UploadDate'];
			$retList->shops[] = $ret;
		}
		$this->db->sql_free_result($res);
		if (!$retList->shops) {
			return NULL;
		}
		return $retList;
	}
	
	public function getGroupChildren($groupId) {
		if (!is_int($groupId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getGroupChildren', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getGroupChildren', 'dataConnector.php');
		} else {
			$groupChildren = new GroupChildren();
			$groupChildren->groupId = $groupId;
			$menuId = $this->oneElementFromSQL("id", "Menu", "GroupId =" . $groupId);
			if (!$menuId) {
				return null;
			}
			$res = $this->db->exec_SELECTquery("GroupId,ProductId,DocumentId", "Menu", "ParentId=" . $menuId, '', 'Id');
			$groupChildren->productIds = new IDList();
			$groupChildren->subGroupIds = new IDList();
			$groupChildren->doucumentIds = new IDList();
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			while ($row = $this->db->sql_fetch_assoc($res)) {
				//$zeile[$row['Id']] = $row;
				//if (isset($zeile)) {
				if (isset($row)) {
					//foreach ($zeile as $value) {
					$value = $row;
					if ($value['GroupId'] != null || !empty($value['GroupId'])) {
						$groupChildren->subGroupIds->ids[] = intval($value['GroupId']);
					} else if ($value['ProductId'] != null || !empty($value['ProductId'])) {
						$groupChildren->productIds->ids[] = intval($value['ProductId']);
					} else if ($value['DocumentId'] != NULL || !empty($value['DocumentId'])) {
						$groupChildren->documentIds->ids[] = intval($value['DocumentId']);
					}
					//}
				}
			}
			$this->db->sql_free_result($res);
			return $groupChildren;
		}
	}

	public function getGroupContent($groupId, FeatureList $filterFeatures = null, $all = false) {

		if (!is_int($groupId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getGroupcontent', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getGroupcontent', 'dataConnector.php');
		} else {
			$groupContent = new GroupContent();
			$menuId = $this->oneElementFromSQL("id", "Menu", "GroupId =" . $groupId);
			if (!$menuId) {
				return NULL;
			}
			$res = $this->db->exec_SELECTquery("*", "Menu", "ParentId=" . $menuId, '', 'Id');
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			$groupContent->groupId = $groupId;
			$groupContent->groups = array();
			$groupContent->products = array();
			$groupContent->documents = array();

			while ($row = $this->db->sql_fetch_assoc($res)) {
				$zeile[$row['Id']] = $row;
			}
			$this->db->sql_free_result($res);
			
			if (isset($zeile)) {
				foreach ($zeile as $value) {
					if ($value['GroupId'] != null) {
						$group = $this->loadGroup($value['GroupId']);
						$group->features = $this->getFeaturesValues('group', $group->groupId, $filterFeatures, $all);
						$groupContent->groups[] = $group;
					} else if ($value['ProductId'] != null) {
						$product = $this->loadProduct($value['ProductId']);
						$product->features = $this->getFeaturesValues('product', $product->productId, $filterFeatures, $all);
						$groupContent->products[] = $product;
					} else if ($value['DocumentId'] != null) {
						$document = $this->loadDocument($value['DocumentId']);
						$document->features = $this->getFeaturesValues('document', $document->documentId, $filterFeatures, $all);
						$groupContent->documents[] = $document;
					}
				}
				return $groupContent;
			}
		}
	}

	private function getSMZFeatures($SMZName, $shopId) {
		$features = array();

		$shopRanges = $this->getIDRangeForShopId($shopId);
		$shopConstraint = $this->getIDRangeConstraint($shopRanges, 't1');

		$smzName = $this->db->sql_escape($SMZName);
		$sql = "SELECT t1.Name as featureName FROM Feature t1,featureComp_feature t2,featureCompilation t3 " .
				"WHERE t3.Name=" . $smzName . " AND t2.featureCompId=t3.id AND t1.id=t2.featureId $shopConstraint ".
				"ORDER BY t2.HierarchyType, t2.sort;";
		$res = $this->db->sql_query($sql);
		$this->checkDBRes($res, __FUNCTION__, __LINE__);
		
		while ($row = $this->db->sql_fetch_assoc($res)) {
			$features[] = $row['featureName'];
		}
		$this->db->sql_free_result($res);
		
		return $features;
	}
	
	public function getContentBySMZ($groupId, $SMZName) {
		$filterFeatures = new FeatureList;
		
		$shopId = $this->getShopIdFromElementId($groupId);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		$groupContent = $this->getGroupContent($groupId, $filterFeatures);
		return $groupContent;
	}

	public function getGroupTree($GroupId) {

		if (!is_int($GroupId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getGroupTree', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getGroupTree', 'dataConnector.php');
		} else {
			return $this->makeTree($GroupId);
		}
	}

	function makeTree($GroupId) {
		$GroupTree = new GroupTree();
		$GroupTree->groupId = intval($GroupId);
		$menuId = $this->oneElementFromSQL("Id", "Menu", "GroupId =" . $GroupId);
		if (!$menuId) {
			return NULL;
		}
		$result = $this->db->exec_SELECTquery("*", "Menu", "ParentId = " . $menuId, '', 'Id');
		$this->checkDBRes($result, __FUNCTION__, __LINE__);
		while ($row = $this->db->sql_fetch_assoc($result)) {
			$zeile[$row['Id']] = $row;
		}
		$this->db->sql_free_result($result);
		
		$GroupTree->children = array();
		if (isset($zeile)) {

			foreach ($zeile as $value) {
				if ($value['GroupId'] != null) {
					$GroupTree->children[] = $this->makeTree($value['GroupId']);
				}
			}
		}
		return $GroupTree;
	}

	public function getProductFeature($prodId, FeatureList $filterFeatures = null, $all = false) {
		if (!$filterFeatures) {
			$filterFeatures = new FeatureList();
		} else if (isset($filterFeatures->feature) && !is_array($filterFeatures->feature) && !empty($filterFeatures->feature)) {
			$filterFeatures->feature = array($filterFeatures->feature);
		}
		
		if (!is_int($prodId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getProductFeatures', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getProductFeatures', 'dataConnector.php');
		} else {
			$product = $this->loadProduct($prodId);
			if (!$product) {
				return NULL;
			}

			$ret = $this->getFeaturesValues('product', $prodId, $filterFeatures, $all);
			$product->features = $ret;

			return $product;
		}
	}
	
	public function getProductBySMZ($productId, $SMZName) {
		$filterFeatures = new FeatureList;
		$shopId = $this->getShopIdFromElementId($productId);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		$product = $this->getProductFeature($productId, $filterFeatures);
		return $product;
	}
	
	public function getProductBySMZType($productId, $SMZType) {
		$filterFeatures = new FeatureList;
		$SMZName = $this->getSMZNameByType($productId, 'P', $SMZType);
		$shopId = $this->getShopIdFromElementId($productId);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		$product = $this->getProductFeature($productId, $filterFeatures);
		return $product;
	}
	
	public function getProductListFeature(IDList $productIds = null, FeatureList $filterFeatures = null, $all = false) {
		$pids = $this->uniqueIDs($productIds);
		if (is_null($pids)) {
			return null;
		}
		$products = array();
		foreach ($pids->ids as $pid) {
			$products[] = $this->getProductFeature($pid, $filterFeatures, $all);
		}
		return $products;
	}

	public function getProductListBySMZ(IDList $productIds = null, $SMZName = null) {
		$filterFeatures = new FeatureList;
		$pids = $this->uniqueIDs($productIds);
		if (is_null($pids)) {
			return null;
		}
		$shopId = $this->getShopIdFromElementId($pids->ids[0]);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		return $this->getProductListFeature($productIds, $filterFeatures);
	}

	public function getGroupFeature($groupId, FeatureList $filterFeatures = null, $all = false) {
		if (!$filterFeatures) {
			$filterFeatures = new FeatureList();
		} else if (isset($filterFeatures->feature) && !is_array($filterFeatures->feature) && !empty($filterFeatures->feature)) {
			$filterFeatures->feature = array($filterFeatures->feature);
		}
		if (!is_int($groupId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getGroupFeature', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getGroupFeature', 'dataConnector.php');
		} else {
			$group = $this->loadGroup($groupId);
			if (!$group) 
				return NULL;

			$ret = $this->getFeaturesValues('group', $groupId, $filterFeatures, $all);
			$group->features = $ret;
			return $group;
		}
	}
	
	public function getGroupBySMZ($groupId, $SMZName) {
		$filterFeatures = new FeatureList;
		$shopId = $this->getShopIdFromElementId($groupId);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		$group = $this->getGroupFeature($groupId, $filterFeatures);
		return $group;
	}
	
	public function getGroupBySMZType($groupId, $SMZType) {
		$filterFeatures = new FeatureList;
		$SMZName = $this->getSMZNameByType($groupId, 'G', $SMZType);
		$shopId = $this->getShopIdFromElementId($groupId);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		$group = $this->getGroupFeature($groupId, $filterFeatures);
		return $group;
	}
	
	public function getGroupListFeature(IDList $groupIds = null, FeatureList $filterFeatures = null, $all = false) {
		$gids = $this->uniqueIDs($groupIds);
		if (is_null($gids)) {
			return null;
		}
		$groups = array();
		foreach ($gids->ids as $gid) {
			$groups[] = $this->getGroupFeature($gid, $filterFeatures, $all);
		}
		return $groups;
	}

	public function getGroupListBySMZ(IDList $groupIds = null, $SMZName = null) {
		$filterFeatures = new FeatureList;
		$gids = $this->uniqueIDs($groupIds);
		if (is_null($gids)) {
			return null;
		}
		$shopId = $this->getShopIdFromElementId($gids->ids[0]);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		return $this->getGroupListFeature($groupIds, $filterFeatures);
	}
	
	public function getDocumentFeature($documentId, FeatureList $filterFeatures = null, $all = false) {
		if (!$filterFeatures) {
			$filterFeatures = new FeatureList();
		} else if (isset($filterFeatures->feature) && !is_array($filterFeatures->feature) && !empty($filterFeatures->feature)) {
			$filterFeatures->feature = array($filterFeatures->feature);
		}
		
		if (!is_int($documentId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getdocumentFeature', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getdocumentFeature', 'dataConnector.php');
		} else {
			$document = $this->loadDocument($documentId);
			if (!$document) {
				return NULL;
			}

			$ret = $this->getFeaturesValues('document', $documentId, $filterFeatures, $all);
			$document->features = $ret;
			return $document;
		}
	}

	public function getDocumentBySMZ($docId, $SMZName) {
		$filterFeatures = new FeatureList;
		$shopId = $this->getShopIdFromElementId($docId);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		$document = $this->getDocumentFeature($docId, $filterFeatures);
		return $document;
	}
	
	public function getDocumentListFeature(IDList $documentIds = null, FeatureList $filterFeatures = null, $all = false) {
		$dids = $this->uniqueIDs($documentIds);
		if (is_null($dids)) {
			return null;
		}
		$documents = array();
		foreach ($dids->ids as $did) {
			$documents[] = $this->getDocumentFeature($did, $filterFeatures, $all);
		}
		return $documents;
	}

	public function getDocumentListBySMZ(IDList $documentIds = null, $SMZName = null) {
		$filterFeatures = new FeatureList;
		$dids = $this->uniqueIDs($documentIds);
		if (is_null($dids)) {
			return null;
		}
		$shopId = $this->getShopIdFromElementId($dids->ids[0]);
		$filterFeatures->feature = $this->getSMZFeatures($SMZName, $shopId);
		return $this->getDocumentListFeature($documentIds, $filterFeatures);
	}
	
	public function getRelations($elementType, $elementId, $filter = NULL) {
		$isString = true;
		if ($filter != NULL) {
			if (!is_string($filter)) {
				$isString = false;
			}
		}
		if (!is_int($elementId) || $isString == false) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getRelations by ' . $elementType, 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getRelations by ' . $elementType, 'dataConnector.php');
		} else {


			$relList = new RelationList;
			$where = $elementType . "Id=" . $elementId;
			if ($filter != NULL) {
				$where.=" AND name=" . $this->db->sql_escape($filter);
			}
			$res = $this->db->exec_SELECTquery("*", "Relations", $where);
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			while ($row = $this->db->sql_fetch_assoc($res)) {
				$rel = new Relation;
				$rel->name = $row['Name'];
				$rel->amount = $row['Amount'];
				$rel->position = $row['Position'];
				$rel->destinationId = $row['DestinationId'];
				switch ($row['DestinationType']) {
					case 1: $rel->destinationType = ObjectType::Group; break;
					case 2: $rel->destinationType = ObjectType::Product; break;
					case 3: $rel->destinationType = ObjectType::Document; break;
					default:
						// Skip
						continue 2;
				}

				$relList->relation[] = $rel;
			}
			
			$this->db->sql_free_result($res);
			return $relList;
		}
	}

	public function searchProduct($searchCriterias, $shopId, $type) {
		
		if ($type == '') $type = 'AND';
		if (!is_array($searchCriterias->searchCriteria)) {
			$searchCriterias->searchCriteria = array($searchCriterias->searchCriteria);
		}
		
		switch ($type) {
			case 'AND':
				list($sql, $shopRangeTable, $isValid) = $this->buildANDSearchProduct($searchCriterias->searchCriteria);
				break;
			case 'OR':
				list($sql, $shopRangeTable, $isValid) = $this->buildORSearchProduct($searchCriterias->searchCriteria);
				break;
			default:
				$this->errorHandler->sendError('Client', 'Unsupported search type', 'dataConnector.php');
				return;
		}
		
		if (!$isValid) {
			//throw new SoapFault('Client', 'Incorrect Inputs in searchProduct', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in searchProduct', 'dataConnector.php');
		} else {
			$shopRange = $this->getIDRangeForShopId($shopId);
			$shopConstraint = $this->getIDRangeConstraint($shopRange, $shopRangeTable);
			$sql .= $shopConstraint;
			$res = $this->db->sql_query($sql);
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			$ids = array();
			while ($row = $this->db->sql_fetch_assoc($res)) {

				$ids[] = $row['ProductId'];
			}
			$this->db->sql_free_result($res);
			return $ids;
		}
	}

	private function buildANDSearchProduct($crit) {
		$i = 0;
		$from = '';
		$where = '';
		$isValid = TRUE;
		foreach ($crit as $criteria) {
			list($fName, $comp) = $this->buildSingleSearchCriteria($criteria);
			if (!$fName) {
				$isValid = FALSE;
			} else {
				$from.=" ProductValue pd$i, Feature f$i,";
				$where.=" f$i.Name=$fName AND pd$i.ContentPlain $comp AND pd$i.featureId=f$i.id AND";
			}
			$i++;
		}
		$from = substr($from, 0, -1);
		$where = substr($where, 0, -3);
		for ($j = 1; $j < $i; $j++) {
			$where.=" AND pd" . ($j - 1) . ".productId=pd" . $j . ".productId";
		}
		
		$sql = "SELECT pd0.ProductId FROM" . $from . " WHERE " . $where;
		
		return array($sql, 'pd0', $isValid);
	}
	
	private function buildORSearchProduct($crit) {
		$isValid = TRUE;
		$where = '';
		$OR = '';
		foreach ($crit as $criteria) {
			list($fName, $comp) = $this->buildSingleSearchCriteria($criteria);
			if (!$fName) {
				$isValid = FALSE;
			} else {
				$where .= "$OR (f.Name=$fName AND pv.ContentPlain $comp)";
				$OR = ' OR';
			}
		}
		
		$sql = "SELECT DISTINCT pv.ProductId FROM ProductValue pv INNER JOIN Feature f ON pv.FeatureId = f.Id WHERE ($where) ";
		
		return array($sql, 'pv', $isValid);
	}
	
	private function buildSingleSearchCriteria($criteria) {
		$fName = $this->db->sql_escape($criteria->feature);
		$fValue = $this->db->sql_escape($criteria->value, false);
		if (!is_string($fName) || $fName == "''" || !is_string($fValue)) {
			return array(false, false);
		}
		$compType = (isset($criteria->compType) ? $criteria->compType : CompType::EQUALS);
		switch ($compType) {
		case CompType::CONTAINS:
			$comp = " LIKE '%$fValue%'";
			break;
		case CompType::EQUALS:
		default:
			$comp = "='$fValue'";
		}
		return array($fName, $comp);
	}
	
	//template
	public function getSMZ($smzName, $shopId) {
		if (!is_string($smzName) || !is_int($shopId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getSMZ', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getSMZ', 'dataConnector.php');
		} else {
			$shopRanges = $this->getIDRangeForShopId($shopId);
			$shopConstraint = $this->getIDRangeConstraint($shopRanges, 't0');
			
			$smzName = $this->db->sql_escape($smzName);
			$query = "SELECT t0.name as SMZName,t0.type as SMZType,t2.name as featName,t1.* FROM featureCompilation t0,featureComp_feature t1,Feature t2 " .
					"WHERE t0.name=" . $smzName . " AND  t0.id=t1.featureCompId AND t1.featureId=t2.id $shopConstraint ORDER BY hierarchyType,sort";
			$res = $this->db->sql_query($query);
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			$tree = array();
			while ($row = $this->db->sql_fetch_assoc($res)) {
				$tree[$row['HierarchyType']][] = $row;
			}
			$this->db->sql_free_result($res);
			$SMZ = $this->SMZFeaturesTree($tree);
			return $SMZ;
		}
		return null;
	}

	public function getSMZByType($elemId, $elemType, $smzType) {
		if (!is_int($elemId) || !is_string($smzType) || ($elemType != 'G' && $elemType != 'P')) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getSMZByType', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getSMZByType', 'dataConnector.php');
		} else {
			if ($elemType == 'G') $field = 'groupId';
			else $field = 'productId';
			$smzType = $this->db->sql_escape($smzType);
			$query = "SELECT t0.name as SMZName,t0.type as SMZType,t2.name as featName,t1.* FROM featureCompilation t0,featureComp_feature t1,Feature t2,FeatureCompValue t3 " .
					"WHERE t0.type=$smzType AND  t0.id=t1.featureCompId AND t1.featureId=t2.id AND t0.id=t3.featureCompId AND t3.$field=$elemId " .
					"ORDER BY hierarchyType,sort";
			$res = $this->db->sql_query($query);
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			$tree = array();
			while ($row = $this->db->sql_fetch_assoc($res)) {
				$tree[$row['HierarchyType']][] = $row;
			}
			$this->db->sql_free_result($res);
			if (empty($tree)) {
				return NULL;
			}
			$SMZ = $this->SMZFeaturesTree($tree);
		}
		return $SMZ;
	}

	function SMZFeaturesTree($tree) {
		if (empty($tree)) {
			return null;
		}
		$SMZ = new SMZ();

		$typenames = array(1 => SMZHierarchyType::FGF, 2 => SMZHierarchyType::SF, 3 => SMZHierarchyType::RGF);
		$br = reset($tree);
		$ro = reset($br);
		$SMZ->name = $ro['SMZName'];
		$SMZ->type = $ro['SMZType'];

		$smzFeatureHierarchyList = array();
		foreach ($tree as $type => $featTypeSet) {
			$SMZFeatureHierarchy = new SMZFeatureHierarchy();
			$SMZFeatureHierarchy->hierarchy = $typenames[$type];
			$pos = 0;
			$hierarchy = $this->getSMZHierarchy($featTypeSet, $pos, 0, null);
			// Hierarchy == Ghost node, überspringen!
			$SMZFeatureHierarchy->SMZFeatures = $hierarchy[0]->childFeatures;
			$smzFeatureHierarchyList[] = $SMZFeatureHierarchy;
		}
		$SMZ->features = $smzFeatureHierarchyList;
		return $SMZ;
	}

	private function getSMZHierarchy($rs, &$i, $level, $SMZFeature) {
		$ret = array();
		$n = count($rs);
		for (; $i < $n; $i++) {
			if ($rs[$i]['Level'] < $level) {
				return $ret;
			}
			$SMZFeature = $this->createAndFillSMZFeatureObj($rs[$i]);
			if ($SMZFeature->isNode) {
				$refI = $i + 1;
				$children = $this->getSMZHierarchy($rs, $refI, $level + 1, null);
				$i = $refI - 1;
				$SMZFeature->childFeatures = $children;
			}
			$ret[] = $SMZFeature;
		}
		return $ret;
	}

	private function createAndFillSMZFeatureObj($rs) {
		$smzFeature = new SMZFeature();
		$smzFeature->featureName = $rs['featName'];
		$smzFeature->displayType = $rs['DisplayType'];
		$smzFeature->filterType = $rs['FilterType'];
		$smzFeature->usePrefix = $rs['UsePrefix'];
		$smzFeature->useUnit = $rs['UseUnit'];
		$smzFeature->useTitle = $rs['UseTitle'];
		$smzFeature->groupChange = $rs['GroupChange'];
		$smzFeature->selectionHelp = $rs['SelectionHelp'];
		$smzFeature->isNode = $rs['IsNode'];
		$smzFeature->level = $rs['Level'];
		$smzFeature->subGroup = $rs['SubGroup'];
		$smzFeature->groupTitle = $rs['GroupTitle'];
		$smzFeature->isComposedGroup = $rs['IsComposedGroup'];
		return $smzFeature;
	}

	public function getSMZNameByType($elemId, $elemType, $smzType) {
		if (!is_int($elemId) || !is_string($smzType) || ($elemType != 'G' && $elemType != 'P')) {
			//throw new SoapFault('Client', 'Incorrect Inputs in getSMZNameByType', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in getSMZNameByType', 'dataConnector.php');
			return NULL;
		}
		
		if ($elemType == 'G') $field = 'groupId';
		else $field = 'productId';
		
		$smzType = $this->db->sql_escape($smzType);
		$query = "SELECT t1.name as smzName FROM featureCompilation t1,FeatureCompValue t2 " .
				"WHERE t1.id=t2.featureCompId AND t1.type=$smzType AND t2.$field=$elemId";
		
		$res = $this->db->sql_query($query);
		$this->checkDBRes($res, __FUNCTION__, __LINE__);
		$row = $this->db->sql_fetch_assoc($res);
		$this->db->sql_free_result($res);
		if (!$row) {
			return NULL;
		}
		$smzName = $row['smzName'];

		return $smzName;
	
	}

	private function oneElementFromSQL($select, $from, $where) {
		$result = $this->db->exec_SELECTquery($select, $from, $where);
		$this->checkDBRes($result, __FUNCTION__, __LINE__);
		$row = $this->db->sql_fetch_row($result);
		$this->db->sql_free_result($result);
		if ($row != null) {
			return $row[0];
		} else {
			return null;
		}
	}

	/**
	 *
	 * @param string $elementType
	 * @param integer $elementId
	 * @param FeatureList $filterFeatures Can be NULL
	 * @param boolean $all 
	 * @return null|\FeatureValue 
	 * 
	 */
	private function getFeaturesValues($elementType, $elementId, FeatureList $filterFeatures, $all = false) {
		$features = null;
		if ($filterFeatures == NULL) {
			return $features;
		}
		if (!$all && empty($filterFeatures->feature)) {
			return $features;
		}
		switch ($elementType) {
			case 'group':
				$table = "GroupValue";
				$col = "GroupId";

				break;
			case 'product':
				$table = "ProductValue";
				$col = "ProductId";
				break;
			case 'document':
				$table = "DocumentValue";
				$col = "DocumentId";
				break;
		}
		
		$restrStatement = '';
		// Must make sure that no SM is returned multiple times!
		// This creates references in the generated XML!
		if ($all)
			$reqFeats = array();
		else if (is_array($filterFeatures->feature))
			$reqFeats = array_unique($filterFeatures->feature);
		else
			$reqFeats = array($filterFeatures->feature);
		
		if ($all) {
			$restrStatement = '';
		} else if (!empty($reqFeats)) {
			$restrStatement = ' AND (';
			foreach ($reqFeats as $featName) {
				$featName = $this->db->sql_escape($featName);
				$restrStatement.=" t1.name=" . $featName . "  OR";
			}
			$restrStatement = substr($restrStatement, 0, -2);
			$restrStatement .=")";
		} else {
			// Should not come here
			return $features;
		}
		
		$sql = "SELECT t1.name,t3.Id,t2.title,t3.contentHtml," .
				"t3.contentNumber,t3.contentPlain,t2.info,t2.unitToken,t2.dimension,t2.prefix " .
				"FROM Feature t1 ,FeatureValue t2," . $table . " t3 " .
				"WHERE t1.id=t2.FeatureId  AND t1.id=t3.FeatureId " .
				"AND t3." . $col . "=" . $elementId . $restrStatement;
		
		$featureVals = array();
		$res = $this->db->sql_query($sql);
		$this->checkDBRes($res, __FUNCTION__, __LINE__, $sql);
		while ($row = $this->db->sql_fetch_assoc($res)) {
			$featureValue = new FeatureValue;
			$id = $row['Id'];
			$featureValue->documentIds = array();
			$featureValue->name = $row['name'];
			$featureValue->title = $row['title'];
			$featureValue->contentHtml = $row['contentHtml'];
			$featureValue->contentNumber = $row['contentNumber'];
			$featureValue->contentPlain = $row['contentPlain'];
			$featureValue->info = $row['info'];
			$featureValue->unitToken = $row['unitToken'];
			$featureValue->dimension = $row['dimension'];
			$featureValue->prefix = $row['prefix'];
			
			$rs = $this->db->exec_SELECTquery("DocumentId", "DocumentLink", $table . "Id=" . $id);
			$this->checkDBRes($rs, __FUNCTION__, __LINE__);
			while ($rowd = $this->db->sql_fetch_assoc($rs)) {
				$featureValue->documentIds[] = $rowd['DocumentId'];
			}
			$this->db->sql_free_result($rs);
			$featureVals[$row['name']] = $featureValue;
		}
		
		$this->db->sql_free_result($res);

		// Ensure ordering
		if (!empty($reqFeats)) {
			$ret = array();
			//$handled = array();
			foreach ($reqFeats as $featName) {
				//if (array_search($featName, $handled) !== false) continue;
				//$handled[] = $featName;
				if (array_key_exists($featName, $featureVals)) {
					$ret[] = $featureVals[$featName];
				}
			}
			$featureVals = $ret;
		} else {
			$featureVals = array_values($featureVals);
		}
		
		return $featureVals;
	}

	public function getParents($elementType, $elementId) { 
		switch ($elementType) {
			case 'group':
				$col = "GroupId";
				break;
			case 'product':
				$col = "ProductId";
				break;
			case 'document':
				$col = "DocumentId";
				break;
		}
		if (!is_int($elementId)) {
			//throw new SoapFault('Client', 'Incorrect Inputs in get' . ucfirst($elementType) . 'Parent', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Incorrect Inputs in get' . ucfirst($elementType) . 'Parent', 'dataConnector.php');
		} else {
			$sql = "SELECT t1.GroupId FROM Menu t1,Menu t2 where t2." . $col . "=" . $elementId . " AND t2.parentId=t1.Id AND t1.GroupId IS NOT NULL";
			$res = $this->db->sql_query($sql);
			$this->checkDBRes($res, __FUNCTION__, __LINE__);
			$Ids = array();
			while ($row = $this->db->sql_fetch_assoc($res)) {
				$Ids[] =  $row['GroupId'];
			}
			$this->db->sql_free_result($res);
		}
		return $Ids;
	}
	
	public function getParentsList($elementType, IDList $elementIds) {
		$eIds = $this->uniqueIDs($elementIds);
		if (is_null($eIds)) {
			return null;
		}
		switch ($elementType) {
		case 'group':
			$col = "GroupId";
			break;
		case 'product':
			$col = "ProductId";
			break;
		case 'document':
			$col = "DocumentId";
			break;
		}
		
		$lstIds = join(',', $eIds->ids);
		$sql = "SELECT t2.$col AS ElementId,t1.GroupId FROM Menu t1,Menu t2 where t2.$col IN ($lstIds) AND t2.parentId=t1.Id AND t1.GroupId IS NOT NULL";
		$res = $this->db->sql_query($sql);
		$this->checkDBRes($res, __FUNCTION__, __LINE__);
		$Ids = array();
		while ($row = $this->db->sql_fetch_assoc($res)) {
			if (!array_key_exists($row['ElementId'], $Ids)) $Ids[$row['ElementId']] = array();
			$Ids[$row['ElementId']][] =  $row['GroupId'];
		}
		$this->db->sql_free_result($res);
		
		return $Ids;
	}
	
	public function getAsimObjectByOid($asimOid, $shopId)
	{	
		$sr = $this->getShopRoot($shopId);
		if (!$sr) {
			return null;
		}
		$lang = $sr->languageId;
		$market = $sr->marketId;
		$aoid = $this->db->sql_escape($asimOid);
		
		$queryTmpl =
			"SELECT '%s' AS Type, t.Id AS Id, t.AsimOid AS AsimOid ".
			"FROM %s t INNER JOIN Menu m ON m.%sId = t.Id ".
			"WHERE t.AsimOid = $aoid AND m.LanguageId = $lang AND m.MarketId = $market";
		
		$g = sprintf($queryTmpl, "g", "`Groups`", "Group");
		$p = sprintf($queryTmpl, "p", "Product", "Product");
		$d = sprintf($queryTmpl, "d", "Document", "Document");
		
		$query = "$g\nUNION\n$p\nUNION\n$d";
		
		$rs = $this->db->sql_query($query);
		$this->checkDBRes($rs, __FUNCTION__, __LINE__, $query);
		$row = $this->db->sql_fetch_assoc($rs);
		$this->db->sql_free_result($rs);
		
		if (!$row) {
			return null;
		}
		
		$asimObject = new AsimObject;
		$asimObject->asimOid = $row['AsimOid'];
		$asimObject->id = $row['Id'];
		switch ($row['Type']) {
			case 'g': $asimObject->type = ObjectType::Group; break;
			case 'p': $asimObject->type = ObjectType::Product; break;
			case 'd': $asimObject->type = ObjectType::Document; break;
		}
		
		return $asimObject;
	}

	public function getDBInfo() {
		$dbInfo = new DBInfo();
		$dbInfo->dbName = tx_ms3commerce_db_factory::getDatabaseName($this->useStage);
		if ($this->useStage) {
			$dbInfo->dbType = DBType::STAGING;
			if (MS3COMMERCE_STAGETYPE == 'DATABASES') {
				$dbInfo->dbAlias = MS3COMMERCE_STAGE_DB;
			} else {
				$dbInfo->dbAlias = MS3COMMERCE_STAGE_SUFFIX;
			}
		} else {
			$dbInfo->dbType = DBType::PRODUCTION;
			if (MS3COMMERCE_STAGETYPE == 'DATABASES') {
				$dbInfo->dbAlias = MS3COMMERCE_PRODUCTION_DB;
			} else {
				$dbInfo->dbAlias = MS3COMMERCE_PRODUCTION_SUFFIX;
			}
		}
		$dbInfo->serviceVersion = SERVICE_VERSION_STRING;
		return $dbInfo;
	}
	
	/**
	 * @return Group
	 */
	private function loadGroup($groupId)
	{
		$groupId = intval($groupId);
		$res = $this->db->exec_SELECTquery("auxiliaryName,name,asimOid", "`Groups`", "id=" . $groupId);
		$this->checkDBRes($res, __FUNCTION__, __LINE__);
		$row = $this->db->sql_fetch_assoc($res);
		$this->db->sql_free_result($res);
		
		if (!$row) return null;
		$group = new Group();
		$group->groupId = $groupId;
		$group->asimOid = $row['asimOid'];
		$group->auxName = $row['auxiliaryName'];
		$group->name = $row['name'];
		return $group;
	}
	
	/**
	 * @return Product
	 */
	private function loadProduct($productId)
	{
		$productId = intval($productId);
		$res = $this->db->exec_SELECTquery("auxiliaryName,Name,asimOid", "Product", "id=" . $productId);
		$this->checkDBRes($res, __FUNCTION__, __LINE__);
		$row = $this->db->sql_fetch_assoc($res);
		$this->db->sql_free_result($res);
		
		if (!$row) return NULL;
		$product = new Product();
		$product->productId = $productId;
		$product->asimOid = $row['asimOid'];
		$product->auxName = $row['auxiliaryName'];
		$product->name = $row['Name'];
		return $product;
	}
	
	/**
	 * @return Document 
	 */
	private function loadDocument($docId)
	{
		$docId = intval($docId);
		$res = $this->db->exec_SELECTquery("auxiliaryName,Name,asimOid,filePath", "Document", "id=" . $docId);
		$this->checkDBRes($res, __FUNCTION__, __LINE__);
		$row = $this->db->sql_fetch_assoc($res);
		$this->db->sql_free_result($res);
		
		if (!$row) return NULL;
		$document = new Document();
		$document->documentId = $docId;
		$document->asimOid = $row['asimOid'];
		$document->auxName = $row['auxiliaryName'];
		$document->name = $row['Name'];
		$document->filePath = $row['filePath'];
		return $document;
	}
	
	private function logError($err) {
		$logPath = __DIR__ . '/../log/log.txt';
		$f = fopen($logPath, 'at');
		$str = date('Y-m-d H:i:s').' '.$err;
		fputs($f, $str."\n");
		fclose($f);
	}
	
	private function checkDBRes($res, $func = '', $ln = 0, $add = null) {
		if (!$res) {
			if ($ln > 0) {
				$func.=', '.$ln;
			}
			$this->logError($func.': '.$this->db->sql_error());
			if ($add) {
				$this->logError('Details:'.$add);
			}
			//throw new SoapFault('Server', 'Internal server error', 'dataConnector.php', '');
			$this->errorHandler->sendError('Client', 'Internal server error', 'dataConnector.php');
		}
	}
	
	private function uniqueIDs(IDList $idlist = null) {
		if (is_null($idlist) || empty($idlist->ids)) {
			return null;
		}
		$handled = array();
		$newList = new IDList();
		if (is_array($idlist->ids)) {
			foreach ($idlist->ids as $id) {
				if (array_key_exists($id, $handled)) {
					continue;
				}
				$handled[$id] = true;	
				$newList->ids[] = $id;
			}
		} else {
			$newList->ids[] = $idlist->ids;
		}
		return $newList;
	}
	
	private function getShopIdFromElementId($elemId) {
		$elemId = intval($elemId);
		$sql = "SELECT ShopId FROM ShopInfo WHERE StartId <= $elemId AND $elemId < EndId";
		$rs = $this->db->sql_query($sql);
		$this->checkDBRes($rs, __FUNCTION__, __LINE__);
		$row = $this->db->sql_fetch_row($rs);
		$this->db->sql_free_result($rs);
		if ($row) {
			return $row[0];
		}
		return null;
	}
	
	private function getIDRangeForShopId($shopId) {
		$shopId = intval($shopId);
		$sql = "SELECT StartId,EndId FROM ShopInfo WHERE ShopId = $shopId";
		$rs = $this->db->sql_query($sql);
		$this->checkDBRes($rs, __FUNCTION__, __LINE__);
		$row = $this->db->sql_fetch_row($rs);
		$this->db->sql_free_result($rs);
		if ($row) {
			return array('start' => $row[0], 'end' => $row[1]);
		}
		return null;
	}
	
	private function getIDRangeConstraint($idRange, $prefix = '') {
		if (!is_array($idRange)) {
			return " AND (0=1) ";
		}
		
		if (!empty($prefix)) {
			$prefix = $prefix.'.';
		}
		
		$constraint = " AND ({$prefix}Id BETWEEN {$idRange['start']} AND {$idRange['end']}-1) ";
		return $constraint;
	}
}

interface dataConnectorErrorHandler {
	public function sendError($code, $string, $actor);
}

?>
