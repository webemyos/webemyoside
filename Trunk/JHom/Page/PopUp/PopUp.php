<?php
/**
 * JHomFramework 
 * Webemyos.com - la plateforme collaborative pour les startups
 * Oliva Jérôme
 * 02/11/2015
 * */

class PopUp
{
	private $Class;
	private $Methode;
	private $Argument=array();
	private $Action = array();
	private $SourceControl = array();

	//Design
	private $Name="popup";
	private $Title="Title";
	private $Width="400";
	private $Height="400";
	private $Top="20%";
	private $Left="20%";
	private $Opacity="50";
	private $BackGroundColor="White";
	private $Overflow="scroll";
	private $ClassCss ="PopUp";
	private $ShowBack = true;

	private $Url="";

	private $Arguments=array();

	//Constructeur
	function PopUp($class, $methode="", $url="")
	{
		//Version
		$this->Version = "2.0.1.0";

		$this->Class=$class;
		$this->Methode=$methode;
		$this->Url=$url;
	}

	//Enregistrement de l'action à effectuer
	function DoAction()
	{
	$Property = array();

	$reflection = new ReflectionObject($this);
	$Properties=$reflection->getProperties();

	//Ajout des propriétés
	foreach($Properties as $control)
	{
		$name=$control->getName();
	    $Property[$name]=$this->$name;
	}

	//Ajout des arguments
	$this->AddArgument("Class",$this->Class);
	$this->AddArgument("Methode",$this->Methode);
	$this->AddArgument("Argument",$this->Argument);
	$this->AddArgument("Url",$this->Url);

	return "var popUp=new PopUp('".Serialization::EncodeCrypt($Property)."','".Serialization::EncodeCrypt($this->Argument)."','".Serialization::EncodeCrypt($this->Action)."','".Serialization::EncodeCrypt($this->SourceControl)."');popUp.Open();";

	}

	function Close()
	{
		return "ClosePopUp('$this->Name')";
	}


	//Ajoute des arguments pour la methode Serveur
	function AddArgument($key, $value)
	{
		$this->Argument[$key]=$value;
	}

	//Ajout des action
	function AddAction($event, $action)
	{
		$this->Action[$event] = $action;
	}

	function AddSourceControl($id)
	{
		$this->SourceControl[$id] = $id;
	}

	//Confirmation de suppression
	function ConfirmDelete()
	{
		//Formulaire d'envoi
		$Form = new FormBlock("Form",JVar::GetPost("Page")."&idEntity=".JVar::Get("idEntity")  ,POST,PAGE);
		$Form->AddNew(new Libelle("AreYouShure"));

		$btnSend = new Button(SUBMIT);
		$btnSend->Value = "Confirm";
		$btnSend->OnClick = new UserAction("ConfirmDelete");

		$Form->AddNew($btnSend);

		echo $Form->Show();
	}


	//Affichage d'un vid�o
	function ShowVideo()
	{
		echo JVar::GetPost("Video");
	}

	//Asseceur
	public function __get($name)
	{
		return $this->$name;
	}

	public function __set($name,$value)
	{
      $this->$name=$value;
	}
}
?>
