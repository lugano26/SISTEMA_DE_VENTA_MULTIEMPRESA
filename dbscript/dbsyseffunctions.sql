DELIMITER $$
create function compareFind(enTexto varchar(1111), delTexto varchar(1111), precisionBusqueda int)
returns int
begin
	declare iterar int;
	declare bandera int;
	declare lengthBandera int;
	declare temp varchar(1111);
	declare coincidencia int;
	declare porcentajeCoincidencia int;
	declare cantidadPalabras int;
	declare lengthFromText int;
	declare inTextWithOutSpace varchar(1111);
	set bandera=1;
	set iterar=1;
	set coincidencia=0;
	set cantidadPalabras=0;
	set delTexto=rtrim(ltrim(delTexto));
	set lengthFromText=length(delTexto);
	set inTextWithOutSpace=replace(enTexto, ' ', '');
	set delTexto=replace(delTexto, 'á', 'a');
	set delTexto=replace(delTexto, 'é', 'e');
	set delTexto=replace(delTexto, 'í', 'i');
	set delTexto=replace(delTexto, 'ó', 'o');
	set delTexto=replace(delTexto, 'ú', 'u');
	set delTexto=replace(delTexto, 'à', 'a');
	set delTexto=replace(delTexto, 'è', 'e');
	set delTexto=replace(delTexto, 'ì', 'i');
	set delTexto=replace(delTexto, 'ò', 'o');
	set delTexto=replace(delTexto, 'ù', 'u');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'á', 'a');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'é', 'e');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'í', 'i');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'ó', 'o');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'ú', 'u');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'à', 'a');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'è', 'e');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'ì', 'i');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'ò', 'o');
	set inTextWithOutSpace=replace(inTextWithOutSpace, 'ù', 'u');
	while(instr(delTexto, '  ')>0) do
		set delTexto=replace(delTexto, '  ', ' ');
	end while;
	while iterar=1 do
		set lengthBandera=instr(mid(delTexto, bandera, length(delTexto)-(bandera-1)), ' ')-1;
		if(lengthBandera=-1) then
			if(length(delTexto)=0) then
				set lengthBandera=1;
			end if;
			if(length(delTexto)!=0) then
				set lengthBandera=lengthFromText-(bandera-1);
			end if;
			set iterar=0;
		end if;
		set temp=mid(delTexto, bandera, lengthBandera);
		if(instr(inTextWithOutSpace, temp)>0) then
			set coincidencia=coincidencia+1;
		end if;
		if(length(temp)=0) then
			set coincidencia=coincidencia+1;
		end if;
		set cantidadPalabras=cantidadPalabras+1;
		set bandera=bandera+lengthBandera+1;
		if(bandera>lengthFromText) then
			set iterar=0;
		end if;
	end while;
	set porcentajeCoincidencia=floor((coincidencia*100)/cantidadPalabras);
	if(porcentajeCoincidencia>=precisionBusqueda) then
		return 1;
	end if;
	return 0;
end;
$$

delimiter $$
create function nombreCategoriaPorCodigoAlmacenProducto
(
	inCodigoAlmacenProducto char(15)
)
returns varchar(700) deterministic reads sql data
begin
declare numeroFilas int default 0;
	declare codigoCategoriaTemporal varchar(30) default "";
	declare categoria varchar(700) default "";
	declare miCursor cursor for select codigoCategoria from talmacenproductotcategoria where codigoAlmacenProducto=inCodigoAlmacenProducto;
	declare continue handler for not found set numeroFilas=1;

	open miCursor;
	miCursor_loop: loop
		fetch miCursor into codigoCategoriaTemporal;

		if(numeroFilas=1) then
			leave miCursor_loop;
		end if;

		if(categoria="") then
			set categoria=(select nombre from tcategoria where codigoCategoria=codigoCategoriaTemporal);
		else
			set categoria=concat(categoria, ",", (select nombre from tcategoria where codigoCategoria=codigoCategoriaTemporal));
		end if;
	end loop miCursor_loop;

	close miCursor;
	return categoria;
end
$$