<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Encryption\Encrypter;

use App\Validation\EmpresaValidation;

use DB;

use App\Model\TEmpresa;

class EmpresaController extends Controller
{
	public function actionVer(Request $request)
	{
		$listaTEmpresa=TEmpresa::all();

		return view('empresa/ver', ['listaTEmpresa' => $listaTEmpresa]);
	}

	public function actionEditar(Request $request, SessionManager $sessionManager, Encrypter $encrypter)
	{
		if($request->has('hdCodigoEmpresa'))
		{
			try
			{
				DB::beginTransaction();

				$tEmpresa=TEmpresa::find($request->input('hdCodigoEmpresa'));

				if($tEmpresa==null)
				{
					return $this->plataformHelper->redirectError('Datos inexistentes.', 'empresa/ver');
				}

				$this->mensajeGlobal=(new EmpresaValidation())->validationEditar($request);

				if($this->mensajeGlobal!='')
				{
					DB::rollBack();

					return $this->plataformHelper->redirectError($this->mensajeGlobal, 'empresa/ver');
				}

				$tEmpresa->ruc=$request->input('txtRuc');
				$tEmpresa->razonSocial=$request->input('txtRazonSocial');
				$tEmpresa->representanteLegal=$request->input('txtRepresentanteLegal');
				$tEmpresa->urlConsultaFactura=$request->input('txtUrlConsultaFactura');
				$tEmpresa->facturacionElectronica=$request->input('radioFacturacionElectronica');
				$tEmpresa->formatoComprobante=$request->input('selectFormatoComprobante');
				$tEmpresa->userNameEf=$request->input('radioFacturacionElectronica') ? trim($request->input('txtUserNameEf')) : $tEmpresa->userNameEf;
				$tEmpresa->passwordEf=$request->input('radioFacturacionElectronica') ? $encrypter->encrypt($request->input('txtPasswordEf')) : $tEmpresa->passwordEf;
				$tEmpresa->demo=$request->input('radioDemo');
				$tEmpresa->estado=$request->input('radioEstado');

				$tEmpresa->save();

				if($request->has('fileLogoEmpresarial'))
				{
					$request->file('fileLogoEmpresarial')->move(public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa, 'logoEmpresarial.png');
				}

				$sessionManager->put('demo', $tEmpresa->demo);

				DB::commit();

				return $this->plataformHelper->redirectCorrecto('Operación realizada correctamente.', 'empresa/ver');
			}
			catch(\Exception $ex)
			{
				DB::rollBack();

				return $this->plataformHelper->capturarExcepcion(__CLASS__, __FUNCTION__, $ex->getMessage(), 'empresa/ver');
			}
		}

		$tEmpresa=TEmpresa::find($request->input('codigoEmpresa'));

		if($tEmpresa==null)
		{
			return $this->plataformHelper->redirectError('Datos inexistentes.', 'empresa/ver');
		}

		$tEmpresa->existeLogoEmpresarialTemp=file_exists(public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png');
		$tEmpresa->existeCertificadoDigitalTemp=file_exists(storage_path().'/app/'.$tEmpresa->codigoEmpresa.'/certificadoDigital.pem');
		$tEmpresa->existeLlaveDigitalTemp=file_exists(storage_path().'/app/'.$tEmpresa->codigoEmpresa.'/llaveDigital.pem');

		return view('empresa/editar', ['tEmpresa' => $tEmpresa]);
	}

	public function actionEditarTipoCambioUsdConAjax(Request $request, SessionManager $sessionManager)
	{
		DB::beginTransaction();

		$tEmpresa=TEmpresa::find($sessionManager->get('codigoEmpresa'));

		if($tEmpresa==null)
		{
			DB::rollBack();

			return response()->json(['error' => true, 'mensajeGlobal' => 'Empresa inexistente o su sesión de usuario ya ha finalizado.']);
		}

		$tEmpresa->tipoCambioUsd=$request->input('tipoCambioUsd');

		$tEmpresa->save();

		$sessionManager->put('tipoCambioUsd', $tEmpresa->tipoCambioUsd);

		DB::commit();

		return response()->json(['correcto' => true, 'mensajeGlobal' => 'Tipo de cambio dólare, modificado correctamente.', 'tipoCambioUsd' => number_format($tEmpresa->tipoCambioUsd, 3, '.', '')]);
	}
}
?>