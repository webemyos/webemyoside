<?php
/**
 * JHomFramework 
 * Webemyos.com - la plateforme collaborative pour les startups
 * Oliva Jérôme
 * 02/11/2015
 * */

class Langs extends JHomEntity
{
	function Langs($core)
	{
		//Version
		$this->Version ="2.0.1.0";

		//Nom de la table
		$this->Core=$core;
		$this->TableName="ee_lang";
		$this->Alias = "lg";

	    //proprietes
		$this->Name = new Property("Name","Name",TEXTBOX,true,$this->Alias);
		$this->Code = new Property("Code","Code",TEXTBOX,true,$this->Alias);

		//Creation de l'entit�
		$this->Create();
	}
}

 //Class pour les codes multilangues
class LangsCode extends JHomEntity
{
	function LangsCode($core)
	{
		//Version
		$this->Version ="2.0.0.0";

		//Nom de la table
		$this->Core=$core;
		$this->TableName="ee_lang_code";
		$this->Alias = "lgcd";

	    //proprietes
		$this->Code = new Property("Code","Code",TEXTBOX,false,$this->Alias);

		//Creation de l'entit�
		$this->Create();
	}
}
//Class pour les elements multilangues
class LangsElement extends JHomEntity
{
	protected $Lang;
	protected $Code;

	function LangsElement($core)
	{
		//Version
		$this->Version ="2.0.0.0";

		//Nom de la table
		$this->Core=$core;
		$this->TableName="ee_lang_element";
		$this->Alias = "lgel";

		//Cle primaire
		$this->AddPrimaryKey("LangId");
		$this->AddPrimaryKey("CodeId");

	    //proprietes
		$this->Libelle = new Property("Libelle","Libelle",TEXTBOX,false,$this->Alias);
		$this->LangId = new Property("Lang","LangId",TEXTBOX,false,$this->Alias);
		$this->Lang = new EntityProperty("Langs","LangId");

		$this->CodeId = new Property("Code","CodeId",TEXTBOX,false,$this->Alias);
		$this->Code = new EntityProperty("LangsCode","CodeId");

		//Creation de l'entit�
		$this->Create();
	}

	function GetAllByLang($lang, $limitStart, $limitNmber)
	{
		//Recuperation du code
		$Lang = new Langs($this->Core);
		$Lang->AddArgument(new Argument("Langs","Code",EQUAL,$lang));
		$Langs=$Lang->GetByArg();

		$requete ="Select code.Id as Id,code.code as Code,element.Libelle as Libelle from ee_lang_code as code" .
				  " left join ee_lang_element as element on element.CodeId=code.Id" .
				  " and element.LangId= '".$Langs[0]->IdEntite."' order by Code limit ".($limitStart*$limitNmber).",".$limitNmber."";

		return $this->Core->Db->GetArray($requete);
	}

	//Retourne le nombre total d'element
	function GetCount($Argument = '')
	{
		$requete ="select count(Id) as nbElement from ee_lang_code;";
		$result =  $this->Core->Db->GetLine($requete);
		return $result["nbElement"];
	}
}
?>
