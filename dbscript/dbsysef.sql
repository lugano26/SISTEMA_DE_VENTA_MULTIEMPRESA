create database dbsysef;
use dbsysef;

create table tempresa
(
codigoEmpresa char(15) not null,
ruc varchar(11) not null,
razonSocial varchar(700) not null,
representanteLegal varchar(110) not null,
facturacionElectronica bool not null,
formatoComprobante varchar(70) not null,/*Normal, Ticket*/
userNameEf varchar(700) not null,
passwordEf varchar(700) not null,
tipoCambioUsd decimal(18, 3) null,
urlConsultaFactura varchar(700) not null,
demo bool not null,
estado bool not null,/*Habilitado, Bloqueado*/
created_at datetime not null,
updated_at datetime not null,
primary key(codigoEmpresa)
) engine=innodb;

create table tempresadeuda
(
codigoEmpresaDeuda char(15) not null,
codigoEmpresa char(15) not null,
descripcion text not null,
monto decimal(10, 2) not null,
incluyeIgv bool not null,
facturaEmitida bool not null,
fechaPagar date not null,
fechaPago date null,
fechaInicioPeriodo date not null,
fechaFinPeriodo date not null,
estado bool not null,/*false => Pendiente, true => Pagado*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on delete cascade on update cascade,
primary key(codigoEmpresaDeuda)
) engine=innodb;

create table tubigeo
(
codigoUbigeo char(15) not null,
codigo char(6) not null,
ubicacion varchar(700) not null,
created_at datetime not null,
updated_at datetime not null,
primary key(codigoUbigeo)
) engine=innodb;

create table toficina
(
codigoOficina char(15) not null,
codigoEmpresa char(15) not null,
descripcion varchar(700) not null,
pais varchar(70) not null,
departamento varchar(70) not null,
provincia varchar(70) not null,
distrito varchar(70) not null,
direccion varchar(700) not null,
manzana varchar(10) not null,
lote varchar(10) not null,
numeroVivienda varchar(10),
numeroInterior varchar(10),
telefono varchar(20) not null,
fechaCreacion date not null,
descripcionComercialComprobante text not null,
estado bool not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on delete cascade on update cascade,
primary key(codigoOficina)
) engine=innodb;

create table talmacen
(
codigoAlmacen char(15) not null,
codigoEmpresa char(15) not null,
descripcion varchar(700) not null,
pais varchar(70) not null,
departamento varchar(70) not null,
provincia varchar(70) not null,
distrito varchar(70) not null,
direccion varchar(700) not null,
manzana varchar(10) not null,
lote varchar(10) not null,
numeroVivienda varchar(10),
numeroInterior varchar(10),
telefono varchar(20) not null,
fechaCreacion date not null,
estado bool not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on delete cascade on update cascade,
primary key(codigoAlmacen)
) engine=innodb;

create table tambiente
(
codigoAmbiente char(15) not null,
codigoOficina char(15) null,/*Tiene que registrarse bien oficina o bien almacén*/
codigoAlmacen char(15) null,/*Tiene que registrarse bien oficina o bien almacén*/
codigo varchar(20) not null,/*Campo opcional - Un código que le asignen al anaquel, oficina u otro, ejemplo: ANQ-001, OFI-001, etc.*/
nombre varchar(700) not null,
tipo varchar(20) not null,/*Oficina, Cuarto, Local, Anaquel, Estante, Almacén*/
nivelUbicacion int not null,/*1, 2, 3, ..., 50 - (Piso)*/
referenciaUbicacion varchar(700) not null,/*Ejemplo: En la esquina derecha de la entrada de la oficina de despacho.*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on delete cascade on update cascade,
foreign key(codigoAlmacen) references talmacen(codigoAlmacen)
on delete cascade on update cascade,
primary key(codigoAmbiente)
) engine=innodb;

create table tambienteespacio
(
codigoAmbienteEspacio char(15) not null,
codigoAmbiente char(15) not null,
seccion int not null,/*Campo opcional (0 por defecto) - 0, 1, 2, 3, ... (Los espacios o secciones de un anaquel) "0 para oficinas, locales o similares y el resto para anaqueles y estantes".*/
estado bool not null,/*false para inhabilitada y true para habilitada*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoAmbiente) references tambiente(codigoAmbiente)
on delete cascade on update cascade,
primary key(codigoAmbienteEspacio)
) engine=innodb;

create table tinventario
(
codigoInventario char(15) not null,
codigoAmbienteEspacio char(15) not null,
codigoBarras varchar(700) not null,/*Campo opcional*/
serie varchar(700) not null,/*Campo opcional*/
modelo varchar(70) not null,/*Campo opcional*/
nombre varchar(70) not null,
descripcion varchar(700) not null,/*Campo opcional*/
dimensionAncho varchar(20) not null,/*Campo opcional - Ejemplo: 1.50 Metro(s). (Debe ingresar la la medida en 2 decimales y la unidad de m. hardcodeado "Metro(s), Centímetro(s), Pie(s) y cualquier otro que sea factible para mediciones de muebles y similares").*/
dimensionLargo varchar(20) not null,/*Campo opcional*/
dimensionAlto varchar(20) not null,/*Campo opcional*/
pesoKg decimal(10,2) not null,/*Campo opcional - 0 por defecto*/
estado varchar(20) not null, /*Inservible, Deteriorado, Con daños leves, En buen estado, Nuevo*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoAmbienteEspacio) references tambienteespacio(codigoAmbienteEspacio)
on delete cascade on update cascade,
primary key(codigoInventario)
) engine=innodb;

create table tpersonal
(
codigoPersonal char(15) not null,
codigoEmpresa char(15) not null,
dni char(8) not null,
nombre varchar(70) not null,
apellido varchar(40) not null,
seguridadSocial varchar(20) not null,/*código del seguro social*/
pais varchar(70) not null,
departamento varchar(70) not null,
provincia varchar(70) not null,
distrito varchar(70) not null,
direccion varchar(700) not null,
manzana varchar(10) not null,
lote varchar(10) not null,
numeroVivienda varchar(10),
numeroInterior varchar(10),
telefono varchar(20) not null,
estadoCivil char(1) not null,/*S, C*/
sexo bool not null,
fechaNacimiento date not null,
correoElectronico varchar(700) not null,
grupoSanguineo varchar(10) not null,
tipoEmpleado varchar(20) not null,/*contratado, nombrado...*/
cargo varchar(20) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on delete cascade on update cascade,
primary key(codigoPersonal)
) engine=innodb;

