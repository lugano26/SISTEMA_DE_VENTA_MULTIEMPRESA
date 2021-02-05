<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TAmbiente;

class AmbienteController extends Controller
{
	public function actionInsertarAjax(Request $request, SessionManager $sessionManager)
	{
		if($_POST)
		{
            $messages = [];
            $isValid = true;
            $ultimoRegistroAmbiente = null;

			try
			{
                DB::beginTransaction();

                $nombreRegistrado = TAmbiente::whereRaw('replace(nombre, \' \', \'\')=replace(?, \' \', \'\') and ((? and codigoOficina=?) or (? and codigoAlmacen=?))', [$request->get('txtNombreAmbiente'), $sessionManager->has('codigoOficina'), $sessionManager->get('codigoOficina'), $sessionManager->has('codigoAlmacen'), $sessionManager->get('codigoAlmacen')])->get();
                
                if($nombreRegistrado && count($nombreRegistrado) > 0)
                {
                    $messages[] = ["field" => "txtNombreAmbiente", "message" => "Ya fue registrado un ambiente con este nombre."];
					
                    $isValid = false;
                }
                
                if($request->filled('txtCodigo'))
                {
                    $codigoRegistrado = TAmbiente::whereRaw('replace(codigo, \' \', \'\')=replace(?, \' \', \'\') and ((? and codigoOficina=?) or (? and codigoAlmacen=?))', [$request->get('txtCodigo'), $sessionManager->has('codigoOficina'), $sessionManager->get('codigoOficina'), $sessionManager->has('codigoAlmacen'), $sessionManager->get('codigoAlmacen')])->get();
                    if($codigoRegistrado && count($codigoRegistrado) > 0)
                    {
                        $messages[] = ["field" => "txtCodigo", "message" => "Ya fue registrado un ambiente con este código."];
                        $isValid = false;
                    }
                }
                
                if(!$isValid)
                {
                    DB::rollback();

                    return response()->json(["isValid" => $isValid, "messages" => $messages, "tAmbiente" => $ultimoRegistroAmbiente]);
                }

				if(
					($sessionManager->has('codigoOficina')==null && $sessionManager->has('codigoAlmacen')==null)
					|| trim($request->get('txtNombreAmbiente'))==''
					|| (
						$request->get('selectTipoAmbiente')!='Oficina'
						&& $request->get('selectTipoAmbiente')!='Cuarto'
						&& $request->get('selectTipoAmbiente')!='Local'
						&& $request->get('selectTipoAmbiente')!='Anaquel'
						&& $request->get('selectTipoAmbiente')!='Estante'
						&& $request->get('selectTipoAmbiente')!='Almacén'
					)
					|| trim($request->get('txtReferenciaUbicacion'))==''
				)
				{
					DB::rollback();

					return response()->json(["isValid" => false, "messages" => 'Datos incorrectos. Por favor no trate de aleterar el comportamiento del sistema.', "tAmbiente" => $ultimoRegistroAmbiente]);
				}

				$tAmbiente=new TAmbiente();

				$tAmbiente->codigoOficina=$sessionManager->has('codigoOficina') ? $sessionManager->get('codigoOficina') : null;
                $tAmbiente->codigoAlmacen=$sessionManager->has('codigoAlmacen') ? $sessionManager->get('codigoAlmacen') : null;
                $tAmbiente->nombre = trim($request->get('txtNombreAmbiente'));
                $tAmbiente->codigo = trim($request->get('txtCodigo'));
                $tAmbiente->tipo = $request->get('selectTipoAmbiente');
                $tAmbiente->nivelUbicacion = $request->get('txtNivelUbicacion');
                $tAmbiente->referenciaUbicacion = trim($request->get('txtReferenciaUbicacion'));

                $tAmbiente->save();
                
                $ultimoRegistroAmbiente=TAmbiente::whereRaw('codigoAmbiente=(select max(codigoAmbiente) from tambiente)')->first();

				DB::commit();

				return response()->json(["isValid" => $isValid, "messages" => $messages, "tAmbiente" => $ultimoRegistroAmbiente]);
			}
			catch(\Exception $e)
			{
				DB::rollback();

				$this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
            }
            
            return response()->json(["isValid" => false, "messages" => $messages, "tAmbiente" => $ultimoRegistroAmbiente]);
        }
    }

    public function actionJsonPorNombreCodigo(Request $request, SessionManager $sessionManager)
    {
        $listaTAmbiente=TAmbiente::whereRaw('replace(concat(nombre, codigo), \' \', \'\') like replace(?, \' \', \'\') and ((? and codigoOficina=?) or (? and codigoAlmacen=?))', ['%' . $request->input('q') . '%', $sessionManager->has('codigoOficina'), $sessionManager->get('codigoOficina'), $sessionManager->has('codigoAlmacen'), $sessionManager->get('codigoAlmacen')])->get();

		$items=[];

		foreach($listaTAmbiente as $item)
		{
			$items[]=['id' => $item->codigoAmbiente, 'text' => $item->nombre . (empty($item->codigo) ? "" : " (" . $item->codigo . ")"), 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
    }

    public function actionCargarAmbientes(Request $request)
    {
        $listaTAmbiente=TAmbiente::whereRaw('codigoOficina=? or codigoAlmacen=?', [$request->get('codigoAlmacenOficina'), $request->get('codigoAlmacenOficina')])->get();

		$items=[];

		foreach($listaTAmbiente as $item)
		{
			$items[]=['id' => $item->codigoAmbiente, 'text' => $item->nombre, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
    }
}