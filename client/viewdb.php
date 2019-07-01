<?php
header('Content-Type: text/html; charset=UTF-8');

function getPagePathURL() {
	$pageURL = 'http';
	if (array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] == "on")
		$pageURL .= "s";
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	$pos = strrpos($pageURL, '/');
	$pageURL = substr($pageURL, 0, $pos);
	return $pageURL;
}

$root = getPagePathURL() . '/../..';

include (dirname(__FILE__) . "/../binding/dataConnectorMessages.php");
include (dirname(__FILE__) . "/../binding/dataConnectorTypes.php");

class main {

	function getDocumentFeature($client, $GroupContent) {
		echo '<br><br><div class="element"><div class="title"><b>Documents</b></div><div class="inelement">';
		if (isset($GroupContent->documents) AND $GroupContent->documents != null) {
			$this->getDocumentRelations($client);
			$this->printChildrenFeature($GroupContent->documents, "document");
		} else {
			echo '<div class="noresult">This group does not include documents!</div>';
		}
		echo '</div></div>';
	}

	function printChildrenFeature($children, $type, $linked = false) {
		$typeId = $type . 'Id';

		if (!is_array($children)) {
			$children = array($children);
		}

		echo '<table border="0" bgcolor="white" class="table">';
		echo '<thead class="fixedHeader"><tr class="firstline">';
		echo "<td><b>$typeId</b></td><td><b>Name</b></td><td><b>asimOid</b></td><td><b>Relations</b></td>";
		$FeatureNames = array();

		// Print ALL Features as header
		foreach ($children as $Child) {
			if (isset($Child->features)) {
				$features = is_array($Child->features) ? $Child->features : array($Child->features);
				foreach ($features as $FeatureValue) {
					if (!in_array($FeatureValue->name, $FeatureNames)) {
						echo '<td title="' . $FeatureValue->title . '"><b class="featureTitle">';
						echo $FeatureValue->name;
						echo '<span style="display:none"><ul class="featureInfo">';
						echo "<li>Title: $FeatureValue->title</li>";
						echo "<li>Dimension: $FeatureValue->dimension</li>";
						echo "<li>Unit: $FeatureValue->unitToken</li>";
						echo "<li>Prefix: $FeatureValue->prefix</li>";
						echo "<li>Info: $FeatureValue->info</li>";
						echo '</ul></span>';
						echo '</b></td>';
						$FeatureNames[] = $FeatureValue->name;
					}
				}
			}
		}

		echo '</tr></thead><tbody class="scrollContent">';
		foreach ($children as $Child) {
			$childId = $Child->$typeId;

			echo '<tr class="line"><td>';
			if ($linked) {
				echo "<a href='?$typeId=$childId'>$childId</a>";
			} else {
				echo $childId;
			}
			echo '</td><td>' . $Child->name . '</td><td>' . $Child->asimOid . '</td><td>';
			echo "<input style=\"float:left\" type=\"button\" onClick=\"location.href='?rel_$typeId=$childId&f=6&groupId={$GLOBALS['groupId']}';\" value='Relations'/>";
			echo '</td>';

			$Feature = array();
			if (isset($Child->features)) {
				$fvs = is_array($Child->features) ? $Child->features : array($Child->features);
				foreach ($fvs as $FeatureValue) {
					$Feature[$FeatureValue->name] = $FeatureValue->contentPlain;
				}
				foreach ($FeatureNames as $Name) {
					echo '<td>';
					if (isset($Feature[$Name])) {
						echo $Feature[$Name];
					} else {
						echo '&nbsp;';
					}
					echo '</td>';
				}
				echo '</tr>';
			}
		}

		echo '</tbody></table>';
	}

	function getGroupFeature($client, $GroupContent) {
		echo '<div class="element"><div class="title"><b>Groups</b> - <a href="?f=4&groupId=' . $GLOBALS['groupId'] . '">GroupTree</a></div><div class="inelement">';
		if (isset($GroupContent->groups) AND $GroupContent->groups != null) {
			$this->getGroupRelations($client);
			$this->printChildrenFeature($GroupContent->groups, 'group', true);
		} else {
			echo '<div class="noresult">This group does not include subgroups!</div>';
		}
		echo '</div></div>';
	}