create table tusuario
(
codigoPersonal char(15) not null,
nombreUsuario varchar(700) not null,
contrasenia varchar(700) not null,
rol varchar(700) not null,/*Súper usuario, Administrador, Almacenero, Ventas*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoPersonal) references tpersonal(codigoPersonal)
on update cascade on delete cascade,
primary key(codigoPersonal)
) engine=innodb;

create table tusuarionotificacion
(
codigoUsuarioNotificacion char(15) not null,
codigoPersonal char(15) not null,
descripcion text not null,
permanente bool not null,
fechaInicioPeriodo date null,
fechaFinPeriodo date null,
url text not null,
estado bool not null,/*false => Pendiente, true => Leído*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoPersonal) references tusuario(codigoPersonal)
on delete cascade on update cascade,
primary key(codigoUsuarioNotificacion)
) engine=innodb;

create table tpersonaltoficina
(
codigoPersonalTOficina char(15) not null,
codigoPersonal char(15) not null,
codigoOficina char(15) not null,/*oficinas a las que tiene acceso*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoPersonal) references tpersonal(codigoPersonal)
on update cascade on delete cascade,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
primary key(codigoPersonalTOficina)
) engine=innodb;

create table tpersonaltalmacen
(
codigoPersonalTAlmacen char(15) not null,
codigoPersonal char(15) not null,
codigoAlmacen char(15) not null,/*oficinas a las que tiene acceso*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoPersonal) references tpersonal(codigoPersonal)
on update cascade on delete cascade,
foreign key(codigoAlmacen) references talmacen(codigoAlmacen)
on update cascade on delete cascade,
primary key(codigoPersonalTAlmacen)
) engine=innodb;

create table tclientenatural
(
codigoClienteNatural char(15) not null,
codigoOficina char(15) not null,/*oficina donde se inscribió*/
dni char(8) not null,
nombre varchar(70) not null,
apellido varchar(40) not null,
pais varchar(70) not null,
departamento varchar(70) not null,
provincia varchar(70) not null,
distrito varchar(70) not null,
direccion varchar(700) not null,
manzana varchar(10) not null,
lote varchar(10) not null,
numeroVivienda varchar(10),
numeroInterior varchar(10),
telefono varchar(20) not null,
sexo bool not null,
correoElectronico varchar(700) not null,
fechaNacimiento date not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
primary key(codigoClienteNatural)
) engine=innodb;

create table tclientejuridico
(
codigoClienteJuridico char(15) not null,
codigoOficina char(15) not null,/*oficina donde se inscribió*/
ruc char(11) not null,
razonSocialCorta varchar(700) not null,
razonSocialLarga varchar(700) not null,
residePais bool not null,
fechaConstitucion date not null,
pais varchar(70) not null,
departamento varchar(70) not null,
provincia varchar(70) not null,
distrito varchar(70) not null,
direccion varchar(700) not null,
manzana varchar(10) not null,
lote varchar(10) not null,
numeroVivienda varchar(10),
numeroInterior varchar(10),
telefono varchar(20) not null,
correoElectronico varchar(700) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
primary key(codigoClienteJuridico)
) engine=innodb;

create table tclientejuridicorepresentante
(
codigoClienteJuridicoRepresentante char(15) not null,
codigoClienteJuridico char(15) not null,
dni char(8) not null,
nombreCompleto varchar(110) not null,
cargo varchar(20) not null,
correoElectronico varchar(700) not null,
domicilio varchar(700) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoClienteJuridico) references tclientejuridico(codigoClienteJuridico)
on update cascade on delete cascade,
primary key(codigoClienteJuridicoRepresentante)
) engine=innodb;

create table tproveedor
(
codigoProveedor char(15) not null,
codigoEmpresa char(15) not null,
documentoIdentidad varchar(11) not null,
nombre varchar(700) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on delete cascade on update cascade,
primary key(codigoProveedor)
) engine=innodb;

