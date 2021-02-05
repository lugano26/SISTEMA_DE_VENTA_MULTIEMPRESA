<?php
namespace App\Validation;

use Validator;
use Session;

use App\Model\TAlmacen;

class AlmacenValidation
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

		$tAlmacen=TAlmacen::whereRaw("replace(descripcion, ' ', '')=replace(?, ' ', '')", [$request->input('txtDescripcion')])->get();

		if(count($tAlmacen)>0)
		{
			$this->mensajeGlobal.='El almacén ya se encuentra registrada en el sistema (Nombre del almacén existente).__BREAKLINE__';
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

		if($validator->fails())
		{
			$errors=$validator->errors()->all();

			foreach($errors as $value)
			{
				$this->mensajeGlobal.=$value;
			}
		}
		
		$tAlmacen=TAlmacen::whereRaw("replace(descripcion, ' ', '')=replace(?, ' ', '') and codigoAlmacen!=? and codigoEmpresa=?", [$request->input('txtDescripcion'), $request->input('hdCodigoAlmacen'), Session::get('codigoEmpresa')])->get();

		if(count($tAlmacen)>0)
		{
			$this->mensajeGlobal.='El almacén ya se encuentra registrada en el sistema (Nombre del almacén existente).__BREAKLINE__';
		}

		return $this->mensajeGlobal;
	}
}
?>