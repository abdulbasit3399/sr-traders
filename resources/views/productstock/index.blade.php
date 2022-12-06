@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Product Stock') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Product Stock')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{--  <!-- <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1" data-bs-toggle="tooltip" title="{{__('Filter')}}">
            <i class="ti ti-filter"></i>
        </a> -->
        <a   data-size="md" data-bs-toggle="tooltip" title="{{__('Import')}}" data-url="{{ route('productservice.file.import') }}" data-ajax-popup="true" data-title="{{__('Import product CSV file')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-import"></i>
        </a>
        <a href="{{route('productservice.export')}}" data-bs-toggle="tooltip" title="{{__('Export')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-file-export"></i>
        </a>  --}}

       <a   data-size="lg" data-url="{{ route('productservice.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Create Product')}}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i>
        </a>

    </div>
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr role="row">
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                 <th>{{ __('Action') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($productServices as $productService)
                                <tr class="font-style">
                                    <td>{{ $productService->name }}</td>
                                    <td>{{\Auth::user()->currencySymbol()}}{{ $productService->sale_price }}</td>
                                    <td>{{ $productService->quantity }}</td>
                                    <td class="">
                                        {{--  <div class="action-btn bg-info ms-2">  --}}
                                            <a data-size="md" class="btn btn-info btn-sm " data-url="{{ route('productstock.edit', $productService->id) }}" data-ajax-popup="true"  data-size="xl" data-bs-toggle="tooltip" title="{{__('Edit Quantity')}}">
                                                <i class="ti ti-edit text-white">Quantity</i>
                                            </a>
                                        {{--  </div>
                                        <div class="action-btn bg-success ms-2">  --}}
                                            <a class="btn btn-success btn-sm " data-url="{{ route('productservice.edit',$productService->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit Product')}}" data-title="{{__('Edit Product')}}">
                                                <i class="ti ti-edit text-white">Product</i>
                                            </a>
                                        {{--  </div>  --}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