create table tproveedorpuntoventa
(
codigoProveedorPuntoVenta char(15) not null,
codigoProveedor char(15) not null,
descripcion varchar(700) not null,
pais varchar(70) not null,
departamento varchar(70) not null,
provincia varchar(70) not null,
distrito varchar(70) not null,
direccion varchar(700) not null,
manzana varchar(10) not null,
lote varchar(10) not null,
numeroVivienda varchar(10),
numeroInterior varchar(10),
telefono varchar(20) not null,
correoElectronico varchar(700) not null,
paginaWeb text not null,
estado bool not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoProveedor) references tproveedor(codigoProveedor)
on update cascade on delete cascade,
primary key(codigoProveedorPuntoVenta)
) engine=innodb;

create table tproveedorproducto
(
codigoProveedorProducto char(15) not null,
codigoProveedor char(15) not null,
nombre varchar(700) not null,
descripcion text not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoProveedor) references tproveedor(codigoProveedor)
on update cascade on delete cascade,
primary key(codigoProveedorProducto)
) engine=innodb;

create table tcategoria
(
codigoCategoria char(15) not null,
codigoCategoriaPadre char(15) not null,
nombre varchar(70) not null,
descripcion text not null,
created_at datetime not null,
updated_at datetime not null,
primary key(codigoCategoria)
) engine=innodb;

create table tpresentacion
(
codigoPresentacion char(15) not null,
nombre varchar(70) not null,
descripcion text not null,
created_at datetime not null,
updated_at datetime not null,
primary key(codigoPresentacion)
) engine=innodb;

create table tunidadmedida
(
codigoUnidadMedida char(15) not null,
nombre varchar(70) not null,
descripcion text not null,
created_at datetime not null,
updated_at datetime not null,
primary key(codigoUnidadMedida)
) engine=innodb;

create table toficinaproducto
(
codigoOficinaProducto char(15) not null,
codigoOficina char(15) not null,
presentacion varchar(70) not null,
unidadMedida varchar(70) not null,
categoria text not null,
codigoBarras varchar(700) not null,
nombre varchar(700) not null,
descripcion text not null,
tipo varchar(20) not null,/*genérico o comercial*/
situacionImpuesto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuesto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacion float not null,
cantidadMinimaAlertaStock int not null,
pesoGramosUnidad float not null,
cantidad decimal(10, 2) not null,
ventaMenorUnidad bool not null,
unidadesBloque int not null,
unidadMedidaBloque varchar(70) not null,
precioCompraUnitario decimal(10, 2) not null,
precioVentaUnitario decimal(10, 2) not null,
fechaVencimiento date not null,
estado bool not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
primary key(codigoOficinaProducto)
) engine=innodb;

create table tcategoriaventa
(
codigoCategoriaVenta char(15) not null,
codigoCategoriaVentaPadre char(15) null,
codigoEmpresa char(15) null,
descripcion varchar(700) not null,
estado boolean not null,/*true -> Activo, false -> Eliminado*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoCategoriaVentaPadre) references tcategoriaventa(codigoCategoriaVenta)
on update cascade on delete cascade,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on update cascade on delete cascade,
primary key(codigoCategoriaVenta)
) engine=innodb;

create table treciboventaoutef
(
codigoReciboVentaOutEf char(15) not null,
codigoOficina char(15) not null,
codigoPersonal char(15) not null,
codigoCategoriaVenta char(15) not null,
nombreCompletoCliente varchar(700) not null,
documentoCliente varchar(11) not null,
direccionCliente varchar(700) not null,
descripcion text not null,/*Alguna descripción adicional de la venta*/
situacionImpuesto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
isc decimal(10, 2) not null,
igv decimal(10, 2) not null,
impuestoAplicado decimal(10, 2) not null,
flete decimal(10, 2) not null,
subTotal decimal(10, 2) not null,
total decimal(10, 2) not null,
tipoRecibo varchar(20) not null,/*Ticket, Recibo, Boleta, Factura*/
numeroRecibo varchar(70) not null,/*Número de comprobante de venta*/
comprobanteEmitido bool not null,
fechaComprobanteEmitido datetime not null,
tipoPago varchar(20) not null,/*Al Contado, Al Crédito...*/
fechaPrimerPago date not null,/*En caso de crédito*/
pagoPersonalizado int not null,/*A Partir de la primera fecha de pago ¿cada cuantos días se debe cobrar? 0 en caso de no elegir esta opción*/
pagoAutomatico varchar(70) not null,/*Semanalmenten los Lunes, Semanalmente los Viernes, Mensual Primer Día, Mensual Último Día*/
letras int not null,
estadoCredito bool not null,/*true->Pagos finalizados, false->Falta concluir pagos*/
estadoEntrega bool not null,/*true->Entregado, false->No Entregado*/
numeroGuiaRemision varchar(70) not null,
documentoReceptor varchar(11) not null,/*Sólo para guía de remisión. Dni o Ruc*/
nombreCompletoReceptor varchar(110) not null,/*Sólo para guía de remisión. Nombre de la persona o razón social*/
documentoTransportista varchar(11) not null,/*Sólo para guía de remisión*/
nombreCompletoTransportista varchar(110) not null,/*Sólo para guía de remisión*/
dniConductorTransportista varchar(8) not null,/*Sólo para guía de remisión*/
placaVehiculoTransportista varchar(700) not null,/*Sólo para guía de remisión*/
numeroContenedorTransporte varchar(70) not null,/*Sólo para guía de remisión*/
pesoBrutoKilosBienes float not null,/*Sólo para guía de remisión*/
fechaIniciaTraslado datetime not null,/*Sólo para guía de remisión*/
motivoTraslado varchar(700) not null,/*Sólo para guía de remisión*/
ubigeoPartida varchar(20) not null,/*Sólo para guía de remisión*/
direccionPartida varchar(700) not null,/*Sólo para guía de remisión*/
ubigeoLlegada varchar(20) not null,/*Sólo para guía de remisión*/
direccionLlegada varchar(700) not null,/*Sólo para guía de remisión*/
estado boolean not null,/*true -> Venta conforme, false -> Venta anulada*/
motivoAnulacion text not null,/*Sólo si estado es false*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
foreign key(codigoPersonal) references tusuario(codigoPersonal)
on update cascade on delete cascade,
foreign key(codigoCategoriaVenta) references tcategoriaventa(codigoCategoriaVenta)
on update cascade on delete cascade,
primary key(codigoReciboVentaOutEf)
) engine=innodb;

create table treciboventadetalleoutef
(
codigoReciboVentaDetalleOutEf char(15) not null,
codigoReciboVentaOutEf char(15) not null,
codigoOficinaProducto char(15) not null,
codigoBarrasProducto varchar(700) not null,
nombreProducto varchar(700) not null,
informacionAdicionalProducto varchar(700) not null,
descripcionProducto text not null,
tipoProducto varchar(30) not null,/*genérico o comercial*/
situacionImpuestoProducto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuestoProducto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacionProducto float not null,
impuestoAplicadoProducto decimal(10, 2) not null,
categoriaProducto varchar(30) not null,
presentacionProducto varchar(30) not null,
unidadMedidaProducto varchar(30) not null,
precioVentaTotalProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
cantidadProducto decimal(20, 10) not null,
cantidadBloqueProducto float not null,
unidadMedidaBloqueProducto varchar(30) not null,
pesoGramosUnidadProducto float not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVentaOutEf) references treciboventaoutef(codigoReciboVentaOutEf)
on update cascade on delete cascade,
primary key(codigoReciboVentaDetalleOutEf)
) engine=innodb;

