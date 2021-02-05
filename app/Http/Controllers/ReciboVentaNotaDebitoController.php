<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;
use App\Helper\NumeroLetras;
use Illuminate\Foundation\Application;

use ZipArchive;
use DB;

use App\Model\TReciboVentaNotaDebito;
use App\Model\TReciboVentaNotaDebitoDetalle;
use App\Model\TReciboVenta;
use App\Model\TEmpresa;
use App\Model\TOficina;
use App\Model\TCajaDetalle;

class ReciboVentaNotaDebitoController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager, Encrypter $encrypter)
	{
		if($request->has('hdCodigoReciboVenta'))
		{
			if(!$sessionManager->get('facturacionElectronica'))
			{
				return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
			}

			try
			{
				DB::beginTransaction();

				if(!($sessionManager->has('codigoOficina')))
				{
					return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
				}

				$tReciboVenta=TReciboVenta::find($request->input('hdCodigoReciboVenta'));

				if($tReciboVenta->tipoRecibo=='Factura' && $tReciboVenta->estadoEnvioSunat!='Aprobado')
				{
					return $this->plataformHelper->redirectError('No se puede generar nota de débito. Es posible que la factura aún no haya sido comunicado a la SUNAT o que ésta haya sido rechazada.', 'reciboventa/ver');
				}

				/*Begin: Generación del número de comprobante, incluido la serie del mismo*/

				$tipoComprobante=$tReciboVenta->tipoRecibo=='Factura' ? 'F' : 'B';

				$serieComprobante=0;

				$listaTOficinaTemp=TOficina::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->orderBy('codigoOficina', 'asc')->get();

				foreach($listaTOficinaTemp as $key => $value)
				{
					$serieComprobante++;

					if($value->codigoOficina==$sessionManager->get('codigoOficina'))
					{
						break;
					}
				}

				$numeroComprobante=substr(TReciboVentaNotaDebito::whereRaw('mid(numeroRecibo, 1, 4)=?', [substr($tReciboVenta->tipoRecibo, 0, 1).str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante])->whereHas('treciboventa.toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->max('numeroRecibo'), 5)+1;

				$numeroComprobante=str_repeat('0', (8-strlen($numeroComprobante))).$numeroComprobante;
				$serieComprobante=str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante;
				
				$serieNumeroComprobante=$tipoComprobante.$serieComprobante.'-'.$numeroComprobante;

				/*End: Generación del número de comprobante, incluido la serie del mismo*/

				if(
					trim($request->input('selectMotivo'))==''
				)
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/ver');
				}

				$tReciboVentaNotaDebito=new TReciboVentaNotaDebito();

				$tReciboVentaNotaDebito->codigoReciboVenta=$tReciboVenta->codigoReciboVenta;
				$tReciboVentaNotaDebito->codigoOficina=$sessionManager->get('codigoOficina');
				$tReciboVentaNotaDebito->codigoPersonal=$sessionManager->get('codigoPersonal');
				$tReciboVentaNotaDebito->isc=0;
				$tReciboVentaNotaDebito->igv=$request->input('txtImpuestoAplicado');
				$tReciboVentaNotaDebito->impuestoAplicado=$request->input('txtImpuestoAplicado');
				$tReciboVentaNotaDebito->subTotal=$request->input('txtSubTotal');
				$tReciboVentaNotaDebito->total=$request->input('txtTotal');
				$tReciboVentaNotaDebito->numeroRecibo=$serieNumeroComprobante;
				$tReciboVentaNotaDebito->codigoMotivo=explode('_', $request->input('selectMotivo'))[0];
				$tReciboVentaNotaDebito->descripcionMotivo=explode('_', $request->input('selectMotivo'))[1];
				$tReciboVentaNotaDebito->fechaComprobanteEmitido=date('Y-m-d H:i:s');
				$tReciboVentaNotaDebito->hash='';
				$tReciboVentaNotaDebito->estadoEnvioSunat='Pendiente de envío';
				$tReciboVentaNotaDebito->codigoCdr='';
				$tReciboVentaNotaDebito->descripcionCdr='';

				$tReciboVentaNotaDebito->save();

				$ultimoRegistroTReciboVentaNotaDebito=TReciboVentaNotaDebito::whereRaw('codigoReciboVentaNotaDebito=(select max(codigoReciboVentaNotaDebito) from treciboventanotadebito)')->first();

				if(
					$request->input('selectMotivo')==''
				)
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/ver');
				}

				$tReciboVentaNotaDebitoDetalle=new TReciboVentaNotaDebitoDetalle();

				$tReciboVentaNotaDebitoDetalle->codigoReciboVentaNotaDebito=$ultimoRegistroTReciboVentaNotaDebito->codigoReciboVentaNotaDebito;
				$tReciboVentaNotaDebitoDetalle->codigoOficinaProducto=env('CODIGO_OFICINA_PRODUCTO_EXTERNO');
				$tReciboVentaNotaDebitoDetalle->codigoBarrasProducto='';
				$tReciboVentaNotaDebitoDetalle->nombreProducto=explode('_', $request->input('selectMotivo'))[1];
				$tReciboVentaNotaDebitoDetalle->informacionAdicionalProducto='';
				$tReciboVentaNotaDebitoDetalle->descripcionProducto='';
				$tReciboVentaNotaDebitoDetalle->tipoProducto='Genérico';
				$tReciboVentaNotaDebitoDetalle->situacionImpuestoProducto='Afecto';
				$tReciboVentaNotaDebitoDetalle->tipoImpuestoProducto='IGV';
				$tReciboVentaNotaDebitoDetalle->porcentajeTributacionProducto=env('PORCENTAJE_IGV');
				$tReciboVentaNotaDebitoDetalle->impuestoAplicadoProducto=$request->input('txtImpuestoAplicado');
				$tReciboVentaNotaDebitoDetalle->categoriaProducto='';
				$tReciboVentaNotaDebitoDetalle->presentacionProducto='Sin definir';
				$tReciboVentaNotaDebitoDetalle->unidadMedidaProducto='Unidad';
				$tReciboVentaNotaDebitoDetalle->precioVentaTotalProducto=$request->input('txtTotal');
				$tReciboVentaNotaDebitoDetalle->precioVentaUnitarioProducto=$request->input('txtTotal');
				$tReciboVentaNotaDebitoDetalle->cantidadProducto=1;
				$tReciboVentaNotaDebitoDetalle->cantidadBloqueProducto=12;
				$tReciboVentaNotaDebitoDetalle->unidadMedidaBloqueProducto='Docena';

				$tReciboVentaNotaDebitoDetalle->save();

				$tCajaDetalle=TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->ingresos+=($tReciboVentaNotaDebito->total*($tReciboVenta->divisa!='Soles' ? $tReciboVenta->tipoCambioUsd : 1));
				$tCajaDetalle->saldoFinal+=($tReciboVentaNotaDebito->total*($tReciboVenta->divisa!='Soles' ? $tReciboVenta->tipoCambioUsd : 1));

				$tCajaDetalle->save();

				/*Begin: Cambio de estado de venta*/

				$tReciboVenta=TReciboVenta::with(['treciboventanotacredito' => function($q){ $q->whereRaw('estadoEnvioSunat!=?', ['Rechazado']); }, 'treciboventanotadebito' => function($q){ $q->whereRaw('estadoEnvioSunat!=?', ['Rechazado']); }])->whereRaw('codigoReciboVenta=?', [$tReciboVenta->codigoReciboVenta])->first();

				if((($tReciboVenta->total+$tReciboVenta->treciboventanotadebito->sum('total'))-($tReciboVenta->treciboventanotacredito->sum('total')))>0)
				{
					$tReciboVenta->estado=true;

					$tReciboVenta->save();
				}

				/*End: Cambio de estado de venta*/

				/*Begin: Generación de archivo XML*/

				if($tReciboVenta->tipoRecibo=='Factura')
				{
					$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));
					$tReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventanotadebitodetalle'])->whereRaw('codigoReciboVentaNotaDebito=?', [$ultimoRegistroTReciboVentaNotaDebito->codigoReciboVentaNotaDebito])->first();

					$listaProductoEf=[];

					foreach($tReciboVentaNotaDebito->treciboventanotadebitodetalle as $value)
					{
						$objectTemp=new \stdClass();

						$objectTemp->nombreProductoEf=trim($value->nombreProducto.' '.$value->informacionAdicionalProducto);
						$objectTemp->cantidadProductoEf=$value->cantidadProducto;
						$objectTemp->subTotalVentaProductoEf=($value->precioVentaTotalProducto-$value->impuestoAplicadoProducto);
						$objectTemp->precioUnitarioVentaProductoEf=$value->precioVentaUnitarioProducto;
						$objectTemp->impuestoTotalVentaProductoEf=$value->impuestoAplicadoProducto;
						$objectTemp->porcentajeTributacionProductoEf=$value->porcentajeTributacionProducto;
						$objectTemp->subTotalPrecioUnitarioVentaProductoEf=$value->precioVentaUnitarioProducto/((($value->porcentajeTributacionProducto)/100)+1);

						$listaProductoEf[]=$objectTemp;
					}

					$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

					$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/debitnote/generatexml',
					[
						'form_params' =>
						[
							'dto.codigoUnicoVenta' => $tReciboVenta->codigoReciboVenta,
							'dto.serieNumeroComprobanteEf' => $tReciboVentaNotaDebito->numeroRecibo,
							'dto.codigoMotivoNota' => $tReciboVentaNotaDebito->codigoMotivo,
							'dto.descripcionMotivoNota' => $tReciboVentaNotaDebito->descripcionMotivo,
							'dto.serieNumeroComprobanteVentaEf' => $tReciboVenta->numeroRecibo,
							'dto.rucEmpresaEf' => $tEmpresa->ruc,
							'dto.razonSocialEmisorEf' => $tEmpresa->razonSocial,
							'dto.userNameEf' => $tEmpresa->userNameEf,
							'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
							'dto.documentoClienteEf' => $tReciboVenta->documentoCliente,
							'dto.denominacionClienteEf' => $tReciboVenta->nombreCompletoCliente,
							'dto.divisaEf' => $tReciboVenta->divisa,
							'dto.totalNotaEf' => $tReciboVentaNotaDebito->total,
							'dto.subTotalNotaEf' => $tReciboVentaNotaDebito->subTotal,
							'dto.listaProductoEf' => $listaProductoEf
						]
					]);

					$dataResponse=(object)json_decode($response->getBody(), true);

					$dataResponse->mo=(object)($dataResponse->mo);

					if($dataResponse->mo->type!='success')
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('No se pudo generar correctamente el archivo XML; por favor, vuelva a intenarlo.', 'reciboventa/ver');
					}

					$dataResponse->dto=(object)($dataResponse->dto);

					$tReciboVentaNotaDebito->hash=$dataResponse->dto->hash;

					$tReciboVentaNotaDebito->save();
				}

				/*End: Generación de archivo XML*/

				$sessionManager->flash('codigoReciboVentaNotaDebito', $ultimoRegistroTReciboVentaNotaDebito->codigoReciboVentaNotaDebito);

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		if(!$sessionManager->get('facturacionElectronica'))
		{
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar operaciones electrónicas con la SUNAT.</div>';exit;
		}

		$tReciboVenta=TReciboVenta::with(['treciboventadetalle', 'treciboventanotadebito.treciboventanotadebitodetalle'])->whereRaw('codigoOficina=? and codigoReciboVenta=?', [$sessionManager->get('codigoOficina'), $request->get('codigoReciboVenta')])->first();

		return view('reciboventanotadebito/insertar', ['tReciboVenta' => $tReciboVenta]);
	}

	public function actionImprimirComprobante(SessionManager $sessionManager, Application $application, $codigoReciboVentaNotaDebito)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		$tReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventa.tpersonal.tusuario'])->whereRaw('codigoReciboVentaNotaDebito=?', [$codigoReciboVentaNotaDebito])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaNotaDebito){ $q->whereRaw('codigoOficina=?', [$tReciboVentaNotaDebito->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 650]);
		}

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventanotadebito/notadebito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaDebito' => $tReciboVentaNotaDebito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaDebito->total), ''), 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($tEmpresa->ruc.'-08-'.$tReciboVentaNotaDebito->numeroRecibo.'.pdf', ['attachment' => false]);
	}

	public function actionDescargarPdfXml(SessionManager $sessionManager, Application $application, ResponseFactory $responseFactory, Encrypter $encrypter, $codigoReciboVentaNotaDebito)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}
		
		$tReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventa'])->whereRaw('codigoReciboVentaNotaDebito=?', [$codigoReciboVentaNotaDebito])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaNotaDebito){ $q->whereRaw('codigoOficina=?', [$tReciboVentaNotaDebito->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventanotadebito/notadebito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaDebito' => $tReciboVentaNotaDebito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaDebito->total), ''), 'base64Logo' => $base64Logo]));

		$rutaFolderTemp=public_path().'/temp';
		$dataTemp=uniqid();

		file_put_contents($rutaFolderTemp.'-'.$dataTemp.'.pdf', $pdf->output());

		$rutaZipTemp=$rutaFolderTemp.'/'.$tReciboVentaNotaDebito->numeroRecibo.'-'.$tEmpresa->ruc.'-'.$dataTemp.'.zip';
		$nameZip = $tReciboVentaNotaDebito->numeroRecibo.'-'.$tEmpresa->ruc. '.zip';

	    $zip=new ZipArchive();
	    
	    $zip->open($rutaZipTemp, ZipArchive::CREATE);

		$zip->addFile($rutaFolderTemp.'-'.$dataTemp.'.pdf', 'Nota de débito-'.$dataTemp.'.pdf');

		/*Begin: optencion de archivo XML*/
		
		if($tReciboVentaNotaDebito->treciboventa->tipoRecibo=='Factura')
		{
			$xmlName = $tEmpresa->ruc.'-08-'.$tReciboVentaNotaDebito->numeroRecibo.'.xml';
			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getfilesxmlaszip',
			[
				'form_params' =>
				[
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.codigoUnicoVenta' => $tReciboVentaNotaDebito->treciboventa->codigoReciboVenta,
					'dto.filesName' => [$xmlName],
					'dto.userNameEf' => $tEmpresa->userNameEf,
					'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf)
				]
			]);

			$dataResponse=(object)json_decode($response->getBody(), true);
			
			$dataResponse->mo=(object)($dataResponse->mo);

			if($dataResponse->mo->type!='success')
			{
				$zip->close();

				unlink($rutaZipTemp);
				unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');

				return $this->plataformHelper->redirectError('No se pudo recuperar correctamente el archivo XML; por favor, vuelva a intenarlo.', 'reciboventa/ver');
			}
			
			if(count((object)($dataResponse->dto)) > 0)
			{
				$zip->addFromString($xmlName . '.zip', base64_decode( ((object)($dataResponse->dto))->zipAsBase64 ));
			}
		}

		/*End: optencion de archivo XML*/

	    $zip->close();

		unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');

	    return $responseFactory->download($rutaZipTemp, $nameZip)->deleteFileAfterSend(true);
	}
}
?>