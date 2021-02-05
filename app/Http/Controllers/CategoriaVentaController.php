<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TCategoriaVenta;

class CategoriaVentaController extends Controller
{
	public function actionMantenimiento(Request $request, SessionManager $sessionManager, $codigoCategoriaVenta = null)
	{
		if($_POST)
		{
			try
			{
                DB::beginTransaction();
                
                $categoriaVentaPadre = TCategoriaVenta::with('tcategoriaventa')->find(trim($request->input('codigoCategoriaVentaPadre')));

                $cantidadCategoria = $categoriaVentaPadre != null 
                    ? TCategoriaVenta::whereRaw("lower(replace(descripcion, ' ', ''))=lower(replace(?, ' ', '')) and codigoCategoriaVentaPadre=? and codigoEmpresa=?", [$request->input('txtDescripcion'), $categoriaVentaPadre->codigoCategoriaVenta, $sessionManager->get('codigoEmpresa')])->count()
                    : TCategoriaVenta::whereRaw("lower(replace(descripcion, ' ', ''))=lower(replace(?, ' ', '')) and codigoCategoriaVentaPadre is NULL and codigoEmpresa=?", [$request->input('txtDescripcion'), $sessionManager->get('codigoEmpresa')])->count();
                
                if($cantidadCategoria>0)
                {
                    DB::rollBack();

                    $request->flash();

                    return $this->plataformHelper->redirectError('La categoría "' . $request->input('txtDescripcion') . '" ya fue registrada.', 'categoriaventa/mantenimiento/' . ($categoriaVentaPadre != null ? $categoriaVentaPadre->codigoCategoriaVenta : '') );
                }

                if($categoriaVentaPadre != null && $categoriaVentaPadre->tcategoriaventa != null && $categoriaVentaPadre->tcategoriaventa->tcategoriaventa != null)
                {
                    DB::rollBack();

					$request->flash();

                    return $this->plataformHelper->redirectError('Solo esta permitido registrar 3 niveles de categorias de venta.', 'categoriaventa/mantenimiento' );
                }

				if(trim($request->input('txtDescripcion'))=='')
				{
					DB::rollBack();

					$request->flash();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'categoriaventa/mantenimiento');
				}

				$tCategoriaVenta=new TCategoriaVenta();

				$tCategoriaVenta->codigoEmpresa=$sessionManager->get('codigoEmpresa');
                $tCategoriaVenta->descripcion=trim($request->input('txtDescripcion'));      
                $tCategoriaVenta->codigoCategoriaVentaPadre = $categoriaVentaPadre != null ? $categoriaVentaPadre->codigoCategoriaVenta : null;
				$tCategoriaVenta->estado=true;

				$tCategoriaVenta->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'categoriaventa/mantenimiento/' . ($categoriaVentaPadre != null ? $categoriaVentaPadre->codigoCategoriaVenta : ''));
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
        }
        
        $categoriaVentaPadre = TCategoriaVenta::with('tcategoriaventa')->whereRaw('estado')->find($codigoCategoriaVenta);
        
        $listaCategoriaVenta = null;

        if($categoriaVentaPadre != null)
        {
            $listaCategoriaVenta = TCategoriaVenta::with('tcategoriaventa')->whereRaw('codigoCategoriaVentaPadre=? and codigoEmpresa=? and estado', [$categoriaVentaPadre->codigoCategoriaVenta, $sessionManager->get('codigoEmpresa')])->get();
        }
        else
        {
            $listaCategoriaVenta = TCategoriaVenta::with('tcategoriaventa')->whereRaw('codigoCategoriaVentaPadre is NULL and codigoEmpresa=? and estado', [$sessionManager->get('codigoEmpresa')])->get();
        }

