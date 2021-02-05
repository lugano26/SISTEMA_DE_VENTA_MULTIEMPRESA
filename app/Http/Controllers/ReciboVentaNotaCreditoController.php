<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use App\Helper\NumeroLetras;

use ZipArchive;
use DB;

use App\Model\TReciboVentaNotaCredito;
use App\Model\TReciboVentaNotaCreditoDetalle;
use App\Model\TReciboVentaNotaDebito;
use App\Model\TReciboVenta;
use App\Model\TEmpresa;
use App\Model\TOficina;
use App\Model\TOficinaProducto;
use App\Model\TCajaDetalle;

class ReciboVentaNotaCreditoController extends Controller
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
					return $this->plataformHelper->redirectError('No se puede generar nota de crédito. Es posible que la factura aún no haya sido comunicado a la SUNAT o que ésta haya sido rechazada.', 'reciboventa/ver');
				}

				if(TReciboVentaNotaDebito::whereRaw('codigoReciboVenta=? and estadoEnvioSunat!=?', [$tReciboVenta->codigoReciboVenta, 'Aprobado'])->first()!=null)
				{
					return $this->plataformHelper->redirectError('No se puede generar nota de crédito porque existen notas de débito pendientes de envío.', 'reciboventa/ver');
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

				$numeroComprobante=substr(TReciboVentaNotaCredito::whereRaw('mid(numeroRecibo, 1, 4)=?', [substr($tReciboVenta->tipoRecibo, 0, 1).str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante])->whereHas('treciboventa.toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->max('numeroRecibo'), 5)+1;

				$numeroComprobante=str_repeat('0', (8-strlen($numeroComprobante))).$numeroComprobante;
				$serieComprobante=str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante;
				
				$serieNumeroComprobante=$tipoComprobante.$serieComprobante.'-'.$numeroComprobante;

				/*End: Generación del número de comprobante, incluido la serie del mismo*/

				if(
					trim($request->input('hdSelectMotivo'))==''
				)
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/ver');
				}

				$tReciboVentaNotaCredito=new TReciboVentaNotaCredito();

				$tReciboVentaNotaCredito->codigoReciboVenta=$tReciboVenta->codigoReciboVenta;
				$tReciboVentaNotaCredito->codigoOficina=$sessionManager->get('codigoOficina');
				$tReciboVentaNotaCredito->codigoPersonal=$sessionManager->get('codigoPersonal');
				$tReciboVentaNotaCredito->isc=0;
				$tReciboVentaNotaCredito->igv=0;
				$tReciboVentaNotaCredito->impuestoAplicado=$request->input('hdImpuestoAplicado');
				$tReciboVentaNotaCredito->subTotal=$request->input('hdSubTotal');
				$tReciboVentaNotaCredito->total=$request->input('hdTotal');
				$tReciboVentaNotaCredito->numeroRecibo=$serieNumeroComprobante;
				$tReciboVentaNotaCredito->codigoMotivo=explode('_', $request->input('hdSelectMotivo'))[0];
				$tReciboVentaNotaCredito->descripcionMotivo=explode('_', $request->input('hdSelectMotivo'))[1];
				$tReciboVentaNotaCredito->fechaComprobanteEmitido=date('Y-m-d H:i:s');
				$tReciboVentaNotaCredito->hash='';
				$tReciboVentaNotaCredito->estadoEnvioSunat='Pendiente de envío';
				$tReciboVentaNotaCredito->codigoCdr='';
				$tReciboVentaNotaCredito->descripcionCdr='';

				$tReciboVentaNotaCredito->save();

				$ultimoRegistroTReciboVentaNotaCredito=TReciboVentaNotaCredito::whereRaw('codigoReciboVentaNotaCredito=(select max(codigoReciboVentaNotaCredito) from treciboventanotacredito)')->first();

				$iscFinalTemp=0;
				$igvFinalTemp=0;

				foreach($request->input('hdCodigoOficinaProducto') as $key => $value)
				{
					$tOficinaProducto=TOficinaProducto::find($request->input('hdCodigoOficinaProducto')[$key]);

					if($tOficinaProducto!=null)
					{
						if(!$tOficinaProducto->ventaMenorUnidad && !preg_match("/^[0-9]+(\.[0]*)?$/", $request->input('hdCantidadProducto')[$key]))
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten reembolsos por unidades enteras en el producto '.($key+1).' de la lista.', 'reciboventa/ver');
						}
					}

					if($request->input('hdTipoImpuestoProducto')[$key]=='ISC')
					{
						$iscFinalTemp+=$request->input('hdImpuestoAplicadoProducto')[$key];
					}
					else
					{
						$igvFinalTemp+=$request->input('hdImpuestoAplicadoProducto')[$key];
					}

					if(
						$request->input('hdCodigoOficinaProducto')[$key]==''
						|| trim($request->input('hdNombreProducto')[$key])==''
						|| !in_array($request->input('hdTipoProducto')[$key], ['Genérico', 'Comercial'])
						|| !in_array($request->input('hdSituacionImpuestoProducto')[$key], ['Afecto'])
						|| !in_array($request->input('hdTipoImpuestoProducto')[$key], ['IGV'])
						|| $request->input('hdPresentacionProducto')[$key]==''
						|| $request->input('hdUnidadMedidaProducto')[$key]==''
					)
					{
						DB::rollBack();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/ver');
					}

					$tReciboVentaNotaCreditoDetalle=new TReciboVentaNotaCreditoDetalle();

					$tReciboVentaNotaCreditoDetalle->codigoReciboVentaNotaCredito=$ultimoRegistroTReciboVentaNotaCredito->codigoReciboVentaNotaCredito;
					$tReciboVentaNotaCreditoDetalle->codigoOficinaProducto=$request->input('hdCodigoOficinaProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->codigoBarrasProducto=$request->input('hdCodigoBarrasProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->nombreProducto=trim($request->input('hdNombreProducto')[$key]);
					$tReciboVentaNotaCreditoDetalle->informacionAdicionalProducto=trim($request->input('hdInformacionAdicionalProducto')[$key]);
					$tReciboVentaNotaCreditoDetalle->descripcionProducto='';
					$tReciboVentaNotaCreditoDetalle->tipoProducto=$request->input('hdTipoProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->situacionImpuestoProducto=$request->input('hdSituacionImpuestoProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->tipoImpuestoProducto=$request->input('hdTipoImpuestoProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->porcentajeTributacionProducto=$request->input('hdPorcentajeTributacionProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->impuestoAplicadoProducto=$request->input('hdImpuestoAplicadoProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->categoriaProducto='';
					$tReciboVentaNotaCreditoDetalle->presentacionProducto=$request->input('hdPresentacionProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->unidadMedidaProducto=$request->input('hdUnidadMedidaProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->precioVentaTotalProducto=$request->input('hdPrecioVentaTotalProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->precioVentaUnitarioProducto=number_format($request->input('hdPrecioVentaTotalProducto')[$key]/$request->input('hdCantidadProducto')[$key], 2, '.', '');
					$tReciboVentaNotaCreditoDetalle->cantidadProducto=$request->input('hdCantidadProducto')[$key];
					$tReciboVentaNotaCreditoDetalle->cantidadBloqueProducto=12;
					$tReciboVentaNotaCreditoDetalle->unidadMedidaBloqueProducto='Docena';

					$tReciboVentaNotaCreditoDetalle->save();
				}

				$tCajaDetalle=TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->egresos+=($tReciboVentaNotaCredito->total*($tReciboVenta->divisa!='Soles' ? $tReciboVenta->tipoCambioUsd : 1));
				$tCajaDetalle->saldoFinal-=($tReciboVentaNotaCredito->total*($tReciboVenta->divisa!='Soles' ? $tReciboVenta->tipoCambioUsd : 1));

				$tCajaDetalle->save();

				$igvFinalTemp=($iscFinalTemp==0 ? $request->input('hdImpuestoAplicado') : $igvFinalTemp);

				$ultimoRegistroTReciboVentaNotaCredito->isc=number_format($iscFinalTemp, 2, '.', '');
				$ultimoRegistroTReciboVentaNotaCredito->igv=number_format($igvFinalTemp, 2, '.', '');

				$ultimoRegistroTReciboVentaNotaCredito->save();

				/*Begin: Cambio de estado de venta*/

				$tReciboVenta=TReciboVenta::with(['treciboventanotacredito' => function($q){ $q->whereRaw('estadoEnvioSunat!=?', ['Rechazado']); }, 'treciboventanotadebito' => function($q){ $q->whereRaw('estadoEnvioSunat!=?', ['Rechazado']); }])->whereRaw('codigoReciboVenta=?', [$tReciboVenta->codigoReciboVenta])->first();

				if((($tReciboVenta->total+$tReciboVenta->treciboventanotadebito->sum('total'))-($tReciboVenta->treciboventanotacredito->sum('total')))==0)
				{
					$tReciboVenta->estado=false;

					$tReciboVenta->save();
				}

				/*End: Cambio de estado de venta*/

				if((($tReciboVenta->total+$tReciboVenta->treciboventanotadebito->sum('total'))-($tReciboVenta->treciboventanotacredito->sum('total')))<0)
				{
					DB::rollback();
					
					return $this->plataformHelper->redirectError('No se puede generar nota de crédito porque el monto se reduciría por debajo de S/0.00.', 'reciboventa/ver');
				}

				/*Begin: Generación de archivo XML*/

				if($tReciboVenta->tipoRecibo=='Factura')
				{
					$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));
					$tReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventanotacreditodetalle'])->whereRaw('codigoReciboVentaNotaCredito=?', [$ultimoRegistroTReciboVentaNotaCredito->codigoReciboVentaNotaCredito])->first();

					$listaProductoEf=[];

					foreach($tReciboVentaNotaCredito->treciboventanotacreditodetalle as $value)
					{
						$objectTemp=new \stdClass();

						$objectTemp->cantidadProductoEf=$value->cantidadProducto;
						$objectTemp->subTotalVentaProductoEf=($value->precioVentaTotalProducto-$value->impuestoAplicadoProducto);
						$objectTemp->precioUnitarioVentaProductoEf=$value->precioVentaUnitarioProducto;
						$objectTemp->impuestoTotalVentaProductoEf=$value->impuestoAplicadoProducto;
						$objectTemp->porcentajeTributacionProductoEf=$value->porcentajeTributacionProducto;
						$objectTemp->subTotalPrecioUnitarioVentaProductoEf=$value->precioVentaUnitarioProducto/((($value->porcentajeTributacionProducto)/100)+1);

						$listaProductoEf[]=$objectTemp;
					}

					$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

					$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/creditnote/generatexml',
					[
						'form_params' =>
						[
							'dto.codigoUnicoVenta' => $tReciboVenta->codigoReciboVenta,
							'dto.serieNumeroComprobanteEf' => $tReciboVentaNotaCredito->numeroRecibo,
							'dto.codigoMotivoNota' => $tReciboVentaNotaCredito->codigoMotivo,
							'dto.descripcionMotivoNota' => $tReciboVentaNotaCredito->descripcionMotivo,
							'dto.serieNumeroComprobanteVentaEf' => $tReciboVenta->numeroRecibo,
							'dto.rucEmpresaEf' => $tEmpresa->ruc,
							'dto.razonSocialEmisorEf' => $tEmpresa->razonSocial,
							'dto.userNameEf' => $tEmpresa->userNameEf,
							'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
							'dto.documentoClienteEf' => $tReciboVenta->documentoCliente,
							'dto.denominacionClienteEf' => $tReciboVenta->nombreCompletoCliente,
							'dto.divisaEf' => $tReciboVenta->divisa,
							'dto.totalNotaEf' => $tReciboVentaNotaCredito->total,
							'dto.subTotalNotaEf' => $tReciboVentaNotaCredito->subTotal,
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

					$tReciboVentaNotaCredito->hash=$dataResponse->dto->hash;

					$tReciboVentaNotaCredito->save();
				}

				/*End: Generación de archivo XML*/

				$sessionManager->flash('codigoReciboVentaNotaCredito', $ultimoRegistroTReciboVentaNotaCredito->codigoReciboVentaNotaCredito);

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

		$tReciboVenta=TReciboVenta::with(['treciboventadetalle', 'treciboventanotacredito.treciboventanotacreditodetalle'])->whereRaw('codigoOficina=? and codigoReciboVenta=?', [$sessionManager->get('codigoOficina'), $request->get('codigoReciboVenta')])->first();

		return view('reciboventanotacredito/insertar', ['tReciboVenta' => $tReciboVenta]);
	}

	public function actionImprimirComprobante(SessionManager $sessionManager, Application $application, $codigoReciboVentaNotaCredito)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		$tReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventa.tpersonal.tusuario'])->whereRaw('codigoReciboVentaNotaCredito=?', [$codigoReciboVentaNotaCredito])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaNotaCredito){ $q->whereRaw('codigoOficina=?', [$tReciboVentaNotaCredito->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

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

		$pdf->loadHTML(view('reciboventanotacredito/notacredito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaCredito' => $tReciboVentaNotaCredito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaCredito->total), ''), 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($tEmpresa->ruc.'-07-'.$tReciboVentaNotaCredito->numeroRecibo.'.pdf', ['attachment' => false]);
	}

	public function actionDescargarPdfXml(SessionManager $sessionManager, Application $application, ResponseFactory $responseFactory, Encrypter $encrypter, $codigoReciboVentaNotaCredito)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}
		
		$tReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventa'])->whereRaw('codigoReciboVentaNotaCredito=?', [$codigoReciboVentaNotaCredito])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaNotaCredito){ $q->whereRaw('codigoOficina=?', [$tReciboVentaNotaCredito->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventanotacredito/notacredito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaCredito' => $tReciboVentaNotaCredito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaCredito->total), ''), 'base64Logo' => $base64Logo]));

		$rutaFolderTemp=public_path().'/temp';
		$dataTemp=uniqid();

		file_put_contents($rutaFolderTemp.'-'.$dataTemp.'.pdf', $pdf->output());

		$rutaZipTemp=$rutaFolderTemp.'/'.$tReciboVentaNotaCredito->numeroRecibo.'-'.$tEmpresa->ruc.'-'.$dataTemp.'.zip';
		$nameZip = $tReciboVentaNotaCredito->numeroRecibo.'-'.$tEmpresa->ruc. '.zip';

	    $zip=new ZipArchive();
	    
	    $zip->open($rutaZipTemp, ZipArchive::CREATE);

		$zip->addFile($rutaFolderTemp.'-'.$dataTemp.'.pdf', 'Nota de crédito-'.$dataTemp.'.pdf');

		/*Begin: optencion de archivo XML*/
		
		if($tReciboVentaNotaCredito->treciboventa->tipoRecibo=='Factura')
		{
			$xmlName = $tEmpresa->ruc.'-07-'.$tReciboVentaNotaCredito->numeroRecibo.'.xml';
			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getfilesxmlaszip',
			[
				'form_params' =>
				[
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.codigoUnicoVenta' => $tReciboVentaNotaCredito->treciboventa->codigoReciboVenta,
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