create table treciboventaletraoutef
(
codigoReciboVentaLetraOutEf char(15) not null,
codigoReciboVentaOutEf char(15) not null,
pagado decimal(10, 2) not null,
porPagar decimal(10, 2) not null,
diasMora int not null,
fechaPagar date not null,/*La fecha que se debe pagar la letra*/
estado bool not null,/*false para letra no pagada y true para letra pagada*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVentaOutEf) references treciboventaoutef(codigoReciboVentaOutEf)
on update cascade on delete cascade,
primary key(codigoReciboVentaLetraOutEf)
) engine=innodb;

create table treciboventapagooutef
(
codigoReciboVentaPagoOutEf char(15) not null,
codigoReciboVentaOutEf char(15) not null,
monto decimal(10, 2) not null,
descripcion text not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVentaOutEf) references treciboventaoutef(codigoReciboVentaOutEf)
on update cascade on delete cascade,
primary key(codigoReciboVentaPagoOutEf)
) engine=innodb;

create table treciboventa
(
codigoReciboVenta char(15) not null,
codigoReciboVentaOutEf char(15) null,
codigoOficina char(15) not null,
codigoPersonal char(15) not null,
codigoCategoriaVenta char(15) not null,
nombreCompletoCliente varchar(700) not null,
documentoCliente varchar(11) not null,
direccionCliente varchar(700) not null,
descripcion text not null,/*Alguna descripción adicional de la venta*/
divisa varchar(20) not null,/*Soles, Dólares*/
tipoCambioUsd decimal(10, 2) not null,
situacionImpuesto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
isc decimal(10, 2) not null,
igv decimal(10, 2) not null,
impuestoAplicado decimal(10, 2) not null,
flete decimal(10, 2) not null,
subTotal decimal(10, 2) not null,
total decimal(10, 2) not null,
tipoRecibo varchar(20) not null,/*Ticket, Recibo, Boleta, Factura*/
numeroRecibo varchar(70) not null,/*Número de comprobante de venta*/
comprobanteEmitido bool not null,
fechaComprobanteEmitido datetime not null,
tipoPago varchar(20) not null,/*Al Contado, Al Crédito...*/
fechaPrimerPago date not null,/*En caso de crédito*/
pagoPersonalizado int not null,/*A Partir de la primera fecha de pago ¿cada cuantos días se debe cobrar? 0 en caso de no elegir esta opción*/
pagoAutomatico varchar(70) not null,/*Semanalmenten los Lunes, Semanalmente los Viernes, Mensual Primer Día, Mensual Último Día*/
letras int not null,
estadoCredito bool not null,/*true->Pagos finalizados, false->Falta concluir pagos*/
estadoEntrega bool not null,/*true->Entregado, false->No Entregado*/
hash varchar(700) not null,
estadoEnvioSunat varchar(20) not null,/*Pendiente de envío, Aprobado, Rechazado*/
codigoCdr varchar(20) not null,
descripcionCdr varchar(700) not null,
estado boolean not null,/*true -> Venta conforme, false -> Venta anulada*/
motivoAnulacion text not null,/*Sólo si estado es false*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVentaOutEf) references treciboventaoutef(codigoReciboVentaOutEf)
on update cascade on delete cascade,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
foreign key(codigoPersonal) references tusuario(codigoPersonal)
on update cascade on delete cascade,
foreign key(codigoCategoriaVenta) references tcategoriaventa(codigoCategoriaVenta)
on update cascade on delete cascade,
primary key(codigoReciboVenta)
) engine=innodb;

create table treciboventadetalle
(
codigoReciboVentaDetalle char(15) not null,
codigoReciboVenta char(15) not null,
codigoOficinaProducto char(15) not null,
codigoBarrasProducto varchar(700) not null,
nombreProducto varchar(700) not null,
informacionAdicionalProducto varchar(700) not null,
descripcionProducto text not null,
tipoProducto varchar(30) not null,/*genérico o comercial*/
situacionImpuestoProducto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuestoProducto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacionProducto float not null,
impuestoAplicadoProducto decimal(10, 2) not null,
categoriaProducto varchar(30) not null,
presentacionProducto varchar(30) not null,
unidadMedidaProducto varchar(30) not null,
precioVentaTotalProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
cantidadProducto decimal(20, 10) not null,
cantidadBloqueProducto float not null,
unidadMedidaBloqueProducto varchar(30) not null,
pesoGramosUnidadProducto float not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVenta) references treciboventa(codigoReciboVenta)
on update cascade on delete cascade,
primary key(codigoReciboVentaDetalle)
) engine=innodb;

