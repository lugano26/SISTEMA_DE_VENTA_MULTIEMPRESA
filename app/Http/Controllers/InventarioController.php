<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TInventario;
use App\Model\TAmbiente;

class InventarioController extends Controller
{
	public function actionInsertar(Request $request, SessionManager $sessionManager)
	{
		if($_POST)
		{
			try
			{
				DB::beginTransaction();

				if($request->get('chkReplicas') !== "on")
				{
					if($request->get('txtCodigoBarras') !== '')
					{
						$registradoInventario = TInventario::whereRaw('replace(codigoBarras, \' \', \'\')=replace(?, \' \', \'\') and codigoAmbienteEspacio in (SELECT codigoAmbienteEspacio FROM tambienteespacio WHERE codigoAmbiente=? and estado=?)', [$request->get('txtCodigoBarras'), $request->get('selectAmbiente'), true])->get();
					
						if($registradoInventario && count($registradoInventario) > 0)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError("Ya se registro un item con ese 'Código de barras'", 'inventario/insertar');
						}
					}

					if($request->get('txtSerie') !== '')
					{
						$registradoInventario = TInventario::whereRaw('replace(serie, \' \', \'\')=replace(?, \' \', \'\') and codigoAmbienteEspacio in (SELECT codigoAmbienteEspacio FROM tambienteespacio WHERE codigoAmbiente=? and estado=?)', [$request->get('txtSerie'), $request->get('selectAmbiente'), true])->get();

						if($registradoInventario && count($registradoInventario) > 0)
						{
							DB::rollBack();

							$request->flash();

							return $this->plataformHelper->redirectError("Ya se registro un item con esa 'Serie'", 'inventario/insertar');
						}
					}

					if(
						trim($request->input('txtNombre'))==''
						|| (
							$request->input('txtEstado')!='Nuevo'
							&& $request->input('txtEstado')!='Buen estado'
							&& $request->input('txtEstado')!='Con daños leves'
							&& $request->input('txtEstado')!='Deteriorado'
							&& $request->input('txtEstado')!='Inservible'
						)
					)
					{
						DB::rollBack();

						$request->flash();

						return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'inventario/insertar');
					}
					
					$tInventario = new TInventario();

					$tInventario->codigoBarras = $request->get('txtCodigoBarras');
					$tInventario->serie = $request->get('txtSerie');
					$tInventario->codigoAmbienteEspacio = $request->get('selectEspacio');
					$tInventario->modelo = $request->get('txtModelo');
					$tInventario->nombre = $request->get('txtNombre');
					$tInventario->descripcion = $request->get('txtDescripcion');
					$tInventario->dimensionAncho = $request->get('txtDimensionAnchoNumero') !== '' ? $request->get('txtDimensionAnchoNumero') . ' ' . $request->get('txtDimensionAnchoMedida') : '';
					$tInventario->dimensionLargo = $request->get('txtDimensionLargoNumero') !== '' ? $request->get('txtDimensionLargoNumero') . ' ' . $request->get('txtDimensionLargoMedida') : '';
					$tInventario->dimensionAlto = $request->get('txtDimensionAltoNumero') !== '' ? $request->get('txtDimensionAltoNumero') . ' ' . $request->get('txtDimensionAltoMedida') : '';
					$tInventario->pesoKg = $request->get('txtPeso');
					$tInventario->estado = $request->get('txtEstado');

					$tInventario->save();
				}
				else
				{
					$tInventarioBatch = [];
					$auxDate = date("Y-m-d H:i:s");

					for($i = 1; $i <= intval($request->get('txtInstancias')); $i++)
					{
						$tInventarioBatch[] = [
							'codigoBarras' => '',
							'serie' => '',
							'codigoAmbienteEspacio' => $request->get('selectEspacio'),
							'modelo' => $request->get('txtModelo'),
							'nombre' => $request->get('txtNombre'),
							'descripcion' => $request->get('txtDescripcion'),
							'dimensionAncho' => $request->get('txtDimensionAnchoNumero') !== '' ? $request->get('txtDimensionAnchoNumero') . ' ' . $request->get('txtDimensionAnchoMedida') : '',
							'dimensionLargo' => $request->get('txtDimensionLargoNumero') !== '' ? $request->get('txtDimensionLargoNumero') . ' ' . $request->get('txtDimensionLargoMedida') : '',
							'dimensionAlto' => $request->get('txtDimensionAltoNumero') !== '' ? $request->get('txtDimensionAltoNumero') . ' ' . $request->get('txtDimensionAltoMedida') : '',
							'pesoKg' => $request->get('txtPeso'),
							'estado' => $request->get('txtEstado'),
							'created_at' => $auxDate,
							'updated_at' => $auxDate
						];
					}

					TInventario::insert($tInventarioBatch);
				}				

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'inventario/insertar');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
        }
		
		$listaTAmbiente = TAmbiente::whereRaw('((? and codigoOficina=?) or (? and codigoAlmacen=?))', [$sessionManager->has('codigoOficina'), $sessionManager->get('codigoOficina'), $sessionManager->has('codigoAlmacen'), $sessionManager->get('codigoAlmacen')])->get();
		
		return view('inventario/insertar', ['listaTAmbiente' => $listaTAmbiente]);
	}
	
	public function actionVer(Request $request, SessionManager $sessionManager, $pagina=1)
	{
		if($request->has('q'))
		{
			$term=$request->input('q');
			$paginationPrepare = null;

			if($request->input('searchPerformance')=='Performance')
			{
				$paginationPrepare=$this->plataformHelper->prepararPaginacion(TInventario::with('tambienteespacio.tambiente')->
				whereRaw('(replace(concat(codigoBarras, nombre), \' \', \'\') like replace(?, \' \', \'\')  or codigoAmbienteEspacio in (select codigoAmbienteEspacio from tambienteespacio where estado=? and replace(seccion, \' \', \'\') like replace(?, \' \', \'\')) or codigoAmbienteEspacio in (select codigoAmbienteEspacio from tambienteespacio WHERE estado=? and codigoAmbiente IN (select codigoAmbiente from tambiente where replace(nombre, \' \', \'\') like replace(?, \' \', \'\'))) )  and codigoAmbienteEspacio in (SELECT codigoAmbienteEspacio FROM tambienteespacio WHERE estado=? and codigoAmbiente IN ( SELECT codigoAmbiente FROM tambiente WHERE (? and codigoOficina=?) or (? and codigoAlmacen=?) ))', ['%'.$term.'%', true, '%'.$term.'%', true, '%'.$term.'%', true, $sessionManager->has('codigoOficina'), $sessionManager->get('codigoOficina'), $sessionManager->has('codigoAlmacen'), $sessionManager->get('codigoAlmacen')])
				->orderBy('created_at', 'desc'), null, $pagina);
			}
			else
			{
				$paginationPrepare=$this->plataformHelper->prepararPaginacion(TInventario::with('tambienteespacio.tambiente')->
				whereRaw('(compareFind(concat(codigoBarras, nombre), ?, 77)=1 or codigoAmbienteEspacio in (select codigoAmbienteEspacio from tambienteespacio where estado=? and compareFind(seccion, ?, 77)=1) or codigoAmbienteEspacio in (select codigoAmbienteEspacio from tambienteespacio where estado=? and codigoAmbiente in (select codigoAmbiente from tambiente where compareFind(nombre, ?, 77)=1)) ) and codigoAmbienteEspacio in (SELECT codigoAmbienteEspacio FROM tambienteespacio WHERE estado=? and codigoAmbiente IN ( SELECT codigoAmbiente FROM tambiente WHERE (? and codigoOficina=?) or (? and codigoAlmacen=?) ))', [$term, true, $term, true, $term, true, $sessionManager->has('codigoOficina'), $sessionManager->get('codigoOficina'), $sessionManager->has('codigoAlmacen'), $sessionManager->get('codigoAlmacen')])				
				->orderBy('created_at', 'desc'), null, $pagina);
			}

			$paginationRender=$this->plataformHelper->renderizarPaginacion('inventario/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
			
			return view('inventario/ver', ['genericHelper' => $this->plataformHelper, 'listaTInventario' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare=$this->plataformHelper->prepararPaginacion(TInventario::with('tambienteespacio.tambiente')->whereRaw('codigoAmbienteEspacio in (SELECT codigoAmbienteEspacio FROM tambienteespacio WHERE estado=? and codigoAmbiente IN ( SELECT codigoAmbiente FROM tambiente WHERE (? and codigoOficina=?) or (? and codigoAlmacen=?) ))', [true, $sessionManager->has('codigoOficina'), $sessionManager->get('codigoOficina'), $sessionManager->has('codigoAlmacen'), $sessionManager->get('codigoAlmacen')])->orderBy('created_at', 'desc'), null, $pagina);
		$paginationRender=$this->plataformHelper->renderizarPaginacion('inventario/ver', $paginationPrepare["cantidadPaginas"], $pagina);

		return view('inventario/ver', ['genericHelper' => $this->plataformHelper, 'listaTInventario' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender]);
	}

	public function actionEditar(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoInventario'))
		{
			try
			{
				DB::beginTransaction();

				$tInventario=tInventario::find($request->input('hdCodigoInventario'));

				if($tInventario == null)
				{
					return $this->plataformHelper->redirectError('No se encontró el item de inventario.', 'inventario/ver' );
				}
				
				if($request->get('txtCodigoBarras') !== '')
				{
					$registradoInventario = TInventario::whereRaw('replace(codigoBarras, \' \', \'\')=replace(?, \' \', \'\') and codigoAmbienteEspacio in (SELECT codigoAmbienteEspacio FROM tambienteespacio WHERE estado=? and codigoAmbiente=?) and codigoInventario <> ?', [$request->get('txtCodigoBarras'), true, $request->get('selectAmbiente'), $request->get('hdCodigoInventario')])->get();
					
					if($registradoInventario && count($registradoInventario) > 0)
					{
						DB::rollBack();
	
						$request->flash();
	
						return $this->plataformHelper->redirectError("Ya se registro un item con ese 'Código de barras'", 'inventario/ver');
					}
				}				
				
				if($request->get('txtSerie') !== '')
				{
					$registradoInventario = TInventario::whereRaw('replace(serie, \' \', \'\')=replace(?, \' \', \'\') and codigoAmbienteEspacio in (SELECT codigoAmbienteEspacio FROM tambienteespacio WHERE estado=? and codigoAmbiente=?) and codigoInventario <> ?', [$request->get('txtSerie'), true, $request->get('selectAmbiente'), $request->get('hdCodigoInventario')])->get();

					if($registradoInventario && count($registradoInventario) > 0)
					{
						DB::rollBack();
	
						$request->flash();
	
						return $this->plataformHelper->redirectError("Ya se registro un item con esa 'Serie'", 'inventario/ver');
					}
				}

				if(
					trim($request->input('txtNombre'))==''
					|| (
						$request->input('txtEstado')!='Nuevo'
						&& $request->input('txtEstado')!='Buen estado'
						&& $request->input('txtEstado')!='Con daños leves'
						&& $request->input('txtEstado')!='Deteriorado'
						&& $request->input('txtEstado')!='Inservible'
					)
				)
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'inventario/insertar');
				}
                
				$tInventario->codigoBarras = $request->get('txtCodigoBarras');
				$tInventario->serie = $request->get('txtSerie');
				$tInventario->codigoAmbienteEspacio = $request->get('selectEspacio');
				$tInventario->modelo = $request->get('txtModelo');
				$tInventario->nombre = $request->get('txtNombre');
				$tInventario->descripcion = $request->get('txtDescripcion');
				$tInventario->dimensionAncho = $request->get('txtDimensionAnchoNumero') !== '' ? $request->get('txtDimensionAnchoNumero') . ' ' . $request->get('txtDimensionAnchoMedida') : '';
				$tInventario->dimensionLargo = $request->get('txtDimensionLargoNumero') !== '' ? $request->get('txtDimensionLargoNumero') . ' ' . $request->get('txtDimensionLargoMedida') : '';
				$tInventario->dimensionAlto = $request->get('txtDimensionAltoNumero') !== '' ? $request->get('txtDimensionAltoNumero') . ' ' . $request->get('txtDimensionAltoMedida') : '';
				$tInventario->pesoKg = $request->get('txtPeso');
				$tInventario->estado = $request->get('txtEstado');

				$tInventario->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'inventario/ver');
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tInventario=TInventario::with('tambienteespacio.tambiente')->find($request->input('codigoInventario'));

		if($tInventario == null)
        {
            return $this->plataformHelper->redirectError('No se encontró el item del inventario.', 'inventario/ver' );
        }

		$listaTAmbiente = TAmbiente::whereRaw('((? and codigoOficina=?) or (? and codigoAlmacen=?))', [$sessionManager->has('codigoOficina'), $sessionManager->get('codigoOficina'), $sessionManager->has('codigoAlmacen'), $sessionManager->get('codigoAlmacen')])->get();

		return view('inventario/editar', ['tInventario' => $tInventario, 'listaTAmbiente' => $listaTAmbiente]);
	}

	public function actionEliminar($codigoInventario)
	{
		try
		{
			DB::beginTransaction();

			$tInventario=TInventario::find($codigoInventario);

			if(!$tInventario)
			{
				return $this->plataformHelper->redirectError('No se encontró el item en el inventario, contacte con el administrador.', 'inventario/ver');
			}
			
			$tInventario->delete();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'inventario/ver');
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}
}