	function getProductFeature($client, $GroupContent) {
		echo '<br><br><div class="element"><div class="title"><b>Products</b></div><div class="inelement">';
		if (isset($GroupContent->products) AND $GroupContent->products != null) {
			$this->getProductRelations($client);
			$this->printChildrenFeature($GroupContent->products, 'product');
		} else {
			echo '<div class="noresult">This group does not include products!</div>';
		}
		echo '</div></div>';
	}

	function getGroupTree($client) {
		if (isset($_REQUEST['f'])) {
			if ($_REQUEST['f'] == 4) {
				$getGroupTree = new getGroupTree();
				$getGroupTree->groupId = $GLOBALS['groupId'];

				$getGroupTreeResponse = $client->getGroupTree($getGroupTree);
				$GroupTree = $getGroupTreeResponse->groupTree;

				echo '<div id="tree" title="GroupTree from groupId ' . $GLOBALS['groupId'] . '" style="display: none;">';
				$this->maketree($GroupTree->children);
				echo '</div>';
				echo '<script>TreeDialog();</script>';
			}
		}
	}

	function maketree($data) {
		if ($data != null) {
			echo '<ul>';
			if (!is_array($data)) {
				$speicher = $data;
				$data = array();
				$data[] = $speicher;
			}
			foreach ($data as $value) {
				$daten = new GroupTree();
				$daten = $value;
				if (!isset($daten->groupId)) {
					$groupId = $daten;
				} else {
					$groupId = $daten->groupId;
				}
				if (isset($groupId)) {
					echo '<li>';
					echo '<a href="?groupId=' . $groupId . '">' . $groupId . '</a>';
					if (isset($daten->children)) {
						$this->maketree($daten->children);
					}
					echo '</li>';
				}
			}
			echo '</ul>';
		}
	}

	/* function getProductParents($client) {
	  $getProductParents = new getProductParents();
	  $getProductParents->productId = $GLOBALS['groupId'];

	  $getProductParentsResponse = $client->getProductParents($getProductParents);
	  $Product = $getProductParentsResponse->parentIds;
	  }

	  function getDocumentParents($client) {
	  $getDocumentParents = new getDocumentParents();
	  $getDocumentParents->documentId = $GLOBALS['groupId'];

	  $getDocumentParentsResponse = $client->getDocumentParents($getDocumentParents);
	  $Document = $getDocumentParentsResponse->parentIds;
	  } */

	function getGroupParents($client) {
		$getGroupParents = new getGroupParents();
		$getGroupParents->groupId = $GLOBALS['groupId'];

		$getGroupParentsResponse = $client->getGroupParents($getGroupParents);
		$parents = @$getGroupParentsResponse->parents->parents->ids;
		if (!is_array($parents)) $parents = array($parents);

		if ($getGroupParentsResponse->parents->elementId != null) {
			?>
			<form style="float:left" name="Input" action="?f=7" method="post">
				<select style="float:left" name="groupId">
					<?php
					foreach ($parents as $p) {
						echo "<option value='$p'>$p</option>";
					}
					?>
				</select>
				<input style ="float:left" type="submit" name="submit" value="<-ToParent" />
			</form>
			<?php
			//onChange="location.href='?relGroupId=<?php echo ; ? >&f=7';"
		}
	}

	function requestheader($client) {
		if (isset($_REQUEST['f']) and isset($_POST['text'])) {
			if ($_REQUEST['f'] == 2) {
				$_SESSION['shop'] = $_POST['text'];
			}
			/*
			  if ($_REQUEST['f'] == 3) {
			  $ProductGroups = $client->findProduct($_POST['text']);
			  if (isset($ProductGroups->groupId)) {
			  if (count($ProductGroups->groupId) == 1) {
			  header("Location: ?groupId=" . $ProductGroups->groupId . "&ProductId=" . $_POST['text']);
			  exit();
			  }
			  }
			  }
			 */
		}
	}

