<?php
class IDList {
	/**
	 * @access public
	 * @var integer[]
	 **/
	var $ids;

}

class FeatureList {
	/**
	 * @access public
	 * @var string[]
	 **/
	var $feature;

}

class FeatureValue {
	/**
	 * @access public
	 * @var string
	 **/
	var $contentHtml;

	/**
	 * @access public
	 * @var double
	 **/
	var $contentNumber;

	/**
	 * @access public
	 * @var string
	 **/
	var $contentPlain;

	/**
	 * @access public
	 * @var string
	 **/
	var $dimension;

	/**
	 * @access public
	 * @var IDList
	 **/
	var $documentIds;

	/**
	 * @access public
	 * @var string
	 **/
	var $info;

	/**
	 * @access public
	 * @var string
	 **/
	var $name;

	/**
	 * @access public
	 * @var string
	 **/
	var $prefix;

	/**
	 * @access public
	 * @var string
	 **/
	var $title;

	/**
	 * @access public
	 * @var string
	 **/
	var $unitToken;

}

class Group {
	/**
	 * @access public
	 * @var string
	 **/
	var $asimOid;

	/**
	 * @access public
	 * @var string
	 **/
	var $auxName;

	/**
	 * @access public
	 * @var FeatureValue[]
	 **/
	var $features;

	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var string
	 **/
	var $name;

}

class Product {
	/**
	 * @access public
	 * @var string
	 **/
	var $asimOid;

	/**
	 * @access public
	 * @var string
	 **/
	var $auxName;

	/**
	 * @access public
	 * @var FeatureValue[]
	 **/
	var $features;

	/**
	 * @access public
	 * @var string
	 **/
	var $name;

	/**
	 * @access public
	 * @var integer
	 **/
	var $productId;

}

class Document {
	/**
	 * @access public
	 * @var string
	 **/
	var $asimOid;

	/**
	 * @access public
	 * @var string
	 **/
	var $auxName;

	/**
	 * @access public
	 * @var integer
	 **/
	var $documentId;

	/**
	 * @access public
	 * @var FeatureValue[]
	 **/
	var $features;

	/**
	 * @access public
	 * @var string
	 **/
	var $filePath;

	/**
	 * @access public
	 * @var string
	 **/
	var $name;

}

class ShopRoot {
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

	/**
	 * @access public
	 * @var integer
	 **/
	var $rootGroupId;

	/**
	 * @access public
	 * @var integer
	 **/
	var $shopId;

	/**
	 * @access public
	 * @var string
	 */
	var $exportDate;

	/**
	 * @access public
	 * @var string
	 */
	var $importDate;

	/**
	 * @access public
	 * @var string
	 */
	var $uploadDate;
}

class ShopRootList {
	/**
	 * @access public
	 * @var ShopRoot[]
	 **/
	var $shopRoots;

}

class GroupChildren {
	/**
	 * @access public
	 * @var IDList
	 **/
	var $documentIds;

	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var IDList
	 **/
	var $productIds;

	/**
	 * @access public
	 * @var IDList
	 **/
	var $subGroupIds;

}

class GroupContent {
	/**
	 * @access public
	 * @var Document[]
	 **/
	var $documents;

	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

	/**
	 * @access public
	 * @var Group[]
	 **/
	var $groups;

	/**
	 * @access public
	 * @var Product[]
	 **/
	var $products;

}

class GroupTree {
	/**
	 * @access public
	 * @var GroupTree[]
	 **/
	var $children;

	/**
	 * @access public
	 * @var integer
	 **/
	var $groupId;

}

class GroupList {
	/**
	 * @access public
	 * @var Group[]
	 **/
	var $groups;

}

class ProductList {
	/**
	 * @access public
	 * @var Product[]
	 **/
	var $products;

}

class DocumentList {
	/**
	 * @access public
	 * @var Document[]
	 **/
	var $documents;

}

class SMZHierarchyType {
	const FGF = 'FGF';
	const RGF = 'RGF';
	const SF = 'SF';
}

