<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;

use App\Model\TAmbienteEspacio;

class AmbienteEspacioController extends Controller
{
	public function actionInsertarAjax(Request $request)
	{
		if($_POST)
		{
			try
			{
                DB::beginTransaction();

                $sinSecciones = $request->get('chkSinSecciones');
                $numeroSecciones = $request->get('txtNumberSeccion');

                if($sinSecciones === 'true')
                {
                    if(TAmbienteEspacio::whereRaw('codigoAmbiente=? and seccion =?', [$request->get('codigoAmbiente'), '0'])->count() === 0)
                    {
                        $tAmbienteEspacio=new TAmbienteEspacio();

                        $tAmbienteEspacio->codigoAmbiente = $request->get('codigoAmbiente');
                        $tAmbienteEspacio->seccion = '0';
                        $tAmbienteEspacio->estado = true;

                        $tAmbienteEspacio->save();
                    }
                    else
                    {
                        TAmbienteEspacio::whereRaw('codigoAmbiente=? and seccion =?', [$request->get('codigoAmbiente'), '0'])->update(['estado' => true]);
                    }

                    TAmbienteEspacio::whereRaw('codigoAmbiente=? and seccion <> 0', [$request->get('codigoAmbiente')])->update(['estado' => false]);
                }
                else
                {
                    TAmbienteEspacio::whereRaw('codigoAmbiente=? and seccion =?', [$request->get('codigoAmbiente'), '0'])->update(['estado' => false]);

                    TAmbienteEspacio::whereRaw('codigoAmbiente=? and seccion >?', [$request->get('codigoAmbiente'), $numeroSecciones])->update(['estado' => false]);

                    $tAmbienteEspacioNuevos = [];
                    $auxDate = date("Y-m-d H:i:s");
                    for($i = 1; $i <= $numeroSecciones; $i++)
                    {
                        $tAmbienteActual = TAmbienteEspacio::whereRaw('codigoAmbiente=? and seccion =?', [$request->get('codigoAmbiente'), $i])->first();

                        if(!$tAmbienteActual)
                        {
                            $tAmbienteEspacioNuevos [] = [
                                "codigoAmbiente" => $request->get('codigoAmbiente'),
                                "seccion" => $i,
                                "estado" => true,
                                'created_at' => $auxDate,
                                'updated_at' => $auxDate
                            ];
                        }
                        else
                        {
                            $tAmbienteActual->estado = true;
                            $tAmbienteActual->save();
                        }
                    }

                    TAmbienteEspacio::insert($tAmbienteEspacioNuevos);
                }

				DB::commit();

				return response()->json(["isValid" => true, "messages" => []]);
			}
			catch(\Exception $e)
			{
				DB::rollback();

				$this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $e->getMessage(), '/');
            }
            
            return response()->json(["isValid" => false, "messages" => ["Ocurrio un error, contacte con el administrador!"]]);
        }
    }

    public function actionCargarEspacios(Request $request)
    {
        $listaTAmbienteEspacio=TAmbienteEspacio::whereRaw('codigoAmbiente=? and estado=?', [$request->get('codigoAmbiente'), true])->get();

		$items=[];

		foreach($listaTAmbienteEspacio as $item)
		{
			$items[]=['id' => $item->codigoAmbienteEspacio, 'text' => $item->seccion, 'row' => $item];
		}

		$result=['items' => $items];

		return response()->json($result);
    }
}