	function requestbody($client) {
		if (isset($_REQUEST['f']) and isset($_POST['text'])) {
			if ($_REQUEST['f'] == 3) {
				$getProductParents = new getProductParents();
				$getProductParents->productId = $_POST['text'];
				$ProductGroups = $client->getProductParents($getProductParents);
				if (isset($ProductGroups->parentIds) && $ProductGroups->parentIds->ids != null) {
					if (is_array($ProductGroups->parentIds->ids) && count($ProductGroups->parentIds->ids) > 1) {
						echo '<div id="product" title="Groups which include the product ' . $_POST['text'] . '" style="display: none;"><b>groupId:</b><ul>';
						foreach ($ProductGroups->parentIds->ids as $value) {
							echo '<li><a href="?groupId=' . $value . '&ProductId=' . $_POST['text'] . '">' . $value . '</a><br /></li>';
						}
						echo '</ul></div>';
						echo '<script>ProductDialog();</script>';
					} else {
						// GOTO PARENT
						$pid = is_array($ProductGroups->parentIds->ids) ? $ProductGroups->parentIds->ids[0] : $ProductGroups->parentIds->ids;
						header("Location: ?groupId=$pid");
						exit();
						echo '<div id="product" title="Groups which include the product ' . $_POST['text'] . '" style="display: none;"><em>GOTO PARENT ' . $pid . '</em></div>';
						echo '<script>ProductDialog();</script>';
					}
				} else {
					echo '<div id="product" title="Groups which include the product ' . $_POST['text'] . '" style="display: none;"><em>Product not found</em></div>';
					echo '<script>ProductDialog();</script>';
				}
			}
		}
	}

	function stagedb() {
		if (isset($_REQUEST['f']) AND isset($_REQUEST['stage'])) {
			if ($_REQUEST['f'] == 5) {
				if (isset($_SESSION['stagedb'])) {
					if ($_REQUEST['stage'] == 'off') {
						$_SESSION['stagedb'] = false;
					}
					if ($_REQUEST['stage'] == 'on') {
						$_SESSION['stagedb'] = true;
					}
				} else {
					$_SESSION['stagedb'] = false;
				}
			}
		}
	}

	/**
	 * @param RelationList $relations 
	 */
	function printRelations($relations, $type, $id) {
		echo '<div id="relations" title="Relations from ' . $type . ' ' . $id . '" style="display: none;">';
		if (isset($relations->relation)) {
			echo '<ul>';
			$rels = is_array($relations->relation) ? $relations->relation : array($relations->relation);
			foreach ($rels as $r) {
				$link = strtolower($r->destinationType) . "Id=$r->destinationId";
				echo "<li>$r->name<ul>";
				echo "<li>Destination: <a href='?$link'>$r->destinationType $r->destinationId</a></li>";
				echo "<li>Position: $r->position</li>";
				echo "<li>Amount: $r->amount</li>";
				echo '</ul></li>';
			}
			echo'</ul>';
		}
		echo '</div>';
		echo '<script>RelationsDialog();</script>';
	}

	function getGroupRelations($client) {
		if (isset($_REQUEST['f']) && isset($_REQUEST['rel_groupId'])) {
			if ($_REQUEST['f'] == 6) {
				$type = "GroupId:";
				$id = $_REQUEST['rel_groupId'];

				$getGroupRelations = new getGroupRelations();
				$getGroupRelations->groupId = $id;

				$getGroupRelationsResponse = $client->getGroupRelations($getGroupRelations);
				$relations = $getGroupRelationsResponse->relationList;

				if ($relations != null) {
					$this->printRelations($relations, $type, $id);
				} else {
					// No Relations ... 	    
				}
			}
		}
	}

	function getProductRelations($client) {
		if (isset($_REQUEST['f']) && isset($_REQUEST['rel_productId'])) {
			if ($_REQUEST['f'] == 6) {
				$type = "ProductId:";
				$id = $_REQUEST['rel_productId'];

				$getProductRelations = new getProductRelations();
				$getProductRelations->productId = $id;

				$getProductRelationsResponse = $client->getProductRelations($getProductRelations);
				$relations = $getProductRelationsResponse->relationList;

				if ($relations != null) {
					$this->printRelations($relations, $type, $id);
				} else {
					// Keine Relations
					//echo '<div class="noresult">This product does not include relations!</div>';
				}
			}
		}
	}

	function getDocumentRelations($client) {
		if (isset($_REQUEST['f']) && isset($_REQUEST['rel_documentId'])) {
			if ($_REQUEST['f'] == 6) {
				$type = "DocumentId:";
				$id = $_REQUEST['rel_documentId'];

				$getDocumentRelations = new getDocumentRelations();
				$getDocumentRelations->documentId = $id;

				$getDocumentRelationsResponse = $client->getDocumentRelations($getDocumentRelations);
				$relations = $getDocumentRelationsResponse->relationList;

				if ($relations != null) {
					$this->printRelations($relations, $type, $id);
				} else {
					// Keine Relations
					//echo '<div class="noresult">This document does not include relations!</div>';
				}
			}
		}
	}

