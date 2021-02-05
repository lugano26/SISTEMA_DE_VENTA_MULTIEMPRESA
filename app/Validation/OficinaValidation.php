<?php
namespace App\Validation;

use Validator;
use Session;

use App\Model\TOficina;

class OficinaValidation
{
	private $mensajeGlobal='';

	public function validationInsertar($request)
	{
		$validator=Validator::make(
		[
			'descripcion' => trim($request->input('txtDescripcion')),
			'pais' => trim($request->input('txtPais')),
			'departamento' => trim($request->input('txtDepartamento')),
			'provincia' => trim($request->input('txtProvincia')),
			'distrito' => trim($request->input('txtDistrito')),
			'direccion' => trim($request->input('txtDireccion')),
			'numeroVivienda' => trim($request->input('txtNumeroVivienda')),
			'telefono' => trim($request->input('txtTelefono'))
		],
		[
			'descripcion' => 'required',
			'pais' => 'required',
			'departamento' => 'required',
			'provincia' => 'required',
			'distrito' => 'required',
			'direccion' => 'required',
			'numeroVivienda' => 'required',
			'telefono' => 'required'
		],
		[
			'descripcion.required' => 'El campo "Descripción" es requerido.__BREAKLINE__',
			'pais.required' => 'El campo "País" es requerido.__BREAKLINE__',
			'departamento.required' => 'El campo "Departamento" es requerido.__BREAKLINE__',
			'provincia.required' => 'El campo "Provincia" es requerido.__BREAKLINE__',
			'distrito.required' => 'El campo "Distrito" es requerido.__BREAKLINE__',
			'direccion.required' => 'El campo "Dirección" es requerido.__BREAKLINE__',
			'numeroVivienda.required' => 'El campo "Número vivienda" es requerido.__BREAKLINE__',
			'telefono.required' => 'El campo "Teléfono" es requerido.__BREAKLINE__'
		]);

		if($validator->fails())
		{
			$errors=$validator->errors()->all();

			foreach($errors as $value)
			{
				$this->mensajeGlobal.=$value;
			}
		}

		$tOficina=TOficina::whereRaw("replace(descripcion, ' ', '')=replace(?, ' ', '')", [$request->input('txtDescripcion')])->get();

		if(count($tOficina)>0)
		{
			$this->mensajeGlobal.='La oficina ya se encuentra registrada en el sistema (Nombre de la oficina existente).__BREAKLINE__';
		}

		return $this->mensajeGlobal;
	}

	public function validationEditar($request)
	{
		$validator=Validator::make(
		[
			'descripcion' => trim($request->input('txtDescripcion')),
			'pais' => trim($request->input('txtPais')),
			'departamento' => trim($request->input('txtDepartamento')),
			'provincia' => trim($request->input('txtProvincia')),
			'distrito' => trim($request->input('txtDistrito')),
			'direccion' => trim($request->input('txtDireccion')),
			'numeroVivienda' => trim($request->input('txtNumeroVivienda')),
			'telefono' => trim($request->input('txtTelefono'))
		],
		[
			'descripcion' => 'required',
			'pais' => 'required',
			'departamento' => 'required',
			'provincia' => 'required',
			'distrito' => 'required',
			'direccion' => 'required',
			'numeroVivienda' => 'required',
			'telefono' => 'required'
		],
		[
			'descripcion.required' => 'El campo "Descripción" es requerido.__BREAKLINE__',
			'pais.required' => 'El campo "País" es requerido.__BREAKLINE__',
			'departamento.required' => 'El campo "Departamento" es requerido.__BREAKLINE__',
			'provincia.required' => 'El campo "Provincia" es requerido.__BREAKLINE__',
			'distrito.required' => 'El campo "Distrito" es requerido.__BREAKLINE__',
			'direccion.required' => 'El campo "Dirección" es requerido.__BREAKLINE__',
			'numeroVivienda.required' => 'El campo "Número vivienda" es requerido.__BREAKLINE__',
			'telefono.required' => 'El campo "Teléfono" es requerido.__BREAKLINE__'
		]);
		
		$tOficina=TOficina::whereRaw("replace(descripcion, ' ', '')=replace(?, ' ', '') and codigoOficina!=? and codigoEmpresa=?", [$request->input('txtDescripcion'), $request->input('hdCodigoOficina'), Session::get('codigoEmpresa')])->get();

		if(count($tOficina)>0)
		{
			$this->mensajeGlobal.='La oficina ya se encuentra registrada en el sistema (Nombre de la oficina existente).__BREAKLINE__';
		}

		return $this->mensajeGlobal;
	}
}
?>