create table treciboventaguiaremision
(
codigoReciboVentaGuiaRemision char(15) not null,
codigoReciboVenta char(15) not null,
codigoOficina char(15) not null,
numeroGuiaRemision varchar(70) not null,
documentoReceptor varchar(11) not null,
nombreCompletoReceptor varchar(110) not null,
documentoTransportista varchar(11) not null,
nombreCompletoTransportista varchar(700) not null,
dniConductorTransportista varchar(8) not null,
placaVehiculoTransportista varchar(700) not null,
numeroContenedorTransporte varchar(70) not null,
pesoBrutoKilosBienes float not null,
fechaIniciaTraslado datetime not null,
motivoTraslado varchar(700) not null,
ubigeoPartida varchar(20) not null,
direccionPartida varchar(700) not null,
ubigeoLlegada varchar(20) not null,
direccionLlegada varchar(700) not null,
observacion varchar(700) not null,
hash varchar(700) not null,
estadoEnvioSunat varchar(20) not null,/*Pendiente de envío, Aprobado, Rechazado*/
codigoCdr varchar(20) not null,
descripcionCdr varchar(700) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVenta) references treciboventa(codigoReciboVenta)
on update cascade on delete cascade,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
primary key(codigoReciboVentaGuiaRemision)
) engine=innodb;

create table treciboventaguiaremisiondetalle
(
codigoReciboVentaGuiaRemisionDetalle char(15) not null,
codigoReciboVentaGuiaRemision char(15) not null,
codigoOficinaProducto char(15) not null,
codigoBarrasProducto varchar(700) not null,
nombreProducto varchar(700) not null,
informacionAdicionalProducto varchar(700) not null,
unidadMedidaProducto varchar(30) not null,
cantidadProducto decimal(20, 10) not null,
pesoKilos decimal(20, 10) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVentaGuiaRemision) references treciboventaguiaremision(codigoReciboVentaGuiaRemision)
on update cascade on delete cascade,
primary key(codigoReciboVentaGuiaRemisionDetalle)
) engine=innodb;

create table treciboventanotadebito
(
codigoReciboVentaNotaDebito char(15) not null,
codigoReciboVenta char(15) not null,
codigoOficina char(15) not null,
codigoPersonal char(15) not null,
isc decimal(10, 2) not null,
igv decimal(10, 2) not null,
impuestoAplicado decimal(10, 2) not null,
subTotal decimal(10, 2) not null,
total decimal(10, 2) not null,
numeroRecibo varchar(70) not null,/*Número de comprobante de nota de débito*/
codigoMotivo varchar(10) not null,
descripcionMotivo varchar(700) not null,
fechaComprobanteEmitido datetime not null,
hash varchar(700) not null,
estadoEnvioSunat varchar(20) not null,/*Pendiente de envío, Aprobado, Rechazado*/
codigoCdr varchar(20) not null,
descripcionCdr varchar(700) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVenta) references treciboventa(codigoReciboVenta)
on update cascade on delete cascade,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
foreign key(codigoPersonal) references tusuario(codigoPersonal)
on update cascade on delete cascade,
primary key(codigoReciboVentaNotaDebito)
) engine=innodb;

create table treciboventanotadebitodetalle
(
codigoReciboVentaNotaDebitoDetalle char(15) not null,
codigoReciboVentaNotaDebito char(15) not null,
codigoOficinaProducto char(15) not null,
codigoBarrasProducto varchar(700) not null,
nombreProducto varchar(700) not null,
informacionAdicionalProducto varchar(700) not null,
descripcionProducto text not null,
tipoProducto varchar(30) not null,/*genérico o comercial*/
situacionImpuestoProducto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuestoProducto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacionProducto float not null,
impuestoAplicadoProducto decimal(10, 2) not null,
categoriaProducto varchar(30) not null,
presentacionProducto varchar(30) not null,
unidadMedidaProducto varchar(30) not null,
precioVentaTotalProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
cantidadProducto decimal(20, 10) not null,
cantidadBloqueProducto float not null,
unidadMedidaBloqueProducto varchar(30) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVentaNotaDebito) references treciboventanotadebito(codigoReciboVentaNotaDebito)
on update cascade on delete cascade,
primary key(codigoReciboVentaNotaDebitoDetalle)
) engine=innodb;

