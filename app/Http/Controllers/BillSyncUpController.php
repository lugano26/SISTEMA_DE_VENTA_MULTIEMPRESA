<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;

use DB;

use App\Model\TEmpresa;
use App\Model\TReciboVenta;
use App\Model\TOficinaProducto;
use App\Model\TDocumentoGeneradoSunat;
use App\Model\TReciboVentaNotaCredito;
use App\Model\TReciboVentaNotaDebito;
use App\Model\TReciboVentaGuiaRemision;
use App\Model\TCajaDetalle;
use App\Model\TUsuarioNotificacion;

class BillSyncUpController extends Controller
{
	public function actionSync(SessionManager $sessionManager, Encrypter $encrypter)
	{
		DB::beginTransaction();

		$verifySyncUp=true;

		$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));

		$tReciboVenta=TReciboVenta::with(['treciboventadetalle'])->whereRaw('codigoOficina=? and codigoCdr=? and tipoRecibo=?', [$sessionManager->get('codigoOficina'), '', 'Factura'])->orderBy('numeroRecibo', 'asc')->first();
		$tReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventa', 'treciboventanotacreditodetalle'])->whereRaw('codigoCdr=?', [''])->whereHas('treciboventa', function($q) use($sessionManager){ $q->whereRaw('codigoOficina=? and tipoRecibo=?', [$sessionManager->get('codigoOficina'), 'Factura']); })->orderBy('numeroRecibo', 'asc')->first();
		$tReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventa', 'treciboventanotadebitodetalle'])->whereRaw('codigoCdr=?', [''])->whereHas('treciboventa', function($q) use($sessionManager){ $q->whereRaw('codigoOficina=? and tipoRecibo=?', [$sessionManager->get('codigoOficina'), 'Factura']); })->orderBy('numeroRecibo', 'asc')->first();
		$tReciboVentaGuiaRemision=TReciboVentaGuiaRemision::with(['treciboventa', 'treciboventaguiaremisiondetalle'])->whereRaw('codigoCdr=?', [''])->whereHas('treciboventa', function($q) use($sessionManager){ $q->whereRaw('codigoOficina=? and tipoRecibo=?', [$sessionManager->get('codigoOficina'), 'Factura']); })->orderBy('numeroGuiaRemision', 'asc')->first();

		if($verifySyncUp && $tReciboVenta!=null)
		{
			$verifySyncUp=false;

			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/send',
			[
				'form_params' =>
				[
					'dto.codigoUnicoVenta' => $tReciboVenta->codigoReciboVenta,
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.userNameEf' => $tEmpresa->userNameEf,
					'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
					'dto.serieNumeroComprobanteEf' => $tReciboVenta->numeroRecibo,
					'dto.demoEf' => $tEmpresa->demo
				]
			]);

			$dataResponse=(object)json_decode($response->getBody(), true);

			$dataResponse->mo=(object)($dataResponse->mo);
			$dataResponse->dto=(object)($dataResponse->dto);

			if($dataResponse->mo->type!='success')
			{
				return response()->json($dataResponse);
			}

			$tReciboVenta->codigoCdr=$dataResponse->dto->codigoCdr;
			$tReciboVenta->estadoEnvioSunat=$dataResponse->dto->estadoEnvioSunat;
			$tReciboVenta->descripcionCdr=$dataResponse->dto->descripcionCdr;

			$tReciboVenta->save();

			$tDocumentoGeneradoSunat=new TDocumentoGeneradoSunat();

			$tDocumentoGeneradoSunat->codigoEmpresa=$sessionManager->get('codigoEmpresa');
			$tDocumentoGeneradoSunat->responseCode=$tReciboVenta->codigoCdr;
			$tDocumentoGeneradoSunat->responseDescription=$tReciboVenta->descripcionCdr;
			$tDocumentoGeneradoSunat->documento=$tReciboVenta->documentoCliente;
			$tDocumentoGeneradoSunat->nombre=$tReciboVenta->nombreCompletoCliente;
			$tDocumentoGeneradoSunat->numeroComprobante=$tReciboVenta->numeroRecibo;
			$tDocumentoGeneradoSunat->numeroComprobanteAfectado='';
			$tDocumentoGeneradoSunat->tipo='Factura';
			$tDocumentoGeneradoSunat->estado=$tReciboVenta->estadoEnvioSunat;

			$tDocumentoGeneradoSunat->save();

			if($tReciboVenta->estadoEnvioSunat=='Rechazado')
			{
				foreach($tReciboVenta->treciboventadetalle as $value)
				{
					$tOficinaProducto=TOficinaProducto::whereRaw('codigoOficinaProducto=?', [$value->codigoOficinaProducto])->first();

					if($tOficinaProducto!=null)
					{
						$tOficinaProducto->cantidad+=$value->cantidadProducto;

						$tOficinaProducto->save();
					}
				}

				$tCajaDetalle=TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->egresos+=($tReciboVenta->total*($tReciboVenta->divisa!='Soles' ? $tReciboVenta->tipoCambioUsd : 1));
				$tCajaDetalle->saldoFinal-=($tReciboVenta->total*($tReciboVenta->divisa!='Soles' ? $tReciboVenta->tipoCambioUsd : 1));

				$tCajaDetalle->save();

				$tUsuarioNotificacion=new TUsuarioNotificacion();
					
				$tUsuarioNotificacion->codigoPersonal=$tReciboVenta->codigoPersonal;
				$tUsuarioNotificacion->descripcion='La factura Nº ['.$tReciboVenta->numeroRecibo.'] ha sido rechazada. <<'.$tReciboVenta->descripcionCdr.'>>';
				$tUsuarioNotificacion->permanente=false;
				$tUsuarioNotificacion->fechaInicioPeriodo=null;
				$tUsuarioNotificacion->fechaFinPeriodo=null;
				$tUsuarioNotificacion->url='';
				$tUsuarioNotificacion->estado=false;

				$tUsuarioNotificacion->save();
			}

			$this->_so->dto=new \stdClass();

			$this->_so->dto->tipoComprobante='Factura';
			$this->_so->dto->codigoRegistro=$tReciboVenta->codigoReciboVenta;
			$this->_so->dto->estadoEnvio=$dataResponse->dto->estadoEnvioSunat;

			$this->_so->mo=$dataResponse->mo;
		}

		if($verifySyncUp && $tReciboVentaNotaCredito!=null)
		{
			$verifySyncUp=false;

			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/creditnote/send',
			[
				'form_params' =>
				[
					'dto.codigoUnicoVenta' => $tReciboVentaNotaCredito->codigoReciboVenta,
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.userNameEf' => $tEmpresa->userNameEf,
					'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
					'dto.serieNumeroComprobanteEf' => $tReciboVentaNotaCredito->numeroRecibo,
					'dto.demoEf' => $tEmpresa->demo
				]
			]);

			$dataResponse=(object)json_decode($response->getBody(), true);

			$dataResponse->mo=(object)($dataResponse->mo);
			$dataResponse->dto=(object)($dataResponse->dto);

			if($dataResponse->mo->type!='success')
			{
				return response()->json($dataResponse);
			}

			$tReciboVentaNotaCredito->codigoCdr=$dataResponse->dto->codigoCdr;
			$tReciboVentaNotaCredito->estadoEnvioSunat=$dataResponse->dto->estadoEnvioSunat;
			$tReciboVentaNotaCredito->descripcionCdr=$dataResponse->dto->descripcionCdr;

			$tReciboVentaNotaCredito->save();

			$tDocumentoGeneradoSunat=new TDocumentoGeneradoSunat();

			$tDocumentoGeneradoSunat->codigoEmpresa=$sessionManager->get('codigoEmpresa');
			$tDocumentoGeneradoSunat->responseCode=$tReciboVentaNotaCredito->codigoCdr;
			$tDocumentoGeneradoSunat->responseDescription=$tReciboVentaNotaCredito->descripcionCdr;
			$tDocumentoGeneradoSunat->documento=$tReciboVentaNotaCredito->treciboventa->documentoCliente;
			$tDocumentoGeneradoSunat->nombre=$tReciboVentaNotaCredito->treciboventa->nombreCompletoCliente;
			$tDocumentoGeneradoSunat->numeroComprobante=$tReciboVentaNotaCredito->numeroRecibo;
			$tDocumentoGeneradoSunat->numeroComprobanteAfectado=$tReciboVentaNotaCredito->treciboventa->numeroRecibo;
			$tDocumentoGeneradoSunat->tipo='Nota de crédito';
			$tDocumentoGeneradoSunat->estado=$tReciboVentaNotaCredito->estadoEnvioSunat;

			$tDocumentoGeneradoSunat->save();

			if($tReciboVentaNotaCredito->estadoEnvioSunat=='Rechazado')
			{
				foreach($tReciboVentaNotaCredito->treciboventanotacreditodetalle as $value)
				{
					$tOficinaProducto=TOficinaProducto::whereRaw('codigoOficinaProducto=?', [$value->codigoOficinaProducto])->first();

					if($tOficinaProducto!=null)
					{
						$tOficinaProducto->cantidad-=$value->cantidadProducto;

						$tOficinaProducto->save();
					}
				}

				$tCajaDetalle=TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->ingresos+=($tReciboVentaNotaCredito->total*($tReciboVentaNotaCredito->treciboventa->divisa!='Soles' ? $tReciboVentaNotaCredito->treciboventa->tipoCambioUsd : 1));
				$tCajaDetalle->saldoFinal+=($tReciboVentaNotaCredito->total*($tReciboVentaNotaCredito->treciboventa->divisa!='Soles' ? $tReciboVentaNotaCredito->treciboventa->tipoCambioUsd : 1));

				$tCajaDetalle->save();

				/*Begin: Cambio de estado de venta*/

				$tReciboVentaTemp=TReciboVenta::whereRaw('codigoReciboVenta=?', [$tReciboVentaNotaCredito->treciboventa->codigoReciboVenta])->first();

				$tReciboVentaTemp->estado=true;

				$tReciboVentaTemp->save();

				/*End: Cambio de estado de venta*/

				$tUsuarioNotificacion=new TUsuarioNotificacion();
					
				$tUsuarioNotificacion->codigoPersonal=$tReciboVentaTemp->codigoPersonal;
				$tUsuarioNotificacion->descripcion='La nota de crédito Nº ['.$tReciboVentaNotaCredito->numeroRecibo.'] ha sido rechazada. <<'.$tReciboVentaNotaCredito->descripcionCdr.'>>';
				$tUsuarioNotificacion->permanente=false;
				$tUsuarioNotificacion->fechaInicioPeriodo=null;
				$tUsuarioNotificacion->fechaFinPeriodo=null;
				$tUsuarioNotificacion->url='';
				$tUsuarioNotificacion->estado=false;

				$tUsuarioNotificacion->save();
			}

			$this->_so->dto=new \stdClass();

			$this->_so->dto->tipoComprobante='Nota de crédito';
			$this->_so->dto->codigoRegistro=$tReciboVentaNotaCredito->codigoReciboVentaNotaCredito;
			$this->_so->dto->estadoEnvio=$dataResponse->dto->estadoEnvioSunat;

			$this->_so->mo=$dataResponse->mo;
		}

		if($verifySyncUp && $tReciboVentaNotaDebito!=null)
		{
			$verifySyncUp=false;

			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/debitnote/send',
			[
				'form_params' =>
				[
					'dto.codigoUnicoVenta' => $tReciboVentaNotaDebito->codigoReciboVenta,
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.userNameEf' => $tEmpresa->userNameEf,
					'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
					'dto.serieNumeroComprobanteEf' => $tReciboVentaNotaDebito->numeroRecibo,
					'dto.demoEf' => $tEmpresa->demo
				]
			]);

			$dataResponse=(object)json_decode($response->getBody(), true);

			$dataResponse->mo=(object)($dataResponse->mo);
			$dataResponse->dto=(object)($dataResponse->dto);

			if($dataResponse->mo->type!='success')
			{
				return response()->json($dataResponse);
			}

			$tReciboVentaNotaDebito->codigoCdr=$dataResponse->dto->codigoCdr;
			$tReciboVentaNotaDebito->estadoEnvioSunat=$dataResponse->dto->estadoEnvioSunat;
			$tReciboVentaNotaDebito->descripcionCdr=$dataResponse->dto->descripcionCdr;

			$tReciboVentaNotaDebito->save();

			$tDocumentoGeneradoSunat=new TDocumentoGeneradoSunat();

			$tDocumentoGeneradoSunat->codigoEmpresa=$sessionManager->get('codigoEmpresa');
			$tDocumentoGeneradoSunat->responseCode=$tReciboVentaNotaDebito->codigoCdr;
			$tDocumentoGeneradoSunat->responseDescription=$tReciboVentaNotaDebito->descripcionCdr;
			$tDocumentoGeneradoSunat->documento=$tReciboVentaNotaDebito->treciboventa->documentoCliente;
			$tDocumentoGeneradoSunat->nombre=$tReciboVentaNotaDebito->treciboventa->nombreCompletoCliente;
			$tDocumentoGeneradoSunat->numeroComprobante=$tReciboVentaNotaDebito->numeroRecibo;
			$tDocumentoGeneradoSunat->numeroComprobanteAfectado=$tReciboVentaNotaDebito->treciboventa->numeroRecibo;
			$tDocumentoGeneradoSunat->tipo='Nota de débito';
			$tDocumentoGeneradoSunat->estado=$tReciboVentaNotaDebito->estadoEnvioSunat;

			$tDocumentoGeneradoSunat->save();

			if($tReciboVentaNotaDebito->estadoEnvioSunat=='Rechazado')
			{
				foreach($tReciboVentaNotaDebito->treciboventanotadebitodetalle as $value)
				{
					$tOficinaProducto=TOficinaProducto::whereRaw('codigoOficinaProducto=?', [$value->codigoOficinaProducto])->first();

					if($tOficinaProducto!=null)
					{
						$tOficinaProducto->cantidad-=$value->cantidadProducto;

						$tOficinaProducto->save();
					}
				}

				$tCajaDetalle=TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->ingresos+=($tReciboVentaNotaDebito->total*($tReciboVentaNotaDebito->treciboventa->divisa!='Soles' ? $tReciboVentaNotaDebito->treciboventa->tipoCambioUsd : 1));
				$tCajaDetalle->saldoFinal+=($tReciboVentaNotaDebito->total*($tReciboVentaNotaDebito->treciboventa->divisa!='Soles' ? $tReciboVentaNotaDebito->treciboventa->tipoCambioUsd : 1));

				$tCajaDetalle->save();

				/*Begin: Cambio de estado de venta*/

				$tReciboVentaTemp=TReciboVenta::with(['treciboventanotacredito' => function($q){ $q->whereRaw('estadoEnvioSunat!=?', ['Rechazado']); }, 'treciboventanotadebito' => function($q){ $q->whereRaw('estadoEnvioSunat!=?', ['Rechazado']); }])->whereRaw('codigoReciboVenta=?', [$tReciboVentaNotaDebito->treciboventa->codigoReciboVenta])->first();

				if((($tReciboVentaTemp->total+$tReciboVentaTemp->treciboventanotadebito->sum('total'))-($tReciboVentaTemp->treciboventanotacredito->sum('total')))==0)
				{
					$tReciboVentaTemp->estado=false;

					$tReciboVentaTemp->save();
				}

				/*End: Cambio de estado de venta*/

				$tUsuarioNotificacion=new TUsuarioNotificacion();
					
				$tUsuarioNotificacion->codigoPersonal=$tReciboVentaTemp->codigoPersonal;
				$tUsuarioNotificacion->descripcion='La nota de débito Nº ['.$tReciboVentaNotaDebito->numeroRecibo.'] ha sido rechazada. <<'.$tReciboVentaNotaDebito->descripcionCdr.'>>';
				$tUsuarioNotificacion->permanente=false;
				$tUsuarioNotificacion->fechaInicioPeriodo=null;
				$tUsuarioNotificacion->fechaFinPeriodo=null;
				$tUsuarioNotificacion->url='';
				$tUsuarioNotificacion->estado=false;

				$tUsuarioNotificacion->save();
			}

			$this->_so->dto=new \stdClass();

			$this->_so->dto->tipoComprobante='Nota de débito';
			$this->_so->dto->codigoRegistro=$tReciboVentaNotaDebito->codigoReciboVentaNotaDebito;
			$this->_so->dto->estadoEnvio=$dataResponse->dto->estadoEnvioSunat;

			$this->_so->mo=$dataResponse->mo;
		}

		if($verifySyncUp && $tReciboVentaGuiaRemision!=null)
		{
			$verifySyncUp=false;

			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/referralguide/send',
			[
				'form_params' =>
				[
					'dto.codigoUnicoVenta' => $tReciboVentaGuiaRemision->treciboventa->codigoReciboVenta,
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.userNameEf' => $tEmpresa->userNameEf,
					'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
					'dto.serieNumeroComprobanteEf' => $tReciboVentaGuiaRemision->numeroGuiaRemision,
					'dto.demoEf' => $tEmpresa->demo
				]
			]);

			$dataResponse=(object)json_decode($response->getBody(), true);

			$dataResponse->mo=(object)($dataResponse->mo);
			$dataResponse->dto=(object)($dataResponse->dto);

			if($dataResponse->mo->type!='success')
			{
				return response()->json($dataResponse);
			}

			$tReciboVentaGuiaRemision->codigoCdr=$dataResponse->dto->codigoCdr;
			$tReciboVentaGuiaRemision->estadoEnvioSunat=$dataResponse->dto->estadoEnvioSunat;
			$tReciboVentaGuiaRemision->descripcionCdr=$dataResponse->dto->descripcionCdr;

			$tReciboVentaGuiaRemision->save();

			$tDocumentoGeneradoSunat=new TDocumentoGeneradoSunat();

			$tDocumentoGeneradoSunat->codigoEmpresa=$sessionManager->get('codigoEmpresa');
			$tDocumentoGeneradoSunat->responseCode=$tReciboVentaGuiaRemision->codigoCdr;
			$tDocumentoGeneradoSunat->responseDescription=$tReciboVentaGuiaRemision->descripcionCdr;
			$tDocumentoGeneradoSunat->documento=$tReciboVentaGuiaRemision->treciboventa->documentoCliente;
			$tDocumentoGeneradoSunat->nombre=$tReciboVentaGuiaRemision->treciboventa->nombreCompletoCliente;
			$tDocumentoGeneradoSunat->numeroComprobante=$tReciboVentaGuiaRemision->numeroGuiaRemision;
			$tDocumentoGeneradoSunat->numeroComprobanteAfectado=$tReciboVentaGuiaRemision->treciboventa->numeroRecibo;
			$tDocumentoGeneradoSunat->tipo='Guía de remisión de remitente';
			$tDocumentoGeneradoSunat->estado=$tReciboVentaGuiaRemision->estadoEnvioSunat;

			$tDocumentoGeneradoSunat->save();

			if($tReciboVentaGuiaRemision->estadoEnvioSunat=='Rechazado')
			{
				$tUsuarioNotificacion=new TUsuarioNotificacion();
					
				$tUsuarioNotificacion->codigoPersonal=$tReciboVentaGuiaRemision->treciboventa->codigoPersonal;
				$tUsuarioNotificacion->descripcion='La guía de remisión de remitente Nº ['.$tReciboVentaGuiaRemision->numeroGuiaRemision.'] ha sido rechazada. <<'.$tReciboVentaGuiaRemision->descripcionCdr.'>>';
				$tUsuarioNotificacion->permanente=false;
				$tUsuarioNotificacion->fechaInicioPeriodo=null;
				$tUsuarioNotificacion->fechaFinPeriodo=null;
				$tUsuarioNotificacion->url='';
				$tUsuarioNotificacion->estado=false;

				$tUsuarioNotificacion->save();
			}

			$this->_so->dto=new \stdClass();

			$this->_so->dto->tipoComprobante='Guía de remisión de remitente';
			$this->_so->dto->codigoRegistro=$tReciboVentaGuiaRemision->codigoReciboVentaGuiaRemision;
			$this->_so->dto->estadoEnvio=$dataResponse->dto->estadoEnvioSunat;

			$this->_so->mo=$dataResponse->mo;
		}

		DB::commit();

		return response()->json($this->_so);
	}
}
?>