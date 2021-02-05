<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TOficina;
use App\Model\TOficinaProducto;
use App\Model\TOficinaProductoRetiro;

class OficinaProductoRetiroController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		try
		{
			if($_POST)
			{
				DB::beginTransaction();
				
				foreach($request->input('hdCodigoOficinaProducto') as $key => $value)
				{
					$tOficinaProducto=TOficinaProducto::find($request->input('hdCodigoOficinaProducto')[$key]);

					if($tOficinaProducto!=null)
					{
						if(
							$request->input('hdNombre')[$key]==''
							|| $request->input('hdPresentacion')[$key]==''
							|| $request->input('hdUnidadMedida')[$key]==''
							|| (
								$request->input('hdTipo')[$key]!='Genérico'
								&& $request->input('hdTipo')[$key]!='Comercial'
							)
						)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'almacenproductoretiro/insertar');
						}

						if($tOficinaProducto->cantidad < $request->input('hdCantidadProducto')[$key])
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Stock insuficiente para el producto '.($key+1).' de la lista.', 'oficinaproductoretiro/insertar');
						}

						if(!$tOficinaProducto->ventaMenorUnidad && !preg_match("/^[0-9]+$/", $request->input('hdCantidadProducto')[$key]))
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten retiros por unidades enteras mayores o iguales a 1 en el producto '.($key+1).' de la lista.', 'oficinaproductoretiro/insertar');
						}

						if(!$tOficinaProducto->ventaMenorUnidad && intval($request->input('hdCantidadProducto')[$key]) < 1)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten retiros por unidades enteras mayores o iguales a 1 en el producto '.($key+1).' de la lista.', 'oficinaproductoretiro/insertar');
						}

						if($tOficinaProducto->ventaMenorUnidad && $request->input('hdCantidadProducto')[$key] <= 0)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten retiros mayores a 0 en el producto '.($key+1).' de la lista.', 'oficinaproductoretiro/insertar');
						}

						if(!preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $request->input('hdMontoPerdido')[$key]))
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('El monto de perdida del producto '.($key+1).' de la lista, es incorrecto.', 'oficinaproductoretiro/insertar');
						}
						
						$tOficina = TOficina::find($request->input('hdCodigoOficinaOrigen'));
						
						$tOficinaProductoRetiro = new TOficinaProductoRetiro();

						$tOficinaProductoRetiro->codigoOficinaProducto = $request->input('hdCodigoOficinaProducto')[$key];
						$tOficinaProductoRetiro->codigoOficina = $request->input('hdCodigoOficinaOrigen');
						$tOficinaProductoRetiro->descripcionOficina = $tOficina->descripcion;
						$tOficinaProductoRetiro->presentacionProducto = $request->input('hdPresentacion')[$key];
						$tOficinaProductoRetiro->unidadMedidaProducto = $request->input('hdUnidadMedida')[$key];
						$tOficinaProductoRetiro->nombreCompletoProducto = $request->input('hdNombre')[$key];
						$tOficinaProductoRetiro->tipoProducto = $request->input('hdTipo')[$key];
						$tOficinaProductoRetiro->precioCompraUnitarioProducto = $request->input('hdPrecioCompraUnitario')[$key];
						$tOficinaProductoRetiro->precioVentaUnitarioProducto = $request->input('hdPrecioVentaUnitario')[$key];
						$tOficinaProductoRetiro->fechaVencimientoProducto = $request->input('hdFechaVencimiento')[$key];
						$tOficinaProductoRetiro->cantidadUnidad = $request->input('hdCantidadProducto')[$key];
						$tOficinaProductoRetiro->descripcion = $request->input('hdDescripcionRetiro');
						$tOficinaProductoRetiro->montoPerdido = $request->input('hdMontoPerdido')[$key];
												
						$tOficinaProductoRetiro->save();

						$tOficinaProducto->cantidad = $tOficinaProducto->cantidad - $request->input('hdCantidadProducto')[$key];

						$tOficinaProducto->save();
					}
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'oficinaproductoretiro/ver');
			}
			$listTOficina=TOficina::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

			return view('oficinaproductoretiro/insertar', ['listTOficina' => $listTOficina]);
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}

	public function actionVer(Request $request, SessionManager $sessionManager, $pagina = 1)
	{
		if($request->input('q'))
		{
			$term = $request->input('q');
			$paginationPrepare = null;

			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TOficinaProductoRetiro::whereRaw('replace(concat(nombreCompletoProducto, descripcion, descripcionOficina), \' \', \'\') like replace(?, \' \', \'\')', ['%'.$term.'%'] )
				->whereHas('toficina', function($query) use ($sessionManager){
					$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')] );
				})
				->orderBy('created_at', 'desc'), null, $pagina);
			}
			else
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TOficinaProductoRetiro::whereRaw('compareFind(concat(nombreCompletoProducto, descripcion, descripcionOficina), ?, 77)=1', [$term] )
				->whereHas('toficina', function($query) use ($sessionManager){
					$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')] );
				})
				->orderBy('created_at', 'desc'), null, $pagina);
			}		

			$paginationRender = $this->plataformHelper->renderizarPaginacion('oficinaproductoretiro/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('oficinaproductoretiro/ver', ['listaTOficinaProductoRetiro' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TOficinaProductoRetiro::
		whereHas('toficina', function($query) use ($sessionManager){
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')] );
		})->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender = $this->plataformHelper->renderizarPaginacion('oficinaproductoretiro/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('oficinaproductoretiro/ver', ['listaTOficinaProductoRetiro' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionDetalle(Request $request)
	{
		$tOficinaProductoRetiro=TOficinaProductoRetiro::with('toficina')->find($request->input('codigoOficinaProductoRetiro'));
		
		return view('oficinaproductoretiro/detalle', ['tOficinaProductoRetiro' => $tOficinaProductoRetiro]);
	}
}
?>