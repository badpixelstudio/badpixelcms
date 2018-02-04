<?php
require_once(sitepath . "include/core/core.class.php");
require_once(sitepath . "include/catpages/catpages.config.php");
require_once(sitepath . "include/catpages/pages.class.php");
require_once(sitepath . "include/extras/images.class.php");
require_once(sitepath . "include/extras/attachments.class.php");
require_once(sitepath . "include/extras/links.class.php");
require_once(sitepath . "include/extras/videos.class.php");
require_once(sitepath . "include/extras/attributes.class.php");
require_once(sitepath . "include/extras/comments.class.php");
require_once(sitepath . "lib/images/thumbs.php");	

class MasterCats extends Core{
	var $title = 'Contenidos';
	var $module = 'catpages';
	var $class= 'catpages';
	var $TreeCats = array();
	var $elementospadre = array();
	var $table = 'catpages';
	var $typemodule='contents';
	var $InstallAdminMenu=array(array('Block' => 'contents', 'Icon' => 'fa-archive'));
	var $FieldsOfImages=array("Image"=>"PagesCatImageOptions");
	var $tables_required=array('catpages', 'catpages_translations', 'catpages_pages', 'catpages_pages_images', 'catpages_pages_attachments', 'catpages_pages_links', 'catpages_pages_videos', 'catpages_pages_comments', 'catpages_pages_related', 'catpages_pages_translations');
	var $permalink_action="action=list";
	var $version="4.0.0.1";
	
	function __construct($values) {
		parent::__construct($values);  
		$title=$this->GetModuleName($this->class);
		if ($title!=$this->class) { $this->title=$title; }
		$this->conf = new ConfigCatPages($this->businessID);
		$this->BreadCrumb[$this->title] = $this->module;
	}
	
	function CatsCreateThreadsDivs($padre, $tabulado, $level) {
		//Usado en la accián "listcats"
		$query_getcats = "SELECT * FROM " . $this->table . " WHERE IDFather = " . $padre;
		if (siteMulti) { $query_getcats.=" AND (IDBusiness= " . $this->businessID . " OR (IDBusiness=0 AND MultiBusiness=1)) "; }
		$query_getcats.=" ORDER BY ID";
		$System_TotalCatsCount=parent::$db->GetDataListFromSQL($query_getcats, $ListaCats);
		if ($System_TotalCatsCount>0) {
			//Buscamos si el padre está en la matriz $elementospadre;
			foreach($ListaCats as $elemento) { 
		   		if ($this->conf->CheckRol('CatLevelAdmin',$level,$elemento['CatLevelAdmin'])) {
					$this->elementospadre[]=$elemento['ID'];
					//print_r($this->elementospadre);
					echo "<div>";
					if($tabulado>0){ $estilo='indent'; } else{ $estilo=''; }
					echo "<div class='item".$estilo."' style='margin-left:".$tabulado."px;' >";
					echo "<span><a href='" . $this->module . "/pages_list/id/" . $elemento['ID'] ."'title='Ver Páginas'>". $elemento['Title'] . "</a></span>";
					echo "<div style='float:right; text-align:right;'>";
					if ($this->conf->CheckRol('PageLevelAdmin',$level,$elemento['PageLevelAdmin'])){
						echo "<a href='" . $this->module . "/pages_list/id/" . $elemento['ID'] ."' class='btn i_book_large' title='Ver Páginas'></a>";  
					} 	    
					if (($this->businessID==$elemento['IDBusiness']) and ($this->conf->CheckRol('CatLevelCreateSub',$level,$elemento['CatLevelCreateSub']))){
					
						echo "<a href='" . $this->module . "/edit/id/" . $elemento['ID'] ."' class='btn i_create_write' title='Editar'></a>";
						echo "<a href='" . $this->module . "/new/idparent/" . $elemento['ID'] ."' class='btn i_plus' title='Crear Subcategoría' ></a>";	
						echo "&nbsp; &nbsp;";
						echo "<a href='" . $this->module . "/delete/id/" . $elemento['ID'] ."' class='btn i_cross cats' rel='". $elemento['Title'] . "' title='Eliminar'></a>";
								
					}
					echo "</div>";
					echo "</div>";
					$this->CatsCreateThreadsDivs($elemento['ID'],($tabulado+20),$level);
					echo "</div>";
				} //Fin control nivel
		   } //Fin Foreach categorias
	   }
	}
	
