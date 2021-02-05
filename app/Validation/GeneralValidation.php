<?php
namespace App\Validation;

use Validator;

use App\Model\TEmpresa;

class GeneralValidation
{
	private $mensajeGlobal='';

	public function validationConfiguracionGlobal($request)
	{
		$validator=Validator::make(
		[
			'rucEmpresa' => trim($request->input('txtRucEmpresa')),
			'razonSocialEmpresa' => trim($request->input('txtRazonSocialEmpresa')),
			'representanteLegalEmpresa' => trim($request->input('txtRepresentanteLegalEmpresa')),
			'descripcionOficina' => trim($request->input('txtDescripcionOficina')),
			'paisOficina' => trim($request->input('txtPaisOficina')),
			'departamentoOficina' => trim($request->input('txtDepartamentoOficina')),
			'provinciaOficina' => trim($request->input('txtProvinciaOficina')),
			'distritoOficina' => trim($request->input('txtDistritoOficina')),
			'direccionOficina' => trim($request->input('txtDireccionOficina')),
			'numeroViviendaOficina' => trim($request->input('txtNumeroViviendaOficina')),
			'telefonoOficina' => trim($request->input('txtTelefonoOficina')),
			'descripcionAlmacen' => trim($request->input('txtDescripcionAlmacen')),
			'paisAlmacen' => trim($request->input('txtPaisAlmacen')),
			'departamentoAlmacen' => trim($request->input('txtDepartamentoAlmacen')),
			'provinciaAlmacen' => trim($request->input('txtProvinciaAlmacen')),
			'distritoAlmacen' => trim($request->input('txtDistritoAlmacen')),
			'direccionAlmacen' => trim($request->input('txtDireccionAlmacen')),
			'numeroViviendaAlmacen' => trim($request->input('txtNumeroViviendaAlmacen')),
			'telefonoAlmacen' => trim($request->input('txtTelefonoAlmacen'))
		],
		[
			'rucEmpresa' => ['required', 'unique:tempresa,ruc'],
			'razonSocialEmpresa' => 'required',
			'representanteLegalEmpresa' => 'required',
			'descripcionOficina' => 'required',
			'paisOficina' => 'required',
			'departamentoOficina' => 'required',
			'provinciaOficina' => 'required',
			'distritoOficina' => 'required',
			'direccionOficina' => 'required',
			'numeroViviendaOficina' => 'required',
			'telefonoOficina' => 'required',
			'descripcionAlmacen' => 'required',
			'paisAlmacen' => 'required',
			'departamentoAlmacen' => 'required',
			'provinciaAlmacen' => 'required',
			'distritoAlmacen' => 'required',
			'direccionAlmacen' => 'required',
			'numeroViviendaAlmacen' => 'required',
			'telefonoAlmacen' => 'required'
		],
		[
			'rucEmpresa.unique' => 'La empresa ya se encuentra registrada en el sistema (RUC de la empresa existente).__BREAKLINE__',
			'rucEmpresa.required' => 'El campo "RUC" es requerido.__BREAKLINE__',
			'razonSocialEmpresa.required' => 'El campo "Razon social" es requerido.__BREAKLINE__',
			'representanteLegalEmpresa.required' => 'El campo "Representante legal" es requerido.__BREAKLINE__',
			'descripcionOficina.required' => 'El campo "Descripción oficina" es requerido.__BREAKLINE__',
			'paisOficina.required' => 'El campo "País oficina" es requerido.__BREAKLINE__',
			'departamentoOficina.required' => 'El campo "Departamento oficina" es requerido.__BREAKLINE__',
			'provinciaOficina.required' => 'El campo "Provincia oficina" es requerido.__BREAKLINE__',
			'distritoOficina.required' => 'El campo "Distrito oficina" es requerido.__BREAKLINE__',
			'direccionOficina.required' => 'El campo "Dirección oficina" es requerido.__BREAKLINE__',
			'numeroViviendaOficina.required' => 'El campo "Número vivienda oficina" es requerido.__BREAKLINE__',
			'telefonoOficina.required' => 'El campo "Teléfono oficina" es requerido.__BREAKLINE__',
			'descripcionAlmacen.required' => 'El campo "Descripción almacén" es requerido.__BREAKLINE__',
			'paisAlmacen.required' => 'El campo "País almacén" es requerido.__BREAKLINE__',
			'departamentoAlmacen.required' => 'El campo "Departamento almacén" es requerido.__BREAKLINE__',
			'provinciaAlmacen.required' => 'El campo "Provincia almacén" es requerido.__BREAKLINE__',
			'distritoAlmacen.required' => 'El campo "Distrito almacén" es requerido.__BREAKLINE__',
			'direccionAlmacen.required' => 'El campo "Dirección almacén" es requerido.__BREAKLINE__',
			'numeroViviendaAlmacen.required' => 'El campo "Número vivienda almacén" es requerido.__BREAKLINE__',
			'telefonoAlmacen.required' => 'El campo "Teléfono almacén" es requerido.__BREAKLINE__'
		]);

		if($validator->fails())
		{
			$errors=$validator->errors()->all();

			foreach($errors as $value)
			{
				$this->mensajeGlobal.=$value;
			}
		}

		$tEmpresa=TEmpresa::whereRaw("replace(razonSocial, ' ', '')=replace(?, ' ', '')", [$request->input('txtRazonSocialEmpresa')])->get();

		if(count($tEmpresa)>0)
		{
			$this->mensajeGlobal.='La empresa ya se encuentra registrada en el sistema (Razón social de la empresa existente).__BREAKLINE__';
		}

		return $this->mensajeGlobal;
	}
}
?>