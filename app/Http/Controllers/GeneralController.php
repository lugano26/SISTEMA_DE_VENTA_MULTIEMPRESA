<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;

use App\Validation\GeneralValidation;

use DB;

use App\Model\TEmpresa;
use App\Model\TOficina;
use App\Model\TAlmacen;
use App\Model\TPersonal;
use App\Model\TUsuario;
use App\Model\TProveedor;
use Carbon\Carbon;
use App\Model\TReciboVenta;
use App\Model\TOficinaProducto;
use App\Model\TReciboCompra;
use App\Model\TEgreso;
use App\Model\TCategoriaVenta;
use App\Model\TReciboVentaOutEf;
use App\Model\TAlmacenProducto;
use App\Model\TDocumentoGeneradoSunat;
use App\Model\TReciboVentaGuiaRemision;
use App\Model\TReciboVentaNotaCredito;
use App\Model\TReciboVentaNotaDebito;

class GeneralController extends Controller
{
	public function actionDataBackup(ResponseFactory $responseFactory)
	{
		$fileName='backup_sysef.sql';
		$fileNameDownload='backup_sysef_'.date('Y-m-d_H-i-s').'.sql';

		exec('mysqldump '.env('DB_DATABASE').' --password='.env('DB_PASSWORD').' --user='.env('DB_USERNAME').' --single-transaction > '.storage_path().'/'.$fileName);

		return $responseFactory->download(storage_path().'/'.$fileName, $fileNameDownload)->deleteFileAfterSend(true);
	}

