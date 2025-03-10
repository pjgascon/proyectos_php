












<style type="text/css">
		<!--
			body {
				margin: 0px; /* para texto general */
				font-family: Verdana, Arial, Helvetica, sans-serif;
				font-size: 10px;
				color: Black;
				font-weight: normal;
				text-align: left;
				overflow: auto;
			}
			
			#contenedor{
				width: 100%;
				height: 100%;
				padding-right: 1px;	
                margin: auto;
            
			}
            
			#contenido {
				text-align: left;
				margin: 0px 10px 10px 0px;
            
			}
			
			#registro1 b{
				font-family: Arial, Helvetica, sans-serif;
				font-size: 11px;
				font-weight: bold;
				color: #C0C0C0;
				margin: 0px;
				padding: 10px 0px 0px 0px;
				height: 17px;
			}
            			
			#copyright {
			
				font-family: Arial, Helvetica, sans-serif;
				font-size: 11px;
				color: black;
				width: 350px;
				margin: auto;
                padding: 5px 5px 0px 10px;	
				
			}

			#ventanaderegistro h5 {
				font-family: Arial, Helvetica, sans-serif;
				font-size: 11px;
				color: #FF6600;
				padding: 0px;
				margin: 10px 20px 0px 40px;
				text-align: center;
			}
            
			#ventanaderegistro h6 {
				font-family: Arial, Helvetica, sans-serif;
				font-size: 14px;
				color:#666;
				display: inline;
                margin-top: 5px;
                text-align: center;
			}
            
			#ventanaderegistro table{
				font-family: Arial, Helvetica, sans-serif;
				font-size: 11px;	
				font-weight: bold;
				color: black;
			}

			#ventanaderegistro {
				border: 1px solid #B1A9B8;
				width: 350px;
				margin: 0px 0px 0px 0px;
    			padding: 10px 0px 0px 0px;
    
			}
			
			#ventanaderegistroinput{	
				font-family: Arial, Helvetica, sans-serif;
				font-size: 11px;
				font-weight: normal;
				color: black;
				border: 1px solid #FF6600;
				height: 15px;	
				background-color: #F3F3F3;
				padding:  0px;
				margin: 0px;
				text-align: left;
			}
			
			.imagenExpandida{
				width: 350px !important;
				height: 251px !important;
			}
			
			.imagenExpandidaSinMsg{
				width: 343px !important;
				height: 241px !important;
			}
		-->  
</style>
<script type="text/javascript" src="/PortalCliente/framework/skins/skinOrange/js/jquery-1.6.2.min.js"></script>


<head>
	<title>Portal del cliente :::::::::::::::: </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   
        <script src="https://herramientapuntoventa.cliente.orange.es:443/PortalCliente/portalCliente/CSRFTokenInjection"></script>
	<script type="text/javascript" language="javascript">
    	function setFocus()
        {
        	if(document.getElementById("ventanaderegistroinput")!=null) 
            	document.getElementById("ventanaderegistroinput").focus();
        }
    </script>
        
</head>





<script type="text/javascript" language="javascript">
function hasClass(ele, cls) {
	if(null!=ele.classList){
		return ele.classList.contains(cls);
	}
	else{
    	return $(ele).attr('class').indexOf(cls) > -1;
	}
}
function addClass(ele, cls) {
    if (ele.classList) {
        ele.classList.add(cls);
    } else if (!hasClass(ele, cls)) {
        $(ele).addClass(cls);
    }
}
function removeClass(ele, cls) {
    if (ele.classList) {
        ele.classList.remove(cls);
    } else if (hasClass(ele, cls)) {
        $(ele).removeClass(cls);
    }
}
function validalogin1() {
	document.getElementById("msgError").style.display="none";
	//Redimensionamos la imagen si estaba expandida
	var imgPrinc = document.getElementById("imgPrincipal");
	if(null!=imgPrinc){
		if(hasClass(imgPrinc, "imagenExpandida")){
			removeClass(imgPrinc, "imagenExpandidaSinMsg");
			imgPrinc.className+=" imagenExpandidaSinMsg";
			removeClass(imgPrinc, "imagenExpandida");
		}
	}
    respuesta=usuario=password=false;		 
	if (!(document.formlog.j_username.value=="")){ 
		
		usuario=true;
	}
	if (!(document.formlog.jpassword.value=="")) {
		
		password=true;		 
	}
	if (!usuario && !password) {
		
		alert('Por favor escriba un "Usuario" y una "Contrase\xF1a".');
	}  
	else {
		if (!usuario) 
			alert('Por favor escriba un "Usuario".');
		if (!password) 
			alert('Por favor escriba una "Contrase\xF1a".');		 
	}
	if (usuario && password) 
		respuesta=true;
	
	return respuesta;
}

