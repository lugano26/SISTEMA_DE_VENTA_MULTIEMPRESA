<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TAlmacen;
use App\Model\TAlmacenProducto;
use \App\Model\TAlmacenProductoRetiro;

class AlmacenProductoRetiroController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		try
		{
			if($_POST)
			{
				DB::beginTransaction();
				
				foreach($request->input('hdCodigoAlmacenProducto') as $key => $value)
				{
					$tAlmacenProducto=TAlmacenProducto::find($request->input('hdCodigoAlmacenProducto')[$key]);

					if($tAlmacenProducto!=null)
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

						if($tAlmacenProducto->cantidad < $request->input('hdCantidadProducto')[$key])
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Stock insuficiente para el producto '.($key+1).' de la lista.', 'almacenproductoretiro/insertar');
						}

						if(!$tAlmacenProducto->ventaMenorUnidad && !preg_match("/^[0-9]+$/", $request->input('hdCantidadProducto')[$key]))
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten retiros por unidades enteras mayores a 1 en el producto '.($key+1).' de la lista.', 'almacenproductoretiro/insertar');
						}

						if(!$tAlmacenProducto->ventaMenorUnidad && intval($request->input('hdCantidadProducto')[$key]) < 1)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten retiros por unidades enteras mayores a 1 en el producto '.($key+1).' de la lista.', 'almacenproductoretiro/insertar');
						}

						if($tAlmacenProducto->ventaMenorUnidad && $request->input('hdCantidadProducto')[$key] <= 0)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('Sólo se permiten retiros mayores a 0 en el producto '.($key+1).' de la lista.', 'almacenproductoretiro/insertar');
						}

						if(!preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $request->input('hdMontoPerdido')[$key]))
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError('El monto de perdida del producto '.($key+1).' de la lista, es incorrecto.', 'almacenproductoretiro/insertar');
						}
						
						$tAlmacen = TAlmacen::find($request->input('hdCodigoAlmacenOrigen'));

						$tAlmacenProductoRetiro = new TAlmacenProductoRetiro();

						$tAlmacenProductoRetiro->codigoAlmacenProducto = $request->input('hdCodigoAlmacenProducto')[$key];
						$tAlmacenProductoRetiro->codigoAlmacen = $request->input('hdCodigoAlmacenOrigen');
						$tAlmacenProductoRetiro->descripcionAlmacen = $tAlmacen->descripcion;
						$tAlmacenProductoRetiro->presentacionProducto = $request->input('hdPresentacion')[$key];
						$tAlmacenProductoRetiro->unidadMedidaProducto = $request->input('hdUnidadMedida')[$key];
						$tAlmacenProductoRetiro->nombreCompletoProducto = $request->input('hdNombre')[$key];
						$tAlmacenProductoRetiro->tipoProducto = $request->input('hdTipo')[$key];
						$tAlmacenProductoRetiro->precioCompraUnitarioProducto = $request->input('hdPrecioCompraUnitario')[$key];
						$tAlmacenProductoRetiro->precioVentaUnitarioProducto = $request->input('hdPrecioVentaUnitario')[$key];
						$tAlmacenProductoRetiro->fechaVencimientoProducto = $request->input('hdFechaVencimiento')[$key];
						$tAlmacenProductoRetiro->cantidadUnidad = $request->input('hdCantidadProducto')[$key];
						$tAlmacenProductoRetiro->descripcion = $request->input('hdDescripcionRetiro');
						$tAlmacenProductoRetiro->montoPerdido = $request->input('hdMontoPerdido')[$key];
												
						$tAlmacenProductoRetiro->save();

						$tAlmacenProducto->cantidad = $tAlmacenProducto->cantidad - $request->input('hdCantidadProducto')[$key];

						$tAlmacenProducto->save();
					}
				}

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'almacenproductoretiro/ver');
			}

			$listTAlmacen=TAlmacen::whereRaw('codigoEmpresa = ?', [$sessionManager->get('codigoEmpresa')])->get();

			return view('almacenproductoretiro/insertar', ['listTAlmacen' => $listTAlmacen]);
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
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProductoRetiro::whereRaw('replace(concat(nombreCompletoProducto, descripcion, descripcionAlmacen), \' \', \'\') like replace(?, \' \', \'\')', ['%'.$term.'%'] )
				->whereHas('talmacen', function($query) use ($sessionManager){
					$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')] );
				})
				->orderBy('created_at', 'desc'), null, $pagina);

			}
			else
			{
				$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProductoRetiro::whereRaw('compareFind(concat(nombreCompletoProducto, descripcion, descripcionAlmacen), ?, 77)=1', [$term] )
				->whereHas('talmacen', function($query) use ($sessionManager){
					$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')] );
				})
				->orderBy('created_at', 'desc'), null, $pagina);
			}

			$paginationRender = $this->plataformHelper->renderizarPaginacion('almacenproductoretiro/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('almacenproductoretiro/ver', ['listaTAlmacenProductoRetiro' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare = $this->plataformHelper->prepararPaginacion(TAlmacenProductoRetiro::whereHas('talmacen', function($query) use ($sessionManager){
			$query->whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')] );
		})
		->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender = $this->plataformHelper->renderizarPaginacion('almacenproductoretiro/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('almacenproductoretiro/ver', ['listaTAlmacenProductoRetiro' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionDetalle(Request $request)
	{
		$tAlmacenProductoRetiro=TAlmacenProductoRetiro::with('talmacen')->find($request->input('codigoAlmacenProductoRetiro'));

		return view('almacenproductoretiro/detalle', ['tAlmacenProductoRetiro' => $tAlmacenProductoRetiro]);
	}
}
?>