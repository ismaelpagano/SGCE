function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

class Nodo {

	constructor (textNode, li_id, id_compra){
		this.li = document.createElement('li');
		this.p = document.createElement('p');
		this.texto = document.createTextNode(textNode);
		this.p.appendChild(this.texto);
		this.li.appendChild(this.p);
		this.li.id = li_id;
		this.li.classList.add('opcion_desplegable');
		this.li.addEventListener('click', nada);

		if(textNode == "Crear oferta"){
			this.li.addEventListener('click', function() { crear_oferta("verificar", id_compra)});
		} else if (textNode == "Guardar llamado"){
			this.li.addEventListener('click', function() { actualizar_estado_compra_detalle(id_compra, 'guardado')});
		} else if (textNode == "Rechazar llamado"){
			this.li.addEventListener('click', function() { actualizar_estado_compra_detalle(id_compra, 'rechazado')});
		}
	}

};

class Desplegable {

	constructor (){
		this.div_desplegable = document.getElementById('estado_interno');
		this.desplegable = document.createElement('div');
		this.desplegable.id = 'menu_desplegable_opciones';
		this.ul_desplegable = document.createElement('ul');
		this.div_desplegable.appendChild(this.desplegable);
		this.desplegable.appendChild(this.ul_desplegable);
		this.coleccion = new Array();
	}

	agregar_li(li_array){
		li_array.forEach(element => {
			this.ul_desplegable.appendChild(element.li);
			this.coleccion.push(element.li.id);
		});
	}

}

var coleccion_mascaras = new Array();
var bool_desplegable = false;
var desplegable;
var mascara_bool = false;
var estado_compra_actual = '';
var id_compra;
var busqueda_objeto_bool = false;
var busqueda_filtros_bool = false;
var scrollbar;


function enmascarar(objetos){

	objetos.forEach(function(index){
		index = document.getElementById(index);
		var mascara = document.createElement('div');
		var objeto = document.getElementById(index.id);
		mascara.classList.add('mascara');
		objeto.append(mascara);
		coleccion_mascaras.push(mascara);
	});

	for(var mascara of this.coleccion_mascaras){
		mascara.addEventListener('click', desenmascarar);
	};
}

function desenmascarar(){

	for(var mascara of coleccion_mascaras){
		mascara.remove();
	};

	if(bool_desplegable){
		opciones_llamado_desplegable(id_compra);
	}
}

function poner_mascara(){
	var div_pantalla = document.getElementById('cont_monitor');
	var div_mascara = document.createElement('div');
	div_mascara.id = 'div_mascara';
	div_pantalla.append(div_mascara);
}

function quitar_mascara(){
	setTimeout(function() {
		var mascara = document.getElementById('div_mascara');
		var pantalla = document.getElementById('cont_monitor');
		pantalla.removeChild(mascara);
	}, 300);
}

function getCookie(cname){
	let name = cname + "=";
	let decodedCookie = decodeURIComponent(document.cookie);
	let ca = decodedCookie.split(';');
	for(let i = 0; i < ca.length; i++) {
	  let c = ca[i];
	  while (c.charAt(0) == ' ') {
		c = c.substring(1);
	  }
	  if (c.indexOf(name) == 0) {
		return c.substring(name.length, c.length);
	  }
	}
	return "";
}

function validarFormActualizador(){
    var fecha_inicio = document.getElementById('inicio').value;
    var fecha_fin = document.getElementById('fin').value;
    var diferencia = Date.parse(fecha_fin) - Date.parse(fecha_inicio);
    if(diferencia > 864000){
		var http = new XMLHttpRequest();
        var url = 'funcs/actualizar_fecha_amplia.php';
        var params = new FormData(document.getElementById("form-actualizador"));
        http.open('POST', url, true);
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
            }
        }
        http.send(params);
    } else {
        var http = new XMLHttpRequest();
        var url = 'funcs/actualizar_compras.php';
        var params = new FormData(document.getElementById("form-actualizador"));
        http.open('POST', url, true);
        http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
            }
        }
        http.send(params);
    }
}

//MONITOR DE LLAMADOS