class SMZFeature {
	/**
	 * @access public
	 * @var SMZFeature[]
	 **/
	var $childFeatures;

	/**
	 * @access public
	 * @var string
	 **/
	var $displayType;

	/**
	 * @access public
	 * @var string
	 **/
	var $featureName;

	/**
	 * @access public
	 * @var string
	 **/
	var $filterType;

	/**
	 * @access public
	 * @var boolean
	 **/
	var $groupChange;

	/**
	 * @access public
	 * @var string
	 **/
	var $groupTitle;

	/**
	 * @access public
	 * @var boolean
	 **/
	var $isComposedGroup;

	/**
	 * @access public
	 * @var boolean
	 **/
	var $isNode;

	/**
	 * @access public
	 * @var integer
	 **/
	var $level;

	/**
	 * @access public
	 * @var boolean
	 **/
	var $selectionHelp;

	/**
	 * @access public
	 * @var string
	 **/
	var $subGroup;

	/**
	 * @access public
	 * @var boolean
	 **/
	var $usePrefix;

	/**
	 * @access public
	 * @var boolean
	 **/
	var $useTitle;

	/**
	 * @access public
	 * @var boolean
	 **/
	var $useUnit;

}

class SMZFeatureHierarchy {
	/**
	 * @access public
	 * @var SMZFeature[]
	 **/
	var $SMZFeatures;

	/**
	 * @access public
	 * @var SMZHierarchyType
	 **/
	var $hierarchy;

}

class SMZ {
	/**
	 * @access public
	 * @var SMZFeatureHierarchy[]
	 **/
	var $features;

	/**
	 * @access public
	 * @var string
	 **/
	var $name;

	/**
	 * @access public
	 * @var string
	 **/
	var $type;

}

class ObjectType {
	const Document = 'Document';
	const Group = 'Group';
	const Product = 'Product';
}

class Relation {
	/**
	 * @access public
	 * @var double
	 **/
	var $amount;

	/**
	 * @access public
	 * @var integer
	 **/
	var $destinationId;

	/**
	 * @access public
	 * @var ObjectType
	 **/
	var $destinationType;

	/**
	 * @access public
	 * @var string
	 **/
	var $name;

	/**
	 * @access public
	 * @var string
	 **/
	var $position;

}

class RelationList {
	/**
	 * @access public
	 * @var Relation[]
	 **/
	var $relation;

}

class CompType {
	const CONTAINS = 'CONTAINS';
	const EQUALS = 'EQUALS';
}

class SearchCriteria {
	/**
	 * @access public
	 * @var CompType
	 **/
	var $compType;

	/**
	 * @access public
	 * @var string
	 **/
	var $feature;

	/**
	 * @access public
	 * @var string
	 **/
	var $value;

}

class SearchCriteriaList {
	/**
	 * @access public
	 * @var SearchCriteria[]
	 **/
	var $searchCriteria;

}

class SearchType {
	const T_AND = 'AND';
	const T_OR = 'OR';
}

class AsimObject {
	/**
	 * @access public
	 * @var string
	 **/
	var $asimOid;

	/**
	 * @access public
	 * @var integer
	 **/
	var $id;

	/**
	 * @access public
	 * @var ObjectType
	 **/
	var $type;

}

class DBType {
	const PRODUCTION = 'PRODUCTION';
	const STAGING = 'STAGING';
}

class DBInfo {
	/**
	 * @access public
	 * @var string
	 **/
	var $dbAlias;

	/**
	 * @access public
	 * @var string
	 **/
	var $dbName;

	/**
	 * @access public
	 * @var DBType
	 **/
	var $dbType;

	/**
	 * @access public
	 * @var string
	 **/
	var $serviceVersion;

}

class ElementParents {
	/**
	 * @access public
	 * @var integer
	 **/
	var $elementId;

	/**
	 * @access public
	 * @var IDList
	 **/
	var $parents;

}

class ParentList {
	/**
	 * @access public
	 * @var ElementParents[]
	 **/
	var $elementParents;

}


?>
