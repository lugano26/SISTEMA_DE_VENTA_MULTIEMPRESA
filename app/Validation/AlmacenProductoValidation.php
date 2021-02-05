<?php
namespace App\Validation;

use Validator;
use Session;

class AlmacenProductoValidation
{
	private $mensajeGlobal='';

	public function validationEditarAgrupado($request)
	{
		$validator=Validator::make(
		[
			'nombre' => trim($request->input('txtNombreProducto')),
			'tipo' => trim($request->input('selectTipoProducto')),
			'situacionImpuesto' => trim($request->input('selectSituacionImpuestoProducto')),
			'tipoImpuesto' => trim($request->input('selectTipoImpuestoProducto'))
		],
		[
			'nombre' => 'required',
			'tipo' => 'required',
			'situacionImpuesto' => 'required',
			'tipoImpuesto' => 'required'
		],
		[
			'nombre.required' => 'El campo "Descripción" es requerido.__BREAKLINE__',
			'tipo.required' => 'El campo "País" es requerido.__BREAKLINE__',
			'situacionImpuesto.required' => 'El campo "Situacion impuesto" es requerido.__BREAKLINE__',
			'tipoImpuesto.required' => 'El campo "Tipo impuesto" es requerido.__BREAKLINE__'
		]);

		if($validator->fails())
		{
			$errors=$validator->errors()->all();

			foreach($errors as $value)
			{
				$this->mensajeGlobal.=$value;
			}
		}

		return $this->mensajeGlobal;
	}
}
?>