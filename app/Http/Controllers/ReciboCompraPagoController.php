<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TReciboCompra;
use App\Model\TReciboCompraPago;

class ReciboCompraPagoController extends Controller
{
	public function actionPago(Request $request)
	{
		$tReciboCompra = TReciboCompra::with('trecibocomprapago')->find($request->input('codigoReciboCompra'));
		
		return view('recibocomprapago/pago', ['tReciboCompra' => $tReciboCompra]);
	}

	public function actionRealizarPago(Request $request, SessionManager $sessionManager)
	{
		try
		{
			DB::beginTransaction();
			
			if(!($sessionManager->has('codigoAlmacen')))
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en un almacén para realizar esta operación.', '/');
			}

			$tReciboCompra=TReciboCompra::with('trecibocomprapago')->find($request->input('codigoReciboCompra'));
			
			if($tReciboCompra->total == $tReciboCompra->tReciboCompraPago->sum('monto'))
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('Ya se pago por completo la compra!.', 'recibocompra/ver');
			}

			$monto = number_format(floatval($request->input('monto')), 2, '.', '');
			
			if($monto <= 0)
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('No puedo realizar un pago con el monto "0".', 'recibocompra/ver');
			}

			if($monto > $tReciboCompra->total)
			{
				DB::rollback();

				return $this->plataformHelper->redirectError('El monto que intenta pagar es mayor al total de la compra.', 'recibocompra/ver');
			}

			$tReciboCompraPago = new TReciboCompraPago();

			$tReciboCompraPago->codigoReciboCompra = $tReciboCompra->codigoReciboCompra;
			$tReciboCompraPago->monto = $monto;
			$tReciboCompraPago->descripcion = '';
			$tReciboCompraPago->save();

			if(($tReciboCompra->tReciboCompraPago->sum('monto') + $monto) == $tReciboCompra->total)
			{
				$tReciboCompra->estadoCredito = true;
				$tReciboCompra->save();
			}

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'recibocompra/ver');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionEliminar($codigoReciboCompraPago, SessionManager $sessionManager)
	{
		try
		{
			DB::beginTransaction();

			if($sessionManager->has('codigoAlmacen')==null)
			{
				return $this->plataformHelper->redirectError('Debe estar logueado en un almacen para realizar esta operación.', '/');
			}

			$tReciboCompraPago = TReciboCompraPago::find($codigoReciboCompraPago);

			if(!$tReciboCompraPago)
			{
				return $this->plataformHelper->redirectError('No se encontró el pago a eliminar, contacte con el administrador.', 'recibocompra/ver');
			}

			$tReciboCompra = TReciboCompra::find($tReciboCompraPago->codigoReciboCompra);
			$tReciboCompra->estadoCredito = false;
			$tReciboCompra->save();

			$tReciboCompraPago->delete();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'recibocompra/ver');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}
}
?>