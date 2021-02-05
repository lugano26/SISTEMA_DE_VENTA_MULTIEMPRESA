delimiter $$
create trigger trggBeforeInsertTEmpresa before insert on tempresa FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoEmpresa) from tempresa);
if @ultimoCodigo is null then
	set @ultimoCodigo="EMPRESAX0000000";
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoEmpresa=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTEmpresaDeuda before insert on tempresadeuda FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoEmpresaDeuda) from tempresadeuda);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoEmpresaDeuda=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTUbigeo before insert on tubigeo FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoUbigeo) from tubigeo);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoUbigeo=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTOficina before insert on toficina FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoOficina) from toficina);
if @ultimoCodigo is null then
	set @ultimoCodigo="OFICINAX0000000";
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoOficina=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTAlmacen before insert on talmacen FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoAlmacen) from talmacen);
if @ultimoCodigo is null then
	set @ultimoCodigo="ALMACENX0000000";
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoAlmacen=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTAmbiente before insert on tambiente FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoAmbiente) from tambiente);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoAmbiente=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTAmbienteEspacio before insert on tambienteespacio FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoAmbienteEspacio) from tambienteespacio);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoAmbienteEspacio=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTInventario before insert on tinventario FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoInventario) from tinventario);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoInventario=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTPersonal before insert on tpersonal FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoPersonal) from tpersonal);
if @ultimoCodigo is null then
	set @ultimoCodigo="PERSONAL0000000";
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoPersonal=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTUsuarioNotificacion before insert on tusuarionotificacion FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoUsuarioNotificacion) from tusuarionotificacion);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoUsuarioNotificacion=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTPersonalTOficina before insert on tpersonaltoficina FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
set @ultimoCodigo=(select max(codigoPersonalTOficina) from tpersonaltoficina);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, "XX", "0000000"));
end if;
set @parteAnio=mid(@ultimoCodigo, 1, 4);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteAnio=@anio then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, 'XX', @codigoNumerico);
set NEW.codigoPersonalTOficina=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTPersonalTAlmacen before insert on tpersonaltalmacen FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
set @ultimoCodigo=(select max(codigoPersonalTAlmacen) from tpersonaltalmacen);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, "XX", "0000000"));
end if;
set @parteAnio=mid(@ultimoCodigo, 1, 4);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteAnio=@anio then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, 'XX', @codigoNumerico);
set NEW.codigoPersonalTAlmacen=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTProveedor before insert on tproveedor FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoProveedor) from tproveedor);
if @ultimoCodigo is null then
	set @ultimoCodigo="PROVEEDO0000000";
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoProveedor=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTProveedorPuntoVenta before insert on tproveedorpuntoventa FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoProveedorPuntoVenta) from tproveedorpuntoventa where codigoProveedor=NEW.codigoProveedor);
if @ultimoCodigo is null then
	set @ultimoCodigo=concat("X", mid(NEW.codigoProveedor, 9, 7) ,"0000000");
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoProveedorPuntoVenta=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTProveedorProducto before insert on tproveedorproducto FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoProveedorProducto) from tproveedorproducto where codigoProveedor=NEW.codigoProveedor);
if @ultimoCodigo is null then
	set @ultimoCodigo=concat("X", mid(NEW.codigoProveedor, 9, 7) ,"0000000");
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoProveedorProducto=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTClienteNatural before insert on tclientenatural FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoClienteNatural) from tclientenatural);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoClienteNatural=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTClienteJuridico before insert on tclientejuridico FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoClienteJuridico) from tclientejuridico);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoClienteJuridico=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTClienteJuridicoRepresentante before insert on tclientejuridicorepresentante FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoClienteJuridicoRepresentante) from tclientejuridicorepresentante);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoClienteJuridicoRepresentante=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTCategoria before insert on tcategoria FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoCategoria) from tcategoria);
if @ultimoCodigo is null then
	set @ultimoCodigo="CATEGORI0000000";
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoCategoria=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTPresentacion before insert on tpresentacion FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoPresentacion) from tpresentacion);
if @ultimoCodigo is null then
	set @ultimoCodigo="PRESENTA0000000";
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoPresentacion=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTUnidadMedida before insert on tunidadmedida FOR EACH ROW
begin
set @ultimoCodigo=(select max(codigoUnidadMedida) from tunidadmedida);
if @ultimoCodigo is null then
	set @ultimoCodigo="UNIDADME0000000";
