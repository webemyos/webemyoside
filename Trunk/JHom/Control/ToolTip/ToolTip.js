
function ToolTip(control)
{
	this.data = "";
	this.url="";
	this.control = control;


	this.Open=function()
	{
		this.page = "Ajax.php";

		this.core = document.createElement('div');
		this.core.style.position='absolute';
		this.core.style.width= "650px";
		this.core.style.height="300px";
	    this.core.style.overflow="auto";
	    this.core.style.border='1px solid black';
	    this.core.style.left="400px";
   		this.core.style.top="300px";
   		this.core.style.backgroundColor="ebebeb";
   		this.core.innerHTML="<table  style='width:100%;' class='titre'><tr><td  style='text-align:left;' ></td><td style='text-align:right;'><img src='Images/delete.png' alt='' title='Fermer' onclick='CloseTool(this)'></td><td style='width:15px'></td></tr></table>";

		document.body.appendChild(this.core);

		this.core.innerHTML += this.Send();
		this.data = "";
	};


	this.Send=function()
	{
	  var JAjax = new ajax();
	  JAjax.data = this.data;
	  return JAjax.GetRequest(this.page);
	};
;}

CloseTool = function(control)
{
	control.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.removeChild(control.parentNode.parentNode.parentNode.parentNode.parentNode);
};