	function searchProduct($client, $searchCriterias) {
		if (!is_array($searchCriterias)) {
			$speicher = $searchCriterias;
			$searchCriterias = array();
			$searchCriterias = $speicher;
		}

		foreach ($searchCriterias->searchCriteria as $criteria) {
			$searchProduct = new searchProduct();
			$searchProduct->searchCriterias = $criteria;

			$searchProductResponse = $client->searchProduct($searchProduct);
			$Search = $searchProductResponse->productIds;
		}
	}

	/* if (isset($_REQUEST['f']) && isset($_REQUEST['relGroupId'])) {
	  if ($_REQUEST['f'] == 6) {
	  $type = "GroupId:";
	  $id = $_REQUEST['relGroupId'];

	  $getGroupRelations = new getGroupRelations();
	  $getGroupRelations->groupId = $_REQUEST['relGroupId'];

	  $getGroupRelationsResponse = $client->getGroupRelations($getGroupRelations);
	  $relations = $getGroupRelationsResponse->relationList;

	  if ($relations != null) {
	  $this->printRelations($relations, $type, $id);
	  } else {
	  // No Relations ...
	  }
	  }
	  } */

	function getSMZ($client) {
		if (isset($_REQUEST['f']) and isset($_REQUEST['smzName'])) {
			if ($_REQUEST['f'] == 8) {
				$getSMZ = new getSMZ();
				$getSMZ->smzName = $_REQUEST['smzName'];

				$getSMZResponse = $client->getSMZ($getSMZ);
				$smzres = $getSMZResponse->smz;

				if ($smzres != null) {
					$this->getSMZTree($smzres);
				} else {
					// Keine SMZ mit dieser ID 
				}
			}
		}
	}

	function getSMZTree($smzres) {
		//print_r($smzres);
		echo '<div id="smz" title="SMZ from ' . $GLOBALS['groupId'] . '" style="display: none;">';
		echo $smzres->name . ' (Type "' . $smzres->type . '")';
		echo '<ul>';
		$hierarchy = (is_array($smzres->features)) ? $smzres->features : array($smzres->features);
		foreach ($hierarchy as $h) {
			echo '<li>' . $h->hierarchy;
			$this->printSMZTree($h->SMZFeatures);
			echo '</li>';
		}
		echo'</ul>';
		echo <<<EOT
		<dl id="smzlegende">
			<dt>D</dt><dd>Displaytype,</dd>
			<dt>F</dt><dd>Filtertype,</dd>
			<dt>P</dt><dd>Use&nbsp;Prefix,</dd>
			<dt>U</dt><dd>Use&nbsp;Unit,</dd>
			<dt>G</dt><dd>Group&nbsp;Change,</dd>
			<dt>S</dt><dd>Selection&nbsp;Help,</dd>
			<dt>C</dt><dd>Composed,</dd>
			<dt>N</dt><dd>Node</dd>
		</dl>
EOT;
		echo '</div>';
		echo '<script>SMZDialog();</script>';
	}

	function printSMZTree($features) {
		echo '<ul>';
		if (!is_array($features))
			$features = array($features);
		foreach ($features as $f) {
			echo '<li>';
			echo $f->featureName;
			echo ' <span class="smzflags">(';
			if ($f->displayType)
				echo " D:$f->displayType";
			if ($f->filterType)
				echo " F:$f->filterType";
			if ($f->usePrefix)
				echo " P";
			if ($f->useUnit)
				echo " U";
			if ($f->groupChange)
				echo " G";
			if ($f->selectionHelp)
				echo " S";
			if ($f->isComposedGroup)
				echo " C";

			if ($f->isNode) {
				echo " N:" . $f->groupTitle . ' / ' . $f->subGroup;
				echo ')</span>';
				if (isset($f->childFeatures)) {
					$this->printSMZTree($f->childFeatures);
				}
			} else {
				echo ' )</span>';
			}
			echo '</li>';
		}
		echo '</ul>';
	}

	/* function getObjectByAsimOid($client){
	  $getObjectByAsimOid = new getObjectByAsimOid();
	  $getObjectByAsimOid->asimOid = $GLOBALS['groupId'];

	  $getObjectByAsimOidResponse = $client->getObjectByAsimOid($getObjectByAsimOid);
	  $getObject = $getObjectByAsimOid->object;

	  return $getObject;
	  } */

