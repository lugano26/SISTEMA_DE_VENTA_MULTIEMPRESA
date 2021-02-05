<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Foundation\Application;
use Carbon\Carbon;

use DB;

use App\Model\TReciboVenta;
use App\Model\TReciboVentaLetra;
use App\Model\TReciboVentaPago;
use App\Model\TEmpresa;
use App\Model\TReciboVentaOutEf;
use App\Model\TReciboVentaLetraOutEf;
use App\Model\TReciboVentaPagoOutEf;

class ReciboVentaLetraController extends Controller
{
	public function actionPagoLetra(Request $request, SessionManager $sessionManager)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			echo '<div class="alert alert-danger"><h4><i class="icon fa fa-ban"></i> Prohibido!</h4>No tiene autorización para realizar operaciones electrónicas con la SUNAT.</div>';exit;
		}

		$tReciboVenta = TReciboVenta::with(['treciboventaletra', 'treciboventapago'])->find($request->input('codigoReciboVenta'));

		return view('reciboventaletra/pagoletra', ['tReciboVenta' => $tReciboVenta]);
	}

	public function actionRealizarPagoLetra(Request $request, SessionManager $sessionManager)
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

			$listaTReciboVentaLetra=TReciboVentaLetra::whereRaw('codigoReciboVenta=? and porPagar > 0', [$request->input('codigoReciboVenta')])->orderBy('fechaPagar', 'asc')->get();
			
			if($listaTReciboVentaLetra == null || $listaTReciboVentaLetra->count() < 1)
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('Ya se pagaron todas las letras.', 'reciboventa/ver');
			}

			$monto = number_format(floatval($request->input('monto')), 2, '.', '');
			
			if($monto <= 0)
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('No puedo realizar un pago con el monto "0".', 'reciboventa/ver');
			}

			if($monto > $listaTReciboVentaLetra->sum('porPagar'))
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('El monto que intenta pagar es mayor al total de la deuda.', 'reciboventa/ver');
			}

			$montoPagar = $monto;

			foreach($listaTReciboVentaLetra as $value)
			{
				$monto = number_format($monto, 2, '.', '');

				if($monto <= 0)
				{
					break;
				}

				$tempMonto = 0;

				if(now()->toDateString() > $value->fechaPagar)
				{
					$diasMora = Carbon::parse($value->fechaPagar)->diffInDays(Carbon::parse(now()->toDateString()));

					$value->diasMora = $diasMora;
				}

				if($monto > $value->porPagar)
				{
					$tempMonto = $value->porPagar;
					$value->pagado += $value->porPagar;
					$value->porPagar = 0;
				}
				else
				{
					$tempMonto = $monto;
					$value->pagado += $monto;
					$value->porPagar -= $monto;						
				}

				$monto -= $tempMonto;

				$value->estado = $value->porPagar == 0 ? true : false;				
				$value->save();				
			}

			$tReciboVentaPago = new TReciboVentaPago();

			$tReciboVentaPago->codigoReciboVenta = $value->codigoReciboVenta;
			$tReciboVentaPago->monto = $montoPagar;
			$tReciboVentaPago->descripcion = '';

			$tReciboVentaPago->save();

			if(! $listaTReciboVentaLetra->contains(function($value, $key){
				return $value->porPagar > 0;
			}))
			{
				$tReciboVenta = TReciboVenta::find($listaTReciboVentaLetra->first()->codigoReciboVenta);

				$tReciboVenta->estadoCredito = true;

				$tReciboVenta->save();
			}

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/ver');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}
	
	public function actionEliminar($codigoReciboVentaPago, SessionManager $sessionManager)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		try
		{
			DB::beginTransaction();

			if($sessionManager->has('codigoOficina')==null)
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en un oficina para realizar esta operación.', '/');
			}

			$tReciboVentaPago = TReciboVentaPago::find($codigoReciboVentaPago);

			if(!$tReciboVentaPago)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'reciboventa/ver');
			}

			$tReciboVenta = TReciboVenta::with('treciboventaletra' )->find($tReciboVentaPago->codigoReciboVenta);

			if(!$tReciboVenta)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'reciboventa/ver');
			}
			
			$montoPagoEliminar = $tReciboVentaPago->monto;

			foreach($tReciboVenta->tReciboVentaLetra->sortByDesc('fechaPagar') as $tReciboVentaLetra)
			{
				if($montoPagoEliminar <= 0)
				{
					break;
				}

				if($tReciboVentaLetra->pagado > 0)
				{	
					$pagadoTemp = $tReciboVentaLetra->pagado;

					$tReciboVentaLetra->porPagar += ($montoPagoEliminar >= $tReciboVentaLetra->pagado ) ? $tReciboVentaLetra->pagado : $montoPagoEliminar;
					$tReciboVentaLetra->pagado -= ($montoPagoEliminar >= $tReciboVentaLetra->pagado ) ? $tReciboVentaLetra->pagado : $montoPagoEliminar;
					$tReciboVentaLetra->estado = false;

					$tReciboVentaLetra->save();

					$montoPagoEliminar -= $pagadoTemp;
				}
			}


			$tReciboVenta->estadoCredito = !$tReciboVenta->TReciboVentaLetra->contains(function($value, $key){
				return $value->porPagar > 0;
			});
			
			$tReciboVenta->save();

			$tReciboVentaPago->delete();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/ver');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionMarcarComoPagadoLetra($codigoReciboVentaLetra, SessionManager $sessionManager)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		try
		{
			DB::beginTransaction();

			if($sessionManager->has('codigoOficina')==null)
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en un oficina para realizar esta operación.', '/');
			}

			$tReciboVentaLetra = TReciboVentaLetra::find($codigoReciboVentaLetra);

			if(!$tReciboVentaLetra)
			{
				return $this->plataformHelper->redirectError('No se encontró la letra a marcar como pagado, contacte con el administrador.', 'reciboventa/ver');
			}

			$tReciboVenta = TReciboVenta::with('treciboventaletra')->find($tReciboVentaLetra->codigoReciboVenta);
			$tReciboVentaLetra = $tReciboVenta->tReciboVentaLetra->first(function ($value, $key) use ($codigoReciboVentaLetra) {
				return $value->codigoReciboVentaLetra == $codigoReciboVentaLetra;
			});

			$letrasAnteriores = $tReciboVenta->treciboventaletra->where('codigoReciboVentaLetra', '<', $tReciboVentaLetra->codigoReciboVentaLetra);

			if(count($letrasAnteriores) && $letrasAnteriores->contains(function($item){
				return !$item->estado;
			}))
			{
				return $this->plataformHelper->redirectError('No se puede marcar esta letra como pagada, tiene letras anteriores sin completar.', 'reciboventa/ver');
			}
			
			$tReciboVentaPago = new TReciboVentaPago();

			$tReciboVentaPago->codigoReciboVenta = $tReciboVentaLetra->codigoReciboVenta;
			$tReciboVentaPago->monto = $tReciboVentaLetra->porPagar;
			$tReciboVentaPago->descripcion = '';

			$tReciboVentaPago->save();

			$tReciboVentaLetra->pagado += $tReciboVentaLetra->porPagar;
			$tReciboVentaLetra->porPagar = 0;
			$tReciboVentaLetra->estado = true;

			$tReciboVentaLetra->save();

			$completadoPagos = true;

			foreach($tReciboVenta->tReciboVentaLetra as $key => $value)
			{
				if(!$value->estado)
				{
					$completadoPagos = false;

					break;
				}
			}

			$tReciboVenta->estadoCredito = $completadoPagos;

			$tReciboVenta->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/ver');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionMarcarComoPagadoLetraSinFe($codigoReciboVentaLetraOutEf, SessionManager $sessionManager)
	{
		try
		{
			DB::beginTransaction();

			if($sessionManager->has('codigoOficina')==null)
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en un oficina para realizar esta operación.', '/');
			}

			$tReciboVentaLetra = TReciboVentaLetraOutEf::find($codigoReciboVentaLetraOutEf);

			if(!$tReciboVentaLetra)
			{
				return $this->plataformHelper->redirectError('No se encontró la letra a marcar como pagado, contacte con el administrador.', 'reciboventa/listasinfe');
			}

			$tReciboVenta = TReciboVentaOutEf::with('tReciboVentaLetraOutEf')->find($tReciboVentaLetra->codigoReciboVentaOutEf);
			$tReciboVentaLetra = $tReciboVenta->tReciboVentaLetraOutEf->first(function ($value, $key) use ($codigoReciboVentaLetraOutEf) {
				return $value->codigoReciboVentaLetraOutEf == $codigoReciboVentaLetraOutEf;
			});

			$letrasAnteriores = $tReciboVenta->treciboventaletraoutef->where('codigoReciboVentaLetraOutEf', '<', $tReciboVentaLetra->codigoReciboVentaLetraOutEf);

			if(count($letrasAnteriores) && $letrasAnteriores->contains(function($item){
				return !$item->estado;
			}))
			{
				return $this->plataformHelper->redirectError('No se puede marcar esta letra como pagada, tiene letras anteriores sin completar.', 'reciboventa/listasinfe');
			}
			
			$tReciboVentaPago = new TReciboVentaPagoOutEf();

			$tReciboVentaPago->codigoReciboVentaOutEf = $tReciboVentaLetra->codigoReciboVentaOutEf;
			$tReciboVentaPago->monto = $tReciboVentaLetra->porPagar;
			$tReciboVentaPago->descripcion = '';

			$tReciboVentaPago->save();

			$tReciboVentaLetra->pagado += $tReciboVentaLetra->porPagar;
			$tReciboVentaLetra->porPagar = 0;
			$tReciboVentaLetra->estado = true;

			$tReciboVentaLetra->save();

			$completadoPagos = true;

			foreach($tReciboVenta->tReciboVentaLetraOutEf as $key => $value)
			{
				if(!$value->estado)
				{
					$completadoPagos = false;
					break;
				}
			}

			$tReciboVenta->estadoCredito = $completadoPagos;
			$tReciboVenta->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/listasinfe');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionPagoLetraSinFe(Request $request)
	{
		$tReciboVenta = TReciboVentaOutEf::with(['treciboventaletraoutef', 'treciboventapagooutef'])->find($request->input('codigoReciboVenta'));

		return view('reciboventaletra/pagoletrasinfe', ['tReciboVenta' => $tReciboVenta]);
	}

	public function actionRealizarPagoLetraSinFe(Request $request, SessionManager $sessionManager)
	{
		try
		{
			DB::beginTransaction();

			if(!($sessionManager->has('codigoOficina')))
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en una oficina para realizar esta operación.', '/');
			}

			$tReciboVenta = TReciboVentaOutEf::find($request->input('codigoReciboVenta'));

			if(!$tReciboVenta->estado)
			{
				return $this->plataformHelper->redirectError('La venta fue anulada, no se puede realizar pagos.', 'reciboventa/listasinfe');
			}

			$listaTReciboVentaLetra=TReciboVentaLetraOutEf::whereRaw('codigoReciboVentaOutEf=? and porPagar > 0', [$request->input('codigoReciboVenta')])->orderBy('fechaPagar', 'asc')->get();
			
			if($listaTReciboVentaLetra == null || $listaTReciboVentaLetra->count() < 1)
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('Ya se pagaron todas las letras.', 'reciboventa/listasinfe');
			}

			$monto = number_format(floatval($request->input('monto')), 2, '.', '');
			
			if($monto <= 0)
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('No puedo realizar un pago con el monto "0".', 'reciboventa/listasinfe');
			}

			if($monto > $listaTReciboVentaLetra->sum('porPagar'))
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('El monto que intenta pagar es mayor al total de la deuda.', 'reciboventa/listasinfe');
			}

			$montoPago = $monto;

			foreach($listaTReciboVentaLetra as $value)
			{
				$monto = number_format($monto, 2, '.', '');

				if($monto <= 0)
				{
					break;
				}

				$tempMonto = 0;

				if(now()->toDateString() > $value->fechaPagar)
				{
					$diasMora = Carbon::parse($value->fechaPagar)->diffInDays(Carbon::parse(now()->toDateString()));

					$value->diasMora = $diasMora;
				}

				if($monto > $value->porPagar)
				{
					$tempMonto = $value->porPagar;
					$value->pagado += $value->porPagar;
					$value->porPagar = 0;
				}
				else
				{
					$tempMonto = $monto;
					$value->pagado += $monto;
					$value->porPagar -= $monto;						
				}

				$monto -= $tempMonto;

				$value->estado = $value->porPagar == 0 ? true : false;				
				$value->save();				
			}

			$tReciboVentaPago = new TReciboVentaPagoOutEf();

			$tReciboVentaPago->codigoReciboVentaOutEf = $value->codigoReciboVentaOutEf;
			$tReciboVentaPago->monto = $montoPago;
			$tReciboVentaPago->descripcion = '';

			$tReciboVentaPago->save();

			if(! $listaTReciboVentaLetra->contains(function($value, $key){
				return $value->porPagar > 0;
			}))
			{
				$tReciboVenta = TReciboVentaOutEf::find($listaTReciboVentaLetra->first()->codigoReciboVentaOutEf);
				$tReciboVenta->estadoCredito = true;

				$tReciboVenta->save();
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

	public function actionEliminarSinFe($codigoReciboVentaPago, SessionManager $sessionManager)
	{
		try
		{
			DB::beginTransaction();

			if($sessionManager->has('codigoOficina')==null)
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en un oficina para realizar esta operación.', '/');
			}

			$tReciboVentaPago = TReciboVentaPagoOutEf::find($codigoReciboVentaPago);

			if(!$tReciboVentaPago)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'reciboventa/listasinfe');
			}

			$tReciboVenta = TReciboVentaOutEf::with('treciboventaletraoutef' )->find($tReciboVentaPago->codigoReciboVentaOutEf);

			if(!$tReciboVenta)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'reciboventa/listasinfe');
			}
			
			$montoPagoEliminar = $tReciboVentaPago->monto;

			foreach($tReciboVenta->tReciboVentaLetraOutEf->sortByDesc('fechaPagar') as $tReciboVentaLetra)
			{
				if($montoPagoEliminar <= 0)
				{
					break;
				}

				if($tReciboVentaLetra->pagado > 0)
				{	
					$pagadoTemp = $tReciboVentaLetra->pagado;

					$tReciboVentaLetra->porPagar += ($montoPagoEliminar >= $tReciboVentaLetra->pagado ) ? $tReciboVentaLetra->pagado : $montoPagoEliminar;
					$tReciboVentaLetra->pagado -= ($montoPagoEliminar >= $tReciboVentaLetra->pagado ) ? $tReciboVentaLetra->pagado : $montoPagoEliminar;
					$tReciboVentaLetra->estado = false;
					$tReciboVentaLetra->save();

					$montoPagoEliminar -= $pagadoTemp;
				}
			}


			$tReciboVenta->estadoCredito = !$tReciboVenta->TReciboVentaLetraOutEf->contains(function($value, $key){
				return $value->porPagar > 0;
			});
			
			$tReciboVenta->save();

			$tReciboVentaPago->delete();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'reciboventa/listasinfe');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actiomImprimirComprobanteSinFe($codigoReciboVentaPago, SessionManager $sessionManager, Application $application)
	{
		try
		{
			if($sessionManager->has('codigoOficina')==null)
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en un oficina para realizar esta operación.', '/');
			}

			$tReciboVentaPago = TReciboVentaPagoOutEf::find($codigoReciboVentaPago);

			if(!$tReciboVentaPago)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'reciboventa/listasinfe');
			}

			$tReciboVenta = TReciboVentaOutEf::with('treciboventaletraoutef' )->find($tReciboVentaPago->codigoReciboVentaOutEf);

			if(!$tReciboVenta)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'reciboventa/listasinfe');
			}
		
			$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

			$pdf=$application->make('dompdf.wrapper');

			$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
			$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
			$dataLogo=file_get_contents($pathLogo);
			$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);
			
			$pdf->loadHTML(view('reciboventaletra/imprimircomprobantesinfe', ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVentaPago' => $tReciboVentaPago, 'tReciboVenta' => $tReciboVenta, 'base64Logo' => $base64Logo]));
			
			return $pdf->stream($tEmpresa->ruc.'-'.$tReciboVentaPago->codigoReciboVentaPagoOutEf.'.pdf', ['attachment' => false]);
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actiomImprimirComprobante($codigoReciboVentaPago, SessionManager $sessionManager, Application $application)
	{
		try
		{
			if($sessionManager->has('codigoOficina')==null)
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en un oficina para realizar esta operación.', '/');
			}

			$tReciboVentaPago = TReciboVentaPago::find($codigoReciboVentaPago);

			if(!$tReciboVentaPago)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'reciboventa/listasinfe');
			}

			$tReciboVenta = TReciboVenta::with('treciboventaletra' )->find($tReciboVentaPago->codigoReciboVenta);

			if(!$tReciboVenta)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'reciboventa/listasinfe');
			}
		
			$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->first();

			$pdf=$application->make('dompdf.wrapper');

			$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
			$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
			$dataLogo=file_get_contents($pathLogo);
			$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);
			
			$pdf->loadHTML(view('reciboventaletra/imprimircomprobante', ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVentaPago' => $tReciboVentaPago, 'tReciboVenta' => $tReciboVenta, 'base64Logo' => $base64Logo]));
			
			return $pdf->stream($tEmpresa->ruc.'-'.$tReciboVentaPago->codigoReciboVentaPago.'.pdf', ['attachment' => false]);
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}
}
?>