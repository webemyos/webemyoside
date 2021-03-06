<?php
/**
 * Classe de base des applications
 * */
class Application
{
	public $Config;
	public $PlugIn;
	public $Url;
	protected $AddWindowsTool;
	public $IdEntity;
	public $Connected = true; 

	/**
	 * Constucteur
	 * */
	public function Application($core, $name)
	{
		//Recuperation du coeur
		$this->Core = $core;
	}

	/**
	 * Démarrage
	 * */
	public function Run($core, $title, $name)
	{
		//Recuperation du template de base
		$textControl = $this->GetAppTemplate();
		
		//Recuperation de l'interface utilisateur
		$this->GetInterface($name);

		//Ajout des informations de base
		$textControl = str_replace("!title", $title, $textControl);
		$textControl = str_replace("!name", $name, $textControl);
		$textControl = str_replace("appRunApp", "appRun$name", $textControl);

		if($this->AddWindowsTool)
		{
			$textControl = str_replace("!windowTool", Eemmys::GetWindowTool(), $textControl);
		}
		else
		{
			$textControl = str_replace("!windowTool", "", $textControl);
		}
		//Ajout du menu
		$Menu = $this->SetMenu($name);
		$textControl = str_replace("!appMenu", $Menu, $textControl);
		$textControl = str_replace("appMenuApp", "appMenu$name", $textControl);

		//Ajout des outils
		$Tool = $this->SetTool($name);
		$textControl = str_replace("!appTool", $Tool, $textControl);

		//Ajout des informations de la partie gauche
		$Left = $this->SetBlock($name, "left");
		$textControl = str_replace("!appLeft", $Left, $textControl);

	    //Ajout des informations de la partie central
		$Center = $this->SetBlock($name, "center");
		$textControl = str_replace("!appCenter", $Center, $textControl);

		//Ajout des informations de la partie right
		$Right = $this->SetBlock($name, "right");
		$textControl = str_replace("!appRight", $Right, $textControl);

		//Ajout de la partie du bas
		$Foot = $this->SetBlock($name, "foot");
		$textControl = str_replace("!appFoot", $Foot, $textControl);

		return $textControl;
	}

	/**
	 * Récupére le template des applications
	 * */
	protected function GetAppTemplate()
	{
		if($this->Connected)
		{
			return JFile::GetFileContent("../JHom/Template/Pages/app.tpl");
		}
		else
		{
			return JFile::GetFileContent("JHom/Template/Pages/app.tpl");
		}
	}

	/**
	 * Recuperation de l'inteface utilisateur
	 * */
	protected function GetInterface($app)
	{
	   	//Lancement depuis le developpeur
	   	if($this->Url)
		{
			$fileName = $this->Url . "/".$app.".xml";
		}
		else if(!$this->Connected)
		{
		 	$fileName = "Apps/$app/".$app.".xml";
		}
		else
		{
	    	$fileName = "../Apps/$app/".$app.".xml";
		}

		if(file_exists($fileName))
		{
			$this->Interface=new JDOMDocument();
			$this->Interface->load($fileName);
		}
		else
		{
			throw new Exception('Fichier interface non trouvé');
		}
	}

	/**
	 * Récupere et crée le menu
	 */
	protected function SetMenu($name)
	{
		$xmlMenu = $this->Interface->GetElementsByTagName("menu");

		if($xmlMenu->item(0) != null)
		{
			$Menu = $xmlMenu->item(0);
			$MenuV = new MenuV("appMenu".$name);

			//Recuperation des item
			$items = $Menu->GetElementsByTagName("item");

			foreach($items as $item)
			{
				$MenuV->AddItem($this->Core->GetCode($item->getAttribute("name")),"", "", "", $item->getAttribute("action"));

				//Recuperation des sous menu
				$subItems = $item->GetElementsByTagName("subitem");

				if(sizeof($subItems) > 0)
				{
					foreach($subItems as $subItem)
					{
						//Recuperation de l'image
						if($subItem->getAttribute("img") != "" && file_exists("../Apps/$name/images/".$subItem->getAttribute("img")))
						{
							$img = "../Apps/$name/images/".$subItem->getAttribute("img");
						}
						else
						{
							$img = $subItem->getAttribute("img");
						}

						//Ajout d'un icone
						if($subItem->getAttribute("icone") != "")
						{
							$iconeName = $subItem->getAttribute("icone");
							$icone = new $iconeName();
						}
						else
						{
							$icone ="";
						}

						$MenuV->AddSubItem($this->Core->GetCode($item->getAttribute("name")), $this->Core->GetCode($subItem->getAttribute("name")),"",$subItem->getAttribute("action"), $img, $icone);
					}
				}
			}
			return $MenuV->Show();
		}

		return "";
	}