create table treciboventanotacredito
(
codigoReciboVentaNotaCredito char(15) not null,
codigoReciboVenta char(15) not null,
codigoOficina char(15) not null,
codigoPersonal char(15) not null,
isc decimal(10, 2) not null,
igv decimal(10, 2) not null,
impuestoAplicado decimal(10, 2) not null,
subTotal decimal(10, 2) not null,
total decimal(10, 2) not null,
numeroRecibo varchar(70) not null,/*Número de comprobante de nota de crédito*/
codigoMotivo varchar(10) not null,
descripcionMotivo varchar(700) not null,
fechaComprobanteEmitido datetime not null,
hash varchar(700) not null,
estadoEnvioSunat varchar(20) not null,/*Pendiente de envío, Aprobado, Rechazado*/
codigoCdr varchar(20) not null,
descripcionCdr varchar(700) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVenta) references treciboventa(codigoReciboVenta)
on update cascade on delete cascade,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
foreign key(codigoPersonal) references tusuario(codigoPersonal)
on update cascade on delete cascade,
primary key(codigoReciboVentaNotaCredito)
) engine=innodb;

create table treciboventanotacreditodetalle
(
codigoReciboVentaNotaCreditoDetalle char(15) not null,
codigoReciboVentaNotaCredito char(15) not null,
codigoOficinaProducto char(15) not null,
codigoBarrasProducto varchar(700) not null,
nombreProducto varchar(700) not null,
informacionAdicionalProducto varchar(700) not null,
descripcionProducto text not null,
tipoProducto varchar(30) not null,/*genérico o comercial*/
situacionImpuestoProducto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuestoProducto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacionProducto float not null,
impuestoAplicadoProducto decimal(10, 2) not null,
categoriaProducto varchar(30) not null,
presentacionProducto varchar(30) not null,
unidadMedidaProducto varchar(30) not null,
precioVentaTotalProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
cantidadProducto decimal(20, 10) not null,
cantidadBloqueProducto float not null,
unidadMedidaBloqueProducto varchar(30) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVentaNotaCredito) references treciboventanotacredito(codigoReciboVentaNotaCredito)
on update cascade on delete cascade,
primary key(codigoReciboVentaNotaCreditoDetalle)
) engine=innodb;

create table treciboventaletra
(
codigoReciboVentaLetra char(15) not null,
codigoReciboVenta char(15) not null,
pagado decimal(10, 2) not null,
porPagar decimal(10, 2) not null,
diasMora int not null,
fechaPagar date not null,/*La fecha que se debe pagar la letra*/
estado bool not null,/*false para letra no pagada y true para letra pagada*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVenta) references treciboventa(codigoReciboVenta)
on update cascade on delete cascade,
primary key(codigoReciboVentaLetra)
) engine=innodb;

create table treciboventapago
(
codigoReciboVentaPago char(15) not null,
codigoReciboVenta char(15) not null,
monto decimal(10, 2) not null,
descripcion text not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboVenta) references treciboventa(codigoReciboVenta)
on update cascade on delete cascade,
primary key(codigoReciboVentaPago)
) engine=innodb;

create table tdocumentogeneradosunat
(
codigoDocumentoGeneradoSunat char(15) not null,
responseCode varchar(10) not null,
responseDescription varchar(700) not null,
codigoEmpresa char(15) not null,
documento varchar(11) not null,
nombre varchar(100) not null,
numeroComprobante varchar(30) not null,
numeroComprobanteAfectado varchar(30) not null,
tipo varchar(70) not null,/*Boleta, Factura, Nota de crédito, Nota de débito, Resumen diario, Comunicación de baja*/
estado varchar(20) not null,/*Aprobado, Rechazado*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on delete cascade on update cascade,
primary key(codigoDocumentoGeneradoSunat)
) engine=innodb;

create table tresumendiario
(
codigoResumenDiario char(15) not null,
codigoEmpresa char(15) not null,
numeroTicket varchar(70) not null,
numeroComprobante varchar(30) not null,/*Número de serie para el resumen diario*/
fecha date not null,
estado varchar(20) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on delete cascade on update cascade,
primary key(codigoResumenDiario)
) engine=innodb;

create table toficinaproductoretiro
(
codigoOficinaProductoRetiro char(15) not null,
codigoOficinaProducto char(15) not null,
codigoOficina char(15) not null,
descripcionOficina varchar(100) not null,
presentacionProducto varchar(30) not null,
unidadMedidaProducto varchar(30) not null,
nombreCompletoProducto varchar(700) not null,
tipoProducto varchar(30) not null,
precioCompraUnitarioProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
fechaVencimientoProducto date not null,
cantidadUnidad decimal(10, 2) not null,
descripcion text not null,
montoPerdido  decimal(10, 2) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
primary key(codigoOficinaProductoRetiro)
) engine=innodb;

