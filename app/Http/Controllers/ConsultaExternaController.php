<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Encryption\Encrypter;
use App\Helper\NumeroLetras;
use Illuminate\Foundation\Application;

use ZipArchive;
use DB;

use App\Model\TReciboVenta;
use App\Model\TResumenDiario;
use App\Model\TReciboVentaNotaCredito;
use App\Model\TReciboVentaNotaDebito;
use App\Model\TEmpresa;
use App\Model\TOficina;
use App\Model\TReciboVentaOutEf;

class ConsultaExternaController extends Controller
{
	public static function addHeaders()
	{
		header('Access-Control-Allow-Origin: *');
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
		{
			$originRequest = $_SERVER['HTTP_ORIGIN'];
			$allowOriginWeb = env('URL_CONSULTA_EXTERNA');
			$allowOriginPage = env('URL_CONSULTA_EXTERNA_PAGE');

			if(strcasecmp($originRequest, 'http://' . $allowOriginWeb) !== 0 &&
				strcasecmp($originRequest, 'https://' . $allowOriginWeb) !== 0 &&
				strcasecmp($originRequest, 'http://' . $allowOriginPage) !== 0 &&
				strcasecmp($originRequest, 'https://' . $allowOriginPage) !== 0)
			{
				dd("Request has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource");
			}
		}
	}

	public function actionClienteExterno(Request $request)
	{
		ConsultaExternaController::addHeaders();
		
		$fechaBusquedaInicial = date("Y-m-d");
		$fechaBusquedaFinal = date("Y-m-d");
		$documentoCliente = preg_match("/^[0-9]{8}([0-9]{3})?$/", trim($request->get('documentoCliente'))) === 1 ? trim($request->get('documentoCliente')) : null;
		$result = [
			'ventasfe' => [],
			'ventaswef' => []
		];

		if($documentoCliente == null)
		{
			return response()->json([]);
		}

		if(\DateTime::createFromFormat('Y-m-d', $request->get('fechaBusquedaInicial')) !== FALSE)
		{
			$fechaBusquedaInicial = \DateTime::createFromFormat('Y-m-d', $request->get('fechaBusquedaInicial'))->format('Y-m-d');
		}

		if(\DateTime::createFromFormat('Y-m-d', $request->get('fechaBusquedaFinal')) !== FALSE)
		{
			$fechaBusquedaFinal = \DateTime::createFromFormat('Y-m-d', $request->get('fechaBusquedaFinal'))->format('Y-m-d');
		}

		$result['ventasfe'] = TReciboVenta::with(['treciboventadetalle', 'treciboventanotacredito', 'treciboventanotadebito', 'toficina.tempresa'])->whereRaw('documentoCliente=? and DATE(fechaComprobanteEmitido) BETWEEN ? and ? and (? or codigoOficina in (select codigoOficina from toficina WHERE codigoEmpresa = (SELECT codigoEmpresa from tempresa WHERE ruc = ?)))', 
			[
				$documentoCliente, 
				$fechaBusquedaInicial, 
				$fechaBusquedaFinal,
				!$request->has('rucEmpresa'),
				$request->get('rucEmpresa')
			])->orderBy('fechaComprobanteEmitido', 'asc')->take(50)->get();

		$result['ventaswef'] = TReciboVentaOutEf::with(['treciboventadetalleoutef', 'treciboventa', 'toficina.tempresa'])->whereRaw('documentoCliente=? and DATE(fechaComprobanteEmitido) BETWEEN ? and ? and (? or codigoOficina in (select codigoOficina from toficina WHERE codigoEmpresa = (SELECT codigoEmpresa from tempresa WHERE ruc = ?)))', 
			[
				$documentoCliente, 
				$fechaBusquedaInicial, 
				$fechaBusquedaFinal,
				!$request->has('rucEmpresa'),
				$request->get('rucEmpresa')
			])->orderBy('fechaComprobanteEmitido', 'asc')->take(50)->get();

		return response()->json($result);
	}

	public function actionComprobantesEmitidos()
	{
		ConsultaExternaController::addHeaders();

		return response()->json(TReciboVenta::count() + TReciboVentaNotaCredito::count() + TReciboVentaNotaDebito::count() + TResumenDiario::count());
    }
    