end if;
set @parteTexto=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7)+1;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@parteTexto, @codigoNumerico);
set NEW.codigoUnidadMedida=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTAlmacenProducto before insert on talmacenproducto FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoAlmacenProducto) from talmacenproducto);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoAlmacenProducto=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTAlmacenProductoTCategoria before insert on talmacenproductotcategoria FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoAlmacenProductoTCategoria) from talmacenproductotcategoria);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoAlmacenProductoTCategoria=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboCompra before insert on trecibocompra FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboCompra) from trecibocompra);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboCompra=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboCompraDetalle before insert on trecibocompradetalle FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboCompraDetalle) from trecibocompradetalle);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboCompraDetalle=(select @codigo);

set @codigoAlmacen=(select codigoAlmacen from trecibocompra where codigoReciboCompra=New.codigoReciboCompra);

set @datosComparacionExistencia=replace(concat(@codigoAlmacen, New.codigoBarrasProducto, New.nombreProducto), ' ','');
set @codigoAlmacenProducto=(select codigoAlmacenProducto from talmacenproducto where replace(concat(codigoAlmacen, codigoBarras, nombre), ' ', '')=@datosComparacionExistencia);

if @codigoAlmacenProducto is null then
	insert into talmacenproducto
	(
		codigoAlmacen, 
		codigoPresentacion, 
		codigoUnidadMedida, 
		codigoBarras, 
		nombre, 
		descripcion, 
		tipo, 
		cantidad, 
		situacionImpuesto, 
		tipoImpuesto, 
		porcentajeTributacion, 
		cantidadMinimaAlertaStock, 
		pesoGramosUnidad, 
		ventaMenorUnidad, 
		unidadesBloque, 
		unidadMedidaBloque,
		precioCompraUnitario, 
		precioVentaUnitario, 
		fechaVencimiento,
		estado,
		updated_at,
		created_at
	) 
	values
	(
		@codigoAlmacen, 
		New.codigoPresentacionProducto, 
		New.codigoUnidadMedidaProducto, 
		New.codigoBarrasProducto, 
		New.nombreProducto, 
		New.descripcionProducto, 
		New.tipoProducto, 
		New.cantidadProducto, 
		New.situacionImpuestoProducto, 
		New.tipoImpuestoProducto, 
		New.porcentajeTributacionProducto, 
		New.cantidadMinimaAlertaStockProducto, 
		New.pesoGramosUnidadProducto, 
		New.ventaMenorUnidadProducto, 
		New.unidadesBloqueProducto, 
		New.unidadMedidaBloqueProducto,
		New.precioCompraUnitarioProducto, 
		New.precioVentaUnitarioProducto, 
		New.fechaVencimientoProducto,
		true,
		(select now()),
		(select now())
	);
else
	set @cantidadAnterior=(select cantidad from talmacenproducto where codigoAlmacenProducto=@codigoAlmacenProducto);
	update talmacenproducto
	set 
		cantidad=@cantidadAnterior+New.cantidadProducto,
		precioCompraUnitario=New.precioCompraUnitarioProducto,
		fechaVencimiento=New.fechaVencimientoProducto
	where codigoAlmacenProducto=@codigoAlmacenProducto;
end if;
end
$$

delimiter $$
create trigger trggBeforeInsertTProductoEnviarStock before insert on tproductoenviarstock FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoProductoEnviarStock) from tproductoenviarstock);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoProductoEnviarStock=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTProductoEnviarStockDetalle before insert on tproductoenviarstockdetalle FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoProductoEnviarStockDetalle) from tproductoenviarstockdetalle);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoProductoEnviarStockDetalle=(select @codigo);