create table talmacenproducto
(
codigoAlmacenProducto char(15) not null,
codigoAlmacen char(15) not null,
codigoPresentacion char(15) not null,
codigoUnidadMedida char(15) not null,
codigoBarras varchar(700) not null,
nombre varchar(700) not null,
descripcion text not null,
tipo varchar(30) not null,/*genérico o comercial*/
cantidad decimal(10, 2) not null,
situacionImpuesto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuesto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacion float not null,
cantidadMinimaAlertaStock int not null,
pesoGramosUnidad float not null,
ventaMenorUnidad bool not null,
unidadesBloque int not null,
unidadMedidaBloque varchar(70) not null,
precioCompraUnitario decimal(10, 2) not null,
precioVentaUnitario decimal(10, 2) not null,
fechaVencimiento date not null,
estado bool not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoAlmacen) references talmacen(codigoAlmacen)
on update cascade on delete cascade,
foreign key(codigoPresentacion) references tpresentacion(codigoPresentacion)
on update cascade on delete cascade,
foreign key(codigoUnidadMedida) references tunidadmedida(codigoUnidadMedida)
on update cascade on delete cascade,
primary key(codigoAlmacenProducto)
) engine=innodb;

create table talmacenproductotcategoria
(
codigoAlmacenProductoTCategoria char(15) not null,
codigoAlmacenProducto char(15) not null,
codigoCategoria char(15) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoAlmacenProducto) references talmacenproducto(codigoAlmacenProducto)
on update cascade on delete cascade,
foreign key(codigoCategoria) references tcategoria(codigoCategoria)
on update cascade on delete cascade,
primary key(codigoAlmacenProductoTCategoria)
) engine=innodb;

create table talmacenproductoretiro
(
codigoAlmacenProductoRetiro char(15) not null,
codigoAlmacenProducto char(15) not null,
codigoAlmacen char(15) not null,
descripcionAlmacen varchar(100) not null,
presentacionProducto varchar(30) not null,
unidadMedidaProducto varchar(30) not null,
nombreCompletoProducto varchar(700) not null,
tipoProducto varchar(30) not null,
precioCompraUnitarioProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
fechaVencimientoProducto date not null,
cantidadUnidad decimal(10, 2) not null,
descripcion text not null,
montoPerdido  decimal(10, 2) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoAlmacen) references talmacen(codigoAlmacen)
on update cascade on delete cascade,
primary key(codigoAlmacenProductoRetiro)
) engine=innodb;

create table trecibocompra
(
codigoReciboCompra char(15) not null,
codigoProveedor varchar(15) not null,
codigoAlmacen char(15) not null,
descripcion text not null,/*Para describir casos de descuento, monto adicional u otros*/
impuestoAplicado decimal(10, 2) not null,
subTotal decimal(10, 2) not null,
total decimal(10, 2) not null,
tipoRecibo varchar(20) not null,/*Recibo, Boleta, Factura, Sin Recibo*/
numeroRecibo varchar(70) not null,/*Número de comprobante del proveedor*/
numeroGuiaRemision varchar(70) not null,
comprobanteEmitido bool not null,
fechaComprobanteEmitido datetime not null,
tipoPago varchar(30) not null,/*Al Contado, Al Crédito...*/
fechaPagar date not null,/*Fecha de vencimiento del crédito*/
estadoCredito bool not null,/*true->Pagos finalizados, false->Falta concluir pagos*/
estado boolean not null,/*true -> Compra conforme, false -> Compra anulada*/
motivoAnulacion text not null,/*Sólo si estado es false*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoProveedor) references tproveedor(codigoProveedor)
on update cascade on delete cascade,
foreign key(codigoAlmacen) references talmacen(codigoAlmacen)
on update cascade on delete cascade,
primary key(codigoReciboCompra)
) engine=innodb;

create table trecibocompradetalle
(
codigoReciboCompraDetalle char(15) not null,
codigoReciboCompra char(15) not null,
codigoPresentacionProducto varchar(30) not null,
codigoUnidadMedidaProducto varchar(30) not null,
codigoBarrasProducto varchar(700) not null,
nombreProducto varchar(700) not null,
descripcionProducto text not null,
tipoProducto varchar(30) not null,/*genérico o comercial*/
situacionImpuestoProducto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuestoProducto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacionProducto float not null,
impuestoAplicadoProducto decimal(10, 2) not null,
cantidadMinimaAlertaStockProducto int not null,
pesoGramosUnidadProducto float not null,
precioCompraTotalProducto decimal(10, 2) not null,
precioCompraUnitarioProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
cantidadProducto decimal(10, 2) not null,
ventaMenorUnidadProducto bool not null,
unidadesBloqueProducto int not null,
unidadMedidaBloqueProducto varchar(30) not null,
fechaVencimientoProducto date not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoPresentacionProducto) references tpresentacion(codigoPresentacion)
on update cascade on delete cascade,
foreign key(codigoUnidadMedidaProducto) references tunidadmedida(codigoUnidadMedida)
on update cascade on delete cascade,
foreign key(codigoReciboCompra) references trecibocompra(codigoReciboCompra)
on update cascade on delete cascade,
primary key(codigoReciboCompraDetalle)
) engine=innodb;

create table trecibocomprapago
(
codigoReciboCompraPago char(15) not null,
codigoReciboCompra char(15) not null,
monto decimal(10, 2) not null,
descripcion text not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoReciboCompra) references trecibocompra(codigoReciboCompra)
on update cascade on delete cascade,
primary key(codigoReciboCompraPago)
) engine=innodb;