	function navigation($client) {
		if (isset($_REQUEST['ProductId'])) {
			$ProductId = $_REQUEST['ProductId'];
		} else {
			$ProductId = -1;
		}
		echo '<div class="nav">';
		$this->getGroupParents($client);
		$this->getSMZ($client);

		$getDBInfoResponse = new getDBInfoResponse();
		$getDBInfoResponse = $client->getDBInfo();
		$DBInfo = $getDBInfoResponse->dbInfo;
		?>
		<script>
			document.onkeydown = function(event) {
				if (event.shiftKey == 1) {
					if (event.keyCode == 83) {
						location.href = '?GroupId=<?php echo $GLOBALS['groupId']; ?>&f=5&stage=<?php
		if ($_SESSION['stagedb']) {
			echo 'off';
		} else {
			echo 'on';
		}
		?>';
					}
				}
			}
		</script>
		<input style="float:left; color: <?php
		if ($_SESSION['stagedb']) {
			echo 'green';
		} else {
			echo 'red';
		}
		?>" type="button" onClick="location.href = '?groupId=<?php echo $GLOBALS['groupId']; ?>&f=5&stage=<?php
			   if ($_SESSION['stagedb']) {
				   echo 'off';
			   } else {
				   echo 'on';
			   }
			   ?>'" value='StageDB'>
		<form style="float:left" name="Input" method="get">
			&nbsp;groupId: 
			<input type="text" size="10" name="groupId" value="<?php echo $GLOBALS['groupId']; ?>" />
			<input type="submit" name="submit" value="Change" />
		</form>
		<form style="float:left" name="Input" action="?f=3&groupId=<?php echo $GLOBALS['groupId']; ?>" method="post">
			&nbsp;ProductId: 
			<input type="text" size="10" name="text" value="<?php echo $ProductId; ?>" />
			<input type="submit" name="submit" value="Change" />
		</form>
		<form style="float:left" name="Input" action="?f=2" method="post">
			&nbsp;ShopId: 
			<input type="text" size="1" name="text" value="<?php echo $_SESSION['shop']; ?>" />
			<input type="submit" name="submit" value="Change" />
		</form>
		<form style="float:left" name="Input" action="?f=8" method="post">
			&nbsp;SmzId:
			<input type ="text" size ="10" name="smzName" value="<?php echo array_key_exists('smzName', $_REQUEST) ? $_REQUEST['smzName'] : ''; ?>" />
			<input type="submit" name="submit" onClick="location.href = '?f=8';" value="getSMZ" />
		</form>  
		<div style="float:left" >&nbsp;StageDB: <?php echo $DBInfo->dbName; ?></div>
		</div>  
		<br><br>
		<?php
	}

	function getGroupId($client) {
		$ShopRoot = new ShopRoot();
		if (!isset($_SESSION['shop'])) {
			$_SESSION['shop'] = 1;
		}
		$getShopRoot = new getShopRoot();
		$getShopRoot->shopId = $_SESSION['shop'];

		$getShopRootResponse = $client->getShopRoot($getShopRoot);
		if (!count(get_object_vars($getShopRootResponse))) {
			// Shop doesn't exist
			$GLOBALS['groupId'] = 0;
			return;
		}
		$ShopRoot = $getShopRootResponse->shopRoot;

		if (isset($_REQUEST['groupId'])) {
			$GLOBALS['groupId'] = $_REQUEST['groupId'];
		} else {
			$GLOBALS['groupId'] = $ShopRoot->rootGroupId;
			//$GLOBALS['groupId'] = 1000002;
		}
	}

	function getGroupChildren($client) {
		$getGroupChildren = new getGroupChildren();
		$getGroupChildren->groupId = $GLOBALS['groupId'];

		$getGroupChildrenResponse = $client->getGroupChildren($getGroupChildren);
		$GroupChildren = $getGroupChildrenResponse->groupChildren;

		return $GroupChildren;
	}

	function getGroupContent($client) {
		$getGroupContent = new getGroupContent();
		$getGroupContent->groupId = $GLOBALS['groupId'];

		$getGroupContentResponse = $client->getGroupContent($getGroupContent);
		if ($getGroupContentResponse && isset($getGroupContentResponse->groupContent)) {
			$GroupContent = $getGroupContentResponse->groupContent;
		} else {
			$GroupContent = null;
		}

		return $GroupContent;
	}

}