		return view('categoriaventa/mantenimiento', ['listaCategoriaVenta' => $listaCategoriaVenta, 'categoriaVenta' => $categoriaVentaPadre]);
    }
    
    public function actionEditar(Request $request, SessionManager $sessionManager)
	{
		if($request->has('hdCodigoCategoriaVenta'))
		{
			try
			{
				DB::beginTransaction();

				$tCategoriaVenta=TCategoriaVenta::with('tcategoriaventa')->find($request->input('hdCodigoCategoriaVenta'));

				if($tCategoriaVenta == null)
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError('No se encontró la categoría.', 'categoriaventa/mantenimiento' );
                }

				if(!$tCategoriaVenta->estado)
				{
					DB::rollBack();
					
					return $this->plataformHelper->redirectError('No se puede editar esta categoría porque ya fue eliminado.', 'categoriaventa/mantenimiento' );
                }
                
                $categoriaVentaPadre = $tCategoriaVenta->tcategoriaventa;

                $cantidadCategoria = $categoriaVentaPadre != null 
                    ? TCategoriaVenta::whereRaw("lower(replace(descripcion, ' ', ''))=lower(replace(?, ' ', '')) and codigoCategoriaVentaPadre=? and codigoCategoriaVenta!=? and codigoEmpresa=?", [$request->input('txtDescripcion'), $categoriaVentaPadre->codigoCategoriaVenta, $tCategoriaVenta->codigoCategoriaVenta, $sessionManager->get('codigoEmpresa')])->count()
                    : TCategoriaVenta::whereRaw("lower(replace(descripcion, ' ', ''))=lower(replace(?, ' ', '')) and codigoCategoriaVentaPadre is NULL and codigoCategoriaVenta!=? and codigoEmpresa=?", [$request->input('txtDescripcion'), $tCategoriaVenta->codigoCategoriaVenta, $sessionManager->get('codigoEmpresa')])->count();
                
                if($cantidadCategoria>0)
                {
					DB::rollBack();

                    return $this->plataformHelper->redirectError('La categoría "' . $request->input('txtDescripcion') . '" ya esta registrada.', 'categoriaventa/mantenimiento/' . ($categoriaVentaPadre != null ? $categoriaVentaPadre->codigoCategoriaVenta : '') );
                }

				if(trim($request->input('txtDescripcion'))=='')
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError('Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', 'categoriaventa/mantenimiento/' . ($categoriaVentaPadre != null ? $categoriaVentaPadre->codigoCategoriaVenta : '') );
				}

				$tCategoriaVenta->descripcion=trim($request->input('txtDescripcion'));

				$tCategoriaVenta->save();

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'categoriaventa/mantenimiento/' . ($categoriaVentaPadre != null ? $categoriaVentaPadre->codigoCategoriaVenta : ''));
			}
			catch(\Exception $e)
			{
				DB::rollback();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
			}
		}

		$tCategoriaVenta=TCategoriaVenta::with('tcategoriaventa')->find($request->input('codigoCategoriaVenta'));

		if($tCategoriaVenta == null)
        {
            return $this->plataformHelper->redirectError('No se encontró la categoría.', 'categoriaventa/mantenimiento' );
        }

		return view('categoriaventa/editar', ['tCategoriaVenta' => $tCategoriaVenta]);
    }
    
    public function actionEliminar($codigoCategoriaVenta)
	{
		try
		{
			DB::beginTransaction();

			$tCategoriaVenta=TCategoriaVenta::with('tcategoriaventa')->find($codigoCategoriaVenta);

            if($tCategoriaVenta == null)
            {
				DB::rollback();

                return $this->plataformHelper->redirectError('No se encontró la categoría.', 'categoriaventa/mantenimiento' );
            }

            $tCategoriaVenta->estado = false;

			$tCategoriaVenta->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'categoriaventa/mantenimiento/' . ($tCategoriaVenta->tcategoriaventa != null ? $tCategoriaVenta->tcategoriaventa->codigoCategoriaVenta : ''));
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
    }
    
    public function actionHabilitar($codigoCategoriaVenta)
	{
		try
		{
			DB::beginTransaction();

			$tCategoriaVenta=TCategoriaVenta::with('tcategoriaventa')->find($codigoCategoriaVenta);

            if($tCategoriaVenta == null)
            {
				DB::rollback();
				
                return $this->plataformHelper->redirectError('No se encontró la categoría.', 'categoriaventa/mantenimiento' );
            }

            $tCategoriaVenta->estado = true;

			$tCategoriaVenta->save();

			DB::commit();

			return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'categoriaventa/mantenimiento/' . ($tCategoriaVenta->tcategoriaventa != null ? $tCategoriaVenta->tcategoriaventa->codigoCategoriaVenta : ''));
		}
		catch(\Exception $e)
		{
			DB::rollback();

			return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
		}
	}
}
?>