function mostrar_llamados(estado, valor, pagina){
	poner_mascara();
	var xmlhttp = new XMLHttpRequest();
	var params = new FormData();
	var url = '';
	if(estado == 'busqueda'){
		if(valor == ''){
			params = new FormData(document.getElementsByTagName('form')[0]);
			params.append('busqueda' , estado);
			params.append('pagina', pagina);
			url += 'busqueda.php';
		}
	} else {
		params.append(estado , valor);
		params.append('pagina', pagina);
		url += 'funcs/llamados.php';
	}
	xmlhttp.open("POST", url, true);
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.open("POST", "funcs/inventario_llamados.php", true);
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById('visor_llamados').innerHTML = this.responseText;
					quitar_mascara();
				}
			}
			var params = new FormData();
			params.append(estado , valor);
			params.append('pagina', pagina);
			xmlhttp.send(params);
		}
	}
	xmlhttp.send(params);
}

function ordenar($bool) {
	var http = new XMLHttpRequest();
	var url = 'funcs/ordenar_bd.php';	
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			if($bool){
				listar_llamados('vigente', 0, 0)
			}
		}
	}
	http.send();
}

//MODIFICADORES DE LLAMADOS

//ESTADO EN ARCE / ESTADO INTERNO 

function actualizarEstado(id, estado) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "funcs/actualizar_estado.php?", true);
	var params = new FormData();
	params.append('id', id);
	params.append('estado', estado);
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById('botones'+ id).innerHTML = this.responseText;
			if(estado == 4){
				document.getElementById(id).classList.remove('guardado');
				document.getElementById(id).classList.remove('ofertado');
				document.getElementById(id).classList.add('rechazado');
			} else if (estado == 2) {
				document.getElementById(id).classList.remove('rechazado');
				document.getElementById(id).classList.remove('ofertado');
				document.getElementById(id).classList.add('guardado');
			} else if (estado == 3){		
				document.getElementById(id).classList.remove('rechazado');
				document.getElementById(id).classList.remove('guardado');
				document.getElementById(id).classList.add('ofertado');
			}
		}
	};
	xmlhttp.send(params);
}

function actualizar_estado_compra_detalle(id_compra, estado){

	var http = new XMLHttpRequest();
	http.open("POST", "funcs/actualizar_estado_compra_detalle.php", true);
	var params = new FormData();
	params.append('id_compra', id_compra);
	params.append('estado', estado);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText);
			boton_estado_header(estado_compra_actual);
			desenmascarar();
			estado_compra_actual = estado;
		}
	};
	http.send(params);
}

function actualizar_estado_compra(id, array_key, estado, bool) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "funcs/actualizar_estado_compra.php?", true);
	var params = new FormData();
	params.append('id', id);
	params.append('array_key', array_key);
	params.append('estado', estado);
	params.append('bool', bool);
	xmlhttp.onreadystatechange = function() {

	};
	xmlhttp.send(params);
}

function listar_llamados(estado, estado_interno, pagina){
	poner_mascara();
	var params = new FormData();
	params.append('estado', estado);
	params.append('estado_interno', estado_interno);
	params.append('pagina', pagina);
	var http = new XMLHttpRequest();
	var url = 'funcs/listar.php';	
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('visor_llamados').innerHTML = this.responseText;
			quitar_mascara();
		}
	}
	http.send(params);
}

function busqueda(){
	var clave = document.getElementById('buscar_llamado').value;
	var params = new FormData();
	params.append('busqueda', clave);
	var http = new XMLHttpRequest();
	var url = 'funcs/busqueda.php';
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			http = new XMLHttpRequest();
			http.open("POST", "funcs/listar.php", true);
			http.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("visor_llamados").innerHTML = this.responseText;
				}
			}
			var params = new FormData();
			params.append('busqueda' , clave);
			http.send(params);
		}
	}
	http.send(params);
}

//SESIÒN DE USUARIO Y SISTEMA

function login(){
	var http = new XMLHttpRequest();
	var url = 'verificacion.php';
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			alert(this.responseText);
		}
	}
	http.send();
}

function cerrar_sesion(){
	var http = new XMLHttpRequest();
	var url = 'cerrar_sesion.php';
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			window.location.assign("");
		}
	}
	http.send();
}

function redirecc(destino){
 
	window.location.assign(destino + '.php');

}

