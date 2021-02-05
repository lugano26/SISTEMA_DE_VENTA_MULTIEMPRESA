<?php
namespace App\Validation;

use Illuminate\Validation\Rule;

use Validator;

use App\Model\TEmpresa;

class EmpresaValidation
{
	private $mensajeGlobal='';

	public function validationEditar($request)
	{
		$validator=Validator::make(
		[
			'ruc' => trim($request->input('txtRuc')),
			'razonSocial' => trim($request->input('txtRazonSocial')),
			'representanteLegal' => trim($request->input('txtRepresentanteLegal')),
			'urlConsultaFactura' => trim($request->input('txtUrlConsultaFactura')),
			'formatoComprobante' => trim($request->input('selectFormatoComprobante'))
		],
		[
			'ruc' => ['required', Rule::unique('tempresa')->where(function($query) use($request) { return $query->where('codigoEmpresa', '<>', $request->input('hdCodigoEmpresa')); })],
			'razonSocial' => 'required',
			'representanteLegal' => 'required',
			'urlConsultaFactura' => 'required',
			'formatoComprobante' => 'required'
		],
		[
			'ruc.unique' => 'La empresa ya se encuentra registrada en el sistema (RUC de la empresa existente).__BREAKLINE__',
			'ruc.required' => 'El campo "RUC" es requerido.__BREAKLINE__',
			'razonSocial.required' => 'El campo "Razon social" es requerido.__BREAKLINE__',
			'representanteLegal.required' => 'El campo "Representante legal" es requerido.__BREAKLINE__',
			'urlConsultaFactura.required' => 'El campo "URL consulta factura" es requerido.__BREAKLINE__',
			'formatoComprobante.required' => 'El campo "ruc" es requerido.__BREAKLINE__'
		]);

		if($validator->fails())
		{
			$errors=$validator->errors()->all();

			foreach($errors as $value)
			{
				$this->mensajeGlobal.=$value;
			}
		}

		$tEmpresa=TEmpresa::whereRaw("replace(razonSocial, ' ', '')=replace(?, ' ', '') and codigoEmpresa!=?", [$request->input('txtRazonSocial'), $request->input('hdCodigoEmpresa')])->get();

		if(count($tEmpresa)>0)
		{
			$this->mensajeGlobal.='La empresa ya se encuentra registrada en el sistema (Razón social de la empresa existente).__BREAKLINE__';
		}

		if(!in_array($request->input('selectFormatoComprobante'),
		[
			'Ticket',
			'Normal'
		]))
		{
			$this->mensajeGlobal.='Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.__BREAKLINE__';
		}

		return $this->mensajeGlobal;
	}
}
?>