create table tproductoenviarstock
(
codigoProductoEnviarStock char(15) not null,
codigoAlmacen char(15) not null,
codigoOficina char(15) not null,
flete decimal(10, 2) not null,
estado boolean not null,
motivoAnulacion text not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
foreign key(codigoAlmacen) references talmacen(codigoAlmacen)
on update cascade on delete cascade,
primary key(codigoProductoEnviarStock)
) engine=innodb;

create table tproductoenviarstockdetalle
(
codigoProductoEnviarStockDetalle char(15) not null,
codigoProductoEnviarStock char(15) not null,
codigoAlmacenProducto char(15) not null,
codigoPresentacionProducto char(15) not null,
codigoUnidadMedidaProducto char(15) not null,
codigoBarrasProducto varchar(700) not null,
nombreProducto varchar(700) not null,
descripcionProducto text not null,
tipoProducto varchar(30) not null,/*genérico o comercial*/
situacionImpuestoProducto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuestoProducto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacionProducto float not null,
cantidadMinimaAlertaStockProducto int not null,
pesoGramosUnidadProducto float not null,
cantidadProducto decimal(10, 2) not null,
ventaMenorUnidadProducto bool not null,
unidadesBloqueProducto int not null,
unidadMedidaBloqueProducto varchar(30) not null,
precioCompraUnitarioProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
fechaVencimientoProducto date not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoProductoEnviarStock) references tproductoenviarstock(codigoProductoEnviarStock)
on update cascade on delete cascade,
foreign key(codigoPresentacionProducto) references tpresentacion(codigoPresentacion)
on update cascade on delete cascade,
foreign key(codigoUnidadMedidaProducto) references tunidadmedida(codigoUnidadMedida)
on update cascade on delete cascade,
primary key(codigoProductoEnviarStockDetalle)
) engine=innodb;

create table tproductotrasladooficina
(
codigoProductoTrasladoOficina char(15) not null,
codigoOficina char(15) not null,
codigoOficinaLlegada char(15) not null,
flete decimal(10, 2) not null,
estado boolean not null,
motivoAnulacion text not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
foreign key(codigoOficinaLlegada) references toficina(codigoOficina)
on update cascade on delete cascade,
primary key(codigoProductoTrasladoOficina)
) engine=innodb;

create table tproductotrasladooficinadetalle
(
codigoProductoTrasladoOficinaDetalle char(15) not null,
codigoProductoTrasladoOficina char(15) not null,
codigoOficinaProducto char(15) not null,
presentacionProducto char(30) not null,
unidadMedidaProducto char(30) not null,
categoriaProducto varchar(700) not null,
codigoBarrasProducto varchar(700) not null,
nombreProducto varchar(700) not null,
descripcionProducto text not null,
tipoProducto varchar(20) not null,/*genérico o comercial*/
situacionImpuestoProducto varchar(20) not null,/*Afecto, Inafecto, Exonerado*/
tipoImpuestoProducto varchar(20) not null,/*IGV, ISC*/
porcentajeTributacionProducto float not null,
cantidadMinimaAlertaStockProducto int not null,
pesoGramosUnidadProducto float not null,
cantidadProducto decimal(10, 2) not null,
ventaMenorUnidadProducto bool not null,
unidadesBloqueProducto int not null,
unidadMedidaBloqueProducto varchar(70) not null,
precioCompraUnitarioProducto decimal(10, 2) not null,
precioVentaUnitarioProducto decimal(10, 2) not null,
fechaVencimientoProducto date not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoProductoTrasladoOficina) references tproductotrasladooficina(codigoProductoTrasladoOficina)
on update cascade on delete cascade,
primary key(codigoProductoTrasladoOficinaDetalle)
) engine=innodb;

create table tcaja
(
codigoCaja char(15) not null,
codigoEmpresa char(15) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoEmpresa) references tempresa(codigoEmpresa)
on delete cascade on update cascade,
primary key(codigoCaja)
) engine=innodb;

create table tcajadetalle
(
codigoCajaDetalle char(15) not null,
codigoCaja char(15) not null,
codigoPersonal char(15) not null,
saldoInicial decimal(10, 2) not null,
egresos decimal(10, 2) not null,
ingresos decimal(10, 2) not null,
saldoFinal decimal(10, 2) not null,
descripcion text not null,
cerrado bool not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoCaja) references tcaja(codigoCaja)
on update cascade on delete cascade,
foreign key(codigoPersonal) references tpersonal(codigoPersonal)
on update cascade on delete cascade,
primary key(codigoCajaDetalle)
) engine=innodb;

create table tegreso
(
codigoEgreso char(15) not null,
codigoOficina char(15) not null,
codigoPersonal char(15) not null,
descripcion text not null,
monto decimal(10, 2) not null,
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoOficina) references toficina(codigoOficina)
on update cascade on delete cascade,
foreign key(codigoPersonal) references tpersonal(codigoPersonal)
on update cascade on delete cascade,
primary key(codigoEgreso)
) engine=innodb;

create table texcepcion
(
codigoExcepcion char(15) not null,
codigoPersonal char(15) null,
controlador varchar(70) not null,
accion varchar(70) not null,
error text not null,
estado varchar(20) not null,/*Pendiente, Atendido*/
created_at datetime not null,
updated_at datetime not null,
foreign key(codigoPersonal) references tusuario(codigoPersonal)
on delete cascade on update cascade,
primary key(codigoExcepcion)
) engine=innodb;