	public function actionIndex(SessionManager $sessionManager, $pagina=1)
	{
		$meses = [
			"Enero",
			"Febrero",
			"Marzo",
			"Abril",
			"Mayo",
			"Junio",
			"Julio",
			"Agosto",
			"Setiembre",
			"Octubre",
			"Noviembre",
			"Diciembre"
		];

		$mesesAbreviado = [
			"Ene",
			"Feb",
			"Mar",
			"Abr",
			"May",
			"Jun",
			"Jul",
			"Ago",
			"Set",
			"Oct",
			"Nov",
			"Dic"
		];

		$monthResults = [];
		$monthResultsSunat = [];
		$documentosRechazados = [];

		$currentDate = Carbon::today();
		$currentMonth = date("n");
		
		for($i=1; $i < 13; $i++)
		{
			$fechaInicio = Carbon::create()->day(1)->month($i);
			$fechaFin = $fechaInicio->copy()->endOfMonth();
			
			if($i >= $currentMonth - 2 && $i <= $currentMonth)
			{
				$documentosRechazados[] =(object) [
					"mes" =>  $meses[$i- 1],
					"documentosSunat" => TDocumentoGeneradoSunat::with('tempresa')->whereRaw(
						'created_at <= ? and created_at >= ? and (? or codigoEmpresa=?) and estado=\'Rechazado\'', 
						[
							$fechaFin->toDateString('yyyy-MM-dd'), 
							$fechaInicio->toDateString('yyyy-MM-dd'),
							(strpos($sessionManager->get('rol'), 'Súper usuario') !== false),
							$sessionManager->get('codigoEmpresa')
						])->orderBy('codigoEmpresa')->orderBy('created_at', 'desc')->orderBy('tipo')->get(),
				];
			}

			$monthResultsSunat[] = [
				"mes" => $mesesAbreviado[$i- 1],
				"documentosGenerado" => TDocumentoGeneradoSunat::whereRaw(
					'created_at <= ? and created_at >= ? and codigoEmpresa=?', 
					[
						$fechaFin->toDateString('yyyy-MM-dd'), 
						$fechaInicio->toDateString('yyyy-MM-dd'),
						$sessionManager->get('codigoEmpresa')
					])->count(),
				"boletasEmitidas" => TReciboVenta::whereRaw(
					'created_at <= ? and created_at >= ? and tipoRecibo=\'Boleta\' and codigoOficina in (select codigoOficina from toficina where codigoEmpresa = ?)', 
					[
						$fechaFin->toDateString('yyyy-MM-dd'), 
						$fechaInicio->toDateString('yyyy-MM-dd'),
						$sessionManager->get('codigoEmpresa')
					])->count(),
				"ventaswefemitidas" => TReciboVentaOutEf::whereRaw(
					'created_at <= ? and created_at >= ? and tipoRecibo=\'Boleta\' and codigoOficina in (select codigoOficina from toficina where codigoEmpresa = ?)', 
					[
						$fechaFin->toDateString('yyyy-MM-dd'), 
						$fechaInicio->toDateString('yyyy-MM-dd'),
						$sessionManager->get('codigoEmpresa')
					])->count(),
			];
			
			$monthResults[] = [
				"mes" => $mesesAbreviado[$i- 1],
				"ventasfe" => TReciboVenta::WhereHas('toficina', function ($query) use ($sessionManager) {
						$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
					})
					->whereRaw('created_at <= ? and created_at >= ? and estado=1', [$fechaFin->toDateString('yyyy-MM-dd'), $fechaInicio->toDateString('yyyy-MM-dd')])
					->sum('total'),
				"ventaswef" => TReciboVentaOutEf::WhereHas('toficina', function ($query) use ($sessionManager) {
						$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
					})
					->whereRaw('created_at <= ? and created_at >= ? and estado=1', [$fechaFin->toDateString('yyyy-MM-dd'), $fechaInicio->toDateString('yyyy-MM-dd')])
					->sum('total'),
				"compras" => TReciboCompra::WhereHas('talmacen', function ($query) use ($sessionManager) {
						$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
					})
					->whereRaw('created_at <= ? and created_at >= ? and estado=1', [$fechaFin->toDateString('yyyy-MM-dd'), $fechaInicio->toDateString('yyyy-MM-dd')])
					->sum('total'),
				"egresos" => TEgreso::WhereHas('toficina', function ($query) use ($sessionManager) {
						$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
					})
					->whereRaw('created_at <= ? and created_at >= ?', [$fechaFin->toDateString('yyyy-MM-dd'), $fechaInicio->toDateString('yyyy-MM-dd')])
					->sum('monto')
			];
		}
		
		$paginationPrepare=$this->plataformHelper->prepararPaginacion(TOficinaProducto::WhereHas('toficina', function ($query) use ($sessionManager) {
				$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
			})
			->whereRaw('cantidad <= cantidadMinimaAlertaStock order by created_at desc, cantidad asc'), 5, $pagina);
		$paginationRender=$this->plataformHelper->renderizarPaginacion('general/index', $paginationPrepare["cantidadPaginas"], $pagina, null, true);

		$cantidadProductosEnStock = TAlmacenProducto::WhereHas('talmacen', function ($query) use ($sessionManager) {
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
		})
		->whereRaw('estado=? and cantidad > 0', [true])->sum('cantidad')
		+ TOficinaProducto::WhereHas('toficina', function ($query) use ($sessionManager) {
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
		})
		->whereRaw('estado=? and cantidad > 0', [true])->sum('cantidad');

		$ventasContretadas = TReciboVenta::WhereHas('toficina', function ($query) use ($sessionManager) {
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
		})
		->whereRaw('estado=?', [true])->count() 
		+ TReciboVentaOutEf::WhereHas('toficina', function ($query) use ($sessionManager) {
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
		})
		->whereRaw('codigoOficina=? and estado=?', [$sessionManager->get('codigoOficina'), true])->count();

		$comprasContretadas = TReciboCompra::WhereHas('talmacen', function ($query) use ($sessionManager) {
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')]);
		})
		->whereRaw('estado=?', [true])->count();

		$cantidadDocumentosGeneradosSunat =TDocumentoGeneradoSunat::whereRaw(
			'codigoEmpresa=?', 
			[
				$sessionManager->get('codigoEmpresa')
			])->count();

		$topProductosVendidosVentaFe = DB::select('select nombreProducto, sum(precioVentaTotalProducto) as totalVenta from treciboventadetalle where codigoReciboVenta in ( select codigoReciboVenta from treciboventa where codigoOficina in (select codigoOficina from toficina where codigoEmpresa = ?)) group by nombreProducto order by totalVenta desc limit 10', [$sessionManager->get('codigoEmpresa')]);

		$topProductosVendidosVentaWef = DB::select('select nombreProducto, sum(precioVentaTotalProducto) as totalVenta from treciboventadetalleoutef where codigoReciboVentaoutef in ( select codigoReciboVentaoutef from treciboventaoutef where codigoOficina in (select codigoOficina from toficina where codigoEmpresa = ?)) group by nombreProducto order by totalVenta desc limit 10', [$sessionManager->get('codigoEmpresa')]);

		$estadoPendiente = 'Pendiente de envío';
		$tDocumentosPendientesEnvio = (object) [
			"facturas" => TReciboVenta::with('toficina.tempresa')
						->whereRaw('estadoEnvioSunat=? and tipoRecibo=? and (? or codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?))', 
						[
							$estadoPendiente, 
							'Factura', 
							(strpos($sessionManager->get('rol'), 'Súper usuario') !== false), 
							$sessionManager->get('codigoEmpresa')
						])
						->orderBy('created_at', 'desc')->get(),
			"notasDebitos" => TReciboVentaNotaDebito::with('treciboventa.toficina.tempresa')
							->whereHas('treciboventa', function($query) {
								$query->where('tipoRecibo', 'Factura');
							})
							->whereRaw('estadoEnvioSunat=? and (? or codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?))', 
							[
								$estadoPendiente,
								(strpos($sessionManager->get('rol'), 'Súper usuario') !== false), 
								$sessionManager->get('codigoEmpresa')
							])
							->orderBy('created_at', 'desc')->get(),
			"notasCreditos" => TReciboVentaNotaCredito::with('treciboventa.toficina.tempresa')
							->whereHas('treciboventa', function($query) {
								$query->where('tipoRecibo', 'Factura');
							})
							->whereRaw('estadoEnvioSunat=? and (? or codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?))', 
							[
								$estadoPendiente,
								(strpos($sessionManager->get('rol'), 'Súper usuario') !== false), 
								$sessionManager->get('codigoEmpresa')
							])
							->orderBy('created_at', 'desc')->get(),
			"guiaRemision" => TReciboVentaGuiaRemision::with('treciboventa.toficina.tempresa')
							->whereHas('treciboventa', function($query) {
								$query->where('tipoRecibo', 'Factura');
							})
							->whereRaw('estadoEnvioSunat=? and (? or codigoOficina in (select codigoOficina from toficina where codigoEmpresa=?))', 
							[
								$estadoPendiente,
								(strpos($sessionManager->get('rol'), 'Súper usuario') !== false), 
								$sessionManager->get('codigoEmpresa')
							])
							->orderBy('created_at', 'desc')->get()
		];
		
		return view('general/index', ["data" => json_encode($monthResults), "dataSunat" => json_encode($monthResultsSunat), "listaProductos" => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "meses" => $meses, 'cantidadProductosEnStock' => $cantidadProductosEnStock, 'comprasContretadas' => $comprasContretadas, 'ventasContretadas' => $ventasContretadas, 'cantidadDocumentosGeneradosSunat' => $cantidadDocumentosGeneradosSunat, 'topProductosVendidosVentaFe' => $topProductosVendidosVentaFe, 'topProductosVendidosVentaWef' => $topProductosVendidosVentaWef, 'documentosRechazados' => $documentosRechazados, 'rolUser'=> $sessionManager->get('rol'), 'tDocumentosPendientesEnvio' => $tDocumentosPendientesEnvio]);
	}