set @codigoOficina=(select codigoOficina from tproductoenviarstock where codigoProductoEnviarStock=New.codigoProductoEnviarStock);
set @presentacion=(select nombre from tpresentacion where codigoPresentacion=New.codigoPresentacionProducto);
set @unidadMedida=(select nombre from tunidadmedida where codigoUnidadMedida=New.codigoUnidadMedidaProducto);

set @datosComparacionExistencia=replace(concat(@codigoOficina, New.codigoBarrasProducto, New.nombreProducto), ' ','');
set @codigoOficinaProducto=(select codigoOficinaProducto from toficinaproducto where replace(concat(codigoOficina, codigoBarras, nombre), ' ', '')=@datosComparacionExistencia);

set @categoria=(select nombreCategoriaPorCodigoAlmacenProducto(New.codigoAlmacenProducto));

if @codigoOficinaProducto is null then	
	insert into toficinaproducto
	(
		codigoOficina, 
		presentacion, 
		unidadMedida, 
		categoria, 
		codigoBarras, 
		nombre, 
		descripcion, 
		tipo, 
		situacionImpuesto, 
		tipoImpuesto, 
		porcentajeTributacion, 
		cantidadMinimaAlertaStock, 
		pesoGramosUnidad, 
		cantidad, 
		ventaMenorUnidad, 
		unidadesBloque, 
		unidadMedidaBloque, 
		precioCompraUnitario, 
		precioVentaUnitario, 
		fechaVencimiento,
		estado,
		updated_at,
		created_at
	) 
	values
	(
		@codigoOficina, 
		@presentacion, 
		@unidadMedida, 
		@categoria,
		New.codigoBarrasProducto, 
		New.nombreProducto, 
		New.descripcionProducto, 
		New.tipoProducto, 
		New.situacionImpuestoProducto, 
		New.tipoImpuestoProducto, 
		New.porcentajeTributacionProducto, 
		New.cantidadMinimaAlertaStockProducto, 
		New.pesoGramosUnidadProducto, 
		New.cantidadProducto, 
		New.ventaMenorUnidadProducto, 
		New.unidadesBloqueProducto, 
		New.unidadMedidaBloqueProducto, 
		New.precioCompraUnitarioProducto, 
		New.precioVentaUnitarioProducto, 
		New.fechaVencimientoProducto,
		true,
		(select now()),
		(select now())
	);
else
	set @cantidadAnterior=(select cantidad from toficinaproducto where codigoOficinaProducto=@codigoOficinaProducto);
	update toficinaproducto
	set 
		cantidad=@cantidadAnterior+New.cantidadProducto,
		categoria=@categoria,
		precioCompraUnitario=New.precioCompraUnitarioProducto,
		fechaVencimiento=New.fechaVencimientoProducto
	where codigoOficinaProducto=@codigoOficinaProducto;
end if;

set @cantidadAnterior=(select cantidad from talmacenproducto where codigoAlmacenProducto=New.codigoAlmacenProducto);
update talmacenproducto
set
	cantidad=@cantidadAnterior-New.cantidadProducto
where codigoAlmacenProducto=New.codigoAlmacenProducto;
end
$$

delimiter $$
create trigger trggBeforeInsertTProductoTrasladoOficina before insert on tproductotrasladooficina FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoProductoTrasladoOficina) from tproductotrasladooficina);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoProductoTrasladoOficina=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTProductoTrasladoOficinaDetalle before insert on tproductotrasladooficinadetalle FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoProductoTrasladoOficinaDetalle) from tproductotrasladooficinadetalle);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoProductoTrasladoOficinaDetalle=(select @codigo);

set @codigoOficinaLlegada=(select codigoOficinaLlegada from tproductotrasladooficina where codigoProductoTrasladoOficina=New.codigoProductoTrasladoOficina);

set @datosComparacionExistencia=replace(concat(@codigoOficinaLlegada, New.codigoBarrasProducto, New.nombreProducto), ' ','');
set @codigoOficinaProducto=(select codigoOficinaProducto from toficinaproducto where replace(concat(codigoOficina, codigoBarras, nombre), ' ', '')=@datosComparacionExistencia);

