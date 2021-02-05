<?php
namespace App\Validation;

use Illuminate\Validation\Rule;

use Validator;
use Session;

use App\Model\TPersonal;

class PersonalValidation
{
	private $mensajeGlobal='';

	public function validationInsertar($request)
	{
		$validator=Validator::make(
		[
			'dni' => trim($request->input('txtDni')),
			'correoElectronico' => trim($request->input('txtCorreoElectronico')),
			'nombre' => trim($request->input('txtNombre')),
			'apellido' => trim($request->input('txtApellido')),
			'direccion' => trim($request->input('txtDireccion')),
			'telefono' => trim($request->input('txtTelefono')),
			'contraseniaUsuario' => trim($request->input('passContraseniaUsuario'))
		],
		[
			'dni' => ['required', Rule::unique('tpersonal')->where(function($query) { return $query->where('codigoEmpresa', Session::get('codigoEmpresa')); })],
			'correoElectronico' => ['required', Rule::unique('tpersonal')->where(function($query) { return $query->where('codigoEmpresa', Session::get('codigoEmpresa')); })],
			'nombre' => 'required',
			'apellido' => 'required',
			'direccion' => 'required',
			'telefono' => 'required',
			'contraseniaUsuario' => 'required'
		],
		[
			'dni.unique' => 'La persona ya se encuentra registrada en el sistema (DNI de la persona existente).__BREAKLINE__',
			'correoElectronico.unique' => 'La persona ya se encuentra registrada en el sistema (Correo electrónico de la persona existente).__BREAKLINE__',
			'correoElectronico.required' => 'El campo "Correo electrónico" es requerido.__BREAKLINE__',
			'nombre.required' => 'El campo "Nombre" es requerido.__BREAKLINE__',
			'apellido.required' => 'El campo "Apellido" es requerido.__BREAKLINE__',
			'direccion.required' => 'El campo "Dirección" es requerido.__BREAKLINE__',
			'telefono.required' => 'El campo "Teléfono" es requerido.__BREAKLINE__',
			'contraseniaUsuario.required' => 'El campo "Contraseña usuario" es requerido.__BREAKLINE__'
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

	public function validationEditar($request)
	{
		$validator=Validator::make(
		[
			'dni' => trim($request->input('txtDni')),
			'correoElectronico' => trim($request->input('txtCorreoElectronico')),
			'nombre' => trim($request->input('txtNombre')),
			'apellido' => trim($request->input('txtApellido')),
			'direccion' => trim($request->input('txtDireccion')),
			'telefono' => trim($request->input('txtTelefono'))
		],
		[
			'dni' => ['required', Rule::unique('tpersonal')->where(function($query) use($request){ return $query->whereRaw('codigoEmpresa=? and codigoPersonal!=?', [Session::get('codigoEmpresa'), $request->input('hdCodigoPersonal')]); })],
			'correoElectronico' => ['required', Rule::unique('tpersonal')->where(function($query) use($request){ return $query->whereRaw('codigoEmpresa=? and codigoPersonal!=?', [Session::get('codigoEmpresa'), $request->input('hdCodigoPersonal')]); })],
			'nombre' => 'required',
			'apellido' => 'required',
			'direccion' => 'required',
			'telefono' => 'required'
		],
		[
			'dni.unique' => 'La persona ya se encuentra registrada en el sistema (DNI de la persona existente).__BREAKLINE__',
			'correo.unique' => 'La persona ya se encuentra registrada en el sistema (Correo electrónico de la persona existente).__BREAKLINE__',
			'correoElectronico.required' => 'El campo "Correo electrónico" es requerido.__BREAKLINE__',
			'nombre.required' => 'El campo "Nombre" es requerido.__BREAKLINE__',
			'apellido.required' => 'El campo "Apellido" es requerido.__BREAKLINE__',
			'direccion.required' => 'El campo "Dirección" es requerido.__BREAKLINE__',
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

		return $this->mensajeGlobal;
	}
}
?>