function filtros(tipo){

	var id_nodo = '';
	var valor = '';

	var params = new FormData();

	if(tipo == 'inciso'){
		valor = document.getElementById('org_contr_in').value;
		id_nodo = 'org_contr_ue';
	} else if (tipo == 'tipo'){
		valor = document.getElementById('tipo_contr').value;
		id_nodo = 'subtipo_contr';
	} else if (tipo == 'familia'){
		valor = document.getElementById('familias_catalogo').value;
		id_nodo = 'subfamilias_catalogo';
		filtros('clase');
		filtros('subfamilia');
	} else if (tipo == 'subfamilia'){
		valor = document.getElementById('subfamilias_catalogo').value;
		var familia = document.getElementById('familias_catalogo').value;
		params.append('familia_sub', familia);
		id_nodo = 'clases_catalogo';
		filtros('clase');
	} else if (tipo == 'clase'){
		valor = document.getElementById('clases_catalogo').value;
		var familia = document.getElementById('familias_catalogo').value;
		var subfamilia = document.getElementById('subfamilias_catalogo').value;
		params.append('familia_sub', familia);
		params.append('subfamilia_sub', subfamilia);
		id_nodo = 'subclases_catalogo';
	} else if (tipo == 'adj'){
		var valor = document.getElementById('filtros_adj').value;
		document.getElementById('tipo_fecha').value = 'pub';
		document.getElementById('tipo_fecha').disabled = true;
		id_nodo = 'proveedor_div';
	} else if (tipo == 'adj_off'){
		var valor = '';
		document.getElementById('tipo_fecha').disabled = false;
		document.getElementById('tipo_fecha').value = 'mod';
		id_nodo = 'proveedor_div';
	}
 
	var http = new XMLHttpRequest();
	params.append(tipo, valor);
	http.open('POST', 'filtros.php', true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById(id_nodo).innerHTML = this.responseText;
		}
	}
	http.send(params);

}

function filtro_catalogo(){

	var valor = '';
	valor = document.querySelector('input[name="catalogo"]:checked').value;
	var http = new XMLHttpRequest();
	var params = new FormData();
	params.append('catalogo', valor);
	http.open('POST', 'filtro_catalogo.php', true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('div_catalogo').innerHTML = this.responseText;
		}
	}
	http.send(params);

}

function crear_oferta(valor, array_key){

	actualizar_estado_compra_detalle(array_key, '3')
	if(valor == 'verificar'){
		var url = 'verificar_ofertas.php';
		var http = new XMLHttpRequest();
		http.open('POST', url, true);
		http.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200){
				if(this.responseText != ''){
					var mascara = document.createElement('div');
					var mascara_bg = document.createElement('div');
					var cartel = document.createElement('div');
					var body = document.getElementsByTagName('body')[0];
					
					mascara.id = 'fullscreen_mask';
					mascara_bg.id = 'fullscreen_mask_bg';
					cartel.id = 'cartel_verificacion';
					
					mascara.append(mascara_bg);
					mascara.append(cartel);
					body.append(mascara);
				
					document.getElementById('cartel_verificacion').innerHTML = this.responseText;
					mascara_bg.addEventListener('click', function() {
							crear_oferta('cancelar');
					});
				} else {
					postear('ruta', 'nueva_oferta');
					crear_oferta(true);
				}
			}
		}
		http.send();
	} else {
		if(valor == 'cancelar'){
			var body = document.getElementsByTagName('body')[0];
			var div = document.getElementById('fullscreen_mask');
			body.removeChild(div);
		} else if (valor){
			window.location.assign('ofertar.php');
		} else if (!valor){
			postear('ruta', 'nueva_oferta');
			window.location.assign('ofertar.php');
		}
	}
}

function detalle_llamado(id_compra){

	var f = document.createElement('form');
	document.body.appendChild(f);
	f.method='POST';
	f.target='_blank';
	f.action= 'detalle.php';
	var params = new FormData(f);
	params.append('id_compra', id_compra);
	var keys = params.keys();
	console.log(keys);
	f.submit()
	//document.body.removeChild(f);
}

function detalle_oferta(id_oferta){

	var f = document.createElement('form');
	document.body.appendChild(f);
	f.action='ofertar.php';
	f.method='POST';
	f.target='_blank';
	var params = new FormData(f);
	params.append('id_oferta', id_oferta);

	f.submit();
}

