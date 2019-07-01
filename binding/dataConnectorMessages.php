<?php
class getShopRoot {
	/**
	 * @access public
	 * @var integer
	 **/
	var $shopId;

}

class getShopRootResponse {
	/**
	 * @access public
	 * @var ShopRoot
	 **/
	var $shopRoot;

}

class getShopRootForLangAndMarket {
	/**
	 * @access public
	 * @var integer
	 **/
	var $languageId;

	/**
	 * @access public
	 * @var integer
	 **/
	var $marketId;

}

class getShopRootForLangAndMarketResponse {
	/**
	 * @access public
	 * @var ShopRoot
	 **/
	var $shopRoot;

}

class getShopRootList {
}

class getShopRootListResponse {
	/**
	 * @access public
	 * @var ShopRootList
	 **/
	var $shopRootList;

}

class getGroupChildren {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class getGroupChildrenResponse {
	/**
	 * @access public
	 * @var GroupChildren
	 **/
	var $groupChildren;

}

class getGroupContent {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class getGroupContentResponse {
	/**
	 * @access public
	 * @var GroupContent
	 **/
	var $groupContent;

}

class getGroupContentBySMZ {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getGroupContentBySMZResponse {
	/**
	 * @access public
	 * @var GroupContent
	 **/
	var $groupContent;

}

class getGroupContentBySMList {
	/**
	 * @access public
	 * @var FeatureList
	 **/
	var $features;

	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class getGroupContentBySMListResponse {
	/**
	 * @access public
	 * @var GroupContent
	 **/
	var $groupContent;

}

class getGroupTree {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class getGroupTreeResponse {
	/**
	 * @access public
	 * @var GroupTree
	 **/
	var $groupTree;

}

class getGroupFeature {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class getGroupFeatureResponse {
	/**
	 * @access public
	 * @var Group
	 **/
	var $group;

}

class getGroupFeatureBySMZ {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getGroupFeatureBySMZResponse {
	/**
	 * @access public
	 * @var Group
	 **/
	var $group;

}

class getGroupFeatureBySMZType {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzType;

}

class getGroupFeatureBySMZTypeResponse {
	/**
	 * @access public
	 * @var Group
	 **/
	var $group;

}

class getGroupFeatureBySMList {
	/**
	 * @access public
	 * @var FeatureList
	 **/
	var $features;

	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class getGroupFeatureBySMListResponse {
	/**
	 * @access public
	 * @var Group
	 **/
	var $group;

}

class getGroupListFeature {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $groupIds;

}

class getGroupListFeatureResponse {
	/**
	 * @access public
	 * @var GroupList
	 **/
	var $groups;

}

class getGroupListFeatureBySMZ {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $groupIds;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getGroupListFeatureBySMZResponse {
	/**
	 * @access public
	 * @var GroupList
	 **/
	var $groups;

}

class getGroupListFeatureBySMList {
	/**
	 * @access public
	 * @var FeatureList
	 **/
	var $features;

	/**
	 * @access public
	 * @var IDList
	 **/
	var $groupIds;

}

class getGroupListFeatureBySMListResponse {
	/**
	 * @access public
	 * @var GroupList
	 **/
	var $groups;

}

class getProductFeature {
	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

}

class getProductFeatureResponse {
	/**
	 * @access public
	 * @var Product
	 **/
	var $product;

}

class getProductFeatureBySMZ {
	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getProductFeatureBySMZResponse {
	/**
	 * @access public
	 * @var Product
	 **/
	var $product;

}

class getProductFeatureBySMZType {
	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzType;

}

class getProductFeatureBySMZTypeResponse {
	/**
	 * @access public
	 * @var Product
	 **/
	var $product;

}

class getProductFeatureBySMList {
	/**
	 * @access public
	 * @var FeatureList
	 **/
	var $features;

	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

}

class getProductFeatureBySMListResponse {
	/**
	 * @access public
	 * @var Product
	 **/
	var $product;

}

class getProductListFeature {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $productIds;

}

class getProductListFeatureResponse {
	/**
	 * @access public
	 * @var ProductList
	 **/
	var $products;

}

class getProductListFeatureBySMZ {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $productIds;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getProductListFeatureBySMZResponse {
	/**
	 * @access public
	 * @var ProductList
	 **/
	var $products;

}

class getProductListFeatureBySMList {
	/**
	 * @access public
	 * @var FeatureList
	 **/
	var $features;

	/**
	 * @access public
	 * @var IDList
	 **/
	var $productIds;

}

class getProductListFeatureBySMListResponse {
	/**
	 * @access public
	 * @var ProductList
	 **/
	var $products;

}

class getDocumentFeature {
	/**
	 * @access public
	 * @var integer
	 **/
	var $documentId;

}

class getDocumentFeatureResponse {
	/**
	 * @access public
	 * @var Document
	 **/
	var $document;

}

class getDocumentFeatureBySMZ {
	/**
	 * @access public
	 * @var integer
	 **/
	var $documentId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getDocumentFeatureBySMZResponse {
	/**
	 * @access public
	 * @var Document
	 **/
	var $document;

}

class getDocumentFeatureBySMList {
	/**
	 * @access public
	 * @var integer
	 **/
	var $documentId;