    public function actionDescargarPdfXml(ResponseFactory $responseFactory, Encrypter $encrypter, Application $application, $codigoComprobante=null, $formato='Normal')
	{
		ConsultaExternaController::addHeaders();

        $tReciboVenta=TReciboVenta::with(['tpersonal.tusuario', 'tcategoriaventa'])->whereRaw('codigoReciboVenta=?', [$codigoComprobante])->first();
        
        if($tReciboVenta==null)
        {
            echo 'without resources';
            return;
        }
        
        $tOficina = TOficina::find($tReciboVenta->codigoOficina);
        
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$tOficina->codigoEmpresa])->first();
	
		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		$tEmpresa->formatoComprobante = $formato;

		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 1000]);
		}

		require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

		$rutaBaseQr=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/qr';
		$nombreArchivoTemp=$tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03').'-'.$tReciboVenta->numeroRecibo;

		if(!file_exists($rutaBaseQr.'/'.$nombreArchivoTemp.'.png'))
		{
			if (!file_exists($rutaBaseQr))
			{
				mkdir($rutaBaseQr, 0777, true);
			}

			$contentQr=$tEmpresa->ruc
			.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03')
			.'|'.explode('-', $tReciboVenta->numeroRecibo)[0]
			.'|'.explode('-', $tReciboVenta->numeroRecibo)[1]
			.'|'.$tReciboVenta->impuestoAplicado
			.'|'.$tReciboVenta->total
			.'|'.$tReciboVenta->fechaComprobanteEmitido
			.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '6' : '1')
			.'|'.$tReciboVenta->documentoCliente;

			\QRcode::png($contentQr, $rutaBaseQr.'/'.$nombreArchivoTemp.'.png', QR_ECLEVEL_L, 4);
		}

		$pathQr=$rutaBaseQr.'/'.$nombreArchivoTemp.'.png';
		$typeQr=pathinfo($pathQr, PATHINFO_EXTENSION);
		$dataQr=file_get_contents($pathQr);
		$base64Qr='data:image/' . $typeQr . ';base64,' . base64_encode($dataQr);

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventa/'.strtolower($tReciboVenta->tipoRecibo), ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVenta' => $tReciboVenta, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVenta->total), ''), 'base64Qr' => $base64Qr, 'base64Logo' => $base64Logo]));

		$rutaFolderTemp=public_path().'/temp';
		$dataTemp=uniqid();

		file_put_contents($rutaFolderTemp.'-'.$dataTemp.'.pdf', $pdf->output());

		$rutaZipTemp=$rutaFolderTemp.'/'.$tReciboVenta->numeroRecibo.'-'.$tEmpresa->ruc.'-'.$dataTemp.'.zip';
		$rutaContenedor=storage_path().'/app/'.$tEmpresa->codigoEmpresa.'/efxml/'.$codigoComprobante;

	    $zip=new ZipArchive();
	    
	    $zip->open($rutaZipTemp, ZipArchive::CREATE);

		$zip->addFile($rutaFolderTemp.'-'.$dataTemp.'.pdf', $tReciboVenta->tipoRecibo.'-'.$dataTemp.'.pdf');

	     /*Begin: optencion de archivo XML*/

		if($tReciboVenta->tipoRecibo=='Factura')
		{
			$xmlName = $tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Boleta' ? '03' : '01').'-'.$tReciboVenta->numeroRecibo.'.xml';
			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getfilesxmlaszip',
			[
				'form_params' =>
				[
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.codigoUnicoVenta' => $tReciboVenta->codigoReciboVenta,
					'dto.filesName' => [$xmlName],
					'dto.userNameEf' => $tEmpresa->userNameEf,
					'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf)
				]
			]);

			$dataResponse=(object)json_decode($response->getBody(), true);
			
			$dataResponse->mo=(object)($dataResponse->mo);

			if($dataResponse->mo->type!='success')
			{
				$zip->close();

				unlink($rutaZipTemp);
				unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');

				dd('Failed to contact biller');
			}
			
			if(count((object)($dataResponse->dto)) > 0)
			{
				$zip->addFromString($xmlName . '.zip', base64_decode( ((object)($dataResponse->dto))->zipAsBase64 ));
			}
		}

		/*End: optencion de archivo XML*/

	    $zip->close();

		unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');

	    return $responseFactory->download($rutaZipTemp)->deleteFileAfterSend(true);
	}

	public function actionImprimirComprobanteVentaSinFe(Application $application, $codigoComprobante=null, $formato='Normal')
	{
		ConsultaExternaController::addHeaders();

        $tReciboVenta=TReciboVentaOutEf::with(['tpersonal.tusuario'])->whereRaw('codigoReciboVentaOutEf=?', [$codigoComprobante])->first();
        
        if($tReciboVenta==null)
        {
            echo 'without resources';
            return;
        }
        
        $tOficina = TOficina::find($tReciboVenta->codigoOficina);
        
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$tOficina->codigoEmpresa])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		$tEmpresa->formatoComprobante = $formato;

		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 650]);
		}

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventa/'.strtolower($tReciboVenta->tipoRecibo).'sinfe', ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVenta' => $tReciboVenta, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVenta->total), ''), 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03').'-'.$tReciboVenta->numeroRecibo.'-PF.pdf', ['attachment' => false]);
    }
    
    public function actionDescargarPdfXmlNotaCredito(ResponseFactory $responseFactory, Encrypter $encrypter, Application $application, $codigoComprobante=null, $formato='Normal')
	{
		ConsultaExternaController::addHeaders();

		$tReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventa.tpersonal.tusuario'])->whereRaw('codigoReciboVentaNotaCredito=?', [$codigoComprobante])->first();
        
        if($tReciboVentaNotaCredito==null)
        {
            echo 'without resources';
            return;
        }

        $tOficina = TOficina::find($tReciboVentaNotaCredito->codigoOficina);
        
        $tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaNotaCredito){ $q->whereRaw('codigoOficina=?', [$tReciboVentaNotaCredito->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$tOficina->codigoEmpresa])->first();
        
		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		$tEmpresa->formatoComprobante = $formato;

		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 1000]);
		}

		require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventanotacredito/notacredito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaCredito' => $tReciboVentaNotaCredito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaCredito->total), ''), 'base64Logo' => $base64Logo]));

		$rutaFolderTemp=public_path().'/temp';
		$dataTemp=uniqid();

		file_put_contents($rutaFolderTemp.'-'.$dataTemp.'.pdf', $pdf->output());

		$rutaZipTemp=$rutaFolderTemp.'/'.$tReciboVentaNotaCredito->numeroRecibo.'-'.$tEmpresa->ruc.'-'.$dataTemp.'.zip';
		$rutaContenedor=storage_path().'/app/'.$tEmpresa->codigoEmpresa.'/efxml/'.$tReciboVentaNotaCredito->codigoReciboVenta;

	    $zip=new ZipArchive();
	    
	    $zip->open($rutaZipTemp, ZipArchive::CREATE);

		$zip->addFile($rutaFolderTemp.'-'.$dataTemp.'.pdf', 'Nota de crédito-'.$dataTemp.'.pdf');

	    /*Begin: optencion de archivo XML*/
		
		if($tReciboVentaNotaCredito->treciboventa->tipoRecibo=='Factura')
		{
			$xmlName = $tEmpresa->ruc.'-07-'.$tReciboVentaNotaCredito->numeroRecibo.'.xml';
			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getfilesxmlaszip',
			[
				'form_params' =>
				[
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.codigoUnicoVenta' => $tReciboVentaNotaCredito->treciboventa->codigoReciboVenta,
					'dto.filesName' => [$xmlName],
					'dto.userNameEf' => $tEmpresa->userNameEf,
					'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf)
				]
			]);

			$dataResponse=(object)json_decode($response->getBody(), true);
			
			$dataResponse->mo=(object)($dataResponse->mo);

			if($dataResponse->mo->type!='success')
			{
				$zip->close();

				unlink($rutaZipTemp);
				unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');

				dd('Failed to contact biller');
			}
			
			if(count((object)($dataResponse->dto)) > 0)
			{
				$zip->addFromString($xmlName . '.zip', base64_decode( ((object)($dataResponse->dto))->zipAsBase64 ));
			}
		}

		/*End: optencion de archivo XML*/

	    $zip->close();

		unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');

	    return $responseFactory->download($rutaZipTemp)->deleteFileAfterSend(true);
    }
    
    public function actionDescargarPdfXmlNotaDebito(ResponseFactory $responseFactory, Encrypter $encrypter, Application $application, $codigoComprobante=null, $formato='Normal')
	{
		ConsultaExternaController::addHeaders();

		$tReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventa.tpersonal.tusuario'])->whereRaw('codigoReciboVentaNotaDebito=?', [$codigoComprobante])->first();
        
        if($tReciboVentaNotaDebito==null)
        {
            echo 'without resources';
            return;
        }

        $tOficina = TOficina::find($tReciboVentaNotaDebito->codigoOficina);
        
        $tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaNotaDebito){ $q->whereRaw('codigoOficina=?', [$tReciboVentaNotaDebito->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$tOficina->codigoEmpresa])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');
		
		$tEmpresa->formatoComprobante = $formato;

		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 1000]);
		}

		require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventanotadebito/notadebito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaDebito' => $tReciboVentaNotaDebito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaDebito->total), ''), 'base64Logo' => $base64Logo]));

		$rutaFolderTemp=public_path().'/temp';
		$dataTemp=uniqid();

		file_put_contents($rutaFolderTemp.'-'.$dataTemp.'.pdf', $pdf->output());

		$rutaZipTemp=$rutaFolderTemp.'/'.$tReciboVentaNotaDebito->numeroRecibo.'-'.$tEmpresa->ruc.'-'.$dataTemp.'.zip';
		$rutaContenedor=storage_path().'/app/'.$tEmpresa->codigoEmpresa.'/efxml/'.$tReciboVentaNotaDebito->codigoReciboVenta;

	    $zip=new ZipArchive();
	    
	    $zip->open($rutaZipTemp, ZipArchive::CREATE);

		$zip->addFile($rutaFolderTemp.'-'.$dataTemp.'.pdf', 'Nota de débito-'.$dataTemp.'.pdf');

	    /*Begin: optencion de archivo XML*/
		
		if($tReciboVentaNotaDebito->treciboventa->tipoRecibo=='Factura')
		{
			$xmlName = $tEmpresa->ruc.'-08-'.$tReciboVentaNotaDebito->numeroRecibo.'.xml';
			$clientHttp=new \GuzzleHttp\Client(['headers' => ['content-type' => 'application/x-www-form-urlencoded']]);

			$response=$clientHttp->request('POST', env('FACTURADOR_CODIDEEP').'/bill/getfilesxmlaszip',
			[
				'form_params' =>
				[
					'dto.rucEmpresaEf' => $tEmpresa->ruc,
					'dto.codigoUnicoVenta' => $tReciboVentaNotaDebito->treciboventa->codigoReciboVenta,
					'dto.filesName' => [$xmlName],
					'dto.userNameEf' => $tEmpresa->userNameEf,
					'dto.passwordEf' => $encrypter->decrypt($tEmpresa->passwordEf)
				]
			]);

			$dataResponse=(object)json_decode($response->getBody(), true);
			
			$dataResponse->mo=(object)($dataResponse->mo);

			if($dataResponse->mo->type!='success')
			{
				$zip->close();

				unlink($rutaZipTemp);
				unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');

				dd('Failed to contact biller');
			}
			
			if(count((object)($dataResponse->dto)) > 0)
			{
				$zip->addFromString($xmlName . '.zip', base64_decode( ((object)($dataResponse->dto))->zipAsBase64 ));
			}
		}

		/*End: optencion de archivo XML*/

	    $zip->close();

		unlink($rutaFolderTemp.'-'.$dataTemp.'.pdf');

	    return $responseFactory->download($rutaZipTemp)->deleteFileAfterSend(true);
	}

	public function actionImprimirComprobanteVenta(Application $application, $codigoComprobante=null, $formato='Normal')
	{
		ConsultaExternaController::addHeaders();

        $tReciboVenta=TReciboVenta::with(['tpersonal.tusuario', 'tcategoriaventa'])->whereRaw('codigoReciboVenta=?', [$codigoComprobante])->first();
        
        if($tReciboVenta==null)
        {
            echo 'without resources';
            return;
        }
        
        $tOficina = TOficina::find($tReciboVenta->codigoOficina);
        
		$tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVenta){ $q->whereRaw('codigoOficina=?', [$tReciboVenta->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$tOficina->codigoEmpresa])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		$tEmpresa->formatoComprobante = $formato;

		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 1000]);
		}

		require_once dirname(__FILE__).'/../../ExternalLib/phpqrcode/qrlib.php';

		$rutaBaseQr=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/qr';
		$nombreArchivoTemp=$tEmpresa->ruc.'-'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03').'-'.$tReciboVenta->numeroRecibo;

		if(!file_exists($rutaBaseQr.'/'.$nombreArchivoTemp.'.png'))
		{
			if (!file_exists($rutaBaseQr))
			{
				mkdir($rutaBaseQr, 0777, true);
			}

			$contentQr=$tEmpresa->ruc
			.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '01' : '03')
			.'|'.explode('-', $tReciboVenta->numeroRecibo)[0]
			.'|'.explode('-', $tReciboVenta->numeroRecibo)[1]
			.'|'.$tReciboVenta->impuestoAplicado
			.'|'.$tReciboVenta->total
			.'|'.$tReciboVenta->fechaComprobanteEmitido
			.'|'.($tReciboVenta->tipoRecibo=='Factura' ? '6' : '1')
			.'|'.$tReciboVenta->documentoCliente;

			\QRcode::png($contentQr, $rutaBaseQr.'/'.$nombreArchivoTemp.'.png', QR_ECLEVEL_L, 4);
		}

		$pathQr=$rutaBaseQr.'/'.$nombreArchivoTemp.'.png';
		$typeQr=pathinfo($pathQr, PATHINFO_EXTENSION);
		$dataQr=file_get_contents($pathQr);
		$base64Qr='data:image/' . $typeQr . ';base64,' . base64_encode($dataQr);

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventa/'.strtolower($tReciboVenta->tipoRecibo), ['genericHelper' => $this->plataformHelper, 'tEmpresa' => $tEmpresa, 'tReciboVenta' => $tReciboVenta, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVenta->total), ''), 'base64Qr' => $base64Qr, 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($nombreArchivoTemp.'.pdf', ['attachment' => false]);
	}

	public function actionImprimirComprobanteNotaCredito(Application $application, $codigoComprobante=null, $formato='Normal')
	{
		ConsultaExternaController::addHeaders();

		$tReciboVentaNotaCredito=TReciboVentaNotaCredito::with(['treciboventa.tpersonal.tusuario'])->whereRaw('codigoReciboVentaNotaCredito=?', [$codigoComprobante])->first();
        
        if($tReciboVentaNotaCredito==null)
        {
            echo 'without resources';
            return;
        }

        $tOficina = TOficina::find($tReciboVentaNotaCredito->codigoOficina);
        
        $tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaNotaCredito){ $q->whereRaw('codigoOficina=?', [$tReciboVentaNotaCredito->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$tOficina->codigoEmpresa])->first();
        
		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		$tEmpresa->formatoComprobante = $formato;

		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 650]);
		}

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventanotacredito/notacredito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaCredito' => $tReciboVentaNotaCredito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaCredito->total), ''), 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($tEmpresa->ruc.'-07-'.$tReciboVentaNotaCredito->numeroRecibo.'.pdf', ['attachment' => false]);
    }
    
    public function actionImprimirComprobanteNotaDebito(Application $application, $codigoComprobante=null, $formato='Normal')
	{
		ConsultaExternaController::addHeaders();

		$tReciboVentaNotaDebito=TReciboVentaNotaDebito::with(['treciboventa.tpersonal.tusuario'])->whereRaw('codigoReciboVentaNotaDebito=?', [$codigoComprobante])->first();
        
        if($tReciboVentaNotaDebito==null)
        {
            echo 'without resources';
            return;
        }

        $tOficina = TOficina::find($tReciboVentaNotaDebito->codigoOficina);
        
        $tEmpresa=TEmpresa::with(['toficina' => function($q) use($tReciboVentaNotaDebito){ $q->whereRaw('codigoOficina=?', [$tReciboVentaNotaDebito->codigoOficina]); }])->whereRaw('codigoEmpresa=?', [$tOficina->codigoEmpresa])->first();

		$convertirNumeroLetra=new NumeroLetras();

		$pdf=$application->make('dompdf.wrapper');

		$tEmpresa->formatoComprobante = $formato;
		
		if($tEmpresa->formatoComprobante=='Ticket')
		{
			$pdf->setPaper([0, 0, 270, 650]);
		}

		$pathLogo=public_path().'/img/empresa/'.$tEmpresa->codigoEmpresa.'/logoEmpresarial.png';
		$typeLogo=pathinfo($pathLogo, PATHINFO_EXTENSION);
		$dataLogo=file_get_contents($pathLogo);
		$base64Logo='data:image/' . $typeLogo . ';base64,' . base64_encode($dataLogo);

		$pdf->loadHTML(view('reciboventanotadebito/notadebito', ['tEmpresa' => $tEmpresa, 'tReciboVentaNotaDebito' => $tReciboVentaNotaDebito, 'valorTotalLetras' => $convertirNumeroLetra->valorEnLetras(($tReciboVentaNotaDebito->total), ''), 'base64Logo' => $base64Logo]));
		
		return $pdf->stream($tEmpresa->ruc.'-08-'.$tReciboVentaNotaDebito->numeroRecibo.'.pdf', ['attachment' => false]);
	}
}