	public function actionConfiguracionGlobal(Request $request, SessionManager $sessionManager, Encrypter $encrypter)
	{
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				$this->mensajeGlobal=(new GeneralValidation())->validationConfiguracionGlobal($request);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'general/configuracionglobal');
				}

				if(TPersonal::first()!=null && strpos($sessionManager->get('rol', 'Público'), 'Súper usuario')===false)
				{
					return $this->plataformHelper->redirectError('No se puede proceder. Por favor no trate de alterar el comportamiento del sistema.', '/');
				}

				$tEmpresa=new TEmpresa();

				$tEmpresa->ruc=trim($request->input('txtRucEmpresa'));
				$tEmpresa->razonSocial=trim($request->input('txtRazonSocialEmpresa'));
				$tEmpresa->representanteLegal=trim($request->input('txtRepresentanteLegalEmpresa'));
				$tEmpresa->facturacionElectronica=$request->input('radioFacturacionElectronicaEmpresa');
				$tEmpresa->userNameEf=$request->input('radioFacturacionElectronicaEmpresa') ? trim($request->input('txtUserNameEfEmpresa')) : '';
				$tEmpresa->passwordEf=$request->input('radioFacturacionElectronicaEmpresa') ? $encrypter->encrypt($request->input('txtPasswordEfEmpresa')) : $encrypter->encrypt('');
				$tEmpresa->urlConsultaFactura='https://web.sysef.com';
				$tEmpresa->demo=false;
				$tEmpresa->tipoCambioUsd=3.333;
				$tEmpresa->formatoComprobante='Ticket';
				$tEmpresa->estado=true;

				$tEmpresa->save();

				$codigoEmpresa=TEmpresa::max('codigoEmpresa');

				if(!file_exists(public_path().'/img/empresa/'.$codigoEmpresa))
				{
					mkdir(public_path().'/img/empresa/'.$codigoEmpresa, 0777, true);
				}

				$request->file('fileLogoEmpresarialEmpresa')->move(public_path().'/img/empresa/'.$codigoEmpresa, 'logoEmpresarial.png');

				$tOficina=new TOficina();

				$tOficina->codigoEmpresa=$codigoEmpresa;
				$tOficina->descripcion=trim($request->input('txtDescripcionOficina'));
				$tOficina->pais=trim($request->input('txtPaisOficina'));
				$tOficina->departamento=trim($request->input('txtDepartamentoOficina'));
				$tOficina->provincia=trim($request->input('txtProvinciaOficina'));
				$tOficina->distrito=trim($request->input('txtDistritoOficina'));
				$tOficina->direccion=trim($request->input('txtDireccionOficina'));
				$tOficina->manzana='';
				$tOficina->lote='';
				$tOficina->descripcionComercialComprobante='';
				$tOficina->numeroVivienda=trim($request->input('txtNumeroViviendaOficina'));
				$tOficina->numeroInterior='';
				$tOficina->telefono=trim($request->input('txtTelefonoOficina'));
				$tOficina->fechaCreacion='1111-11-11';
				$tOficina->estado=true;

				$tOficina->save();

				$tAlmacen=new TAlmacen();

				$tAlmacen->codigoEmpresa=$codigoEmpresa;
				$tAlmacen->descripcion=trim($request->input('txtDescripcionAlmacen'));
				$tAlmacen->pais=trim($request->input('txtPaisAlmacen'));
				$tAlmacen->departamento=trim($request->input('txtDepartamentoAlmacen'));
				$tAlmacen->provincia=trim($request->input('txtProvinciaAlmacen'));
				$tAlmacen->distrito=trim($request->input('txtDistritoAlmacen'));
				$tAlmacen->direccion=trim($request->input('txtDireccionAlmacen'));
				$tAlmacen->manzana='';
				$tAlmacen->lote='';
				$tAlmacen->numeroVivienda=trim($request->input('txtNumeroViviendaAlmacen'));
				$tAlmacen->numeroInterior='';
				$tAlmacen->telefono=trim($request->input('txtTelefonoAlmacen'));
				$tAlmacen->fechaCreacion='1111-11-11';
				$tAlmacen->estado=true;

				$tAlmacen->save();

				$tPersonal=new TPersonal();

				$tPersonal->codigoEmpresa=$codigoEmpresa;
				$tPersonal->dni='77777777';
				$tPersonal->nombre='Noe';
				$tPersonal->apellido='Lujan Gutierrez';
				$tPersonal->seguridadSocial='';
				$tPersonal->pais='Perú';
				$tPersonal->departamento='Apurímac';
				$tPersonal->provincia='Andahuaylas';
				$tPersonal->distrito='Chicmo';
				$tPersonal->direccion='';
				$tPersonal->manzana='';
				$tPersonal->lote='';
				$tPersonal->numeroVivienda='';
				$tPersonal->numeroInterior='';
				$tPersonal->telefono='935330432';
				$tPersonal->estadoCivil='';
				$tPersonal->sexo=true;
				$tPersonal->fechaNacimiento='1994-01-02';
				$tPersonal->correoElectronico='lujangutierreznoe@gmail.com';
				$tPersonal->grupoSanguineo='';
				$tPersonal->tipoEmpleado='Nombrado';
				$tPersonal->cargo='Súper usuario';
				
				$tPersonal->save();

				$codigoPersonal=TPersonal::max('codigoPersonal');

				$tUsuario=new TUsuario();

				$tUsuario->codigoPersonal=$codigoPersonal;
				$tUsuario->nombreUsuario='lugano26';
				$tUsuario->contrasenia=$encrypter->encrypt('1994lujan');
				$tUsuario->rol='Súper usuario';

				$tUsuario->save();

				$tPersonal=new TPersonal();

				$tPersonal->codigoEmpresa=$codigoEmpresa;
				$tPersonal->dni='11111111';
				$tPersonal->nombre='Josue';
				$tPersonal->apellido='Lujan Gutierrez';
				$tPersonal->seguridadSocial='';
				$tPersonal->pais='Perú';
				$tPersonal->departamento='Apurímac';
				$tPersonal->provincia='Andahuaylas';
				$tPersonal->distrito='Chicmo';
				$tPersonal->direccion='';
				$tPersonal->manzana='';
				$tPersonal->lote='';
				$tPersonal->numeroVivienda='';
				$tPersonal->numeroInterior='';
				$tPersonal->telefono='952296425';
				$tPersonal->estadoCivil='';
				$tPersonal->sexo=true;
				$tPersonal->fechaNacimiento='1991-07-16';
				$tPersonal->correoElectronico='lujangutierrezjosue@gmail.com';
				$tPersonal->grupoSanguineo='';
				$tPersonal->tipoEmpleado='Nombrado';
				$tPersonal->cargo='Súper usuario';
				
				$tPersonal->save();

				$codigoPersonal=TPersonal::max('codigoPersonal');

				$tUsuario=new TUsuario();

				$tUsuario->codigoPersonal=$codigoPersonal;
				$tUsuario->nombreUsuario='josue91';
				$tUsuario->contrasenia=$encrypter->encrypt('josue');
				$tUsuario->rol='Súper usuario';

				$tUsuario->save();

				$tProveedor=new TProveedor();

				$tProveedor->codigoEmpresa=$codigoEmpresa;
				$tProveedor->documentoIdentidad='00000000000';
				$tProveedor->nombre='No especificado';

				$tProveedor->save();

				$tCategoriaVenta=new TCategoriaVenta();

				$tCategoriaVenta->codigoEmpresa=$codigoEmpresa;
				$tCategoriaVenta->descripcion='Público en general';
				$tCategoriaVenta->estado=true;

				$tCategoriaVenta->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'usuario/login');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		if(TPersonal::first()!=null && strpos($sessionManager->get('rol', 'Público'), 'Súper usuario')===false)
		{
			return $this->plataformHelper->redirectError('Está tratando de acceder a un lugar restringido. Por favor no trate de alterar el comportamiento del sistema.', 'usuario/login');
		}

		return view('general/configuracionglobal');
	}

	public function actionManualesUsuario()
	{
		return view('general/manualesusuario');
	}
}
?>