	function PopulateCatsCombo($padre, $level) {
		//Usado en la accián "listcats"
		$query_getcats = "SELECT * FROM " . $this->table . " WHERE IDFather = " . $padre;
		if (siteMulti) { $query_getcats.=" AND (IDBusiness= " . $this->businessID . " OR (IDBusiness=0 AND MultiBusiness=1)) "; }
		$query_getcats.=" ORDER BY ID";
		$System_TotalCatsCount=parent::$db->GetDataListFromSQL($query_getcats, $ListaCats);
		if ($System_TotalCatsCount>0) {
			//Buscamos si el padre está en la matriz $elementospadre;
			foreach($ListaCats as $elemento) { 
		   		if ($this->conf->CheckRol('CatLevelAdmin',$this->userLevel,$elemento['CatLevelAdmin'])) {
					$this->elementospadre[]=$elemento['ID'];
					//print_r($this->elementospadre);
					echo "<option value='" . $elemento['ID'] . "'>" . stripslashes($elemento['Title']) . "</option>";
					$this->PopulateCatsCombo($elemento['ID'],$level);
				} //Fin control nivel
		   } //Fin Foreach categorias
	   }
	}

	//generar el submenu de una categoria
	static function GenerateMenu($padre) {
	   $query_getpages = "SELECT * FROM " . $this->table . "_pages WHERE IDFather = " . $padre;
	   if (siteMulti) { $query_getpages.=" WHERE IDBusiness= " . $this->businessID; }
	   $query_getpages.=" ORDER BY Orden";
	   $System_TotalPagesCount=parent::$db->GetDataListFromSQL($query_getpages, $ListaPages);
	   if ($System_TotalPagesCount>0) {
		   $cont=1;
		   $class='';
		   foreach($ListaPages as $elemento) { 
		   	   if($cont==1){ $class="first"; }
			   if($cont==$System_TotalPagesCount){ $class="last"; }
			   echo "<li>";
			   echo "<a href='". self::GetPagePermanentLink($elemento['ID'],'./') ."' class=".$class.">" . $elemento['Title'] . "</a>";
			   echo "</li>";
			   $cont++;
		   } 
	   }
	}

	function GetTreeItems(&$level,$padre=0) {
		if ($level=="") { $level=$this->Items; }
		$query_getcats = "SELECT ID, Title, Orden FROM " . $this->table . " WHERE IDFather = " . $padre;
		if (siteMulti) { $query_getcats.=" AND (IDBusiness= " . $this->businessID . " OR (IDBusiness=0 AND MultiBusiness=1)) "; }
		$query_getcats.=" ORDER BY Orden, ID";
		$TotalListado=self::$db->GetDataListFromSQL($query_getcats, $Listado);
		if ($TotalListado>0) {
			foreach($Listado as $item) {
				$item['Items']=array();
				$this->GetTreeItems($item['Items'],$item['ID']);
				$level[]=$item;
				
			}
		} 
	}

	function GetTreeBreadCrumb($father,$with_permalinks=false) {
		if ($father!=0) {
			$tmp=parent::$db->GetDataRecordFromSQL("SELECT ID,Title,IDFather FROM " . $this->table . " WHERE ID=" .$father);
			$prior=$tmp['IDFather'];
			$this->GetTreeBreadCrumb($prior,$with_permalinks);
			$link=$this->module . "/pages_list/idparent/" . $tmp['ID'];
			if($with_permalinks) { $link=$this->GetPermalink($this->table,$tmp['ID']); }
			if ($tmp['ID']!=0) { $this->BreadCrumb[$tmp['Title']] = $link; }
		}
	}