	/**
	 * @access public
	 * @var FeatureList
	 **/
	var $features;

}

class getDocumentFeatureBySMListResponse {
	/**
	 * @access public
	 * @var Document
	 **/
	var $document;

}

class getDocumentListFeature {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $documentIds;

}

class getDocumentListFeatureResponse {
	/**
	 * @access public
	 * @var DocumentList
	 **/
	var $documents;

}

class getDocumentListFeatureBySMZ {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $documentIds;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getDocumentListFeatureBySMZResponse {
	/**
	 * @access public
	 * @var DocumentList
	 **/
	var $documents;

}

class getDocumentListFeatureBySMList {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $documentIds;

	/**
	 * @access public
	 * @var FeatureList
	 **/
	var $features;

}

class getDocumentListFeatureBySMListResponse {
	/**
	 * @access public
	 * @var DocumentList
	 **/
	var $documents;

}

class getSMZ {
	/**
	 * @access public
	 * @var integer
	 **/
	var $shopId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getSMZResponse {
	/**
	 * @access public
	 * @var SMZ
	 **/
	var $smz;

}

class getSMZNameByType {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzType;

}

class getSMZNameByTypeResponse {
	/**
	 * @access public
	 * @var string
	 **/
	var $smzName;

}

class getSMZByType {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

	/**
	 * @access public
	 * @var string
	 **/
	var $smzType;

}

class getSMZByTypeResponse {
	/**
	 * @access public
	 * @var SMZ
	 **/
	var $smz;

}

class getGroupRelations {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class getGroupRelationsResponse {
	/**
	 * @access public
	 * @var RelationList
	 **/
	var $relationList;

}

class getGroupRelationsForName {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var string
	 **/
	var $relationName;

}

class getGroupRelationsForNameResponse {
	/**
	 * @access public
	 * @var RelationList
	 **/
	var $relationList;

}

class getProductRelations {
	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

}

class getProductRelationsResponse {
	/**
	 * @access public
	 * @var RelationList
	 **/
	var $relationList;

}

class getProductRelationsForName {
	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

	/**
	 * @access public
	 * @var string
	 **/
	var $relationName;

}

class getProductRelationsForNameResponse {
	/**
	 * @access public
	 * @var RelationList
	 **/
	var $relationList;

}

class getDocumentRelations {
	/**
	 * @access public
	 * @var integer
	 **/
	var $documentId;

}

class getDocumentRelationsResponse {
	/**
	 * @access public
	 * @var RelationList
	 **/
	var $relationList;

}

class getDocumentRelationsForName {
	/**
	 * @access public
	 * @var integer
	 **/
	var $documentId;

	/**
	 * @access public
	 * @var string
	 **/
	var $relationName;

}

class getDocumentRelationsForNameResponse {
	/**
	 * @access public
	 * @var RelationList
	 **/
	var $relationList;

}

class searchProduct {
	/**
	 * @access public
	 * @var SearchCriteriaList
	 **/
	var $searchCriterias;

	/**
	 * @access public
	 * @var integer
	 **/
	var $shopId;

	/**
	 * @access public
	 * @var SearchType
	 **/
	var $type;

}

class searchProductResponse {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $productIds;

}

class getGroupParents {
	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class getGroupParentsResponse {
	/**
	 * @access public
	 * @var ElementParents
	 **/
	var $parents;

}

class getGroupListParents {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $groupIds;

}

class getGroupListParentsResponse {
	/**
	 * @access public
	 * @var ParentList
	 **/
	var $parents;

}

class getProductParents {
	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

}

class getProductParentsResponse {
	/**
	 * @access public
	 * @var ElementParents
	 **/
	var $parents;

}

class getProductListParents {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $productIds;

}

class getProductListParentsResponse {
	/**
	 * @access public
	 * @var ParentList
	 **/
	var $parents;

}

class getDocumentParents {
	/**
	 * @access public
	 * @var integer
	 **/
	var $documentId;

}

class getDocumentParentsResponse {
	/**
	 * @access public
	 * @var ElementParents
	 **/
	var $parents;

}

class getDocumentListParents {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $documentIds;

}

class getDocumentListParentsResponse {
	/**
	 * @access public
	 * @var ParentList
	 **/
	var $parents;

}

class getObjectByAsimOid {
	/**
	 * @access public
	 * @var string
	 **/
	var $asimOid;

	/**
	 * @access public
	 * @var integer
	 **/
	var $shopId;

}

class getObjectByAsimOidResponse {
	/**
	 * @access public
	 * @var AsimObject
	 **/
	var $object;

}

class getDBInfo {
}

class getDBInfoResponse {
	/**
	 * @access public
	 * @var DBInfo
	 **/
	var $dbInfo;

}


?>
