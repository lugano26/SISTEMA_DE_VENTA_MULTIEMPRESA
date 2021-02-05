<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Application;

use ZipArchive;
use DB;

use App\Model\TReciboVenta;
use App\Model\TReciboVentaGuiaRemision;
use App\Model\TReciboVentaGuiaRemisionDetalle;
use App\Model\TEmpresa;
use App\Model\TOficina;

class ReciboVentaGuiaRemisionController extends Controller
{
	public function actionGestionarGuiaRemision(Request $request, SessionManager $sessionManager, Encrypter $encrypter)
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

				$tReciboVenta=TReciboVenta::with(['treciboventadetalle'])->whereRaw('codigoReciboVenta=?', [$request->input('hdCodigoReciboVenta')])->first();

				if($tReciboVenta->tipoRecibo!='Factura')
				{
					return $this->plataformHelper->redirectError('Las notas de crédito sólo pueden ser generadas a partir de facturas.', 'reciboventa/ver');
				}

				if($tReciboVenta->estadoEnvioSunat!='Aprobado')
				{
					return $this->plataformHelper->redirectError('No se puede generar nota de crédito. Es posible que la factura aún no haya sido comunicado a la SUNAT o que ésta haya sido rechazada.', 'reciboventa/ver');
				}

				if(!$tReciboVenta->estado)
				{
					return $this->plataformHelper->redirectError('No se puede generar guías de remisión de una venta anulada.', '/');
				}

				/*Begin: Generación del número de comprobante, incluido la serie del mismo*/

				$tipoComprobante='T';

				$serieComprobante=0;

				$listaTOfcinaTemp=TOficina::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->orderBy('codigoOficina', 'asc')->get();

				foreach($listaTOfcinaTemp as $key => $value)
				{
					$serieComprobante++;

					if($value->codigoOficina==$sessionManager->get('codigoOficina'))
					{
						break;
					}
				}

				$numeroComprobante=substr(TReciboVentaGuiaRemision::whereRaw('mid(numeroGuiaRemision, 2, 3)=?', [str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante])->whereHas('treciboventa.toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->max('numeroGuiaRemision'), 5)+1;

				$serieComprobante=str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante;
				$numeroComprobante=str_repeat('0', (8-strlen($numeroComprobante))).$numeroComprobante;
				
				$serieNumeroComprobante=$tipoComprobante.$serieComprobante.'-'.$numeroComprobante;

				/*End: Generación del número de comprobante, incluido la serie del mismo*/

				$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));

