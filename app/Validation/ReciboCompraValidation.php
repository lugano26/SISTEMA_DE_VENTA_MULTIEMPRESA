<?php
namespace App\Validation;

use Validator;
use Session;

class ReciboCompraValidation
{
	private $mensajeGlobal='';

	public function validationEditarAgrupado($request, $documentoIdentidadProveedor, $nombreProveedor)
	{
		$validator=Validator::make(
		[
			'documentoIdentidadProveedor' => trim($documentoIdentidadProveedor),
			'nombreProveedor' => trim($nombreProveedor),
			'selectTipoRecibo' => trim($request->input('selectTipoRecibo')),
			'tipoPago' => trim($request->input('selectTipoPago'))
		],
		[
			'documentoIdentidadProveedor' => 'required',
			'nombreProveedor' => 'required',
			'selectTipoRecibo' => 'required',
			'tipoPago' => 'required'
		],
		[
			'documentoIdentidadProveedor.required' => 'El campo "Documento identidad proveedor" es requerido.__BREAKLINE__',
			'nombreProveedor.required' => 'El campo "Nombre proveedor" es requerido.__BREAKLINE__',
			'selectTipoRecibo.required' => 'El campo "Tipo recibo" es requerido.__BREAKLINE__',
			'tipoPago.required' => 'El campo "Tipo pago" es requerido.__BREAKLINE__'
		]);

		if($validator->fails())
		{
			$errors=$validator->errors()->all();

			foreach($errors as $value)
			{
				$this->mensajeGlobal.=$value;
			}
		}

		if(
			$request->input('selectTipoRecibo')!='Ninguno'
			&& $request->input('selectTipoRecibo')!='Boleta'
			&& $request->input('selectTipoRecibo')!='Factura'
		)
		{
			$this->mensajeGlobal.='Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.__BREAKLINE__';
		}

		if(
			$request->input('selectTipoPago')!='Al contado'
			&& $request->input('selectTipoPago')!='Al crédito'
		)
		{
			$this->mensajeGlobal.='Datos incorrectos. Por favor no trate de alterar el comportamiento del sistema.__BREAKLINE__';
		}

		return $this->mensajeGlobal;
	}
}
?>