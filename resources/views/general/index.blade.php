@extends('template.layoutgeneral')
@section('titulo', 'Página principal')
@section('subTitulo', 'Estadísticas generales')
@section('cuerpoGeneral')
<link rel="stylesheet" href="{{asset('viewResources/general/index.css?x='.env('CACHE_LAST_UPDATE'))}}">
<div class="row">
    <div class="col-md-3">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="ion ion-ios-cart-outline"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Ventas realizadas</span>
            <span class="info-box-number">{{$ventasContretadas}}</span>

            <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
                    Ventas concretadas
                </span>
            </div>
        </div>        
    </div>
    <div class="col-md-3">
        <div class="info-box bg-red">
            <span class="info-box-icon"><i class="icon ion-card"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Compras realizadas</span>
            <span class="info-box-number">{{$comprasContretadas}}</span>

            <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
                    Compras concretadas
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="ion ion-ios-cloud-upload-outline"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Doc. Electrónicos</span>
            <span class="info-box-number">{{$cantidadDocumentosGeneradosSunat}}</span>

            <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
                    Emitidos a la SUNAT
                </span>
            </div>
        </div>                
    </div>
    <div class="col-md-3">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>

            <div class="info-box-content">
            <span class="info-box-text">Prod. en inventario</span>
            <span class="info-box-number">{{$cantidadProductosEnStock}}</span>

            <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
                    Stock total de productos
                </span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Documentos rechazados | Alertas </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-7">
                        <p class="text-center">
                            <strong>Doc. rechazados | Pend.</strong>
                        </p>
                        <div class="nav-tabs-custom" style="box-shadow: none !important;">
                            <ul class="nav nav-tabs">
                                @php $active = 1; @endphp
                                @foreach ($documentosRechazados as $documentoRechazado)
                                <li class="{{ $active == count($documentosRechazados)? 'active' : ''}}"><a href="#tab_{{$documentoRechazado->mes}}" data-toggle="tab">{{$documentoRechazado->mes}}</a></li>
                                @php $active++; @endphp
                                @endforeach
                                <li class="pull-right" style="background: #fff7be"><a href="#tab_pendientes" data-toggle="tab"><i class="fa fa-refresh"></i> Pendien. de env.</a></li>
                            </ul>
                            <div class="tab-content scroll-type-1" style="max-height: 300px !important; overflow: scroll;">
                                @php $active = 1; @endphp
                                @foreach ($documentosRechazados as $documentoRechazado)
                                    <div class="tab-pane {{ $active == count($documentosRechazados) ? 'active' : ''}}" id="tab_{{$documentoRechazado->mes}}">
                                        @php $active++; @endphp
                                        <div class="table-responsive no-padding">
                                            <table class="table table-hover table-striped">
                                                <tr>
                                                    @if((strpos($rolUser, 'Súper usuario') !== false))
                                                    <th>Empresa</th>
                                                    @endif
                                                    <th>Tipo</th>
                                                    <th class="text-center">Comprobante</th>
                                                    <!-- <th>Cliente</th> -->
                                                    <th>F. Registro</th>
                                                </tr>
                                                @forelse($documentoRechazado->documentosSunat as $documento)
                                                <tr>
                                                    @if((strpos($rolUser, 'Súper usuario') !== false))
                                                    <td>{{$documento->tempresa->razonSocial}}</td>
                                                    @endif
                                                    <td>{{$documento->tipo}}</td>
                                                    <td class="text-center">
														<div>{{$documento->numeroComprobante}}</div>
														<small style="color: #d7a61b;font-weight: bold;">{{$documento->numeroComprobanteAfectado}}</small>
													</td>
                                                    <!-- <td>({{$documento->documento}}) {{$documento->nombre}}</td> -->
                                                    <td>{{date_format($documento->created_at, "Y-m-d")}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">
                                                            No hay documentos rechazados
                                                        </td>
                                                    </tr>
                                                @endforelse 
                                            </table>
                                        </div>
                                    </div> 
                                @endforeach    
                                <div class="tab-pane" id="tab_pendientes">                                    
                                    <div class="table-responsive no-padding">
                                        <table class="table table-hover table-striped">
                                            <tr>
                                                @if((strpos($rolUser, 'Súper usuario') !== false))
                                                <th>Empresa</th>
                                                @endif
                                                <th>Tipo</th>
                                                <th>Comprobante</th>
                                                <!-- <th>Cliente</th> -->
                                                <th>F. Registro</th>
                                            </tr>
                                            @foreach($tDocumentosPendientesEnvio->facturas as $documento)
                                            <tr>
                                                @if((strpos($rolUser, 'Súper usuario') !== false))
                                                <td>{{$documento->toficina->tempresa->razonSocial}}</td>
                                                @endif
                                                <td>{{$documento->tipoRecibo}}</td>
                                                <td>{{$documento->numeroRecibo}}</td>
                                                <!-- <td>({{$documento->documento}}) {{$documento->nombre}}</td> -->
                                                <td>{{date_format($documento->created_at, "Y-m-d")}}</td>
                                            </tr>
                                            @endforeach
                                            @foreach($tDocumentosPendientesEnvio->notasCreditos as $documento)
                                            <tr>
                                                @if((strpos($rolUser, 'Súper usuario') !== false))
                                                <td>{{$documento->treciboventa->toficina->tempresa->razonSocial}}</td>
                                                @endif
                                                <td>Nota de crédito</td>
                                                <td>{{$documento->numeroRecibo}}</td>
                                                <!-- <td>({{$documento->documento}}) {{$documento->nombre}}</td> -->
                                                <td>{{date_format($documento->created_at, "Y-m-d")}}</td>
                                            </tr>
                                            @endforeach
                                            @foreach($tDocumentosPendientesEnvio->notasDebitos as $documento)
                                            <tr>
                                                @if((strpos($rolUser, 'Súper usuario') !== false))
                                                <td>{{$documento->treciboventa->toficina->tempresa->razonSocial}}</td>
                                                @endif
                                                <td>Nota de débito</td>
                                                <td>{{$documento->numeroRecibo}}</td>
                                                <!-- <td>({{$documento->documento}}) {{$documento->nombre}}</td> -->
                                                <td>{{date_format($documento->created_at, "Y-m-d")}}</td>
                                            </tr>
                                            @endforeach
                                            @foreach($tDocumentosPendientesEnvio->guiaRemision as $documento)
                                            <tr>
                                                @if((strpos($rolUser, 'Súper usuario') !== false))
                                                <td>{{$documento->treciboventa->toficina->tempresa->razonSocial}}</td>
                                                @endif
                                                <td>Guía de remisión</td>
                                                <td>{{$documento->numeroRecibo}}</td>
                                                <!-- <td>({{$documento->documento}}) {{$documento->nombre}}</td> -->
                                                <td>{{date_format($documento->created_at, "Y-m-d")}}</td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">                        
                        <p class="text-center">
                            <strong>Alertas de stock</strong>
                        </p>
                        @php($i = 0)
                        @php($registros = false)
                        @forelse ($listaProductos as $producto)
                            @php($registros = true)
                            <div class="progress-group">
                                <span class="progress-text"><span class="fa fa-tag"></span>&nbsp; {{substr($producto->nombre, 0, 35)}}</span>
                                <span class="progress-number text-yellow"><span class="fa fa-warning"></span> &nbsp;<b>{{number_format($producto->cantidad, 0, ',', '')}}</b></span>
                                <div class="progress sm">
                                    @php($colors = ["aqua", "blue", "green", "yellow", "red"])
                                    <div class="progress-bar progress-bar-{{ $colors[$i++] }}" style="width: {{ $producto->cantidad * 100 / $producto->cantidadMinimaAlertaStock }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center">
                                No hay productos en alerta de stock
                            </div>
                        @endforelse                
                        
                        @if($registros)
                        <div id="pagination">
                            {!!$pagination!!}
                        </div>
                        @endif
                    </div> 
                </div>                                             
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Estadísticas </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body"> 
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-center">
                            <strong>Estadísticas del {{ Carbon\Carbon::today()->subMonths(5)->startOfMonth()->toDateString()}} al {{ Carbon\Carbon::today()->toDateString('dd/MM/yyyy')}}</strong>
                        </p>
                        <div class="chart">
                            <canvas id="salesChart" style="height: 280px;"></canvas>
                        </div>
                        <div id="legend">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <p class="text-center">
                            <strong>Documentos emitidos a la SUNAT del {{ Carbon\Carbon::create()->day(1)->month(1)->toDateString()}} al {{ Carbon\Carbon::create()->day(31)->month(12)->toDateString('dd/MM/yyyy')}}</strong>
                        </p>
                        <div class="chart">
                            <canvas id="salesChartSunat" style="height: 280px;"></canvas>
                        </div>
                        <div id="legendSunat">

                        </div>
                    </div>                                                          
                </div>                           
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Productos más vendidos [Ventas FE]</h3>
                <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <ul class="products-list product-list-in-box">
                    @foreach ($topProductosVendidosVentaFe as $item)
                        <li class="item">
                            <div class="product-img">
                            <img src="{{asset('img/general/products.png')}}" alt="Product Image">
                            </div>
                            <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">{{substr($item->nombreProducto, 0, 15)}}
                            <span class="label label-info pull-right">S/{{$item->totalVenta}}</span></a>
                            <span class="product-description">
                                    {{$item->nombreProducto}}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Productos más vendidos [Ventas WEF]</h3>
                <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <ul class="products-list product-list-in-box">
                    @foreach ($topProductosVendidosVentaWef as $item)
                        <li class="item">
                            <div class="product-img">
                            <img src="{{asset('img/general/products2.png')}}" alt="Product Image">
                            </div>
                            <div class="product-info">
                            <a href="javascript:void(0)" class="product-title">{{substr($item->nombreProducto, 0, 15)}}
                            <span class="label label-info pull-right">S/{{$item->totalVenta}}</span></a>
                            <span class="product-description">
                                    {{$item->nombreProducto}}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>      
</div>
<script>
    var dataSalesView={!!$data!!};
	var dataSalesSunatView={!!$dataSunat!!};
</script>
<script src="{{asset('viewResources/general/index.js?x='.env('CACHE_LAST_UPDATE'))}}"></script>
@endsection