function postear(nombre, valor){
	var http = new XMLHttpRequest();
	var params = new FormData();
	params.append('nombre', nombre);
	params.append('valor', valor);
	http.open('POST', 'postear.php', true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
		}
	}
	http.send(params);
}

function opciones_llamado_desplegable(id_compra = null){

	if(bool_desplegable){

		bool_desplegable = false;

		for(element of desplegable.coleccion){
			document.getElementById(element).remove();
		};

		boton_estado_header(estado_compra_actual);
		
	} else {

		enmascarar(['pag', 'header_detalle']);

		desplegable = new Desplegable();
		var guardar = new Nodo("Guardar llamado", "li_guardar", id_compra);
		var ofertar = new Nodo("Crear oferta", "li_ofertar", id_compra);
		var rechazar = new Nodo("Rechazar llamado", "li_rechazar", id_compra);
	
		if(estado_compra_actual == 'visto' || estado_compra_actual == 'no_visto'){
			desplegable.agregar_li([guardar, ofertar, rechazar]);
		} else if (estado_compra_actual == 'guardado' || estado_compra_actual == 'elaborando_oferta'){
			desplegable.agregar_li([ofertar, rechazar]);
		} else if (estado_compra_actual == 'rechazado'){
			desplegable.agregar_li([guardar, ofertar]);
		}

		bool_desplegable = true;
	}
}

function nada(event){
	event.stopPropagation();
}

function boton_estado_header(estado){

	estado_compra_actual = estado;
	
	var http = new XMLHttpRequest();
	http.open("POST", "funcs/boton_estado_header.php", true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('botones_header').innerHTML = this.responseText;
		}
	}
	http.send();
}

function busqueda_filtros(){
	var http = new XMLHttpRequest();
	var url = 'busqueda.php';
	var params = new FormData(document.getElementsByTagName("form")[0]);
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			var http = new XMLHttpRequest();
			var url = 'funcs/inventario_llamados.php';
			http.open('POST', url, true);
			http.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200){
					document.getElementById('visor_llamados').innerHTML = this.responseText;
				}
			}
			http.send();
		}
	}
	http.send(params);
}

function nueva_busqueda(clave, pagina){
	poner_mascara();
	document.getElementById('scrolleable_cont').scroll(0, document.getElementById('scrolleable_cont').scrollTop * -1);
	var xmlhttp = new XMLHttpRequest();
	var params = new FormData(document.getElementsByTagName('form')[0]);
	params.append('key', clave);
	params.append('pag', pagina);
	xmlhttp.open("POST", 'busqueda.php', true);
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText);
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.open("POST", "funcs/inventario_llamados.php", true);
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById('visor_llamados').innerHTML = this.responseText;
					var cant = getCookie('result');
					if(cant == 0){
						document.getElementById('cant_resultados').innerHTML = 'La consulta no devolvió resultados.';
					} else if (cant == 1){
						document.getElementById('cant_resultados').innerHTML = 'La consulta devolvió 1 resultado.';
					} else {
						document.getElementById('cant_resultados').innerHTML = 'La consulta devolvió ' + cant + ' resultados.';
					}
					quitar_mascara();
					// scrollbar.set_scrollbar();
				}
			}
			xmlhttp.send();
		}
	}
	xmlhttp.send(params);
}

function copiar_portapapeles(){
	var objeto = document.getElementById("objeto").innerHTML;
	navigator.clipboard.writeText(objeto);
}

function monitor_trabajo(){
	poner_mascara();
	document.getElementById('escena').scroll(0, document.getElementById('escena').scrollTop * - 1);
	var xmlhttp = new XMLHttpRequest();
	var params = new FormData(document.getElementsByTagName('form')[0]);
	xmlhttp.open("POST", 'busqueda.php', true);
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			console.log(this.responseText);
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.open("POST", "funcs/inventario_llamados.php", true);
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById('visor_llamados').innerHTML = this.responseText;
					var cant = getCookie('result');
					if(cant == 0){
						document.getElementById('cant_resultados').innerHTML = 'La consulta no devolvió resultados.';
					} else if (cant == 1){
						document.getElementById('cant_resultados').innerHTML = 'La consulta devolvió 1 resultado.';
					} else {
						document.getElementById('cant_resultados').innerHTML = 'La consulta devolvió ' + cant + ' resultados.';
					}
					quitar_mascara();
				}
			}
			xmlhttp.send();
		}
	}
	xmlhttp.send(params);
}

