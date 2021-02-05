<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;

use DB;

use App\Model\TResumenDiario;
use App\Model\TReciboVenta;
use App\Model\TReciboVentaNotaCredito;
use App\Model\TReciboVentaNotaDebito;
use App\Model\TEmpresa;
use App\Model\TDocumentoGeneradoSunat;

class ResumenDiarioController extends Controller
{
	public function actionGestionar(Request $request, SessionManager $sessionManager, Encrypter $encrypter, $pagina=1)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}
		
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				if(!($sessionManager->has('codigoOficina')))
				{
					return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
				}

				$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));

				$tResumenDiarioTemp=TResumenDiario::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->orderBy('codigoResumenDiario', 'desc')->first();

				if($tResumenDiarioTemp!=null && $tResumenDiarioTemp->estado=='En proceso')
				{
					$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

					$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/dailysummary/getstatus',
					[
						'form_params'=>
						[
							'dto.rucEmpresaEf' => $tEmpresa->ruc,
							'dto.userNameEf' => $tEmpresa->userNameEf,
							'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
							'dto.serieNumeroComprobanteEf' => $tResumenDiarioTemp->numeroComprobante,
							'dto.ticketNumeroEf' => $tResumenDiarioTemp->numeroTicket
						]
					]);

					$dataResponse=(object)json_decode($response->getBody(), true);

					$dataResponse->mo=(object)($dataResponse->mo);
					$dataResponse->dto=(object)($dataResponse->dto);

					if($dataResponse->mo->type!='success')
					{
						DB::rollBack();

						return $this->plataformHelper->redirectError('No se puede verificar el estado del resumen; por favor, comuníquese con el administrador del sistema.', 'resumendiario/gestionar');
					}

					if($dataResponse->dto->estadoResumen=='Pendiente')
					{
						DB::rollBack();

						return $this->plataformHelper->redirectAlerta('El resumen diario aún se encuentra en proceso de revisión.', 'resumendiario/gestionar');
					}
					else
					{
						$tResumenDiarioTemp->estado=$dataResponse->dto->estadoResumen;

						$tResumenDiarioTemp->save();

						$tDocumentoGeneradoSunat=new TDocumentoGeneradoSunat();

						$tDocumentoGeneradoSunat->codigoEmpresa=$tEmpresa->codigoEmpresa;
						$tDocumentoGeneradoSunat->responseCode=$dataResponse->dto->codigoCdr;
						$tDocumentoGeneradoSunat->responseDescription=$dataResponse->dto->descripcionCdr;
						$tDocumentoGeneradoSunat->documento='';
						$tDocumentoGeneradoSunat->nombre='';
						$tDocumentoGeneradoSunat->numeroComprobante=$tResumenDiarioTemp->numeroComprobante;
						$tDocumentoGeneradoSunat->numeroComprobanteAfectado='';
						$tDocumentoGeneradoSunat->tipo='Resumen diario';
						$tDocumentoGeneradoSunat->estado=$dataResponse->dto->estadoResumen;

						$tDocumentoGeneradoSunat->save();

						if($dataResponse->dto->codigoCdr==0)
						{
							TReciboVenta::whereRaw('(tipoRecibo=? and mid(created_at, 1, 10)=?)', ['Boleta', $tResumenDiarioTemp->fecha])->whereHas('toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->update(
							[
								'estadoEnvioSunat' => 'Aprobado',
								'codigoCdr' => 0,
								'descripcionCdr' => 'La boleta ha sido aceptada'
							]);

							TReciboVentaNotaCredito::whereRaw('mid(created_at, 1, 10)=?', [$tResumenDiarioTemp->fecha])->whereHas('treciboventa', function($q){ $q->where('tipoRecibo', 'Boleta'); })->whereHas('treciboventa.toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->update(
							[
								'estadoEnvioSunat' => 'Aprobado',
								'codigoCdr' => 0,
								'descripcionCdr' => 'La nota de crédito ha sido aceptada'
							]);

							TReciboVentaNotaDebito::whereRaw('mid(created_at, 1, 10)=?', [$tResumenDiarioTemp->fecha])->whereHas('treciboventa', function($q){ $q->where('tipoRecibo', 'Boleta'); })->whereHas('treciboventa.toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->update(
							[
								'estadoEnvioSunat' => 'Aprobado',
								'codigoCdr' => 0,
								'descripcionCdr' => 'La nota de débito ha sido aceptada'
							]);
						}

						DB::commit();

						return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'resumendiario/gestionar');
					}
				}

				$listaTReciboVenta=null;
				$listaTReciboVentaNotaCredito=null;
				$listaTReciboVentaNotaDebito=null;

				$ultimoTResumenDiario=TResumenDiario::whereRaw('codigoEmpresa=? and estado=?', [$sessionManager->get('codigoEmpresa'), 'Aprobado'])->orderBy('codigoResumenDiario', 'desc')->first();

				if($ultimoTResumenDiario!=null)
				{
					$listaTReciboVenta=TReciboVenta::with(['treciboventadetalle'])->whereRaw('mid(numeroRecibo, 1, 1)=? and mid(created_at, 1, 10)>?', ['B', $ultimoTResumenDiario->fecha])->whereHas('toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->get();
					$listaTReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventa'])->whereRaw('mid(numeroRecibo, 1, 1)=? and mid(created_at, 1, 10)>?', ['B', $ultimoTResumenDiario->fecha])->whereHas('toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->get();
					$listaTReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventa'])->whereRaw('mid(numeroRecibo, 1, 1)=? and mid(created_at, 1, 10)>?', ['B', $ultimoTResumenDiario->fecha])->whereHas('toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->get();
				}
				else
				{
					$listaTReciboVenta=TReciboVenta::with(['treciboventadetalle'])->whereRaw('mid(numeroRecibo, 1, 1)=?', ['B'])->whereHas('toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->get();
					$listaTReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventa'])->whereRaw('mid(numeroRecibo, 1, 1)=?', ['B'])->whereHas('toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->get();
					$listaTReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventa'])->whereRaw('mid(numeroRecibo, 1, 1)=?', ['B'])->whereHas('toficina', function($q) use($tEmpresa){ $q->where('codigoEmpresa', $tEmpresa->codigoEmpresa); })->get();
				}

				if(count($listaTReciboVenta) || count($listaTReciboVentaNotaCredito) || count($listaTReciboVentaNotaDebito))
				{
					$fechaParaResumen='3000-01-01';

					$fechaParaResumen=((count($listaTReciboVenta) && $listaTReciboVenta[0]->created_at<$fechaParaResumen) ? substr($listaTReciboVenta[0]->created_at, 0, 10) : $fechaParaResumen);
					$fechaParaResumen=((count($listaTReciboVentaNotaCredito) && $listaTReciboVentaNotaCredito[0]->created_at<$fechaParaResumen) ? substr($listaTReciboVentaNotaCredito[0]->created_at, 0, 10) : $fechaParaResumen);
					$fechaParaResumen=((count($listaTReciboVentaNotaDebito) && $listaTReciboVentaNotaDebito[0]->created_at<$fechaParaResumen) ? substr($listaTReciboVentaNotaDebito[0]->created_at, 0, 10) : $fechaParaResumen);

					if($fechaParaResumen<date('Y-m-d'))
					{
						/*Begin: Generación del número de comprobante, incluido la serie del mismo*/

						$tipoComprobante='RC';

						$serieComprobante=date('Ymd');

						$numeroComprobante=TDocumentoGeneradoSunat::whereRaw('codigoEmpresa=? and tipo=?', [$sessionManager->get('codigoEmpresa'), 'Resumen diario'])->count()+1;

						$numeroComprobante=str_repeat('0', (5-strlen($numeroComprobante))).$numeroComprobante;
						
						$serieNumeroComprobante=$tipoComprobante.'-'.$serieComprobante.'-'.$numeroComprobante;

						/*End: Generación del número de comprobante, incluido la serie del mismo*/

						$listaComprobanteEf=[];

						foreach($listaTReciboVenta as $key => $value)
						{
							if(substr($value->created_at, 0, 10)!=$fechaParaResumen)
							{
								continue;
							}

							$totalOutImpuestoGravado=0;/*Afectos por IGV o ISC*/
							$totalOutImpuestoInafecto=0;
							$totalOutImpuestoExonerado=0;

							foreach($value->treciboventadetalle as $index => $item)
							{
								$totalOutImpuestoGravado+=($item->situacionImpuesto=='Afecto' ? (($item->precioVentaTotalProducto)-($item->impuestoAplicadoProducto)) : 0);
								$totalOutImpuestoInafecto+=($item->situacionImpuesto=='Inafecto' ? $item->precioVentaTotalProducto : 0);
								$totalOutImpuestoExonerado+=($item->situacionImpuesto=='Exonerado' ? $item->precioVentaTotalProducto : 0);
							}

							$objectTemp=new \stdClass();

							$objectTemp->documentTypeCode='03';
							$objectTemp->numeroRecibo=$value->numeroRecibo;
							$objectTemp->numeroReciboRelacionado='';
							$objectTemp->documentoCliente=$value->documentoCliente;
							$objectTemp->total=$value->total*($value->divisa!='Soles' ? $value->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoGravado=$totalOutImpuestoGravado*($value->divisa!='Soles' ? $value->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoInafecto=$totalOutImpuestoInafecto*($value->divisa!='Soles' ? $value->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoExonerado=$totalOutImpuestoExonerado*($value->divisa!='Soles' ? $value->tipoCambioUsd : 1);
							$objectTemp->situacionImpuesto=$value->situacionImpuesto;
							$objectTemp->isc=$value->isc*($value->divisa!='Soles' ? $value->tipoCambioUsd : 1);
							$objectTemp->igv=$value->igv*($value->divisa!='Soles' ? $value->tipoCambioUsd : 1);
							$objectTemp->impuestoAplicado=$value->impuestoAplicado*($value->divisa!='Soles' ? $value->tipoCambioUsd : 1);

							$listaComprobanteEf[]=$objectTemp;
						}

						foreach($listaTReciboVentaNotaCredito as $index => $item)
						{
							if($item->codigoMotivo=='10' || substr($item->created_at, 0, 10)!=$fechaParaResumen)
							{
								continue;
							}

							$totalOutImpuestoGravadoTemp=0;/*Afectos por IGV o ISC*/
							$totalOutImpuestoInafectoTemp=0;
							$totalOutImpuestoExoneradoTemp=0;

							$item->situacionImpuesto='Exonerado';

							foreach($item->treciboventanotacreditodetalle as $k => $v)
							{
								$totalOutImpuestoGravadoTemp+=($v->situacionImpuestoProducto=='Afecto' ? (($v->precioVentaTotalProducto)-($v->impuestoAplicadoProducto)) : 0);
								$totalOutImpuestoInafectoTemp+=($v->situacionImpuestoProducto=='Inafecto' ? $v->precioVentaTotalProducto : 0);
								$totalOutImpuestoExoneradoTemp+=($v->situacionImpuestoProducto=='Exonerado' ? $v->precioVentaTotalProducto : 0);

								if($item->situacionImpuesto!='Afecto' && ($v->situacionImpuestoProducto=='Afecto' || $v->situacionImpuestoProducto=='Inafecto'))
								{
									$item->situacionImpuesto=$v->situacionImpuestoProducto;
								}
							}

							$objectTemp=new \stdClass();

							$objectTemp->documentTypeCode='07';
							$objectTemp->numeroRecibo=$item->numeroRecibo;
							$objectTemp->numeroReciboRelacionado=$item->treciboventa->numeroRecibo;
							$objectTemp->documentoCliente=$item->treciboventa->documentoCliente;
							$objectTemp->total=$item->total*($item->treciboventa->divisa!='Soles' ? $item->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoGravado=$totalOutImpuestoGravadoTemp*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoInafecto=$totalOutImpuestoInafectoTemp*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoExonerado=$totalOutImpuestoExoneradoTemp*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->situacionImpuesto=$item->situacionImpuesto;
							$objectTemp->isc=$item->isc*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->igv=$item->igv*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->impuestoAplicado=$item->impuestoAplicado*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);

							$listaComprobanteEf[]=$objectTemp;
						}

						foreach($listaTReciboVentaNotaDebito as $index => $item)
						{
							if($item->codigoMotivo=='03' || substr($item->created_at, 0, 10)!=$fechaParaResumen)
							{
								continue;
							}

							$totalOutImpuestoGravadoTemp=0;/*Afectos por IGV o ISC*/
							$totalOutImpuestoInafectoTemp=0;
							$totalOutImpuestoExoneradoTemp=0;

							$item->situacionImpuesto='Exonerado';

							foreach($item->treciboventanotadebitodetalle as $k => $v)
							{
								$totalOutImpuestoGravadoTemp+=($v->situacionImpuestoProducto=='Afecto' ? (($v->precioVentaTotalProducto)-($v->impuestoAplicadoProducto)) : 0);
								$totalOutImpuestoInafectoTemp+=($v->situacionImpuestoProducto=='Inafecto' ? $v->precioVentaTotalProducto : 0);
								$totalOutImpuestoExoneradoTemp+=($v->situacionImpuestoProducto=='Exonerado' ? $v->precioVentaTotalProducto : 0);

								if($item->situacionImpuesto!='Afecto' && ($v->situacionImpuestoProducto=='Afecto' || $v->situacionImpuestoProducto=='Inafecto'))
								{
									$item->situacionImpuesto=$v->situacionImpuestoProducto;
								}
							}

							$objectTemp=new \stdClass();

							$objectTemp->documentTypeCode='08';
							$objectTemp->numeroRecibo=$item->numeroRecibo;
							$objectTemp->numeroReciboRelacionado=$item->treciboventa->numeroRecibo;
							$objectTemp->documentoCliente=$item->treciboventa->documentoCliente;
							$objectTemp->total=$item->total*($item->treciboventa->divisa!='Soles' ? $item->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoGravado=$totalOutImpuestoGravadoTemp*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoInafecto=$totalOutImpuestoInafectoTemp*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->totalOutImpuestoExonerado=$totalOutImpuestoExoneradoTemp*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->situacionImpuesto=$item->situacionImpuesto;
							$objectTemp->isc=$item->isc*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->igv=$item->igv*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);
							$objectTemp->impuestoAplicado=$item->impuestoAplicado*($item->treciboventa->divisa!='Soles' ? $item->treciboventa->tipoCambioUsd : 1);

							$listaComprobanteEf[]=$objectTemp;
						}

						$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

						$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/dailysummary/send',
						[
							'form_params'=>
							[
								'dto.rucEmpresaEf' => $tEmpresa->ruc,
								'dto.razonSocialEmpresaEf' => $tEmpresa->razonSocial,
								'dto.userNameEf' => $tEmpresa->userNameEf,
								'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
								'dto.serieNumeroComprobanteEf' => $serieNumeroComprobante,
								'dto.fechaParaResumenEf' => $fechaParaResumen,
								'dto.demoEf' => $tEmpresa->demo,
								'dto.listaComprobanteEf' => $listaComprobanteEf
							]
						]);

						$dataResponse=(object)json_decode($response->getBody(), true);

						$dataResponse=(object)$dataResponse;

						$dataResponse->mo=(object)($dataResponse->mo);
						$dataResponse->dto=(object)($dataResponse->dto);

						if($dataResponse->mo->type!='success')
						{
							DB::rollBack();

							return $this->plataformHelper->redirectError('No se pudo generar el resumen; por favor, comuníquese con el administrador del sistema.', 'resumendiario/gestionar');
						}

						if(TResumenDiario::whereRaw('numeroComprobante=? and codigoEmpresa=?', [$serieNumeroComprobante, $tEmpresa->codigoEmpresa])->first()==null)
						{
							$tResumenDiario=new TResumenDiario();

							$tResumenDiario->numeroTicket=$dataResponse->dto->ticketNumero;
							$tResumenDiario->codigoEmpresa=$tEmpresa->codigoEmpresa;
							$tResumenDiario->numeroComprobante=$serieNumeroComprobante;
							$tResumenDiario->fecha=$fechaParaResumen;
							$tResumenDiario->estado='En proceso';

							$tResumenDiario->save();
						}
					}
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'resumendiario/gestionar');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		if(!($sessionManager->has('codigoOficina')))
		{
			return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
		}

		$paginationPrepare=$this->plataformHelper->prepararPaginacion(TResumenDiario::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->orderBy('codigoResumenDiario', 'desc'), null, $pagina);
		$paginationRender=$this->plataformHelper->renderizarPaginacion('resumendiario/gestionar', $paginationPrepare['cantidadPaginas'], $pagina);

		return view('resumendiario/gestionar', ['listaTResumenDiario' => $paginationPrepare['listaRegistros'], 'pagination' => $paginationRender]);
	}

	public function actionCambiarEstado(SessionManager $sessionManager, $codigoResumenDiario, $estado)
	{
		try
		{
			DB::beginTransaction();

			if(!$sessionManager->get('facturacionElectronica'))
			{
				return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
			}

			$tResumenDiario=TResumenDiario::find($codigoResumenDiario);

			if(!($this->plataformHelper->verificarExistenciaAutorizacion($tResumenDiario, true, true, $mensajeOut)))
			{
				return $this->plataformHelper->redirectError($mensajeOut, 'resumendiario/gestionar');
			}

			if($tResumenDiario->estado=='Aprobado' || $tResumenDiario->estado=='Rechazado')
			{
				return $this->plataformHelper->redirectError('No se puede cambiar este resumen ha rechazado porque su estado actual ya no lo permite.', 'resumendiario/gestionar');
			}

			if($estado=='Rechazado')
			{
				$tDocumentoGeneradoSunat=new TDocumentoGeneradoSunat();

				$tDocumentoGeneradoSunat->codigoEmpresa=$sessionManager->get('codigoEmpresa');
				$tDocumentoGeneradoSunat->responseCode='2512';
				$tDocumentoGeneradoSunat->responseDescription='Rechazo asignado manualmente';
				$tDocumentoGeneradoSunat->documento='';
				$tDocumentoGeneradoSunat->nombre='';
				$tDocumentoGeneradoSunat->numeroComprobante=$tResumenDiario->numeroComprobante;
				$tDocumentoGeneradoSunat->numeroComprobanteAfectado='';
				$tDocumentoGeneradoSunat->tipo='Resumen diario';
				$tDocumentoGeneradoSunat->estado=$estado;

				$tDocumentoGeneradoSunat->save();
			}

			$tResumenDiario->estado=$estado;

			$tResumenDiario->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'resumendiario/gestionar');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionDescargarXml(SessionManager $sessionManager, ResponseFactory $responseFactory, $codigoResumenDiario)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		$tResumenDiario=TResumenDiario::find($codigoResumenDiario);

		if(!($this->plataformHelper->verificarExistenciaAutorizacion($tResumenDiario, true, true, $mensajeOut)))
		{
			return $this->plataformHelper->redirectError($mensajeOut, 'resumendiario/gestionar');
		}

		$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));

		$rutaZipTemp=storage_path('app/'.$tEmpresa->codigoEmpresa.'/efxml/resumendiario/R'.$tEmpresa->ruc.'-'.$tResumenDiario->numeroComprobante.'.zip');

		if(!file_exists($rutaZipTemp))
		{
			return $this->plataformHelper->redirectError('Archivo no encontrado.', 'resumendiario/gestionar');
		}

	    return $responseFactory->download($rutaZipTemp);
	}
}
?>