if @codigoOficinaProducto is null then	
	insert into toficinaproducto
	(
		codigoOficina, 
		presentacion, 
		unidadMedida, 
		categoria, 
		codigoBarras, 
		nombre, 
		descripcion, 
		tipo, 
		situacionImpuesto, 
		tipoImpuesto, 
		porcentajeTributacion, 
		cantidadMinimaAlertaStock, 
		pesoGramosUnidad, 
		cantidad, 
		ventaMenorUnidad, 
		unidadesBloque, 
		unidadMedidaBloque, 
		precioCompraUnitario, 
		precioVentaUnitario, 
		fechaVencimiento,
		estado,
		updated_at,
		created_at
	) 
	values
	(
		@codigoOficinaLlegada, 
		New.presentacionProducto, 
		New.unidadMedidaProducto, 
		New.categoriaProducto,
		New.codigoBarrasProducto, 
		New.nombreProducto, 
		New.descripcionProducto, 
		New.tipoProducto, 
		New.situacionImpuestoProducto, 
		New.tipoImpuestoProducto, 
		New.porcentajeTributacionProducto, 
		New.cantidadMinimaAlertaStockProducto, 
		New.pesoGramosUnidadProducto, 
		New.cantidadProducto, 
		New.ventaMenorUnidadProducto, 
		New.unidadesBloqueProducto, 
		New.unidadMedidaBloqueProducto, 
		New.precioCompraUnitarioProducto, 
		New.precioVentaUnitarioProducto, 
		New.fechaVencimientoProducto,
		true,
		(select now()),
		(select now())
	);
else
	set @cantidadAnterior=(select cantidad from toficinaproducto where codigoOficinaProducto=@codigoOficinaProducto);
	update toficinaproducto
	set 
		cantidad=@cantidadAnterior+New.cantidadProducto,
		fechaVencimiento=New.fechaVencimientoProducto
	where codigoOficinaProducto=@codigoOficinaProducto;
end if;

set @cantidadAnterior=(select cantidad from toficinaproducto where codigoOficinaProducto=New.codigoOficinaProducto);
update toficinaproducto
set
	cantidad=@cantidadAnterior-New.cantidadProducto
where codigoOficinaProducto=New.codigoOficinaProducto;
end
$$

delimiter $$
create trigger trggBeforeInsertTOficinaProducto before insert on toficinaproducto FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoOficinaProducto) from toficinaproducto);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoOficinaProducto=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTCategoriaVenta before insert on tcategoriaventa FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoCategoriaVenta) from tcategoriaventa);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoCategoriaVenta=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaOutEf before insert on treciboventaoutef FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaOutEf) from treciboventaoutef);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaOutEf=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaDetalleOutEf before insert on treciboventadetalleoutef FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaDetalleOutEf) from treciboventadetalleoutef);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaDetalleOutEf=(select @codigo);

set @cantidadAnterior=(select cantidad from toficinaproducto where codigoOficinaProducto=New.codigoOficinaProducto);
update toficinaproducto
set 
	cantidad=@cantidadAnterior-New.cantidadProducto
where codigoOficinaProducto=New.codigoOficinaProducto;
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaLetraOutEf before insert on treciboventaletraoutef FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaLetraOutEf) from treciboventaletraoutef);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaLetraOutEf=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaPagoOutEf before insert on treciboventapagooutef FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaPagoOutEf) from treciboventapagooutef);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaPagoOutEf=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVenta before insert on treciboventa FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVenta) from treciboventa);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVenta=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaDetalle before insert on treciboventadetalle FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaDetalle) from treciboventadetalle);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaDetalle=(select @codigo);

set @cantidadAnterior=(select cantidad from toficinaproducto where codigoOficinaProducto=New.codigoOficinaProducto);
update toficinaproducto
set 
	cantidad=@cantidadAnterior-New.cantidadProducto