function agregar_rubro_oferta(){
	var http = new XMLHttpRequest();
	var url = 'agregar_rubro.php';
	var params = new FormData(document.getElementsByTagName("form")[0]);
	http.open("POST", url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById('div_rubros').innerHTML = this.responseText;
		}
	}
	http.send(params);

}

function filtros_arce(){

	if(!busqueda_filtros_bool){
		busqueda_filtros_bool = true;
		if(busqueda_objeto_bool){
			busqueda_objeto();
		}
	} else {
		busqueda_filtros_bool = false;
	}

	show_filtros_arce();

}

function show_filtros_arce(){

	if(busqueda_filtros_bool){
		var http = new XMLHttpRequest();
		http.open("POST", "filtros_busq.php", true);
		http.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var ul = document.getElementById('filtros_arce');
				var list_item = document.createElement('div');
				list_item.id = 'div_filtros';
				list_item.classList.add("nb-llamados-bot");
				ul.appendChild(list_item);
				list_item.innerHTML = this.responseText;
				filtro_catalogo();
			}
		}
		http.send();
	} else {
		var li = document.getElementById("div_filtros");
		document.getElementById("filtros_arce").removeChild(li);
	}
}

function redir_apel(id_compra){
	window.open("https://www.comprasestatales.gub.uy/sice/login/" + id_compra, '_blank');
}

function anadir_subc(){

	var params = new FormData(document.getElementsByTagName("form")[0]);
	var http = new XMLHttpRequest();
	var url = 'anadir_subc.php';
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('cont_tabla_subc').innerHTML = this.responseText;
		}
	}
	http.send(params);
}

function main(){
	// scrollbar = new Scrollbar('scrollbar_monitor');
	nueva_busqueda('', 0);
}

function publicar_comentario(id_contexto){

	var http = new XMLHttpRequest();
	var url = 'publicar_comentario.php';
	var form = document.getElementById('form_comentario');
	var comentario = form.elements['comentario'].value;
	var params = new FormData();
	params.append('id_contexto', id_contexto);
	params.append('comentario', comentario);
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			cargar_comentarios(id_contexto);
		}
	}
	http.send(params);

}

function cargar_comentarios(id_contexto){
	var http = new XMLHttpRequest();
	var url = 'comentarios.php';
	var params = new FormData();
	params.append('id_contexto', id_contexto);
	http.open('POST', url, true);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('contenedor_comentarios').innerHTML = this.responseText;
		}
	}
	http.send(params);
}

function form_req(){

	var tipo = document.getElementById('tipo_req').value;

	var ref = document.getElementById('tipo_req');

	ref = ref.getAttribute('data-ref');

	alert(ref);

	var nodo = document.getElementById('form_requerimiento').lastElementChild;
	
	document.getElementById('form_requerimiento').removeChild(nodo);

	var nodo_aux = document.createElement('div');
	nodo_aux.id = 'nodo_aux';
	document.getElementById('form_requerimiento').appendChild(nodo_aux);

	var http = new XMLHttpRequest();
	var url = 'requerimientos.php';
	http.open('POST', url, true);

	var params = new FormData();
	params.append('tipo', tipo);
	params.append('ref', ref);

	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('nodo_aux').innerHTML = this.responseText;
		}
	}
	http.send(params);

}