				if(
					$request->input('txtDocumentoReceptorGuiaRemision')==''
					|| trim($request->input('txtNombreCompletoReceptorGuiaRemision'))==''
					|| $request->input('txtDocumentoTransportistaGuiaRemision')==''
					|| trim($request->input('txtNombreCompletoTransportistaGuiaRemision'))==''
					|| $request->input('txtDniConductorTransportistaGuiaRemision')==''
					|| trim($request->input('txtPlacaVehiculoTransportistaGuiaRemision'))==''
					|| $request->input('selectMotivoTrasladoGuiaRemision')==''
					|| $request->input('selectUbigeoPartidaGuiaRemision')==''
					|| trim($request->input('txtDireccionPartidaGuiaRemision'))==''
					|| $request->input('selectUbigeoLlegadaGuiaRemision')==''
					|| trim($request->input('txtDireccionLlegadaGuiaRemision'))==''
				)
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/ver');
				}

				$tReciboVentaGuiaRemision=new TReciboVentaGuiaRemision();

				$tReciboVentaGuiaRemision->codigoReciboVenta=$tReciboVenta->codigoReciboVenta;
				$tReciboVentaGuiaRemision->codigoOficina=$sessionManager->get('codigoOficina');
				$tReciboVentaGuiaRemision->documentoReceptor=$request->input('txtDocumentoReceptorGuiaRemision');
				$tReciboVentaGuiaRemision->nombreCompletoReceptor=trim($request->input('txtNombreCompletoReceptorGuiaRemision'));
				$tReciboVentaGuiaRemision->documentoTransportista=$request->input('txtDocumentoTransportistaGuiaRemision');
				$tReciboVentaGuiaRemision->nombreCompletoTransportista=trim($request->input('txtNombreCompletoTransportistaGuiaRemision'));
				$tReciboVentaGuiaRemision->dniConductorTransportista=$request->input('txtDniConductorTransportistaGuiaRemision');
				$tReciboVentaGuiaRemision->placaVehiculoTransportista=trim($request->input('txtPlacaVehiculoTransportistaGuiaRemision'));
				$tReciboVentaGuiaRemision->numeroContenedorTransporte=$request->input('txtNumeroContenedorTransporteGuiaRemision');
				$tReciboVentaGuiaRemision->pesoBrutoKilosBienes=$request->input('txtPesoBrutoKilosBienesGuiaRemision');
				$tReciboVentaGuiaRemision->fechaIniciaTraslado=$request->input('dateFechaIniciaTrasladoGuiaRemision');
				$tReciboVentaGuiaRemision->motivoTraslado=$request->input('selectMotivoTrasladoGuiaRemision');
				$tReciboVentaGuiaRemision->ubigeoPartida=$request->input('selectUbigeoPartidaGuiaRemision');
				$tReciboVentaGuiaRemision->direccionPartida=trim($request->input('txtDireccionPartidaGuiaRemision'));
				$tReciboVentaGuiaRemision->ubigeoLlegada=$request->input('selectUbigeoLlegadaGuiaRemision');
				$tReciboVentaGuiaRemision->direccionLlegada=trim($request->input('txtDireccionLlegadaGuiaRemision'));
				$tReciboVentaGuiaRemision->observacion=trim($request->input('txtObservacion'));
				$tReciboVentaGuiaRemision->numeroGuiaRemision=$serieNumeroComprobante;
				$tReciboVentaGuiaRemision->hash='';
				$tReciboVentaGuiaRemision->estadoEnvioSunat='Pendiente de envío';
				$tReciboVentaGuiaRemision->codigoCdr='';
				$tReciboVentaGuiaRemision->descripcionCdr='';

				$tReciboVentaGuiaRemision->save();

				$ultimoRegistroTReciboVentaGuiaRemision=TReciboVentaGuiaRemision::whereRaw('codigoReciboVentaGuiaRemision=(select max(codigoReciboVentaGuiaRemision) from treciboventaguiaremision)')->first();

				foreach($request->input('hdCodigoOficinaProducto') as $key => $value)
				{
					if(
						$request->input('hdCodigoOficinaProducto')[$key]==''
						|| $request->input('hdNombreProducto')[$key]==''
						|| $request->input('hdUnidadMedidaProducto')[$key]==''
					)
					{
						DB::rollBack();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/ver');
					}

					$tReciboVentaGuiaRemisionDetalle=new TReciboVentaGuiaRemisionDetalle();

					$tReciboVentaGuiaRemisionDetalle->codigoReciboVentaGuiaRemision=$ultimoRegistroTReciboVentaGuiaRemision->codigoReciboVentaGuiaRemision;
					$tReciboVentaGuiaRemisionDetalle->codigoOficinaProducto=$request->input('hdCodigoOficinaProducto')[$key];
					$tReciboVentaGuiaRemisionDetalle->codigoBarrasProducto=$request->input('hdCodigoBarrasProducto')[$key];
					$tReciboVentaGuiaRemisionDetalle->nombreProducto=$request->input('hdNombreProducto')[$key];
					$tReciboVentaGuiaRemisionDetalle->informacionAdicionalProducto=$request->input('hdInformacionAdicionalProducto')[$key];
					$tReciboVentaGuiaRemisionDetalle->unidadMedidaProducto=$request->input('hdUnidadMedidaProducto')[$key];
					$tReciboVentaGuiaRemisionDetalle->cantidadProducto=$request->input('hdCantidadProducto')[$key];
					$tReciboVentaGuiaRemisionDetalle->pesoKilos=$request->input('hdPesoKilos')[$key];

					$tReciboVentaGuiaRemisionDetalle->save();
				}

				/*Begin: Generación de archivo XML*/

				$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));
				$ultimoRegistroTReciboVentaGuiaRemision=TReciboVentaGuiaRemision::with(['treciboventa', 'treciboventaguiaremisiondetalle'])->whereRaw('codigoReciboVentaGuiaRemision=?', [$ultimoRegistroTReciboVentaGuiaRemision->codigoReciboVentaGuiaRemision])->first();

				$listaProductoEf=[];

				foreach($ultimoRegistroTReciboVentaGuiaRemision->treciboventaguiaremisiondetalle as $value)
				{
					$objectTemp=new \stdClass();

					$objectTemp->codigoOficinaProductoEf=$value->codigoOficinaProducto;
					$objectTemp->nombreProductoEf=trim($value->nombreProducto.' '.$value->informacionAdicionalProducto);
					$objectTemp->cantidadProductoEf=$value->cantidadProducto;

					$listaProductoEf[]=$objectTemp;
				}

				$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

				$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/referralguide/generatexml',
				[
					'form_params' =>
					[
						'dto.codigoUnicoVenta' => $ultimoRegistroTReciboVentaGuiaRemision->treciboventa->codigoReciboVenta,
						'dto.rucEmpresaEf' => $tEmpresa->ruc,
						'dto.userNameEf' => $tEmpresa->userNameEf,
						'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
						'dto.razonSocialEmisorEf' => $tEmpresa->razonSocial,
						'dto.serieNumeroComprobanteEf' => $ultimoRegistroTReciboVentaGuiaRemision->numeroGuiaRemision,
						'dto.observacionEf' => $ultimoRegistroTReciboVentaGuiaRemision->observacion,
						'dto.numeroReciboVentaEf' => $ultimoRegistroTReciboVentaGuiaRemision->treciboventa->numeroRecibo,
						'dto.documentoReceptorEf' => $ultimoRegistroTReciboVentaGuiaRemision->documentoReceptor,
						'dto.nombreCompletoReceptorEf' => $ultimoRegistroTReciboVentaGuiaRemision->nombreCompletoReceptor,
						'dto.motivoTrasladoEf' => $ultimoRegistroTReciboVentaGuiaRemision->motivoTraslado,
						'dto.pesoBrutoKilosBienesEf' => $ultimoRegistroTReciboVentaGuiaRemision->pesoBrutoKilosBienes,
						'dto.fechaIniciaTrasladoEf' => $ultimoRegistroTReciboVentaGuiaRemision->fechaIniciaTraslado,
						'dto.placaVehiculoTransportistaEf' => $ultimoRegistroTReciboVentaGuiaRemision->placaVehiculoTransportista,
						'dto.dniConductorTransportistaEf' => $ultimoRegistroTReciboVentaGuiaRemision->dniConductorTransportista,
						'dto.ubigeoPartidaEf' => $ultimoRegistroTReciboVentaGuiaRemision->ubigeoPartida,
						'dto.direccionPartidaEf' => $ultimoRegistroTReciboVentaGuiaRemision->direccionPartida,
						'dto.ubigeoLlegadaEf' => $ultimoRegistroTReciboVentaGuiaRemision->ubigeoLlegada,
						'dto.direccionLlegadaEf' => $ultimoRegistroTReciboVentaGuiaRemision->direccionLlegada,
						'dto.numeroContenedorTransporteEf' => $ultimoRegistroTReciboVentaGuiaRemision->numeroContenedorTransporte,
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

				$ultimoRegistroTReciboVentaGuiaRemision->hash=$dataResponse->dto->hash;

				$ultimoRegistroTReciboVentaGuiaRemision->save();

				/*End: Generación de archivo XML*/

				$sessionManager->flash('codigoReciboVentaGuiaRemision', $ultimoRegistroTReciboVentaGuiaRemision->codigoReciboVentaGuiaRemision);

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

		$tReciboVenta=TReciboVenta::with(['treciboventadetalle'])->find($request->input('codigoReciboVenta'));
		$listaTReciboVentaGuiaRemision=TReciboVentaGuiaRemision::with(['treciboventaguiaremisiondetalle'])->whereRaw('codigoReciboVenta=?', [$request->input('codigoReciboVenta')])->get();

		if(!$tReciboVenta->estado)
		{
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No se puede generar guías de remisión de una venta anulada.</div>';exit;
		}

		return view('reciboventaguiaremision/gestionarguiaremision', ['tReciboVenta' => $tReciboVenta, 'listaTReciboVentaGuiaRemision' => $listaTReciboVentaGuiaRemision]);
	}

	public function actionDescargarPdfXml(SessionManager $sessionManager, Application $application, ResponseFactory $responseFactory,  Encrypter $encrypter, $codigoReciboVentaGuiaRemision)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}
		
		$tReciboVentaGuiaRemision=TReciboVentaGuiaRemision::with(['treciboventaguiaremisiondetalle', 'treciboventa'])->whereRaw('codigoReciboVentaGuiaRemision=?', [$codigoReciboVentaGuiaRemision])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaGuiaRemision){ $q->whereRaw('codigoOficina=?', [$tReciboVentaGuiaRemision->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$pdf=$application->make('dompdf.wrapper');

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventaguiaremision/guiaremision', ['tEmpresa' => $tEmpresa, 'tReciboVentaGuiaRemision' => $tReciboVentaGuiaRemision, 'base64Logo' => $base64Logo]));

		$rutaFolderTemp=public_path().'/temp';
		$dataTemp=uniqid();

		file_put_contents($rutaFolderTemp.'-'.$dataTemp.'.pdf', $pdf->output());

		$rutaZipTemp=$rutaFolderTemp.'/'.$tReciboVentaGuiaRemision->numeroGuiaRemision.'-'.$tEmpresa->ruc.'-'.$dataTemp.'.zip';
		$nameZip = $tReciboVentaGuiaRemision->numeroGuiaRemision.'-'.$tEmpresa->ruc.'.zip';
	    $zip=new ZipArchive();
	    
	    $zip->open($rutaZipTemp, ZipArchive::CREATE);

		$zip->addFile($rutaFolderTemp.'-'.$dataTemp.'.pdf', 'Guía de remisión remitente-'.$dataTemp.'.pdf');

		/*Begin: optencion de archivo XML*/
		
		if($tReciboVentaGuiaRemision->treciboventa->tipoRecibo=='Factura' || $tReciboVentaGuiaRemision->treciboventa->tipoRecibo=='Boleta')
		{
			$xmlName = $tEmpresa->ruc.'-31-'.$tReciboVentaGuiaRemision->numeroGuiaRemision.'.xml';
			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getfilesxmlaszip',
			[
				'form_params' =>
				[
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.codigoUnicoVenta' => $tReciboVentaGuiaRemision->treciboventa->codigoReciboVenta,
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

	public function actionImprimirComprobante(SessionManager $sessionManager, Application $application, $codigoReciboVentaGuiaRemision)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		$tReciboVentaGuiaRemision=TReciboVentaGuiaRemision::with(['treciboventaguiaremisiondetalle', 'treciboventa', 'tubigeopartida', 'tubigeollegada'])->whereRaw('codigoReciboVentaGuiaRemision=?', [$codigoReciboVentaGuiaRemision])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaGuiaRemision){ $q->whereRaw('codigoOficina=?', [$tReciboVentaGuiaRemision->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$pdf=$application->make('dompdf.wrapper');

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventaguiaremision/guiaremision', ['tEmpresa' => $tEmpresa, 'tReciboVentaGuiaRemision' => $tReciboVentaGuiaRemision, 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($tEmpresa->ruc.'-31-'.$tReciboVentaGuiaRemision->numeroGuiaRemision.'.pdf', ['attachment' => false]);
	}
}
?>