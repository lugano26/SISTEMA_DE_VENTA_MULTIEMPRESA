<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

use DB;

use App\Model\TDocumentoGeneradoSunat;

class DocumentoGeneradoSunatController extends Controller
{
	public function actionVer(Request $request, SessionManager $sessionManager, $pagina=1)
	{
		if(!$sessionManager->get('facturacionElectronica'))
		{
			return $this->plataformHelper->redirectError('No tiene autorización para realizar operaciones electrónicas con la SUNAT.', '/');
		}

		if($request->input('q'))
		{
			$term = $request->input('q');
			
			$paginationPrepare=$this->plataformHelper->prepararPaginacion(TDocumentoGeneradoSunat::whereRaw('codigoEmpresa=? and (numeroComprobante=? or numeroComprobanteAfectado=?)', [$sessionManager->get('codigoEmpresa'), $term, $term])->orderBy('created_at', 'desc'), env('NUMERO_REGISTROS_PAGINACION'), $pagina);

			$paginationRender = $this->plataformHelper->renderizarPaginacion('documentogeneradosunat/ver', $paginationPrepare["cantidadPaginas"], $pagina, $request->input('q'));
	
			return view('documentogeneradosunat/ver', ['listaTDocumentoGeneradoSunat' => $paginationPrepare["listaRegistros"], "pagination" => $paginationRender, "q" => $request->input('q')]);
		}

		$paginationPrepare=$this->plataformHelper->prepararPaginacion(TDocumentoGeneradoSunat::whereRaw('codigoEmpresa=?', [$sessionManager->get('codigoEmpresa')])->orderBy('created_at', 'desc'), env('NUMERO_REGISTROS_PAGINACION'), $pagina);
		$paginationRender=$this->plataformHelper->renderizarPaginacion('documentogeneradosunat/ver', $paginationPrepare['cantidadPaginas'], $pagina);

		return view('documentogeneradosunat/ver', ['listaTDocumentoGeneradoSunat' => $paginationPrepare['listaRegistros'], 'pagination' => $paginationRender]);
	}
}
?>