function visita(){

	var box = document.getElementById('checkbox_visita');

	if(box.checked){

		var div_fechas = document.getElementById('div_fechas_visita');
		var fecha_fin = document.getElementById('div_fecha_fin');

		var div_hora_visita = document.createElement('div');
		div_hora_visita.id = 'div_hora_visita';

		var label_hora_visita = document.createElement('label');
		label_hora_visita.for = 'input_hora_visita';
		label_hora_visita.innerText = 'Hora:';

		var input_hora_visita = document.createElement('input');
		input_hora_visita.id = 'input_hora_visita';
		input_hora_visita.name = 'input_hora_visita';
		input_hora_visita.type = 'text';

		input_hora_visita.addEventListener('change', hora_visita);

		div_hora_visita.appendChild(label_hora_visita);
		div_hora_visita.appendChild(input_hora_visita);

		div_fechas.appendChild(div_hora_visita);

		document.getElementById('label_visita').innerText = 'Fecha:';

		div_fechas.removeChild(fecha_fin);

	} else {

		var fecha_fin = document.createElement('div');
		fecha_fin.id = 'div_fecha_fin';

		var label_fin = document.createElement('label');
		label_fin.for = 'fecha_fin';
		label_fin.innerText = 'Fecha fin:';

		var date_fin = document.createElement('input');
		date_fin.type = 'date';
		date_fin.name = 'fecha_fin';
		date_fin.id = 'fecha_fin';

		fecha_fin.appendChild(label_fin);
		fecha_fin.appendChild(date_fin);

		document.getElementById('div_fechas_visita').appendChild(fecha_fin);

		var div_hora_visita = document.getElementById('div_hora_visita');

		document.getElementById('div_fechas_visita').removeChild(div_hora_visita);

		document.getElementById('label_visita').innerText = 'Fecha inicio:';

	}

	function hora_visita(){

		var valor = document.getElementById('input_hora_visita').value;

		for( var [key, value] of Object.entries(valor)){

			if(key == 0 && ( value > 2 || value < 0 || !value.isInteger())){
				valor[key] = 0;
			}

		}

		alert(valor);
		document.getElementById('input_hora_visita').value = valor;

	}


}

function buscarObjeto(clave){
	
	var http = new XMLHttpRequest();
	http.open('POST', 'objetos.php', true);
	var params = new FormData();
	params.append('clave', clave);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementsByTagName('body')[0].innerHTML = this.responseText;
		}
	}
	http.send(params);
	

}

function cambiar_nombre_compra($id_compra){

	if(!mascara_bool){

		mascara_bool = true;

		var mascara = document.createElement('div');
		mascara.id = 'fullpage_mask';
		document.getElementsByTagName('body')[0].append(mascara);
	
		var mascara_relleno = document.createElement('div');
		mascara_relleno.id = 'fullpage_mask_fill';
		document.getElementById('fullpage_mask').append(mascara_relleno);
		mascara_relleno.addEventListener('click', function() {mask_off()});
	
		var cartel = document.createElement('div');
		cartel.id = 'ventana_cambio_nombre';
		document.getElementById('fullpage_mask').append(cartel);
		
	}

	var http = new XMLHttpRequest();
	http.open('POST', 'form_cambio_nombre_compra.php', true);
	var params = new FormData();
	params.append('id_compra', $id_compra);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('ventana_cambio_nombre').innerHTML = this.responseText;
		}
	}
	http.send(params);

}

function mask_off(){

	var mascara = document.getElementById('fullpage_mask');
	var body = document.getElementsByTagName('body')[0];

	body.removeChild(mascara);

	mascara_bool = false;

}

function cambiar_nombre(hash){

	if(hash != ''){
		var http = new XMLHttpRequest();
		var params = new FormData(document.getElementsByTagName("form")[1]);
		params.append('objeto', hash);
		http.open('POST', 'cambiar_nombre_compra.php', true);
		http.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200){
				document.getElementById('header_title').innerHTML = this.responseText;
				boton_estado_header('guardado');
				mask_off();
			}
		}
		http.send(params);
	}
}

function nombre_predeterminado(hash){

	var http = new XMLHttpRequest();
	http.open('POST', 'cambiar_nombre_compra.php', true);
	var params = new FormData();
	params.append('nombre', 'PREDETERMINADO');
	params.append('objeto', hash);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			document.getElementById('header_title').innerHTML = this.responseText;
			mask_off();
		}
	}
	http.send(params);

}

function set_req(){

	var http = new XMLHttpRequest();
	http.open('POST', 'alta_requerimiento.php', true);
	var params = new FormData(document.getElementById('form_requerimiento'));
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
		}
	}
	http.send(params);

}

function alerta(identificador){
	var http = new XMLHttpRequest();
	http.open('POST', 'puente_funciones.php', true);
	var params = new FormData();
	params.append('objeto', identificador);
	http.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200){
			console.log(this.responseText);
		}
	}
	http.send(params);
}

function prueba(){

	var f = document.createElement('form');
	f.name= 'form';
	f.action= 'detalle.php';
	f.method='POST';
	f.target='_blank';
	document.body.appendChild(f);
	var params = new FormData(f);
	params.append('id_compra', '1000000');	
	f.submit();
	//document.body.removeChild(f);
}