	function ListAdmItems() {
		$this->MaxDepth=$this->conf->Export("CatMaxChildren");
		$this->GetTreeItems($this->Items,0);
		$this->PrepareTableList();
		$this->LoadTemplate('nestable.tpl.php');
	}


	function NewAdmItem() {
		if ($this->idparent!=0) {
			//Obtenemos los datos de configuración de la categoria padre...
			$this->conf->LoadActualconfig($this->table,$this->idparent);   
			parent::$db->LoadFormData($this,$this->idparent);
			$this->CheckLevel($this->Data['CatLevelCreateSub']);			
			//Borramos los valores no concernientes a configuración...
 			$values['Title']='';
			$values['Description']='';
			$values['Image']='';
			$values['IDFather']=$this->idparent;
		} else {
			$values['IDBusiness']=$this->businessID;
			$values['IDFather']=$this->idparent;
		}
		$this->GetTreeBreadCrumb($this->idparent);
		$this->NewItem($values);
		$this->CheckItemBusinessPermission($this->Data);
		$this->CheckLevel($this->Data['CatLevelCreateSub']);
		$this->TotalLanguages=0;	
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}

	function EditAdmItem($id="") {
		$valid=$this->EditItem($id);
		if (! $valid) { 
			$return=siteprotocol . sitedomain . sitePanelFolder ."/home";
			if(isset($_SERVER['HTTP_REFERER'])){ $return=$_SERVER['HTTP_REFERER'];}
			$return.="/error/" . urlencode(base64_encode("El elemento especificado no existe"));
			header("Location: " . $return); exit;
		}
		$this->GetTreeBreadCrumb($this->Data['IDFather']);
		$this->CheckLevel($this->Data['CatLevelAdmin']); 
		$this->CheckItemBusinessPermission($this->Data);
		$this->PrepareLangMenu(true);
		$this->PrepareForm();
		$this->LoadTemplate('edit.tpl.php');
	}


	function PostAdmItem($redirect=true) {
		//Cargamos los datos base al array...
		parent::$db->LoadFormData($this,$_POST['Form_IDFather']);
		//Si es una cat nueva y utilizamos la configuración por defecto, precargamos los datos...
		if (($this->Check('UseConfigDefault')) and ($_POST['System_Action']=="new")){
			//Cargamos la estructura de la tabla y si no se ha enviado datos para el campo, se carga el de Data..
			$sql="SHOW COLUMNS FROM " . $this->table;
			$TotalCampos=parent::$db->GetDataListFromSQL($sql,$Campos);
			if ($TotalCampos>0) {
				foreach($Campos as $campo) {
					$nombre_campo=$campo['Field'];
					if ((isset($this->Data[$nombre_campo])) and (! isset($_POST['Form_' . $nombre_campo])) and ($nombre_campo!="ID")) {
						$_POST['Form_' . $nombre_campo]=$this->Data[$nombre_campo];
					}
				}
			}
		}
		$this->PostItem($redirect);
	}

	function BeforePostItem() {
		$carpeta="";
		$padre=$_POST['Form_IDFather'];
		while ($padre!=0) {
			$cat=parent::$db->GetDataRecordFromSQL("SELECT Title,IDFather FROM " . $this->table . " WHERE ID=" . $padre);
			if ($carpeta!="") { $carpeta="/" . $carpeta; }
			$carpeta=$cat['Title'] . $carpeta;
			$padre=$cat['IDFather'];
			unset($cat);
		}
		$this->permalink_conf="TempPermalinkFolder";
		$this->conf->CreateTempConf("TempPermalinkFolder","STRING",$carpeta);
	}
	
	
	function DeleteItem($id=0) {
		if ($id==0) { $id=$this->id; }
		$Datos=$this->GetDataRecord($this->table,$id);
		$this->CheckLevel($Datos['CatLevelCreateSub']);
		if ($this->DeleteContentTree($id)){
			//Borramos la categoria principal seleccionada
			if ($this->DeleteContentItem($this->id)) { 
				return 1; 
			} else { 
				return 0; 
			}
		} else { 
			return 0; 
		}		
	}
	