session_start();
$main = new main();
$main->stagedb();
if (defined('AC_DATACONNECTOR_DUMMY')) {
	$client = new SoapClient(dirname(__FILE__) . '/../../dataConnectorV2/schema/dataConnector.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
} else {
	$client = new SoapClient($root . '/dataConnectorV2/schema/dataConnector.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
}


/* var_dump($client);
  var_dump($client->__getFunctions());
  try { var_dump($client->GetVersionInfo());}
  catch (Exception $e){echo $e->getMessage();}
 */

if (!isset($_SESSION['stagedb'])) {
	$_SESSION['stagedb'] = false;
}

if (isset($_SESSION['stagedb'])) {
	if ($_SESSION['stagedb']) {
		$client->__setLocation($root . '/dataConnectorV2/server/dataConnector_DLW_handler.php?db=staging');
	} else {
		$client->__setLocation($root . '/dataConnectorV2/server/dataConnector_DLW_handler.php?db=production');
	}
}

$main->requestheader($client);
$main->getGroupId($client);
$GroupContent = $main->getGroupContent($client);
?>
<!DOCTYPE html> 
<html>
    <head>
        <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script src="js/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>

        <link rel="stylesheet" href="css/smoothness/jquery-ui-1.10.3.custom.min.css" type="text/css" media="all" />
        <style type="text/css">
			.ui-widget, body {
				font-family: serif;
				font-size: 16px;
			}
            body {
                margin:0;
            }
            a {
                color: #000;
            }
            a:hover {
                text-decoration: none
            }
            .table {
                box-shadow:0px 0px 8px 2px #444;
                margin: 10px;
            }
            .firstline {
                background:#CCC;
            }
            .line {
                background:#EEE
            }
            .line:hover {
                background:#DDD
            }
            .nav {
                position: fixed;
                width: 100%;
                border-bottom: 1px solid black;
                background:#DDD;
                height: 25px;
                padding: 3px;
                box-shadow:0px 5px 8px 1px #666;
                background: -moz-linear-gradient(top,  #DDD,  #CCC); 
                background: -o-linear-gradient(top, #DDD, #CCC);
                background: linear-gradient(top, #DDD, #CCC);
                background: -webkit-gradient(linear, left top, left bottom, from(#DDD), to(#CCC));
            }
            .content {
                padding: 10px;
                margin-top: 10px;
            }
            .element {
				background:#EEE;
				border-radius: 5px;
				box-shadow:0px 0px 8px 2px #666;
				border: 1px solid #AAA;
            }
            .title {
				background:#CCC;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
				padding:  2px;
				font-size: 17px;
				background: -moz-linear-gradient(top,  #DDD,  #CCC);  
				background: -o-linear-gradient(top, #DDD, #CCC); 
				background: linear-gradient(top, #DDD, #CCC);
				background: -webkit-gradient(linear, left top, left bottom, from(#DDD), to(#CCC));
            }
            .inelement {
				margin: 5px;
				overflow:auto;
				max-height: 600px;
            }
			.smzflags {
				color: #888888;
			}
			#smzlegende {
				font-size: 9pt;
				color: #888888;
				display: inline;
			}
			#smzlegende dt {
				display: inline;
			}
			#smzlegende dd {
				margin-left: 5px;
				margin-right: 10px;
				display: inline;
			}
			ul.featureInfo {
				font-size: 11pt;
				margin: 5px;
				padding: 0 0 0 5px;
			}
        </style>
    </head>
    <body>
        <script>
		function TreeDialog() {
			$("#tree").dialog({width: 350});
			;
		}
		function ProductDialog() {
			$("#product").dialog({width: 350});
			;
		}
		function RelationsDialog() {
			$("#relations").dialog({width: 350});
			;
		}
		function SMZDialog() {
			$("#smz").dialog({width: 350});
			;
		}

		$(function() {
			$(".featureTitle").tooltip({
				items: "b",
				content: function() {
					var e = $(this);
					return e.children("span").html();
				}
			})
		});
        </script>
		<?php
		$main->navigation($client);
		$main->requestbody($client);
		echo '<div class="content">';
		$main->getGroupFeature($client, $GroupContent);
		$main->getProductFeature($client, $GroupContent);
		$main->getDocumentFeature($client, $GroupContent);

		$main->getGroupTree($client);


		echo '</div>';
		?>
    </body>
</html>