function validaCaptcha() {
    var captcha=false;
    if (null!=document.formlog.captchaInput && "undefined"!=document.formlog.captchaInput)
    {
		if (document.formlog.captchaInput.value!="") 
		{		
			captcha=true;		 
		}
		else
		{
			captcha=false;
			alert('Por favor rellena el captcha.');
		}	
    }
    else
    {
    	captcha=true;
    }
	         
	return captcha;
}
</script>



<!--<body class="bea-portal-body" onload="initSkin(); document.formlog.submit();">-->
<body class="bea-portal-body" onload="setFocus()">

<form action="/PortalCliente/appmanager/PortalCliente/portalCliente?OWASP_CSRFTOKEN=NXGQ-8242-3278-S01R-0ATI-4OYG-WL8J-B1GR&" method="post" name="formlog" id="formlog" autocomplete="off">
	<table width="804" height="600" align="center" cellspacing="0">
	<tr valign="top">
  		<td height="150" align="center"><img src="/PortalCliente/images/arriba_login.gif" width="900"/></td>
	</tr>

	     
				 
    <tr>
    	<td align="center" valign="top">
			<table width="650" border="0" cellspacing="0" cellpadding="0">
            <tr>
            	<td align="center" width="354">
                <div id="ventanaderegistro">					  	
                <h6>Bienvenido</h6>
                                                    
                <h5>Para entrar al portal debes introducir tu nombre de usuario y contrase&ntilde;a inicial de acceso.</h5><br>
                <table width="80%" border="0" align="center" cellspacing="8">
               	<tr>
                	<td>Usuario</td>
                    <td align="right">
                    	<input type="text" name="j_username" style="width:150px" id="ventanaderegistroinput" value="" autocomplete="off">
                    </td>
                </tr>
                <tr>
                	<td>Contrase&ntilde;a</td>
                    <td align="right">
                    	<input type="password" name="jpassword" style="width:150px" id="ventanaderegistroinput" value="" autocomplete="off">
                    </td>
                </tr>
                <tr>
                <td colspan="2" align="center" style="color:red;"><span id="msgError"></span></td>
                	<!-- <td colspan="2" align="center" style="color:red;"><span id="msgError"></span></td> -->
                </tr>
                <tr>
                    
                </tr>
                <tr>
                	<td>&nbsp;</td>
                   	<td align="right">
                    	<input type="submit" onClick= "if (!validalogin1()) { return false;} else { if (!validaCaptcha()){ return false;} else {  this.disabled=true; submit(); }}" class="boton" value="Entrar" style='font-family: verdana, arial,sans-serif;font-size : 11px; color : #000000;border: 1px solid #C0C0C0; background-color:#E9E9E9;'>
                   	</td>
               	</tr>
               	</table>
                </div>
              	</td>
              
              	<td width="290" valign="bottom">
              		<img src="/PortalCliente/images/imagen_cliente.jpg" align="right" style="width: 290px; height: 193px" id="imgPrincipal" />
              	</td>
            </tr>
     		
     		<tr>
             	<td colspan="2" valign="top" align="center"> 
              	<div id="copyright">	
            		<p>&copy; Orange Espagne S.A.U. Todos los derechos reservados</p>
            	</div>
            	</td>
            </tr>
          	</table>
		</td>
	</tr>     
	          
                                                                   
	</table>

</form> 
</body>



