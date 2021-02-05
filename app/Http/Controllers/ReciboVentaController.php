<?php
namespace App\Http\Controllers;

use App\Helper\NumeroLetras;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Application;
use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Routing\ResponseFactory;

use App\Validation\ReciboVentaValidation;

use ZipArchive;
use Mail;
use DB;

use App\Model\TPresentacion;
use App\Model\TEmpresa;
use App\Model\TOficina;
use App\Model\TCajaDetalle;
use App\Model\TReciboVenta;
use App\Model\TUnidadMedida;
use App\Model\TCategoriaVenta;
use App\Model\TClienteNatural;
use App\Model\TClienteJuridico;
use App\Model\TOficinaProducto;
use App\Model\TReciboVentaLetra;
use App\Model\TReciboVentaOutEf;
use App\Model\TReciboVentaDetalle;
use App\Model\TReciboVentaLetraOutEf;
use App\Model\TReciboVentaNotaDebito;
use App\Model\TReciboVentaNotaCredito;
use App\Model\TReciboVentaDetalleOutEf;
use App\Model\TReciboVentaGuiaRemision;

class ReciboVentaController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager, Encrypter $encrypter)
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

				if(
					!in_array($request->input('selectTipoRecibo'), ['Boleta', 'Factura'])
					|| !in_array($request->input('selectDivisa'), ['Soles', 'Dólares'])
					|| !in_array($request->input('selectTipoPago'), ['Al crédito', 'Al contado'])
				)
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/insertar');
				}

				if($request->input('selectTipoRecibo')=='Boleta')
				{
					if($request->input('txtDniCliente')!='' && trim($request->input('txtNombreCliente'))!='' && trim($request->input('txtApellidoCliente'))!='')
					{
						if(TClienteNatural::whereRaw('dni=? and codigoOficina=?', [$request->input('txtDniCliente'), $sessionManager->get('codigoOficina')])->count()>0)
						{
							TClienteNatural::whereRaw('dni=? and codigoOficina=?', [$request->input('txtDniCliente'), $sessionManager->get('codigoOficina')])->update(
							[
								'nombre' => trim($request->input('txtNombreCliente')),
								'apellido' => trim($request->input('txtApellidoCliente')),
								'direccion' => trim($request->input('txtDireccionCliente'))
							]);
						}

						if(TClienteNatural::whereRaw('codigoOficina=? and dni=?', [$sessionManager->get('codigoOficina'), $request->input('txtDniCliente')])->count()==0)
						{
							$tClienteNatural=new TClienteNatural();

							$tClienteNatural->codigoOficina=$sessionManager->get('codigoOficina');
							$tClienteNatural->dni=$request->input('txtDniCliente');
							$tClienteNatural->nombre=trim($request->input('txtNombreCliente'));
							$tClienteNatural->apellido=trim($request->input('txtApellidoCliente'));
							$tClienteNatural->pais='';
							$tClienteNatural->departamento='';
							$tClienteNatural->provincia='';
							$tClienteNatural->distrito='';
							$tClienteNatural->direccion=trim($request->input('txtDireccionCliente'));
							$tClienteNatural->manzana='';
							$tClienteNatural->lote='';
							$tClienteNatural->numeroVivienda='';
							$tClienteNatural->numeroInterior='';
							$tClienteNatural->telefono='';
							$tClienteNatural->sexo=1;
							$tClienteNatural->correoElectronico='';
							$tClienteNatural->fechaNacimiento='1111-11-11';

							$tClienteNatural->save();
						}
					}
				}
				else
				{
					if($request->input('txtRucEmpresa')=='' || trim($request->input('selectRazonSocialEmpresa'))=='' || trim($request->input('txtDireccionEmpresa'))=='')
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/insertar');
					}

					if(TClienteJuridico::whereRaw('ruc=? and codigoOficina=?', [$request->input('txtRucEmpresa'), $sessionManager->get('codigoOficina')])->count()>0)
					{
						TClienteJuridico::whereRaw('ruc=? and codigoOficina=?', [$request->input('txtRucEmpresa'), $sessionManager->get('codigoOficina')])->update(
						[
							'razonSocialLarga' => trim($request->input('selectRazonSocialEmpresa')),
							'direccion' => trim($request->input('txtDireccionEmpresa'))
						]);
					}

					if(TClienteJuridico::whereRaw('codigoOficina=? and ruc=?', [$sessionManager->get('codigoOficina'), $request->input('txtRucEmpresa')])->count()==0)
					{
						$tClienteJuridico=new TClienteJuridico();

						$tClienteJuridico->codigoOficina=$sessionManager->get('codigoOficina');
						$tClienteJuridico->ruc=$request->input('txtRucEmpresa');
						$tClienteJuridico->razonSocialCorta=trim($request->input('selectRazonSocialEmpresa'));
						$tClienteJuridico->razonSocialLarga=trim($request->input('selectRazonSocialEmpresa'));
						$tClienteJuridico->residePais=true;
						$tClienteJuridico->fechaConstitucion='1111-11-11';
						$tClienteJuridico->pais='';
						$tClienteJuridico->departamento='';
						$tClienteJuridico->provincia='';
						$tClienteJuridico->distrito='';
						$tClienteJuridico->direccion=trim($request->input('txtDireccionEmpresa'));
						$tClienteJuridico->manzana='';
						$tClienteJuridico->lote='';
						$tClienteJuridico->numeroVivienda='';
						$tClienteJuridico->numeroInterior='';
						$tClienteJuridico->telefono='';
						$tClienteJuridico->correoElectronico='';

						$tClienteJuridico->save();
					}
				}

				/*Begin: Generación del número de comprobante, incluido la serie del mismo*/

				$tipoComprobante=$request->input('selectTipoRecibo')=='Factura' ? 'F' : 'B';

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

				$numeroComprobante=substr(TReciboVenta::whereRaw('mid(numeroRecibo, 2, 3)=? and tipoRecibo=?', [str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante, $request->input('selectTipoRecibo')])->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->max('numeroRecibo'), 5)+1;

				$serieComprobante=str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante;
				$numeroComprobante=str_repeat('0', (8-strlen($numeroComprobante))).$numeroComprobante;
				
				$serieNumeroComprobante=$tipoComprobante.$serieComprobante.'-'.$numeroComprobante;

				/*End: Generación del número de comprobante, incluido la serie del mismo*/

				DB::commit();

				DB::beginTransaction();

				$tReciboVenta=new TReciboVenta();

				$tReciboVenta->codigoOficina=$sessionManager->get('codigoOficina');
				$tReciboVenta->codigoPersonal=$sessionManager->get('codigoPersonal');
				$tReciboVenta->codigoCategoriaVenta=($request->input('selectCategoriaVentaNivelTres')==null || $request->input('selectCategoriaVentaNivelTres')=='' ? ($request->input('selectCategoriaVentaNivelDos')==null || $request->input('selectCategoriaVentaNivelDos')=='' ? $request->input('selectCategoriaVentaNivelUno') : $request->input('selectCategoriaVentaNivelDos')) : $request->input('selectCategoriaVentaNivelTres'));
				$tReciboVenta->nombreCompletoCliente=$request->input('selectTipoRecibo')=='Boleta' ? (trim($request->input('txtNombreCliente').' '.$request->input('txtApellidoCliente'))=='' ? 'Anónimo' : trim($request->input('txtNombreCliente').' '.$request->input('txtApellidoCliente'))) : trim($request->input('selectRazonSocialEmpresa'));
				$tReciboVenta->documentoCliente=$request->input('selectTipoRecibo')=='Boleta' ? ($request->input('txtDniCliente')=='' ? '00000000' : $request->input('txtDniCliente')) : $request->input('txtRucEmpresa');
				$tReciboVenta->direccionCliente=$request->input('selectTipoRecibo')=='Boleta' ? trim($request->input('txtDireccionCliente')) : trim($request->input('txtDireccionEmpresa'));
				$tReciboVenta->descripcion='';
				$tReciboVenta->divisa=$request->input('selectDivisa');
				$tReciboVenta->tipoCambioUsd=$sessionManager->get('tipoCambioUsd');
				$tReciboVenta->situacionImpuesto='';
				$tReciboVenta->isc=0;
				$tReciboVenta->igv=0;
				$tReciboVenta->impuestoAplicado=$request->input('hdImpuestoAplicado');
				$tReciboVenta->flete=0;
				$tReciboVenta->subTotal=$request->input('hdSubTotal');
				$tReciboVenta->total=$request->input('hdTotal');
				$tReciboVenta->tipoRecibo=$request->input('selectTipoRecibo');
				$tReciboVenta->numeroRecibo=$serieNumeroComprobante;
				$tReciboVenta->comprobanteEmitido=true;
				$tReciboVenta->fechaComprobanteEmitido=date('Y-m-d H:i:s');
				$tReciboVenta->tipoPago=$request->input('selectTipoPago');
				$tReciboVenta->fechaPrimerPago=$request->input('dateFechaPrimerPago');
				$tReciboVenta->pagoPersonalizado=0;
				$tReciboVenta->pagoAutomatico=($request->input('selectTipoPago')=='Al crédito' ? $request->input('selectPagoAutomatico') : '');
				$tReciboVenta->letras=$request->input('txtLetras')=='' ? 0 : $request->input('txtLetras');
				$tReciboVenta->estadoCredito=($request->input('selectTipoPago')=='Al crédito' ? false : true);
				$tReciboVenta->estadoEntrega=true;
				$tReciboVenta->hash='';
				$tReciboVenta->estadoEnvioSunat='Pendiente de envío';
				$tReciboVenta->codigoCdr='';
				$tReciboVenta->descripcionCdr='';
				$tReciboVenta->estado=true;
				$tReciboVenta->motivoAnulacion='';

				$tReciboVenta->save();

				$ultimoRegistroTReciboVenta=TReciboVenta::whereRaw('codigoReciboVenta=(select max(codigoReciboVenta) from treciboventa)')->first();

				$situacionImpuestoTemp='Exonerado';
				$iscFinalTemp=0;
				$igvFinalTemp=0;

				$totalOutImpuestoInafecto=0;

				foreach($request->input('hdCodigoOficinaProducto') as $key => $value)
				{
					$tOficinaProducto=TOficinaProducto::find($request->input('hdCodigoOficinaProducto')[$key]);

					if($tOficinaProducto!=null)
					{
						if($tOficinaProducto->cantidad<$request->input('hdCantidadProducto')[$key])
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Stock insuficiente para el producto '.($key+1).' de la lista.', 'reciboventa/insertar');
						}

						if(!$tOficinaProducto->ventaMenorUnidad && !preg_match("/^[0-9]+(\.[0]*)?$/", $request->input('hdCantidadProducto')[$key]))
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten ventas por unidades enteras en el producto '.($key+1).' de la lista.', 'reciboventa/insertar');
						}
					}

					$totalOutImpuestoInafecto+=($request->input('hdSituacionImpuestoProducto')[$key]=='Inafecto' ? $request->input('hdPrecioVentaTotalProducto')[$key] : 0);

					if($request->input('hdSituacionImpuestoProducto')[$key]!='Exonerado' && $situacionImpuestoTemp!='Afecto')
					{
						$situacionImpuestoTemp=$request->input('hdSituacionImpuestoProducto')[$key];
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
						trim($request->input('hdNombreProducto')[$key])==''
						|| !in_array($request->input('hdTipoProducto')[$key], ['Genérico', 'Comercial'])
						|| !in_array($request->input('hdSituacionImpuestoProducto')[$key], ['Afecto'])
						|| !in_array($request->input('hdTipoImpuestoProducto')[$key], ['IGV'])
						|| $request->input('hdPresentacionProducto')[$key]==''
						|| $request->input('hdUnidadMedidaProducto')[$key]==''
					)
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/insertar');
					}

					$tReciboVentaDetalle=new TReciboVentaDetalle();

					$tReciboVentaDetalle->codigoReciboVenta=$ultimoRegistroTReciboVenta->codigoReciboVenta;
					$tReciboVentaDetalle->codigoOficinaProducto=$request->input('hdCodigoOficinaProducto')[$key];
					$tReciboVentaDetalle->codigoBarrasProducto=$request->input('hdCodigoBarrasProducto')[$key];
					$tReciboVentaDetalle->nombreProducto=trim($request->input('hdNombreProducto')[$key]);
					$tReciboVentaDetalle->informacionAdicionalProducto=trim($request->input('hdInformacionAdicionalProducto')[$key]);
					$tReciboVentaDetalle->descripcionProducto='';
					$tReciboVentaDetalle->tipoProducto=$request->input('hdTipoProducto')[$key];
					$tReciboVentaDetalle->situacionImpuestoProducto=$request->input('hdSituacionImpuestoProducto')[$key];
					$tReciboVentaDetalle->tipoImpuestoProducto=$request->input('hdTipoImpuestoProducto')[$key];
					$tReciboVentaDetalle->porcentajeTributacionProducto=$request->input('hdPorcentajeTributacionProducto')[$key];
					$tReciboVentaDetalle->impuestoAplicadoProducto=$request->input('hdImpuestoAplicadoProducto')[$key];
					$tReciboVentaDetalle->categoriaProducto='';
					$tReciboVentaDetalle->presentacionProducto=$request->input('hdPresentacionProducto')[$key];
					$tReciboVentaDetalle->unidadMedidaProducto=$request->input('hdUnidadMedidaProducto')[$key];
					$tReciboVentaDetalle->pesoGramosUnidadProducto=$request->input('hdPesoGramosUnidadProducto')[$key];
					$tReciboVentaDetalle->precioVentaTotalProducto=$request->input('hdPrecioVentaTotalProducto')[$key];
					$tReciboVentaDetalle->precioVentaUnitarioProducto=number_format($request->input('hdPrecioVentaTotalProducto')[$key]/$request->input('hdCantidadProducto')[$key], 2, '.', '');
					$tReciboVentaDetalle->cantidadProducto=$request->input('hdCantidadProducto')[$key];
					$tReciboVentaDetalle->cantidadBloqueProducto=12;
					$tReciboVentaDetalle->unidadMedidaBloqueProducto='Docena';

					$tReciboVentaDetalle->save();
				}

				if($request->input('selectTipoPago')=='Al crédito')
				{
					if(
						!in_array($request->input('selectPagoAutomatico'), ['Primer día laboral del mes', 'Semanalmente los lunes', 'Semanalmente los viernes'])
					)
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/insertar');
					}

					$dia=substr($request->input('dateFechaPrimerPago'), 8, 2);
					$mes=substr($request->input('dateFechaPrimerPago'), 5, 2);
					$anio=substr($request->input('dateFechaPrimerPago'), 0, 4);

					$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));

					$porPagar=number_format($request->input('hdTotal')/$request->input('txtLetras'), 1, '.', '');
					
					$fechaPagar=null;

					$viernesRestarSemana=0;

					for($i=0; $i<$request->input('txtLetras'); $i++)
					{
						if($i==0)
						{
							$fechaPagar=$request->input('dateFechaPrimerPago');
						}
						else
						{
							switch($request->input('selectPagoAutomatico'))
							{
								case 'Semanalmente los lunes':
									$fechaTemporal=date('Y-m-d', strtotime($request->input('dateFechaPrimerPago')." +$i week"));
									$dia=substr($fechaTemporal, 8, 2);
									$mes=substr($fechaTemporal, 5, 2);
									$anio=substr($fechaTemporal, 0, 4);
									$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));
									$fechaPagar=date('Y-m-d', mktime(0, 0, 0, $mes, $dia-$diaSemana+1, $anio));

									break;

								case 'Semanalmente los viernes':
									$sumarSemanas=$i-$viernesRestarSemana;
									$fechaTemporal=date('Y-m-d', strtotime($request->input('dateFechaPrimerPago')." +$sumarSemanas week"));
									$dia=substr($fechaTemporal, 8, 2);
									$mes=substr($fechaTemporal, 5, 2);
									$anio=substr($fechaTemporal, 0, 4);
									$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));

									if($viernesRestarSemana=0 && $diaSemana<5)
									{
										$viernesRestarSemana=1;
										$sumarSemanas=$i-$viernesRestarSemana;
										$fechaTemporal=date('Y-m-d', strtotime($request->input('dateFechaPrimerPago')." +$sumarSemanas week"));
										$dia=substr($fechaTemporal, 8, 2);
										$mes=substr($fechaTemporal, 5, 2);
										$anio=substr($fechaTemporal, 0, 4);
										$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));
									}

									$fechaPagar=date('Y-m-d', mktime(0, 0, 0, $mes, $dia+(7-($diaSemana+2)), $anio));

									break;

								case 'Primer día laboral del mes':
									$fechaTemporal=date('Y-m-d', strtotime($request->input('dateFechaPrimerPago')." +$i month"));
									$mes=substr($fechaTemporal, 5, 2);
									$anio=substr($fechaTemporal, 0, 4);
									$dia=date("d", mktime(0,0,0, $mes, 1, $anio));
									$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));

									$fechaPagar=date('Y-m-d', mktime(0,0,0, $mes, $dia, $anio));

									if($diaSemana==0)
									{
										$fechaPagar=date('Y-m-d', strtotime($fechaPagar." +1 day"));
									}

									break;
							}
						}

						if($i==($request->input('txtLetras')-1))
						{
							$porPagarUltimaLetra=number_format(($request->input('hdTotal')-($porPagar*($i))), 1, '.', '');
							$porPagar=$porPagarUltimaLetra<=0 ? 0 : $porPagarUltimaLetra;
						}

						$tReciboVentaLetra=new TReciboVentaLetra;

						$tReciboVentaLetra->codigoReciboVenta=$ultimoRegistroTReciboVenta->codigoReciboVenta;
						$tReciboVentaLetra->pagado=0;
						$tReciboVentaLetra->porPagar=$porPagar;
						$tReciboVentaLetra->diasMora=0;
						$tReciboVentaLetra->fechaPagar=$fechaPagar;
						$tReciboVentaLetra->estado=false;

						$tReciboVentaLetra->save();
					}
				}

				$tCajaDetalle=TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->ingresos+=($tReciboVenta->total*($tReciboVenta->divisa!='Soles' ? $tReciboVenta->tipoCambioUsd : 1));
				$tCajaDetalle->saldoFinal+=($tReciboVenta->total*($tReciboVenta->divisa!='Soles' ? $tReciboVenta->tipoCambioUsd : 1));

				$tCajaDetalle->save();

				$igvFinalTemp=($iscFinalTemp==0 ? $request->input('hdImpuestoAplicado') : $igvFinalTemp);

				$ultimoRegistroTReciboVenta->situacionImpuesto=$situacionImpuestoTemp;
				$ultimoRegistroTReciboVenta->isc=number_format($iscFinalTemp, 2, '.', '');
				$ultimoRegistroTReciboVenta->igv=number_format($igvFinalTemp, 2, '.', '');

				$ultimoRegistroTReciboVenta->save();

				/*Begin: Generación de archivo XML*/

				if($request->input('selectTipoRecibo')=='Factura')
				{
					$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));
					$tOficina=TOficina::find($sessionManager->get('codigoOficina'));
					$tReciboVenta=TReciboVenta::with(['treciboventadetalle'])->whereRaw('codigoReciboVenta=?', [$ultimoRegistroTReciboVenta->codigoReciboVenta])->first();

					$listaProductoEf=[];

					foreach($tReciboVenta->treciboventadetalle as $value)
					{
						$objectTemp=new \stdClass();

						$objectTemp->nombreProductoEf=trim($value->nombreProducto.' '.$value->informacionAdicionalProducto);
						$objectTemp->cantidadProductoEf=$value->cantidadProducto;
						$objectTemp->precioTotalVentaProductoEf=$value->precioVentaTotalProducto;
						$objectTemp->subTotalVentaProductoEf=($value->precioVentaTotalProducto-$value->impuestoAplicadoProducto);
						$objectTemp->impuestoTotalVentaProductoEf=$value->impuestoAplicadoProducto;
						$objectTemp->precioUnitarioVentaProductoEf=$value->precioVentaUnitarioProducto;
						$objectTemp->subTotalPrecioUnitarioVentaProductoEf=$value->precioVentaUnitarioProducto/((($value->porcentajeTributacionProducto)/100)+1);
						$objectTemp->impuestoUnitarioVentaProductoEf=number_format($value->precioVentaUnitarioProducto-($value->precioVentaUnitarioProducto/((($value->porcentajeTributacionProducto)/100)+1)), 2, '.', '');
						$objectTemp->situacionImpuestoProductoEf=$value->situacionImpuestoProducto;
						$objectTemp->porcentajeTributacionProductoEf=$value->porcentajeTributacionProducto;

						$listaProductoEf[]=$objectTemp;
					}

					$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

					$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/generatexml',
					[
						'form_params' =>
						[
							'dto.codigoUnicoVenta' => $tReciboVenta->codigoReciboVenta,
							'dto.serieNumeroComprobanteEf' => $tReciboVenta->numeroRecibo,
							'dto.fechaEmisionComprobanteEf' => $tReciboVenta->created_at->toDateString(),
							'dto.rucEmpresaEf' => $tEmpresa->ruc,
							'dto.razonSocialEmisorEf' => $tEmpresa->razonSocial,
							'dto.representanteLegalEmisorEf' => $tEmpresa->representanteLegal,
							'dto.direccionEmisorEf' => ($tOficina->direccion.' '.$tOficina->numeroVivienda),
							'dto.userNameEf' => $tEmpresa->userNameEf,
							'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
							'dto.documentoClienteEf' => $tReciboVenta->documentoCliente,
							'dto.tipoDocumentoClienteEf' => 'RUC',
							'dto.denominacionClienteEf' => $tReciboVenta->nombreCompletoCliente,
							'dto.divisaEf' => $tReciboVenta->divisa,
							'dto.totalVentaEf' => $tReciboVenta->total,
							'dto.subTotalVentaEf' => $tReciboVenta->subTotal,
							'dto.totalImpuestoInafectoEf' => $totalOutImpuestoInafecto,
							'dto.listaProductoEf' => $listaProductoEf
						]
					]);

					$dataResponse=(object)json_decode($response->getBody(), true);

					$dataResponse->mo=(object)($dataResponse->mo);

					if($dataResponse->mo->type!='success')
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('No se pudo generar correctamente el archivo XML; por favor, vuelva a intenarlo.', 'reciboventa/insertar');
					}

					$dataResponse->dto=(object)($dataResponse->dto);

					$tReciboVenta->hash=$dataResponse->dto->hash;

					$tReciboVenta->save();
				}

				/*End: Generación de archivo XML*/

				$sessionManager->flash('codigoReciboVenta', $ultimoRegistroTReciboVenta->codigoReciboVenta);

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/insertar');
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

		$listaTUnidadMedida=TUnidadMedida::all();
		$listaTPresentacion=TPresentacion::all();
		$listaTCategoriaVenta=TCategoriaVenta::with(['tcategoriaventachild.tcategoriaventachild'])->whereRaw('codigoEmpresa=? and estado=? and codigoCategoriaVentaPadre is null', [$sessionManager->get('codigoEmpresa'), true])->get();

		return view('reciboventa/insertar', ['listaTUnidadMedida' => $listaTUnidadMedida, 'listaTPresentacion' => $listaTPresentacion, 'listaTCategoriaVenta' => $listaTCategoriaVenta]);
	}

	public function actionInsertarSinFe(Request $request, SessionManager $sessionManager)
	{
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				if(!($sessionManager->has('codigoOficina')))
				{
					return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
				}

				if(
					!in_array($request->input('selectTipoRecibo'), ['Boleta', 'Factura'])
					|| !in_array($request->input('selectTipoPago'), ['Al crédito', 'Al contado'])
				)
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/insertarsinfe');
				}

				if($request->input('selectTipoRecibo')=='Boleta')
				{
					if($request->input('txtDniCliente')!='' && trim($request->input('txtNombreCliente'))!='' && trim($request->input('txtApellidoCliente'))!='')
					{
						if(TClienteNatural::whereRaw('dni=? and codigoOficina=?', [$request->input('txtDniCliente'), $sessionManager->get('codigoOficina')])->count()>0)
						{
							TClienteNatural::whereRaw('dni=? and codigoOficina=?', [$request->input('txtDniCliente'), $sessionManager->get('codigoOficina')])->update(
							[
								'nombre' => trim($request->input('txtNombreCliente')),
								'apellido' => trim($request->input('txtApellidoCliente')),
								'direccion' => trim($request->input('txtDireccionCliente'))
							]);
						}

						if(TClienteNatural::whereRaw('codigoOficina=? and dni=?', [$sessionManager->get('codigoOficina'), $request->input('txtDniCliente')])->count()==0)
						{
							$tClienteNatural=new TClienteNatural();

							$tClienteNatural->codigoOficina=$sessionManager->get('codigoOficina');
							$tClienteNatural->dni=$request->input('txtDniCliente');
							$tClienteNatural->nombre=trim($request->input('txtNombreCliente'));
							$tClienteNatural->apellido=trim($request->input('txtApellidoCliente'));
							$tClienteNatural->pais='';
							$tClienteNatural->departamento='';
							$tClienteNatural->provincia='';
							$tClienteNatural->distrito='';
							$tClienteNatural->direccion=trim($request->input('txtDireccionCliente'));
							$tClienteNatural->manzana='';
							$tClienteNatural->lote='';
							$tClienteNatural->numeroVivienda='';
							$tClienteNatural->numeroInterior='';
							$tClienteNatural->telefono='';
							$tClienteNatural->sexo=1;
							$tClienteNatural->correoElectronico='';
							$tClienteNatural->fechaNacimiento='1111-11-11';

							$tClienteNatural->save();
						}
					}
				}
				else
				{
					if($request->input('txtRucEmpresa')=='' || trim($request->input('selectRazonSocialEmpresa'))=='' || trim($request->input('txtDireccionEmpresa'))=='')
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/insertarsinfe');
					}

					if(TClienteJuridico::whereRaw('ruc=? and codigoOficina=?', [$request->input('txtRucEmpresa'), $sessionManager->get('codigoOficina')])->count()>0)
					{
						TClienteJuridico::whereRaw('ruc=? and codigoOficina=?', [$request->input('txtRucEmpresa'), $sessionManager->get('codigoOficina')])->update(
						[
							'razonSocialLarga' => trim($request->input('selectRazonSocialEmpresa')),
							'direccion' => trim($request->input('txtDireccionEmpresa'))
						]);
					}

					if(TClienteJuridico::whereRaw('codigoOficina=? and ruc=?', [$sessionManager->get('codigoOficina'), $request->input('txtRucEmpresa')])->count()==0)
					{
						$tClienteJuridico=new TClienteJuridico();

						$tClienteJuridico->codigoOficina=$sessionManager->get('codigoOficina');
						$tClienteJuridico->ruc=$request->input('txtRucEmpresa');
						$tClienteJuridico->razonSocialCorta=trim($request->input('selectRazonSocialEmpresa'));
						$tClienteJuridico->razonSocialLarga=trim($request->input('selectRazonSocialEmpresa'));
						$tClienteJuridico->residePais=true;
						$tClienteJuridico->fechaConstitucion='1111-11-11';
						$tClienteJuridico->pais='';
						$tClienteJuridico->departamento='';
						$tClienteJuridico->provincia='';
						$tClienteJuridico->distrito='';
						$tClienteJuridico->direccion=trim($request->input('txtDireccionEmpresa'));
						$tClienteJuridico->manzana='';
						$tClienteJuridico->lote='';
						$tClienteJuridico->numeroVivienda='';
						$tClienteJuridico->numeroInterior='';
						$tClienteJuridico->telefono='';
						$tClienteJuridico->correoElectronico='';

						$tClienteJuridico->save();
					}
				}

				/*Begin: Generación del número de comprobante, incluido la serie del mismo*/

				$tipoComprobante=$request->input('selectTipoRecibo')=='Factura' ? 'F' : 'B';

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

				$numeroComprobante=null;

				$numeroComprobante=TReciboVentaOutEf::whereRaw('mid(numeroRecibo, 2, 3)=? and tipoRecibo=?', [str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante, $request->input('selectTipoRecibo')])->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->count()+1;
				
				$serieComprobante=str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante;
				$numeroComprobante=str_repeat('0', (8-strlen($numeroComprobante))).$numeroComprobante;
				
				$serieNumeroComprobante=$tipoComprobante.$serieComprobante.'-'.$numeroComprobante;

				/*End: Generación del número de comprobante, incluido la serie del mismo*/

				DB::commit();

				DB::beginTransaction();

				$tReciboVenta=new TReciboVentaOutEf();

				$tReciboVenta->codigoOficina=$sessionManager->get('codigoOficina');
				$tReciboVenta->codigoPersonal=$sessionManager->get('codigoPersonal');
				$tReciboVenta->codigoCategoriaVenta=($request->input('selectCategoriaVentaNivelTres')==null || $request->input('selectCategoriaVentaNivelTres')=='' ? ($request->input('selectCategoriaVentaNivelDos')==null || $request->input('selectCategoriaVentaNivelDos')=='' ? $request->input('selectCategoriaVentaNivelUno') : $request->input('selectCategoriaVentaNivelDos')) : $request->input('selectCategoriaVentaNivelTres'));
				$tReciboVenta->nombreCompletoCliente=$request->input('selectTipoRecibo')=='Boleta' ? (trim($request->input('txtNombreCliente').' '.$request->input('txtApellidoCliente'))=='' ? 'Anónimo' : trim($request->input('txtNombreCliente').' '.$request->input('txtApellidoCliente'))) : trim($request->input('selectRazonSocialEmpresa'));
				$tReciboVenta->documentoCliente=$request->input('selectTipoRecibo')=='Boleta' ? ($request->input('txtDniCliente')=='' ? '00000000' : $request->input('txtDniCliente')) : $request->input('txtRucEmpresa');
				$tReciboVenta->direccionCliente=$request->input('selectTipoRecibo')=='Boleta' ? trim($request->input('txtDireccionCliente')) : trim($request->input('txtDireccionEmpresa'));
				$tReciboVenta->descripcion='';
				$tReciboVenta->situacionImpuesto='';
				$tReciboVenta->isc=0;
				$tReciboVenta->igv=0;
				$tReciboVenta->impuestoAplicado=$request->input('hdImpuestoAplicado');
				$tReciboVenta->flete=0;
				$tReciboVenta->subTotal=$request->input('hdSubTotal');
				$tReciboVenta->total=$request->input('hdTotal');
				$tReciboVenta->tipoRecibo=$request->input('selectTipoRecibo');
				$tReciboVenta->numeroRecibo=$serieNumeroComprobante;
				$tReciboVenta->comprobanteEmitido=true;
				$tReciboVenta->fechaComprobanteEmitido=date('Y-m-d H:i:s');
				$tReciboVenta->tipoPago=$request->input('selectTipoPago');
				$tReciboVenta->fechaPrimerPago=$request->input('dateFechaPrimerPago');
				$tReciboVenta->pagoPersonalizado=0;
				$tReciboVenta->pagoAutomatico=($request->input('selectTipoPago')=='Al crédito' ? $request->input('selectPagoAutomatico') : '');
				$tReciboVenta->letras=$request->input('txtLetras')=='' ? 0 : $request->input('txtLetras');
				$tReciboVenta->estadoCredito=($request->input('selectTipoPago')=='Al crédito' ? false : true);
				$tReciboVenta->estadoEntrega=true;
				$tReciboVenta->estado=true;
				$tReciboVenta->motivoAnulacion='';

				$tReciboVenta->save();

				$ultimoRegistroTReciboVenta=TReciboVentaOutEf::whereRaw('codigoReciboVentaOutEf=(select max(codigoReciboVentaOutEf) from treciboventaoutef)')->first();

				$situacionImpuestoTemp='Exonerado';
				$iscFinalTemp=0;
				$igvFinalTemp=0;

				foreach($request->input('hdCodigoOficinaProducto') as $key => $value)
				{
					$tOficinaProducto=TOficinaProducto::find($request->input('hdCodigoOficinaProducto')[$key]);

					if($tOficinaProducto!=null)
					{
						if($tOficinaProducto->cantidad<$request->input('hdCantidadProducto')[$key])
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Stock insuficiente para el producto '.($key+1).' de la lista.', 'reciboventa/insertarsinfe');
						}

						if(!$tOficinaProducto->ventaMenorUnidad && !preg_match("/^[0-9]+(\.[0]*)?$/", $request->input('hdCantidadProducto')[$key]))
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten ventas por unidades enteras en el producto '.($key+1).' de la lista.', 'reciboventa/insertarsinfe');
						}
					}

					if($request->input('hdSituacionImpuestoProducto')[$key]!='Exonerado' && $situacionImpuestoTemp!='Afecto')
					{
						$situacionImpuestoTemp=$request->input('hdSituacionImpuestoProducto')[$key];
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
						trim($request->input('hdNombreProducto')[$key])==''
						|| !in_array($request->input('hdTipoProducto')[$key], ['Genérico', 'Comercial'])
						|| !in_array($request->input('hdSituacionImpuestoProducto')[$key], ['Afecto'])
						|| !in_array($request->input('hdTipoImpuestoProducto')[$key], ['IGV'])
						|| $request->input('hdPresentacionProducto')[$key]==''
						|| $request->input('hdUnidadMedidaProducto')[$key]==''
					)
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/insertarsinfe');
					}

					$tReciboVentaDetalle=new TReciboVentaDetalleOutEf();

					$tReciboVentaDetalle->codigoReciboVentaOutEf=$ultimoRegistroTReciboVenta->codigoReciboVentaOutEf;
					$tReciboVentaDetalle->codigoOficinaProducto=$request->input('hdCodigoOficinaProducto')[$key];
					$tReciboVentaDetalle->codigoBarrasProducto=$request->input('hdCodigoBarrasProducto')[$key];
					$tReciboVentaDetalle->nombreProducto=trim($request->input('hdNombreProducto')[$key]);
					$tReciboVentaDetalle->informacionAdicionalProducto=trim($request->input('hdInformacionAdicionalProducto')[$key]);
					$tReciboVentaDetalle->descripcionProducto='';
					$tReciboVentaDetalle->tipoProducto=$request->input('hdTipoProducto')[$key];
					$tReciboVentaDetalle->situacionImpuestoProducto=$request->input('hdSituacionImpuestoProducto')[$key];
					$tReciboVentaDetalle->tipoImpuestoProducto=$request->input('hdTipoImpuestoProducto')[$key];
					$tReciboVentaDetalle->porcentajeTributacionProducto=$request->input('hdPorcentajeTributacionProducto')[$key];
					$tReciboVentaDetalle->impuestoAplicadoProducto=$request->input('hdImpuestoAplicadoProducto')[$key];
					$tReciboVentaDetalle->categoriaProducto='';
					$tReciboVentaDetalle->presentacionProducto=$request->input('hdPresentacionProducto')[$key];
					$tReciboVentaDetalle->unidadMedidaProducto=$request->input('hdUnidadMedidaProducto')[$key];
					$tReciboVentaDetalle->pesoGramosUnidadProducto=$request->input('hdPesoGramosUnidadProducto')[$key];
					$tReciboVentaDetalle->precioVentaTotalProducto=$request->input('hdPrecioVentaTotalProducto')[$key];
					$tReciboVentaDetalle->precioVentaUnitarioProducto=number_format($request->input('hdPrecioVentaTotalProducto')[$key]/$request->input('hdCantidadProducto')[$key], 2, '.', '');
					$tReciboVentaDetalle->cantidadProducto=$request->input('hdCantidadProducto')[$key];
					$tReciboVentaDetalle->cantidadBloqueProducto=12;
					$tReciboVentaDetalle->unidadMedidaBloqueProducto='Docena';

					$tReciboVentaDetalle->save();
				}

				if($request->input('selectTipoPago')=='Al crédito')
				{
					if(
						!in_array($request->input('selectPagoAutomatico'), ['Primer día laboral del mes', 'Semanalmente los lunes', 'Semanalmente los viernes'])
					)
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.', 'reciboventa/insertarsinfe');
					}

					$dia=substr($request->input('dateFechaPrimerPago'), 8, 2);
					$mes=substr($request->input('dateFechaPrimerPago'), 5, 2);
					$anio=substr($request->input('dateFechaPrimerPago'), 0, 4);

					$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));

					$porPagar=number_format($request->input('hdTotal')/$request->input('txtLetras'), 1, '.', '');
					
					$fechaPagar=null;

					$viernesRestarSemana=0;

					for($i=0; $i<$request->input('txtLetras'); $i++)
					{
						if($i==0)
						{
							$fechaPagar=$request->input('dateFechaPrimerPago');
						}
						else
						{
							switch($request->input('selectPagoAutomatico'))
							{
								case 'Semanalmente los lunes':
									$fechaTemporal=date('Y-m-d', strtotime($request->input('dateFechaPrimerPago')." +$i week"));
									$dia=substr($fechaTemporal, 8, 2);
									$mes=substr($fechaTemporal, 5, 2);
									$anio=substr($fechaTemporal, 0, 4);
									$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));
									$fechaPagar=date('Y-m-d', mktime(0, 0, 0, $mes, $dia-$diaSemana+1, $anio));

									break;

								case 'Semanalmente los viernes':
									$sumarSemanas=$i-$viernesRestarSemana;
									$fechaTemporal=date('Y-m-d', strtotime($request->input('dateFechaPrimerPago')." +$sumarSemanas week"));
									$dia=substr($fechaTemporal, 8, 2);
									$mes=substr($fechaTemporal, 5, 2);
									$anio=substr($fechaTemporal, 0, 4);
									$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));

									if($viernesRestarSemana=0 && $diaSemana<5)
									{
										$viernesRestarSemana=1;
										$sumarSemanas=$i-$viernesRestarSemana;
										$fechaTemporal=date('Y-m-d', strtotime($request->input('dateFechaPrimerPago')." +$sumarSemanas week"));
										$dia=substr($fechaTemporal, 8, 2);
										$mes=substr($fechaTemporal, 5, 2);
										$anio=substr($fechaTemporal, 0, 4);
										$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));
									}

									$fechaPagar=date('Y-m-d', mktime(0, 0, 0, $mes, $dia+(7-($diaSemana+2)), $anio));

									break;

								case 'Primer día laboral del mes':
									$fechaTemporal=date('Y-m-d', strtotime($request->input('dateFechaPrimerPago')." +$i month"));
									$mes=substr($fechaTemporal, 5, 2);
									$anio=substr($fechaTemporal, 0, 4);
									$dia=date("d", mktime(0,0,0, $mes, 1, $anio));
									$diaSemana=date('w', mktime(0, 0, 0, $mes, $dia, $anio));

									$fechaPagar=date('Y-m-d', mktime(0,0,0, $mes, $dia, $anio));

									if($diaSemana==0)
									{
										$fechaPagar=date('Y-m-d', strtotime($fechaPagar." +1 day"));
									}

									break;
							}
						}

						if($i==($request->input('txtLetras')-1))
						{
							$porPagarUltimaLetra=number_format(($request->input('hdTotal')-($porPagar*($i))), 1, '.', '');
							$porPagar=$porPagarUltimaLetra<=0 ? 0 : $porPagarUltimaLetra;
						}

						$tReciboVentaLetra=new TReciboVentaLetraOutEf();

						$tReciboVentaLetra->codigoReciboVentaOutEf=$ultimoRegistroTReciboVenta->codigoReciboVentaOutEf;
						$tReciboVentaLetra->pagado=0;
						$tReciboVentaLetra->porPagar=$porPagar;
						$tReciboVentaLetra->diasMora=0;
						$tReciboVentaLetra->fechaPagar=$fechaPagar;
						$tReciboVentaLetra->estado=false;

						$tReciboVentaLetra->save();
					}
				}

				$tCajaDetalle=TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->ingresos+=$tReciboVenta->total;
				$tCajaDetalle->saldoFinal+=$tReciboVenta->total;

				$tCajaDetalle->save();

				$igvFinalTemp=($iscFinalTemp==0 ? $request->input('hdImpuestoAplicado') : $igvFinalTemp);

				$ultimoRegistroTReciboVenta->situacionImpuesto=$situacionImpuestoTemp;
				$ultimoRegistroTReciboVenta->isc=number_format($iscFinalTemp, 2, '.', '');
				$ultimoRegistroTReciboVenta->igv=number_format($igvFinalTemp, 2, '.', '');

				$ultimoRegistroTReciboVenta->save();

				$sessionManager->flash('codigoReciboVenta', $ultimoRegistroTReciboVenta->codigoReciboVentaOutEf);

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/insertarsinfe');
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

		$listaTUnidadMedida=TUnidadMedida::all();
		$listaTPresentacion=TPresentacion::all();
		$listaTCategoriaVenta=TCategoriaVenta::with(['tcategoriaventachild.tcategoriaventachild'])->whereRaw('codigoEmpresa=? and estado=? and codigoCategoriaVentaPadre is null', [$sessionManager->get('codigoEmpresa'), true])->get();

		return view('reciboventa/insertarsinfe', ['listaTUnidadMedida' => $listaTUnidadMedida, 'listaTPresentacion' => $listaTPresentacion, 'listaTCategoriaVenta' => $listaTCategoriaVenta]);
	}

	public function actionAnularVentaSinFe(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoReciboVentaOutEf'))
		{
			try
			{
				DB::beginTransaction();

				if(!($sessionManager->has('codigoOficina')))
				{
					return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
				}

				$tReciboVentaOutEf=TReciboVentaOutEf::with('treciboventa', 'treciboventadetalleoutef')->find($request->input('hdCodigoReciboVentaOutEf'));

				$tReciboVentaOutEf->motivoAnulacion=$request->input('txtMotivoAnulacion');
				$tReciboVentaOutEf->save();

				if(!$tReciboVentaOutEf->estado)
				{
					DB::rollback();
					
					return $this->plataformHelper->redirectError('La venta sin FE ya fue anulada.', 'reciboventa/listasinfe');
				}

				if(isset($tReciboVentaOutEf->treciboventa->numeroRecibo))
				{
					DB::rollback();

					return $this->plataformHelper->redirectError('La venta fue restaurada (a Facturación Electrónica), no puede ser anulada.', 'reciboventa/listasinfe');
				}

				$tCajaDetalle=TCajaDetalle::find($sessionManager->get('codigoCajaDetalle'));

				$tCajaDetalle->egresos+=$tReciboVentaOutEf->total;
				$tCajaDetalle->saldoFinal-=$tReciboVentaOutEf->total;

				$tCajaDetalle->save();

				$tReciboVentaOutEf->estado = false;
				$tReciboVentaOutEf->save();

				foreach($tReciboVentaOutEf->treciboventadetalleoutef as $value)
				{
					$tOficinaProducto = TOficinaProducto::find($value->codigoOficinaProducto);

					if($tOficinaProducto != null)
					{
						$tOficinaProducto->cantidad += $value->cantidadProducto;
						$tOficinaProducto->save();
					}
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/listasinfe');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tReciboVentaOutEf=TReciboVentaOutEf::whereRaw('codigoOficina=? and codigoReciboVentaOutEf=?', [$sessionManager->get('codigoOficina'), $request->get('codigoReciboVentaOutEf')])->first();

		return view('reciboventa/anularventasinfe', ['tReciboVentaOutEf' => $tReciboVentaOutEf]);
	}

	public function actionGenerarVentaConFe(SessionManager $sessionManager, Encrypter $encrypter, $codigoReciboVentaOutEf)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		if(!($sessionManager->has('codigoOficina')))
		{
			return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
		}

		$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));
		$tReciboVentaOutEf=TReciboVentaOutEf::with(['treciboventadetalleoutef', 'treciboventa'])->whereRaw('codigoReciboVentaOutEf=?', [$codigoReciboVentaOutEf])->first();

		if($tReciboVentaOutEf==null)
		{
			return $this->plataformHelper->redirectError('No se encontró la venta a generar.', '/');
		}

		if(isset($tReciboVentaOutEf->treciboventa))
		{
			return $this->plataformHelper->redirectError('La venta ya fue restaurada!.', 'reciboventa/listasinfe');
		}

		if($sessionManager->has('codigoOficina')!=$tReciboVentaOutEf->codigoOficina)
		{
			return $this->plataformHelper->redirectError('Para restaurar la venta debe estar logueado en la misma oficina donde se procesó esta operación.', '/');
		}

		if(!$tReciboVentaOutEf->estado)
		{
			return $this->plataformHelper->redirectError('La venta que intenta generar fue anulada.', '/');
		}

		$tipoComprobante=$tReciboVentaOutEf->tipoRecibo=='Factura' ? 'F' : 'B';

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

		$numeroComprobante=substr(TReciboVenta::whereRaw('mid(numeroRecibo, 2, 3)=? and tipoRecibo=?', [str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante, $tReciboVentaOutEf->tipoRecibo])->whereHas('toficina', function($q) use($sessionManager){ $q->where('codigoEmpresa', $sessionManager->get('codigoEmpresa')); })->max('numeroRecibo'), 5)+1;

		$serieComprobante=str_repeat('0', (3-strlen($serieComprobante))).$serieComprobante;
		$numeroComprobante=str_repeat('0', (8-strlen($numeroComprobante))).$numeroComprobante;
		
		$serieNumeroComprobante=$tipoComprobante.$serieComprobante.'-'.$numeroComprobante;

		try
		{
			DB::beginTransaction();
			
			$tReciboVenta=new TReciboVenta();

			$tReciboVenta->codigoReciboVentaOutEf=$tReciboVentaOutEf->codigoReciboVentaOutEf;
			$tReciboVenta->codigoOficina=$tReciboVentaOutEf->codigoOficina;
			$tReciboVenta->codigoPersonal=$tReciboVentaOutEf->codigoPersonal;
			$tReciboVenta->codigoCategoriaVenta=$tReciboVentaOutEf->codigoCategoriaVenta;
			$tReciboVenta->nombreCompletoCliente=$tReciboVentaOutEf->nombreCompletoCliente;
			$tReciboVenta->documentoCliente=$tReciboVentaOutEf->documentoCliente;
			$tReciboVenta->direccionCliente=$tReciboVentaOutEf->direccionCliente;
			$tReciboVenta->descripcion=$tReciboVentaOutEf->descripcion;
			$tReciboVenta->divisa='Soles';
			$tReciboVenta->tipoCambioUsd=$sessionManager->get('tipoCambioUsd');
			$tReciboVenta->situacionImpuesto=$tReciboVentaOutEf->situacionImpuesto;
			$tReciboVenta->isc=$tReciboVentaOutEf->isc;
			$tReciboVenta->igv=$tReciboVentaOutEf->igv;
			$tReciboVenta->impuestoAplicado=$tReciboVentaOutEf->impuestoAplicado;
			$tReciboVenta->flete=$tReciboVentaOutEf->flete;
			$tReciboVenta->subTotal=$tReciboVentaOutEf->subTotal;
			$tReciboVenta->total=$tReciboVentaOutEf->total;
			$tReciboVenta->tipoRecibo=$tReciboVentaOutEf->tipoRecibo;
			$tReciboVenta->numeroRecibo=$serieNumeroComprobante;
			$tReciboVenta->comprobanteEmitido=$tReciboVentaOutEf->comprobanteEmitido;
			$tReciboVenta->fechaComprobanteEmitido=date('Y-m-d H:i:s');
			$tReciboVenta->tipoPago='Al contado';
			$tReciboVenta->fechaPrimerPago=$tReciboVentaOutEf->fechaPrimerPago;
			$tReciboVenta->pagoPersonalizado=$tReciboVentaOutEf->pagoPersonalizado;
			$tReciboVenta->pagoAutomatico=$tReciboVentaOutEf->pagoAutomatico;
			$tReciboVenta->letras=$tReciboVentaOutEf->letras;
			$tReciboVenta->estadoCredito=$tReciboVentaOutEf->estadoCredito;
			$tReciboVenta->estadoEntrega=$tReciboVentaOutEf->estadoEntrega;
			$tReciboVenta->hash='';
			$tReciboVenta->estadoEnvioSunat='Pendiente de envío';
			$tReciboVenta->codigoCdr='';
			$tReciboVenta->descripcionCdr='';
			$tReciboVenta->estado=$tReciboVentaOutEf->estado;
			$tReciboVenta->motivoAnulacion=$tReciboVentaOutEf->motivoAnulacion;

			$tReciboVenta->save();

			$ultimoRegistroTReciboVenta=TReciboVenta::whereRaw('codigoReciboVenta=(select max(codigoReciboVenta) from treciboventa)')->first();

			$totalOutImpuestoInafecto=0;

			foreach($tReciboVentaOutEf->treciboventadetalleoutef as $key => $value)
			{
				DB::table('toficinaproducto')->where('codigoOficinaProducto', $value->codigoOficinaProducto)->update(['cantidad' => DB::raw('`cantidad` + ' . $value->cantidadProducto)]);

				$totalOutImpuestoInafecto+=($value->situacionImpuestoProducto=='Inafecto' ? $value->precioVentaTotalProducto : 0);

				$tReciboVentaDetalle=new TReciboVentaDetalle();

				$tReciboVentaDetalle->codigoReciboVenta=$ultimoRegistroTReciboVenta->codigoReciboVenta;
				$tReciboVentaDetalle->codigoOficinaProducto=$value->codigoOficinaProducto;
				$tReciboVentaDetalle->codigoBarrasProducto=$value->codigoBarrasProducto;
				$tReciboVentaDetalle->nombreProducto=$value->nombreProducto;
				$tReciboVentaDetalle->informacionAdicionalProducto=$value->informacionAdicionalProducto;
				$tReciboVentaDetalle->descripcionProducto=$value->descripcionProducto;
				$tReciboVentaDetalle->tipoProducto=$value->tipoProducto;
				$tReciboVentaDetalle->situacionImpuestoProducto=$value->situacionImpuestoProducto;
				$tReciboVentaDetalle->tipoImpuestoProducto=$value->tipoImpuestoProducto;
				$tReciboVentaDetalle->porcentajeTributacionProducto=$value->porcentajeTributacionProducto;
				$tReciboVentaDetalle->impuestoAplicadoProducto=$value->impuestoAplicadoProducto;
				$tReciboVentaDetalle->categoriaProducto=$value->categoriaProducto;
				$tReciboVentaDetalle->presentacionProducto=$value->presentacionProducto;
				$tReciboVentaDetalle->unidadMedidaProducto=$value->unidadMedidaProducto;
				$tReciboVentaDetalle->pesoGramosUnidadProducto=$value->pesoGramosUnidadProducto;
				$tReciboVentaDetalle->precioVentaTotalProducto=$value->precioVentaTotalProducto;
				$tReciboVentaDetalle->precioVentaUnitarioProducto=$value->precioVentaUnitarioProducto;
				$tReciboVentaDetalle->cantidadProducto=$value->cantidadProducto;
				$tReciboVentaDetalle->cantidadBloqueProducto=$value->cantidadBloqueProducto;
				$tReciboVentaDetalle->unidadMedidaBloqueProducto=$value->unidadMedidaBloqueProducto;

				$tReciboVentaDetalle->save();
			}

			/*Begin: Generación de archivo XML*/

			if($tReciboVenta->tipoRecibo=='Factura')
			{
				$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));
				$tOficina=TOficina::find($sessionManager->get('codigoOficina'));
				$tReciboVenta=TReciboVenta::with(['treciboventadetalle'])->whereRaw('codigoReciboVenta=?', [$ultimoRegistroTReciboVenta->codigoReciboVenta])->first();

				$listaProductoEf=[];

				foreach($tReciboVenta->treciboventadetalle as $value)
				{
					$objectTemp=new \stdClass();

					$objectTemp->nombreProductoEf=trim($value->nombreProducto.' '.$value->informacionAdicionalProducto);
					$objectTemp->cantidadProductoEf=$value->cantidadProducto;
					$objectTemp->precioTotalVentaProductoEf=$value->precioVentaTotalProducto;
					$objectTemp->subTotalVentaProductoEf=($value->precioVentaTotalProducto-$value->impuestoAplicadoProducto);
					$objectTemp->impuestoTotalVentaProductoEf=$value->impuestoAplicadoProducto;
					$objectTemp->precioUnitarioVentaProductoEf=$value->precioVentaUnitarioProducto;
					$objectTemp->subTotalPrecioUnitarioVentaProductoEf=$value->precioVentaUnitarioProducto/((($value->porcentajeTributacionProducto)/100)+1);
					$objectTemp->impuestoUnitarioVentaProductoEf=number_format($value->precioVentaUnitarioProducto-($value->precioVentaUnitarioProducto/((($value->porcentajeTributacionProducto)/100)+1)), 2, '.', '');
					$objectTemp->situacionImpuestoProductoEf=$value->situacionImpuestoProducto;
					$objectTemp->porcentajeTributacionProductoEf=$value->porcentajeTributacionProducto;

					$listaProductoEf[]=$objectTemp;
				}

				$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

				$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/generatexml',
				[
					'form_params'=>
					[
						'dto.codigoUnicoVenta' => $tReciboVenta->codigoReciboVenta,
						'dto.serieNumeroComprobanteEf' => $tReciboVenta->numeroRecibo,
						'dto.fechaEmisionComprobanteEf' => $tReciboVenta->created_at->toDateString(),
						'dto.rucEmpresaEf' => $tEmpresa->ruc,
						'dto.razonSocialEmisorEf' => $tEmpresa->razonSocial,
						'dto.representanteLegalEmisorEf' => $tEmpresa->representanteLegal,
						'dto.direccionEmisorEf' => ($tOficina->direccion.' '.$tOficina->numeroVivienda),
						'dto.userNameEf' => $tEmpresa->userNameEf,
						'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf),
						'dto.documentoClienteEf' => $tReciboVenta->documentoCliente,
						'dto.tipoDocumentoClienteEf' => 'RUC',
						'dto.denominacionClienteEf' => $tReciboVenta->nombreCompletoCliente,
						'dto.divisaEf' => $tReciboVenta->divisa,
						'dto.totalVentaEf' => $tReciboVenta->total,
						'dto.subTotalVentaEf' => $tReciboVenta->subTotal,
						'dto.totalImpuestoInafectoEf' => $totalOutImpuestoInafecto,
						'dto.listaProductoEf' => $listaProductoEf
					]
				]);

				$dataResponse=(object)json_decode($response->getBody(), true);

				$dataResponse->mo=(object)($dataResponse->mo);

				if($dataResponse->mo->type!='success')
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('No se pudo generar correctamente el archivo XML; por favor, vuelva a intenarlo.', 'reciboventa/insertar');
				}

				$dataResponse->dto=(object)($dataResponse->dto);

				$tReciboVenta->hash=$dataResponse->dto->hash;

				$tReciboVenta->save();
			}

			/*End: Generación de archivo XML*/

			$sessionManager->flash('codigoReciboVenta', $ultimoRegistroTReciboVenta->codigoReciboVenta);

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/listasinfe');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionEnviarPdfXml(Request $request, SessionManager $sessionManager, Application $application,  Encrypter $encrypter)
	{
		if($request->has('hdCodigoReciboVenta'))
		{
			if(!$sessionManager->get('facturacionElectronica'))
			{
				return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
			}

			$codigoReciboVenta = $request->get('hdCodigoReciboVenta');
			$tReciboVenta=TReciboVenta::find($codigoReciboVenta);

			$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

			/** BEGIN ADD FILES TO ZIP */

			$rutaFolderTemp=public_path().'/temp';
			$dataTemp=uniqid();
			$dataTempNC = [];
			$dataTempND = [];
			$dataTempGR = [];
			$xmlNames = [];
			$rutaZipTemp=$rutaFolderTemp.'/'.$tReciboVenta->numeroRecibo.'-'.$tEmpresa->ruc.'-'.$dataTemp.'.zip';
			$filePdfAdded = false;
					
			$zip=new ZipArchive();
			
			$zip->open($rutaZipTemp, ZipArchive::CREATE);
			
			foreach ($request->get('listaFicherosEnviar') as $key => $value) 
			{
				$typeFile = explode('~', $value)[0];
				$codeKey = explode('~', $value)[1];

				if($typeFile === 'comprobante')
				{
					$convertirNumeroLetra=new NumeroLetras();

					$pdf=$application->make('dompdf.wrapper');

					require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

					$rutaBaseQr=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/qr';
					$nombreArchivoTemp=$tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03').'-'.$tReciboVenta->numeroRecibo;

					if(!file_exists($rutaBaseQr.'/'.$nombreArchivoTemp.'.png'))
					{
						if (!file_exists($rutaBaseQr))
						{
							mkdir($rutaBaseQr, 0777, true);
						}

						$contentQr=$tEmpresa->ruc
						.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03')
						.'|'.explode('-', $tReciboVenta->numeroRecibo)[0]
						.'|'.explode('-', $tReciboVenta->numeroRecibo)[1]
						.'|'.$tReciboVenta->impuestoAplicado
						.'|'.$tReciboVenta->total
						.'|'.$tReciboVenta->fechaComprobanteEmitido
						.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '6' : '1')
						.'|'.$tReciboVenta->documentoCliente;

						\QRcode::png($contentQr, $rutaBaseQr.'/'.$nombreArchivoTemp.'.png', QR_ECLEVEL_L, 4);
					}

					$pathQr=$rutaBaseQr.'/'.$nombreArchivoTemp.'.png';
					$typeQr=pathinfo($pathQr, PATHINFO_EXTENSION);
					$dataQr=file_get_contents($pathQr);
					$base64Qr='data:image/' . $typeQr . ';base64,' . base64_encode($dataQr);

					$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
					$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
					$dataLogo=file_get_contents($pathLogo);
					$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);
					
					$pdf->loadHTML(view('reciboventa/'.strtolower($tReciboVenta->tipoRecibo), ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVenta' => $tReciboVenta, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVenta->total), ''), 'base64Qr' => $base64Qr, 'base64Logo' => $base64Logo]));

					file_put_contents($rutaFolderTemp.'-'.$dataTemp.'.pdf', $pdf->output());

					$zip->addFile($rutaFolderTemp.'-'.$dataTemp.'.pdf', $tReciboVenta->tipoRecibo.'-'.$dataTemp.'.pdf');

					$filePdfAdded = true;
				}
				else if($typeFile === 'notacredito')
				{
					$tReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventa'])->whereRaw('codigoReciboVentaNotaCredito=?', [$codeKey])->first();
					
					$convertirNumeroLetra=new NumeroLetras();

					$pdf=$application->make('dompdf.wrapper');

					require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

					$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
					$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
					$dataLogo=file_get_contents($pathLogo);
					$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

					$pdf->loadHTML(view('reciboventanotacredito/notacredito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaCredito' => $tReciboVentaNotaCredito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaCredito->total), ''), 'base64Logo' => $base64Logo]));

					$rutaFolderTemp=public_path().'/temp';
					$dataTempNCGenerate=uniqid();

					$dataTempNC[] = $dataTempNCGenerate;

					file_put_contents($rutaFolderTemp.'-'.$dataTempNCGenerate.'.pdf', $pdf->output());

					$zip->addFile($rutaFolderTemp.'-'.$dataTempNCGenerate.'.pdf', 'Nota de crédito-'.$dataTempNCGenerate.'.pdf');
				}
				else if($typeFile === 'notadebito')
				{
					$tReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventa'])->whereRaw('codigoReciboVentaNotaDebito=?', [$codeKey])->first();
		
					$convertirNumeroLetra=new NumeroLetras();

					$pdf=$application->make('dompdf.wrapper');

					require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

					$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
					$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
					$dataLogo=file_get_contents($pathLogo);
					$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

					$pdf->loadHTML(view('reciboventanotadebito/notadebito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaDebito' => $tReciboVentaNotaDebito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaDebito->total), ''), 'base64Logo' => $base64Logo]));

					$rutaFolderTemp=public_path().'/temp';
					$dataTempNDGenerate=uniqid();

					$dataTempND[] = $dataTempNDGenerate;

					file_put_contents($rutaFolderTemp.'-'.$dataTempNDGenerate.'.pdf', $pdf->output());

					$zip->addFile($rutaFolderTemp.'-'.$dataTempNDGenerate.'.pdf', 'Nota de débito-'.$dataTempNDGenerate.'.pdf');					
				}
				else if($typeFile === 'guiaremision')
				{
					$tReciboVentaGuiaRemision=TReciboVentaGuiaRemision::with(['treciboventaguiaremisiondetalle', 'treciboventa'])->whereRaw('codigoReciboVentaGuiaRemision=?', [$codeKey])->first();

					$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaGuiaRemision){ $q->whereRaw('codigoOficina=?', [$tReciboVentaGuiaRemision->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

					$pdf=$application->make('dompdf.wrapper');

					$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
					$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
					$dataLogo=file_get_contents($pathLogo);
					$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

					$pdf->loadHTML(view('reciboventaguiaremision/guiaremision', ['tEmpresa' => $tEmpresa, 'tReciboVentaGuiaRemision' => $tReciboVentaGuiaRemision, 'base64Logo' => $base64Logo]));
					
					$rutaFolderTemp=public_path().'/temp';
					$dataTempGRGenerate=uniqid();

					$dataTempGR[] = $dataTempGRGenerate;

					file_put_contents($rutaFolderTemp.'-'.$dataTempGRGenerate.'.pdf', $pdf->output());

					$zip->addFile($rutaFolderTemp.'-'.$dataTempGRGenerate.'.pdf', 'Guía de remisión-'.$dataTempGRGenerate.'.pdf');				
				}
				else 
				{
					$xmlNames[] = $typeFile;
				}
			}

			/*Begin: optencion de archivo XML*/
		
			if(count($xmlNames) > 0 && $tReciboVenta->tipoRecibo=='Factura')
			{
				$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

				$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getfilesxmlaszip',
				[
					'form_params' =>
					[
						'dto.rucEmpresaEf' => $tEmpresa->ruc,
						'dto.codigoUnicoVenta' => $tReciboVenta->codigoReciboVenta,
						'dto.filesName' => $xmlNames,
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
					if($filePdfAdded !== false)
					{
						unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');
					}

					foreach($dataTempNC as $key => $value)
					{
						unlink($rutaFolderTemp.'-'.$value.'.pdf');	
					}

					foreach($dataTempND as $key => $value)
					{
						unlink($rutaFolderTemp.'-'.$value.'.pdf');	
					}

					foreach($dataTempGR as $key => $value)
					{
						unlink($rutaFolderTemp.'-'.$value.'.pdf');	
					}			

					return $this->plataformHelper->redirectError('No se pudo recuperar correctamente el archivo XML; por favor, vuelva a intenarlo.', 'reciboventa/ver');
				}

				$xmlName = $tEmpresa->ruc.'-Agrupado-'.$tReciboVenta->numeroRecibo.'.xml';
				if(count((object)($dataResponse->dto)) > 0)
					$zip->addFromString($xmlName . '.zip', base64_decode( ((object)($dataResponse->dto))->zipAsBase64 ));
			}

			/*End: optencion de archivo XML*/

			$zip->close();

			if($filePdfAdded !== false)
			{
				unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');
			}

			foreach($dataTempNC as $key => $value)
			{
				unlink($rutaFolderTemp.'-'.$value.'.pdf');	
			}

			foreach($dataTempND as $key => $value)
			{
				unlink($rutaFolderTemp.'-'.$value.'.pdf');	
			}

			foreach($dataTempGR as $key => $value)
			{
				unlink($rutaFolderTemp.'-'.$value.'.pdf');	
			}			

			/** END ADD FILES TO ZIP */
			
			Mail::send('email.venta.comprobante', ['mensaje' => (preg_replace("/\r\n|\r|\n/",'<br/>', $request->get('txtMessage'))), 'nombreEmpresa' => $tEmpresa->razonSocial, 'logoEmpresa' => 'http://sysef.com/img/empresa/'.$tEmpresa->codigoEmpresa . '/logoEmpresarial.png'], function($x) use($rutaZipTemp, $request, $sessionManager)
			{
				$x->from(env('MAIL_USERNAME'), 'sysef.com');
				$x->to($request->get('txtEmail'), 'Estimado usuario')->subject('sysef.com: Envio de comprobante ['. $sessionManager->get('razonSocialEmpresa') .']');
				$x->attach($rutaZipTemp);
			});
			
			File::delete($rutaZipTemp);

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/ver');
		}

		$tReciboVenta=TReciboVenta::with(['treciboventanotacredito', 'treciboventanotadebito', 'treciboventaguiaremision'])->whereRaw('codigoOficina=? and codigoReciboVenta=?', [$sessionManager->get('codigoOficina'), $request->get('codigoReciboVenta')])->first();

		$codigoReciboVenta = $codigoReciboVenta = $request->get('codigoReciboVenta');
		$listaFicheros = [
			(object) [
				"name" => "comprobante",
				"serie" => $tReciboVenta->numeroRecibo,
				"denomination" => $tReciboVenta->tipoRecibo,
				"pk" => $tReciboVenta->codigoReciboVenta,
				"type" => 'pdf'
			]
		];
		foreach($tReciboVenta->treciboventanotacredito as $key => $value)
		{
			$listaFicheros[] = 
				(object) [
					"name" => "notacredito",
					"serie" => $value->numeroRecibo,
					"pk" => $value->codigoReciboVentaNotaCredito,
					"denomination" => 'Nota de crédito',
					"type" => 'pdf'
				];
		}

		foreach($tReciboVenta->treciboventanotadebito as $key => $value)
		{
			$listaFicheros[] = 
				(object) [
					"name" => "notadebito",
					"serie" => $value->numeroRecibo,
					"pk" => $value->codigoReciboVentaNotaDebito,
					"denomination" => 'Nota de débito',
					"type" => 'pdf'
				];
		}

		foreach($tReciboVenta->treciboventaguiaremision as $key => $value)
		{
			$listaFicheros[] = 
				(object) [
					"name" => "guiaremision",
					"serie" => $value->numeroGuiaRemision,
					"pk" => $value->codigoReciboVentaGuiaRemision,
					"denomination" => 'Guía de remisión',
					"type" => 'pdf'
				];
		}
		
		/*Begin: optencion de archivo XML*/
		
		$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));
		
		$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

		$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getxmlfilenamelist',
		[
			'form_params' =>
			[
				'dto.rucEmpresaEf' => $tEmpresa->ruc,
				'dto.codigoUnicoVenta' => $request->get('codigoReciboVenta'),
				'dto.userNameEf' => $tEmpresa->userNameEf,
				'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf)
			]
		]);

		$dataResponse=(object)json_decode($response->getBody(), true);
		
		$dataResponse->mo=(object)($dataResponse->mo);
				
		if($dataResponse->mo->type=='success')
		{
			$listXml = ((object)($dataResponse->dto))->listFileName;
			
			foreach($listXml as $xml)
			{
				$serie = explode('-', $xml)[2] . '-' . explode('.', explode('-', $xml)[3])[0];

				$listaFicheros[] = (object) [
					"name" =>  $xml,
					"serie" => $serie,
					"pk" => '-',
					"denomination" => 
					(explode('-', $xml)[1] == '01' 
						? 'Factura' 
						: (
							explode('-', $xml)[1] == '03'
							? 'Boleta'
							: (
								explode('-', $xml)[1] == '07'
								? 'Nota de crédito'
								: (
									explode('-', $xml)[1] == '31'
									? 'Guía de remisión'
									: 'Nota de débito'
								)
							)
						) 
					), 
					"type" => 'xml'
				];
			}
		}

		/*End: optencion de archivo XML*/

		return view('reciboventa/enviarpdfxml', ['tReciboVenta' => $tReciboVenta, 'listaFicheros' => $listaFicheros]);
	}

	public function actionVer(Request $request, SessionManager $sessionManager, $pagina=1)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		if(!($sessionManager->has('codigoOficina')))
		{
			return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
		}

		if($request->input('q'))
		{
			$term=$request->input('q');
			$paginationPrepare = null;
			
			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare=$this->plataformHelper->prepararPaginacion(TReciboVenta::with('treciboventadetalle')->
				whereRaw('(replace(concat(documentoCliente, nombreCompletoCliente, numeroRecibo, tipoPago, tipoRecibo), \' \', \'\') like replace(?, \' \', \'\') or codigoPersonal in (select codigoPersonal from tpersonal where replace(concat(nombre, apellido, correoElectronico, dni), \' \', \'\') like replace(?, \' \', \'\'))) and codigoOficina=?', ['%'.$term.'%', '%'.$term.'%', $sessionManager->get('codigoOficina')])
				->orWhereHas('treciboventadetalle', function($query) use ($term)
				{
					$query->whereRaw('replace(concat(codigoBarrasProducto, nombreProducto), \' \', \'\') like replace(?, \' \', \'\') ', ['%'.$term.'%']);
				})
				->whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])
				->orderBy('created_at', 'desc'), null, $pagina);
			}
			else
			{
				$paginationPrepare=$this->plataformHelper->prepararPaginacion(TReciboVenta::with('treciboventadetalle')->
				whereRaw('(compareFind(concat(documentoCliente, nombreCompletoCliente, numeroRecibo, tipoPago, tipoRecibo), ?, 77)=1 or codigoPersonal in (select codigoPersonal from tpersonal where compareFind(concat(nombre, apellido, correoElectronico, dni), ?, 77)=1)) and codigoOficina=?', [$term, $term, $sessionManager->get('codigoOficina')])
				->orWhereHas('treciboventadetalle', function($query) use ($term)
				{
					$query->whereRaw('compareFind(concat(codigoBarrasProducto, nombreProducto), ?, 77)=1 ', [$term]);
				})
				->whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])
				->orderBy('created_at', 'desc'), null, $pagina);
			}

			$paginationRender=$this->plataformHelper->renderizarPaginacion('reciboventa/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));

			$listaEstadisticaVenta=TReciboVenta::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa'])->
			whereRaw('(compareFind(concat(documentoCliente, nombreCompletoCliente, numeroRecibo, tipoPago, tipoRecibo), ?, 77)=1 or codigoPersonal in (select codigoPersonal from tpersonal where compareFind(concat(nombre, apellido, correoElectronico, dni), ?, 77)=1)) and codigoOficina=?', [$term, $term, $sessionManager->get('codigoOficina')])
			->orWhereHas('treciboventadetalle', function($query) use ($term)
			{
				$query->whereRaw('compareFind(concat(codigoBarrasProducto, nombreProducto), ?, 77)=1 ', [$term]);
			})
			->whereRaw('codigoOficina=? and estado', [$sessionManager->get('codigoOficina')])
			->groupBy(['codigoCategoriaVenta'])
			->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
			->orderBy('codigoCategoriaVenta', 'asc')
			->get();
			
			return view('reciboventa/ver', ['genericHelper' => $this->plataformHelper, 'listaEstadisticaVenta' => $listaEstadisticaVenta, 'listaTReciboVenta' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare=$this->plataformHelper->prepararPaginacion(TReciboVenta::with('treciboventadetalle')->whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender=$this->plataformHelper->renderizarPaginacion('reciboventa/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		$listaEstadisticaVenta=TReciboVenta::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa'])->whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])->groupBy(['codigoCategoriaVenta'])->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')->orderBy('codigoCategoriaVenta', 'asc')->get();

		return view('reciboventa/ver', ['genericHelper' => $this->plataformHelper, 'listaEstadisticaVenta' => $listaEstadisticaVenta, 'listaTReciboVenta' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}
	
	public function actionListaSinFe(Request $request, SessionManager $sessionManager, $pagina=1)
	{
		if(!($sessionManager->has('codigoOficina')))
		{
			return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
		}

		if($request->input('q'))
		{
			$term=$request->input('q');
			$paginationPrepare = null;

			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare=$this->plataformHelper->prepararPaginacion(TReciboVentaOutEf::with('treciboventadetalleoutef', 'treciboventa')->
				whereRaw('(replace(concat(documentoCliente, nombreCompletoCliente, numeroRecibo, tipoPago, tipoRecibo), \' \', \'\') like replace(?, \' \', \'\') or codigoPersonal in (select codigoPersonal from tpersonal where replace(concat(nombre, apellido, correoElectronico, dni), \' \', \'\') like replace(?, \' \', \'\'))) and codigoOficina=?', ['%'.$term.'%', '%'.$term.'%', $sessionManager->get('codigoOficina')])
				->orWhereHas('treciboventadetalleoutef', function($query) use ($term)
				{
					$query->whereRaw('replace(concat(codigoBarrasProducto, nombreProducto), \' \', \'\') like replace(?, \' \', \'\') ', ['%'.$term.'%']);
				})
				->whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])
				->orderBy('created_at', 'desc'), null, $pagina);				
			}
			else
			{
				$paginationPrepare=$this->plataformHelper->prepararPaginacion(TReciboVentaOutEf::with('treciboventadetalleoutef', 'treciboventa')->
				whereRaw('(compareFind(concat(documentoCliente, nombreCompletoCliente, numeroRecibo, tipoPago, tipoRecibo), ?, 77)=1 or codigoPersonal in (select codigoPersonal from tpersonal where compareFind(concat(nombre, apellido, correoElectronico, dni), ?, 77)=1)) and codigoOficina=?', [$term, $term, $sessionManager->get('codigoOficina')])
				->orWhereHas('treciboventadetalleoutef', function($query) use ($term)
				{
					$query->whereRaw('compareFind(concat(codigoBarrasProducto, nombreProducto), ?, 77)=1 ', [$term]);
				})
				->whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])
				->orderBy('created_at', 'desc'), null, $pagina);
			}

			$paginationRender=$this->plataformHelper->renderizarPaginacion('reciboventa/listasinfe', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));

			$listaEstadisticaVenta=TReciboVentaOutEf::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa'])->
			whereRaw('(compareFind(concat(documentoCliente, nombreCompletoCliente, numeroRecibo, tipoPago, tipoRecibo), ?, 77)=1 or codigoPersonal in (select codigoPersonal from tpersonal where compareFind(concat(nombre, apellido, correoElectronico, dni), ?, 77)=1)) and codigoOficina=?', [$term, $term, $sessionManager->get('codigoOficina')])
			->orWhereHas('treciboventadetalleoutef', function($query) use ($term)
			{
				$query->whereRaw('compareFind(concat(codigoBarrasProducto, nombreProducto), ?, 77)=1 ', [$term]);
			})
			->whereRaw('codigoOficina=? and estado', [$sessionManager->get('codigoOficina')])
			->groupBy(['codigoCategoriaVenta'])
			->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')
			->orderBy('codigoCategoriaVenta', 'asc')
			->get();
	
			return view('reciboventa/listasinfe', ['genericHelper' => $this->plataformHelper, 'listaEstadisticaVenta' => $listaEstadisticaVenta, 'listaTReciboVenta' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare=$this->plataformHelper->prepararPaginacion(TReciboVentaOutEf::with('treciboventadetalleoutef', 'treciboventa', 'tcategoriaventa.tcategoriaventa.tcategoriaventa')->whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender=$this->plataformHelper->renderizarPaginacion('reciboventa/listasinfe', $paginationPrepare["cantidadPaginas"], $pagina);

		$listaEstadisticaVenta=TReciboVentaOutEf::with(['tcategoriaventa.tcategoriaventa.tcategoriaventa'])->whereRaw('codigoOficina=?', [$sessionManager->get('codigoOficina')])->groupBy(['codigoCategoriaVenta'])->selectRaw('codigoCategoriaVenta, count(*) as cantidadVentas')->orderBy('codigoCategoriaVenta', 'asc')->get();

		return view('reciboventa/listasinfe', ['genericHelper' => $this->plataformHelper, 'listaEstadisticaVenta' => $listaEstadisticaVenta, 'listaTReciboVenta' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}
	
	public function actionDetalle(Request $request, SessionManager $sessionManager)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar operaciones electrónicas con la SUNAT.</div>';exit;
		}
		
		$tReciboVenta=TReciboVenta::with('treciboventadetalle')->whereRaw('codigoReciboVenta=?', [$request->input('codigoReciboVenta')])->first();

		return view('reciboventa/detalle', ['genericHelper' => $this->plataformHelper, 'tReciboVenta' => $tReciboVenta]);
	}

	public function actionDetalleSinFe(Request $request)
	{
		$tReciboVenta=TReciboVentaOutEf::with('treciboventadetalleoutef')->whereRaw('codigoReciboVentaOutEf=?', [$request->input('codigoReciboVenta')])->first();

		return view('reciboventa/detallesinfe', ['genericHelper' => $this->plataformHelper, 'tReciboVenta' => $tReciboVenta]);
	}

	public function actionImprimirComprobante(SessionManager $sessionManager, Application $application, $codigoReciboVenta)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		$tReciboVenta=TReciboVenta::with(['tpersonal.tusuario', 'tcategoriaventa'])->whereRaw('codigoReciboVenta=?', [$codigoReciboVenta])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 1000]);
		}

		require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

		$rutaBaseQr=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/qr';
		$nombreArchivoTemp=$tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03').'-'.$tReciboVenta->numeroRecibo;

		if(!file_exists($rutaBaseQr.'/'.$nombreArchivoTemp.'.png'))
		{
			if (!file_exists($rutaBaseQr))
			{
				mkdir($rutaBaseQr, 0777, true);
			}

			$contentQr=$tEmpresa->ruc
			.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03')
			.'|'.explode('-', $tReciboVenta->numeroRecibo)[0]
			.'|'.explode('-', $tReciboVenta->numeroRecibo)[1]
			.'|'.$tReciboVenta->impuestoAplicado
			.'|'.$tReciboVenta->total
			.'|'.$tReciboVenta->fechaComprobanteEmitido
			.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '6' : '1')
			.'|'.$tReciboVenta->documentoCliente;

			\QRcode::png($contentQr, $rutaBaseQr.'/'.$nombreArchivoTemp.'.png', QR_ECLEVEL_L, 4);
		}

		$pathQr=$rutaBaseQr.'/'.$nombreArchivoTemp.'.png';
		$typeQr=pathinfo($pathQr, PATHINFO_EXTENSION);
		$dataQr=file_get_contents($pathQr);
		$base64Qr='data:image/' . $typeQr . ';base64,' . base64_encode($dataQr);

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventa/'.strtolower($tReciboVenta->tipoRecibo), ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVenta' => $tReciboVenta, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVenta->total), ''), 'base64Qr' => $base64Qr, 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($nombreArchivoTemp.'.pdf', ['attachment' => false]);
	}

	public function actionProforma(Request $request, SessionManager $sessionManager, Application $application)
	{
		if($_POST)
		{
			try
			{
				if(!($sessionManager->has('codigoOficina')))
				{
					return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
				}
				
				$tReciboVenta=new \stdClass;

				$tReciboVenta->codigoOficina=$sessionManager->get('codigoOficina');
				$tReciboVenta->codigoPersonal=$sessionManager->get('codigoPersonal');
				$tReciboVenta->codigoCategoriaVenta=($request->input('selectCategoriaVentaNivelTres')==null || $request->input('selectCategoriaVentaNivelTres')=='' ? ($request->input('selectCategoriaVentaNivelDos')==null || $request->input('selectCategoriaVentaNivelDos')=='' ? $request->input('selectCategoriaVentaNivelUno') : $request->input('selectCategoriaVentaNivelDos')) : $request->input('selectCategoriaVentaNivelTres'));
				$tReciboVenta->nombreCompletoCliente=$request->input('selectTipoRecibo')=='Boleta' ? (trim($request->input('txtNombreCliente').' '.$request->input('txtApellidoCliente'))=='' ? 'Anónimo' : trim($request->input('txtNombreCliente').' '.$request->input('txtApellidoCliente'))) : trim($request->input('selectRazonSocialEmpresa'));
				$tReciboVenta->documentoCliente=$request->input('selectTipoRecibo')=='Boleta' ? ($request->input('txtDniCliente')=='' ? '00000000' : $request->input('txtDniCliente')) : $request->input('txtRucEmpresa');
				$tReciboVenta->direccionCliente=$request->input('selectTipoRecibo')=='Boleta' ? trim($request->input('txtDireccionCliente')) : trim($request->input('txtDireccionEmpresa'));
				$tReciboVenta->descripcion='';
				$tReciboVenta->divisa=$request->input('selectDivisa');
				$tReciboVenta->tipoCambioUsd=$sessionManager->get('tipoCambioUsd');
				$tReciboVenta->situacionImpuesto='';
				$tReciboVenta->isc=0;
				$tReciboVenta->igv=0;
				$tReciboVenta->impuestoAplicado=$request->input('hdImpuestoAplicado');
				$tReciboVenta->flete=0;
				$tReciboVenta->subTotal=$request->input('hdSubTotal');
				$tReciboVenta->total=$request->input('hdTotal');
				$tReciboVenta->tipoRecibo=$request->input('selectTipoRecibo');
				$tReciboVenta->numeroRecibo='';
				$tReciboVenta->comprobanteEmitido=true;
				$tReciboVenta->fechaComprobanteEmitido=date('Y-m-d H:i:s');
				$tReciboVenta->tipoPago=$request->input('selectTipoPago');
				$tReciboVenta->fechaPrimerPago=$request->input('dateFechaPrimerPago');
				$tReciboVenta->pagoPersonalizado=0;
				$tReciboVenta->pagoAutomatico=($request->input('selectTipoPago')=='Al crédito' ? $request->input('selectPagoAutomatico') : '');
				$tReciboVenta->letras=$request->input('txtLetras')=='' ? 0 : $request->input('txtLetras');
				$tReciboVenta->estadoCredito=($request->input('selectTipoPago')=='Al crédito' ? false : true);
				$tReciboVenta->estadoEntrega=true;
				$tReciboVenta->estado=true;
				$tReciboVenta->motivoAnulacion='';

				$situacionImpuestoTemp='Exonerado';
				$iscFinalTemp=0;
				$igvFinalTemp=0;

				foreach($request->input('hdCodigoOficinaProducto') as $key => $value)
				{
					if($request->input('hdSituacionImpuestoProducto')[$key]!='Exonerado' && $situacionImpuestoTemp!='Afecto')
					{
						$situacionImpuestoTemp=$request->input('hdSituacionImpuestoProducto')[$key];
					}

					if($request->input('hdTipoImpuestoProducto')[$key]=='ISC')
					{
						$iscFinalTemp+=$request->input('hdImpuestoAplicadoProducto')[$key];
					}
					else
					{
						$igvFinalTemp+=$request->input('hdImpuestoAplicadoProducto')[$key];
					}

					$tReciboVentaDetalle=new \stdClass;

					$tReciboVentaDetalle->codigoOficinaProducto=$request->input('hdCodigoOficinaProducto')[$key];
					$tReciboVentaDetalle->codigoBarrasProducto=$request->input('hdCodigoBarrasProducto')[$key];
					$tReciboVentaDetalle->nombreProducto=trim($request->input('hdNombreProducto')[$key]);
					$tReciboVentaDetalle->informacionAdicionalProducto=trim($request->input('hdInformacionAdicionalProducto')[$key]);
					$tReciboVentaDetalle->descripcionProducto='';
					$tReciboVentaDetalle->tipoProducto=$request->input('hdTipoProducto')[$key];
					$tReciboVentaDetalle->situacionImpuestoProducto=$request->input('hdSituacionImpuestoProducto')[$key];
					$tReciboVentaDetalle->tipoImpuestoProducto=$request->input('hdTipoImpuestoProducto')[$key];
					$tReciboVentaDetalle->porcentajeTributacionProducto=$request->input('hdPorcentajeTributacionProducto')[$key];
					$tReciboVentaDetalle->impuestoAplicadoProducto=$request->input('hdImpuestoAplicadoProducto')[$key];
					$tReciboVentaDetalle->categoriaProducto='';
					$tReciboVentaDetalle->presentacionProducto=$request->input('hdPresentacionProducto')[$key];
					$tReciboVentaDetalle->unidadMedidaProducto=$request->input('hdUnidadMedidaProducto')[$key];
					$tReciboVentaDetalle->precioVentaTotalProducto=$request->input('hdPrecioVentaTotalProducto')[$key];
					$tReciboVentaDetalle->precioVentaUnitarioProducto=number_format($request->input('hdPrecioVentaTotalProducto')[$key]/$request->input('hdCantidadProducto')[$key], 2, '.', '');
					$tReciboVentaDetalle->cantidadProducto=$request->input('hdCantidadProducto')[$key];
					$tReciboVentaDetalle->cantidadBloqueProducto=12;
					$tReciboVentaDetalle->unidadMedidaBloqueProducto='Docena';

					$tReciboVenta->treciboventadetalle[] = $tReciboVentaDetalle;
				}

				$igvFinalTemp=($iscFinalTemp==0 ? $request->input('hdImpuestoAplicado') : $igvFinalTemp);

				$tReciboVenta->situacionImpuesto=$situacionImpuestoTemp;
				$tReciboVenta->isc=number_format($iscFinalTemp, 2, '.', '');
				$tReciboVenta->igv=number_format($igvFinalTemp, 2, '.', '');

				$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

				$convertirNumeroLetra=new NumeroLetras();

				$pdf=$application->make('dompdf.wrapper');

				if($tEmpresa->formatoComprobante=='Ticket')
				{
					$pdf->setPaper([0, 0, 270, 1000]);
				}

				$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
				$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
				$dataLogo=file_get_contents($pathLogo);
				$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

				$pdf->loadHTML(view('reciboventa/proforma', ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVenta' => $tReciboVenta, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVenta->total), ''),'base64Logo' => $base64Logo]));
				
				return $pdf->stream('proforma_' . date('Y-m-d') . '.pdf', ['attachment' => false]);

			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		return $this->plataformHelper->redirectError('Operación invalida.', '/');
	}

	public function actionImprimirComprobanteSinFe(SessionManager $sessionManager, Application $application, $codigoReciboVentaOutEf)
	{
		$tReciboVenta=TReciboVentaOutEf::with(['tpersonal.tusuario'])->whereRaw('codigoReciboVentaOutEf=?', [$codigoReciboVentaOutEf])->first();
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

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

		$pdf->loadHTML(view('reciboventa/'.strtolower($tReciboVenta->tipoRecibo).'sinfe', ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVenta' => $tReciboVenta, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVenta->total), ''), 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03').'-'.$tReciboVenta->numeroRecibo.'-PF.pdf', ['attachment' => false]);
	}

	public function actionDescargarPdfXml(SessionManager $sessionManager, Application $application, ResponseFactory $responseFactory, Encrypter $encrypter, $codigoReciboVenta)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		$tReciboVenta=TReciboVenta::find($codigoReciboVenta);
		
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

		$rutaBaseQr=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/qr';
		$nombreArchivoTemp=$tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03').'-'.$tReciboVenta->numeroRecibo;

		if(!file_exists($rutaBaseQr.'/'.$nombreArchivoTemp.'.png'))
		{
			if (!file_exists($rutaBaseQr))
			{
				mkdir($rutaBaseQr, 0777, true);
			}

			$contentQr=$tEmpresa->ruc
			.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03')
			.'|'.explode('-', $tReciboVenta->numeroRecibo)[0]
			.'|'.explode('-', $tReciboVenta->numeroRecibo)[1]
			.'|'.$tReciboVenta->impuestoAplicado
			.'|'.$tReciboVenta->total
			.'|'.$tReciboVenta->fechaComprobanteEmitido
			.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '6' : '1')
			.'|'.$tReciboVenta->documentoCliente;

			\QRcode::png($contentQr, $rutaBaseQr.'/'.$nombreArchivoTemp.'.png', QR_ECLEVEL_L, 4);
		}

		$pathQr=$rutaBaseQr.'/'.$nombreArchivoTemp.'.png';
		$typeQr=pathinfo($pathQr, PATHINFO_EXTENSION);
		$dataQr=file_get_contents($pathQr);
		$base64Qr='data:image/' . $typeQr . ';base64,' . base64_encode($dataQr);

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventa/'.strtolower($tReciboVenta->tipoRecibo), ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVenta' => $tReciboVenta, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVenta->total), ''), 'base64Qr' => $base64Qr, 'base64Logo' => $base64Logo]));

		$rutaFolderTemp=public_path().'/temp';
		$dataTemp=uniqid();

		file_put_contents($rutaFolderTemp.'-'.$dataTemp.'.pdf', $pdf->output());

		$rutaZipTemp=$rutaFolderTemp.'/'.$tReciboVenta->numeroRecibo.'-'.$tEmpresa->ruc.'-'.$dataTemp.'.zip';
		$nameZip=$tReciboVenta->numeroRecibo.'-'.$tEmpresa->ruc.'.zip';

	    $zip=new ZipArchive();
	    
	    $zip->open($rutaZipTemp, ZipArchive::CREATE);

		$zip->addFile($rutaFolderTemp.'-'.$dataTemp.'.pdf', $tReciboVenta->tipoRecibo.'-'.$dataTemp.'.pdf');

	    /*Begin: optencion de archivo XML*/

		if($tReciboVenta->tipoRecibo=='Factura')
		{
			$xmlName = $tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Boleta' ? '03' : '01').'-'.$tReciboVenta->numeroRecibo.'.xml';
			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getfilesxmlaszip',
			[
				'form_params' =>
				[
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.codigoUnicoVenta' => $tReciboVenta->codigoReciboVenta,
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