	/**
	 * Récupere et crée la barre d'outil
	 */
	protected function SetTool($app)
	{
		$xmlToolBar = $this->Interface->GetElementsByTagName("toolbar");

		if($xmlToolBar->item(0) != null)
		{
			$tools = $xmlToolBar->item(0)->GetElementsByTagName("tool");
			$textControl = "<table><tr>";

			foreach($tools as $tool)
			{
				$img = new Image("../Apps/$app/images/".$tool->getAttribute("img"));
				$img->AddStyle("width","20px");
				$img->Title = $tool->getAttribute("title");
				$img->Alt = $tool->getAttribute("title");
				$img->Id= $tool->getAttribute("action");

				$textControl .= "<td>".$img->Show()."</td>";
			}
			$textControl .= "</tr></table>";

			return $textControl;
		}

		return "";
	}

	/*
	 * Récupère et crée la partie gauche
	 * */
	protected function SetBlock($app, $div)
	{
		$xmlLeft = $this->Interface->GetElementsByTagName($div);
		$textControl = "";

		if($xmlLeft->item(0) != null)
		{
			//Le premier enfant determine si on utilise des onglet
			//Verification
			if($xmlLeft->item(0)->childNodes->item(0)->nodeName != "#text")
			{
				$i = 0;
			}
			else
			{
				$i = 1;
			}

			if($xmlLeft->item(0)->childNodes->item($i)->nodeName == "item")
			{
				//Creation du tabStrip
				$tabStrip = new TabStrip($xmlLeft->item(0)->childNodes->item($i)->getAttribute("TabStripName"), $app);

				//Ajout des onglets
				foreach($xmlLeft->item(0)->childNodes as $node)
				{
					if($node->nodeName != "#text")
					{
						//Recuperation des elements enfants
						$textTab  = "";
						$textTab .= $this->SetControls($node);

						if($node->getAttribute("img"))
						{
							$img = "../Apps/$app/images/".$node->getAttribute("img");
						}
						else
						{
							$img = '';
						}
						
						if($node->getAttribute("class"))
						{
							$class = $node->getAttribute("class");
						}
						else
						{
							$class = '';
						}
						

						$tabStrip->AddTab($this->Core->GetCode($node->getAttribute("text")), new libelle($textTab),"", $img, $class);
					}
				}

				$textControl = $tabStrip->Show();
			}
			else
			{
				foreach($xmlLeft->item(0)->childNodes as $node)
				{
					if($node->nodeName != "#text")
					{
						 $textControl .= $this->SetControl($node);
					}
				}
			}

			return $textControl;
		}

		return "";
	}
	
	/*
	* Ajoute le controle
	*/
	function SetControl($child)
	{
		$textTab = "";
		switch($child->nodeName)
				{
					case "label":
						$textTab .= $this->Core->GetCode($child->nodeValue);
					break;
					case "module":
						$nameBlock  = $child->getAttribute("type");
						$block = new $nameBlock($this->Core);

						//Verification des parametres
						$properties = $child->getElementsByTagName("property");

						if(sizeof($properties) > 0)
						{
							foreach($properties as $propertie)
							{
								$name = $propertie->getAttribute("Name");

								$block->$name->Value = $propertie->getAttribute("Value");
							}
						}

						$textTab .= $block->Show();
					break;
					default :
						$nameControl = $child->nodeName;
						$control = new $nameControl($child->getAttribute("name"));
						$control->Style = $child->getAttribute("style");

						$control->Id = $child->getAttribute("name");

						if($nameControl == 'button')
						{
							$control->Value = $this->Core->GetCode($child->getAttribute("value"));
						}
						else
						{
							$control->Value = $child->getAttribute("value");
						}
						$textTab .= $control->Show();
					break;
				}

		return $textTab;
	}
	/**
	 * Crée le controle
	 */
	function SetControls($node)
	{
		$textTab = "";
		//Ajout des controle ou module
		foreach($node->childNodes as $child)
		{
			if($child->nodeName != "#text")
			{
				$textTab .= $this->SetControl($child);
			}
		}
		return $textTab;
	}

	/**
	 * Crée la partie du bas
	 */
	function SetFoot()
	{
		return "Eemmys";
	}
	
	/*
	* Affiche l'appli en mode deconnecté
	*/
	function Display()
	{
		$this->IncludeEemmys();
		$this->Connected = false;
	}

	/*
	* Inclue les fichiers de base
	*/
	function IncludeEemmys()
	{
		include("Core/Eemmys.php");
	}
}
?>