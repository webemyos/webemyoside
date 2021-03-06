<?php
/**
 * JHomFramework 
 * Webemyos.com - la plateforme collaborative pour les startups
 * Oliva Jérôme
 * 02/11/2015
 * */

class AjaxFormBlock extends JHomBlock
{
    /*
     * Propriété
     */
    public $App;
    public $Action;
    private $Controls = array();
    private $Arguments = array();
    
    function AjaxFormBlock($core, $name)
    {
        $this->Core = $core;
        $this->Name = $name;
    }
    
    /**
     * Ajoute un tableau de control
     * @param type $controls
     */
    function AddControls($controls)
    {
       $this->Controls = $controls;
    }
    
    /**
     * Ajoute un arguments
     * @param type $argument
     */
    function AddArgument($key, $value)
    {
       $this->Arguments[$key] = $value;
    }
    
    /**
     * Affiche le formulaire ajax
     */
    function Show()
    {
        $block = new JBlock($this->Core, $this->Name);
        $block->Frame = false;
        $block->Table = true;
        
        //Action Ajax
        $action = new AjaxAction($this->App, $this->Action);
        $action->AddArgument("App", $this->App);
        
         //Ajout des arguments
        foreach($this->Arguments as $argument => $value)
        {
           $action->AddArgument($argument, $value);
        }
        
        $action->ChangedControl = $this->Name;
        
        foreach($this->Controls as $control)
        {
            switch($control["Type"])
            {
                case "EntityListBox" : 
                    $ctr = new EntityListBox($control["Name"], $this->Core);
                    $ctr->Entity = $control["Entity"] ;
                    $ctr->ListBox->Libelle = $control["Libelle"];
                    $ctr->Libelle = $control["Libelle"];
                    $ctr->ListBox->Selected = $control["Value"];
                    
                    if($control["Field"])
                    {
                        $ctr->AddField($control["Field"]);
                    }
                    
                    if(isset($control["Argument"]))
                    {
                       $ctr->AddArgument($control["Argument"]); 
                    }
                    
                    break;
                case "ListBox" :
                    $ctr = new ListBox($control["Name"]);
                    $ctr->Libelle = $control["Libelle"];
                  
                    foreach($control["Value"] as $key => $value)
                    {
                        $ctr->Add($key, $value);
                    }
                    
                    
                    break;
                case "AutoCompleteBox" : 
                    $ctr = new AutoCompleteBox($control["Name"], $this->Core);
                    $ctr->Entity = $control["Entity"] ;
                    $ctr->Methode = $control["Methode"];
                    $ctr->Libelle = $control["Libelle"];
                    
                    
                    break;
                 case "BsTextBox"  :
                         $ctr = new BsTextBox($control["Name"], $this->Core);
                         $ctr->Title = $control["Title"];
                    break;
                 case "BsEmailBox"  :
                         $ctr = new BsEmailBox($control["Name"], $this->Core);
                         $ctr->Title = $control["Title"];
                    break;
                    case "BsPassword"  :
                         $ctr = new BsPassword($control["Name"], $this->Core);
                         $ctr->Title = $control["Title"];
                     break;
                 
                case "CheckBox" :
                    $ctr = new CheckBox($control["Name"]);
                    $ctr->Value = $control["Value"];
                    $ctr->Libelle = $control["Libelle"];
                    $ctr->CssClass = $control["CssClass"];
                    $ctr->Checked = $control["Value"];
                    break;
                case "Button" : 
                    $ctr = new Button(BUTTON);
                    $ctr->Value = $control["Value"];
                    $ctr->Libelle = $control["Libelle"];
                    $ctr->CssClass = $control["CssClass"];
                    
                    $ctr->OnClick = $action;
                    
                    break;
                case "UploadAjaxFile" : 
                    $app = $control["App"];
                    $idEntite = $control["IdEntite"];
                    $callBack = $control["CallBack"];
                    $UploadAction = $control["Action"];
                    
                    $ctr = new UploadAjaxFile($app, $idEntite, $callBack, $UploadAction);
                    
                    break;
                case "Libelle":
                    
                    $ctr = new Libelle($control["Value"]);
                    
                    break;
                default :
                    $type = $control["Type"];
                    
                    $ctr = new $type($control["Name"]);
                    $ctr->Libelle = $control["Libelle"];
                    $ctr->Value = $control["Value"];
                
                    break;
            }
           
            $action->AddControl($ctr->Id);
           
            if($control["Type"] == "Button"  || $control["Type"] == "UploadAjaxFile" )
            {
                $block->AddNew($ctr, 2, ALIGNRIGHT);
            }
            else
            {
                 $block->AddNew($ctr);
            }
        }
        
        return $block->Show();
    }
}