where codigoOficinaProducto=New.codigoOficinaProducto;
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaGuiaRemision before insert on treciboventaguiaremision FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaGuiaRemision) from treciboventaguiaremision);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaGuiaRemision=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaGuiaRemisionDetalle before insert on treciboventaguiaremisiondetalle FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaGuiaRemisionDetalle) from treciboventaguiaremisiondetalle);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaGuiaRemisionDetalle=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaNotaDebito before insert on treciboventanotadebito FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaNotaDebito) from treciboventanotadebito);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaNotaDebito=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaNotaDebitoDetalle before insert on treciboventanotadebitodetalle FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaNotaDebitoDetalle) from treciboventanotadebitodetalle);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaNotaDebitoDetalle=(select @codigo);

set @cantidadAnterior=(select cantidad from toficinaproducto where codigoOficinaProducto=New.codigoOficinaProducto);
update toficinaproducto
set 
	cantidad=@cantidadAnterior-New.cantidadProducto
where codigoOficinaProducto=New.codigoOficinaProducto;
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaNotaCredito before insert on treciboventanotacredito FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaNotaCredito) from treciboventanotacredito);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaNotaCredito=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaNotaCreditoDetalle before insert on treciboventanotacreditodetalle FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaNotaCreditoDetalle) from treciboventanotacreditodetalle);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaNotaCreditoDetalle=(select @codigo);

set @cantidadAnterior=(select cantidad from toficinaproducto where codigoOficinaProducto=New.codigoOficinaProducto);
update toficinaproducto
set 
	cantidad=@cantidadAnterior+New.cantidadProducto
where codigoOficinaProducto=New.codigoOficinaProducto;
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaLetra before insert on treciboventaletra FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaLetra) from treciboventaletra);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaLetra=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboVentaPago before insert on treciboventapago FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboVentaPago) from treciboventapago);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboVentaPago=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTDocumentoGeneradoSunat before insert on tdocumentogeneradosunat FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoDocumentoGeneradoSunat) from tdocumentogeneradosunat);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoDocumentoGeneradoSunat=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTResumenDiario before insert on tresumendiario FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoResumenDiario) from tresumendiario);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoResumenDiario=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTReciboCompraPago before insert on trecibocomprapago FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoReciboCompraPago) from trecibocomprapago);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoReciboCompraPago=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTCaja before insert on tcaja FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoCaja) from tcaja);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoCaja=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTCajaDetalle before insert on tcajadetalle FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoCajaDetalle) from tcajadetalle);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoCajaDetalle=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeInsertTEgreso before insert on tegreso FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoEgreso) from tegreso);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoEgreso=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeAlmacenProductoRetiro before insert on talmacenproductoretiro FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoAlmacenProductoRetiro) from talmacenproductoretiro);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoAlmacenProductoRetiro=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeOficinaProductoRetiro before insert on toficinaproductoretiro FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoOficinaProductoRetiro) from toficinaproductoretiro);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoOficinaProductoRetiro=(select @codigo);
end
$$

delimiter $$
create trigger trggBeforeExcepcion before insert on texcepcion FOR EACH ROW
begin
set @anio=(select YEAR(NOW()));
set @mes=(select MONTH(NOW()));
set @dia=(select DAY(NOW()));
if length(@mes)=1 then
	set @mes=concat('0', @mes);
end if;
if length(@dia)=1 then
	set @dia=concat('0', @dia);
end if;
set @ultimoCodigo=(select max(codigoExcepcion) from texcepcion);
if @ultimoCodigo is null then
	set @ultimoCodigo=(select concat(@anio, @mes, @dia, "0000000"));
end if;
set @parteFecha=mid(@ultimoCodigo, 1, 8);
set @parteNumerica=mid(@ultimoCodigo, 9, 7);
if @parteFecha=concat(@anio,@mes,@dia) then
	set @parteNumerica=@parteNumerica+1;
else
	set @parteNumerica=1;
end if;
set @longitudNumero=(select length(@parteNumerica));
set @codigoNumerico=concat(repeat('0', 7-@longitudNumero), @parteNumerica);
set @codigo=concat(@anio, @mes, @dia, @codigoNumerico);
set NEW.codigoExcepcion=(select @codigo);
end
$$