	function DeleteContentItem($id) {
		$Categoria=parent::$db->GetDataRecord($this->table,$id);
		DeleteOptionsImagesFolders($this->conf->Export('PagesCatImageOptions'),$Categoria['Image']);
		$ItemsCount=parent::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . "_pages WHERE IDFather = " . $id, $Items);
		if ($ItemsCount>0) {
			foreach ($Items as $elemento) {
				DeleteOptionsImagesFolders($this->conf->Export('PagesCatImageOptions'),$elemento['Image']);
				$SubItemsCount= parent::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . "_images WHERE IDFather = " . $elemento['ID'], $SubItems);
				if ($SubItemsCount>0) {
					foreach($Subitems as $subelemento) {
						DeleteOptionsImagesFolders($elemento['PageFirstImageOptions'],$subelemento['Image']);
					}
				}	
				$SubItemsCount= parent::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . "_attachments WHERE IDFather = " . $elemento['ID'], $SubItems);
				if ($SubItemsCount>0) {
					foreach($Subitems as $subelemento) {
						deletefile(sitepath . '/public/files/' . $subelemento['File']);
					}
				}
				$borrarexecute = parent::$db->Qry("DELETE FROM " . $this->table . "_images WHERE IDFather=" . $elemento['ID']);
				$borrarexecute = parent::$db->Qry("DELETE FROM " . $this->table . "_attachments WHERE IDFather=" . $elemento['ID']);		
				$borrarexecute = parent::$db->Qry("DELETE FROM " . $this->table . "_links WHERE IDFather=" . $elemento['ID']);
				$borrar= "DELETE FROM " . $this->table . "_comments WHERE IDFather=" . $elemento['ID'];
				$borrarexecute = parent::$db->Qry("DELETE FROM " . $this->table . "_comments WHERE IDFather=" . $elemento['ID']);
				$this->DeletePermalink($this->table . '_pages',$elemento['ID']);
				$this->DeleteTranslations($this->table . '_pages',$elemento['ID']);
			}
	   }
	   $borrarexecute = parent::$db->Qry("DELETE FROM " . $this->table . "_pages WHERE IDFather=" . $id);
	   $borrarexecute = parent::$db->Qry("DELETE FROM " . $this->table . " WHERE id=" . $id);
	   $this->DeletePermalink($this->table,$id);
	   $this->DeleteTranslations($this->table,$id);
	   return true;
	}

	function DeleteContentTree($id) {
		$ItemsCount=parent::$db->GetDataListFromSQL("SELECT * FROM " . $this->table . " WHERE IDFather = " . $id, $Items);
		if ($ItemsCount>0) {
			foreach($Items as $item) {
				$this->DeleteContentTree($item['ID']);
				$this->DeleteContentItem($item['ID']);
			}
		}	
		return true;		
	}	
	
	function PrepareTableList() {
		$this->AddMainMenu('Crear',$this->module . '/new');
		$this->AddTableContent('Nombre','data','{{Title}}','',$this->module . '/pages_list/idparent/{{ID}}');
		$in_block=$this->AddTableContent('Operaciones','menu');
		$this->AddTableOperations($in_block);
		$this->AddTableOperations($in_block,'Crear subcategoria',$this->module . '/new/idparent/{{ID}}','{{CheckMaxDepth}}<' . $this->conf->Export('CatMaxChildren'));
		$this->AddTableOperations($in_block,'Editar',$this->module . '/edit/id/{{ID}}');
		$this->AddTableOperations($in_block,'Eliminar',$this->module . '/delete/id/{{ID}}');
	}

	function PrepareForm() {
		$in_block=$this->AddFormBlock('General');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Nombre de la categoría","FieldName":"Form_Title","Value":"' . addcslashes($this->Data['Title'],'\\"') . '","Required": true}');
		if($this->Check('CatUseType')){ $this->AddFormContent($in_block,'{"Type":"combo-json","Text":"Tipo de contenido","FieldName":"Form_CategoryType","Value":"' . $this->Data['CategoryType'] . '", "JsonValues": {"0":"Páginas","1":"Redirección"}}'); }
		if($this->Check('CatEnableDescription')){ $this->AddFormContent($in_block,'{"Type":"html","Text":"Descripción","FieldName":"Form_Description","Value":"' . addcslashes($this->Data['Description'],'\\"') . '"}'); }
		if($this->Check('CatEnableImage')){ $this->AddFormContent($in_block,'{"Type":"upload","Text":"Imagen","FieldName":"Form_Image","Value":"' . $this->Data['Image'] . '","UploadType": "image", "UploadItem":"first", "Extensions": "jpg,jpeg,gif,png", "PreviewFolder": "public/thumbnails", "External":"' . $this->Data['RenameImage'] . '"}'); }
		if($this->Check('CatEnableAuthor')){$this->AddFormContent($in_block,'{"Type":"combo","Text":"Administrador","FieldName":"Form_IDAuthor","Value":"' . $this->Data['IDAuthor'] . '", "ListTable": "users", "ListValue": "ID", "ListOption": "UserName", "ListOrder":"UserName", "NullValue": "0"}'); }
		$in_block=$this->AddFormBlock('Configuración Páginas');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Permitir crear subcategorías a","FieldName":"Form_CatLevelCreateSub","Value":"' . $this->Data['CatLevelCreateSub'] . '", "ListTable": "users_roles", "ListValue": "IDRol", "ListOption": "RolName", "ListOrder":"RolName", "NullValue": "0", "DisableValue": "-1"}');
		$this->AddFormContent($in_block,'{"Type":"combo","Text":"Permitir gestionar categoría a","FieldName":"Form_CatLevelAdmin","Value":"' . $this->Data['CatLevelAdmin'] . '", "ListTable": "users_roles", "ListValue": "IDRol", "ListOption": "RolName", "ListOrder":"RolName", "NullValue": "0", "DisableValue": "-1"}');
		if($this->Check('PageUsePreTitle')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar Antetítulo en páginas","FieldName":"Form_PageUsePreTitle","Value":"' . $this->Data['PageUsePreTitle'] . '"}'); }
		if($this->Check('PageUsePostTitle')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar Subtítulo en páginas","FieldName":"Form_PageUsePostTitle","Value":"' . $this->Data['PageUsePostTitle'] . '"}'); }
		if($this->Check('PageUseSummary')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar Resumen en páginas","FieldName":"Form_PageUseSummary","Value":"' . $this->Data['PageUseSummary'] . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar imagen principal en páginas","FieldName":"Form_PageUseFirstImage","Value":"' . $this->Data['PageUseFirstImage'] . '"}');
		if($this->Check('PageUseDates')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar uso de fechas en páginas","FieldName":"Form_PageUseDates","Value":"' . $this->Data['PageUseDates'] . '"}'); }
		if($this->Check('PageUseAuthorInfo')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar información de autor en páginas","FieldName":"Form_PageUseAuthorInfo","Value":"' . $this->Data['PageUseAuthorInfo'] . '"}'); }
		if($this->Check('PageUseTags')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar etiquetas en páginas","FieldName":"Form_PageUseTags","Value":"' . $this->Data['PageUseTags'] . '"}'); }
		if($this->Check('PageUseActivation')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar uso de activación de páginas","FieldName":"Form_PageUseActivation","Value":"' . $this->Data['PageUseActivation'] . '"}'); }			
		if($this->Check('PageUseSocial')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Permitir autopublicación en Redes Sociales de páginas","FieldName":"Form_PageUseSocial","Value":"' . $this->Data['PageUseSocial'] . '"}'); }
		if($this->Check('PageUseGeolocation')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar geolocalización en páginas","FieldName":"Form_PageUseGeolocation","Value":"' . $this->Data['PageUseGeolocation'] . '"}'); }
		if($this->Check('PageUseImages')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar galería de imágenes en páginas","FieldName":"Form_PageUseImages","Value":"' . $this->Data['PageUseImages'] . '"}'); }
		if($this->Check('PageUseAttachments')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar adjuntos en páginas","FieldName":"Form_PageUseAttachments","Value":"' . $this->Data['PageUseAttachments'] . '"}'); }
		if($this->Check('PageUseLinks')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar enlaces en páginas","FieldName":"Form_PageUseLinks","Value":"' . $this->Data['PageUseLinks'] . '"}'); }
		if($this->Check('PageUseVideos')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar videos en páginas","FieldName":"Form_PageUseVideos","Value":"' . $this->Data['PageUseVideos'] . '"}'); }
		if($this->Check('PageUseComments')){ $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Habilitar comentarios en páginas","FieldName":"Form_PageUseComments","Value":"' . $this->Data['PageUseComments'] . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Opciones de corte de imagen principal","FieldName":"Form_PageFirstImageOptions","Value":"' . $this->Data['PageFirstImageOptions'] . '","Help": "Cada opción entre paréntesis y separados por punto y coma. Para cada opción: (carpeta,ancho,alto,[color de fondo][crop],[posicion marca de agua],[repeticiones marca],[imagen marca],[margen marca])"}');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Opciones de corte de imágenes de la galería","FieldName":"Form_PageImagesOptions","Value":"' . $this->Data['PageImagesOptions'] . '","Help": "Cada opción entre paréntesis y separados por punto y coma. Para cada opción: (carpeta,ancho,alto,[color de fondo][crop],[posicion marca de agua],[repeticiones marca],[imagen marca],[margen marca])"}');		
		$in_block=$this->AddFormBlock('Configuración Redirección');
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Redirigir a la página","FieldName":"Form_AccessURL","Value":"' . addslashes($this->Data['AccessURL']) . '","Help":"Puede establecerse una ruta relativa o absoluta"}');
		$in_block=$this->AddFormBlock('Avanzado');
		if (($this->Check('EnableMultiBusiness')) and ($this->businessID==0) and (siteMulti)) { $this->AddFormContent($in_block,'{"Type":"checkbox","Text":"Compartida para todas las empresas","FieldName":"Form_MultiBusiness","Value":"' . $this->Data['MultiBusiness'] . '"}'); }
		$this->AddFormContent($in_block,'{"Type":"text","Text":"Enlace permanente","FieldName":"Permalink","Value":"' . $this->Data['Permalink'] . '", "Help": "En blanco genera la dirección URL de forma automática"}');
		$this->AddFormHiddenContent("System_Action",$this->Data['Action']);
		$this->AddFormHiddenContent("System_ID",$this->Data['ID']);
		$this->AddFormHiddenContent("Form_IDFather",$this->Data['IDFather']);
		$this->AddFormHiddenContent("Cnf_Default",intval($this->Check('UseConfigDefault')));
		if(! $this->Check('CatUseType')){ $this->AddFormHiddenContent("Form_CategoryType",$this->Data['CategoryType']); }
	}

	function RunPages($action) {
		$action=str_replace("pages_", "", $this->action);
		$this->Xtra= new Pages($this->_values);
		$this->Xtra->action=$action;
		$this->Xtra->RunAction();
	}

	function RunAction() {
		//Patch to delete extra images
		if ($this->action=="images_delete") {
			$this->xtraimages_options="PageImagesOptions";
			if (! $this->conf->Check("UseConfigDefault")) { $this->conf->LoadActualconfig($this->table,$this->id);}
		}

		if (strpos($this->action, "pages_")!==false) { $this->RunPages($this->action); exit; }
		parent::RunAction();
	}
}
?>