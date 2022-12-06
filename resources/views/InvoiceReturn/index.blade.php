@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Invoice Returns') }}
@endsection

@section('breadcrumb')
    @if(\Auth::guard('customer')->check())
    <li class="breadcrumb-item"><a href="{{route('customer.dashboard')}}">{{__('Dashboard')}}</a></li>
    @else
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    @endif
    <li class="breadcrumb-item">{{__('Invoice Return')}}</li>
@endsection

@section('action-btn')
    <div class="float-end">
        <!-- <a class="btn btn-sm btn-primary" data-bs-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1" data-bs-toggle="tooltip" title="{{__('Filter')}}">
            <i class="ti ti-filter"></i>
        </a> -->

        <a href="{{ route('invoice.export') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Export')}}">
            <i class="ti ti-file-export"></i>
        </a>

        @can('create invoice')
            <a href="{{ route('InvoiceReturn.create', 0) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="{{__('Create')}}">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection



@section('content')

{{--  <div class="row">
    <div class="col-sm-12">
        <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    @if (!\Auth::guard('customer')->check())
                        {{ Form::open(['route' => ['invoice.index'], 'method' => 'GET', 'id' => 'customer_submit']) }}
                    @else
                        {{ Form::open(['route' => ['customer.invoice'], 'method' => 'GET', 'id' => 'customer_submit']) }}
                    @endif
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                <div class="btn-box">
                                    {{ Form::label('issue_date', __('Date'), ['class' => 'text-type']) }}

                                    {{ Form::text('issue_date', isset($_GET['issue_date'])?$_GET['issue_date']:null, array('class' => 'form-control month-btn','id'=>'pc-daterangepicker-1')) }}

                                </div>
                            </div>
                            @if (!\Auth::guard('customer')->check())
                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                    <div class="btn-box">
                                        {{ Form::label('customer', __('Customer'), ['class' => 'text-type']) }}

                                        {{ Form::select('customer', $customer, isset($_GET['customer']) ? $_GET['customer'] : '', ['class' => 'form-control select2']) }}
                                    </div>
                                </div>
                            @endif
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
                                <div class="btn-box">
                                    {{ Form::label('status', __('Status'), ['class' => 'text-type']) }}

                                    {{ Form::select('status', ['' => 'Select Status'] + $status, isset($_GET['status']) ? $_GET['status'] : '', ['class' => 'form-control select2']) }}
                                </div>
                            </div>
                            <div class="col-auto float-end ms-2 mt-4">

                                <a href="#" class="btn btn-sm btn-primary"
                                onclick="document.getElementById('customer_submit').submit(); return false;"
                                data-bs-toggle="tooltip" title="{{__('Search')}}" data-original-title="{{ __('apply') }}">
                                    <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                </a>

                                @if (!\Auth::guard('customer')->check())
                                    <a href="{{ route('invoice.index') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                                    </a>
                                @else
                                    <a href="{{ route('customer.invoice') }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="{{__('Reset')}}">
                                        <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                                    </a>
                                @endif
                        </div>

                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>  --}}

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body table-border-style">
                <h5></h5>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th> {{ __('Invoice') }}</th>
                                @if (!\Auth::guard('customer')->check())
                                    <th>{{ __('Customer') }}</th>
                                @endif
                                <th>{{ __('Issue Date') }}</th>
                                <th>{{ __('Due Date') }}</th>
                                <th>{{ __('Due Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                    <th>{{ __('Action') }}</th>
                                @endif
                                {{-- <th>
                                <td class="barcode">
                                    {!! DNS1D::getBarcodeHTML($invoice->sku, "C128",1.4,22) !!}
                                    <p class="pid">{{$invoice->sku}}</p>
                                </td>
                            </th> --}}
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="Id">
                                        @if (\Auth::guard('customer')->check())
                                            <a href="{{ route('customer.invoice.return.show', \Crypt::encrypt($invoice->id)) }}" class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                        @else
                                            <a href="{{ route('customer.invoice.return.show', \Crypt::encrypt($invoice->id)) }}" class="btn btn-outline-primary">{{ AUth::user()->invoiceNumberFormat($invoice->invoice_id) }}</a>
                                        @endif
                                    </td>
                                    @if (!\Auth::guard('customer')->check())
                                        <td> {{ !empty($invoice->customer) ? $invoice->customer->name : '' }} </td>
                                    @endif
                                    <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                    <td>
                                        @if ($invoice->due_date < date('Y-m-d'))
                                            <p class="text-danger">
                                                {{ \Auth::user()->dateFormat($invoice->due_date) }}</p>
                                        @else
                                            {{ \Auth::user()->dateFormat($invoice->due_date) }}
                                        @endif
                                    </td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->getDue()) }}</td>
                                    <td>
                                        @if ($invoice->status == 0)
                                            <span
                                                class="badge fix_badges bg-primary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 1)
                                            <span
                                                class="badge fix_badges bg-info p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 2)
                                            <span
                                                class="badge fix_badges bg-secondary p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 3)
                                            <span
                                                class="badge fix_badges bg-warning p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @elseif($invoice->status == 4)
                                            <span
                                                class="badge fix_badges bg-danger p-2 px-3 rounded">{{ __(\App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                        @endif
                                    </td>
                                    @if (Gate::check('edit invoice') || Gate::check('delete invoice') || Gate::check('show invoice'))
                                        <td class="Action">
                                            <span>
                                                {{--  @can('duplicate invoice')

                                                    <div class="action-btn bg-secondary ms-2">
                                                            {!! Form::open(['method' => 'get', 'route' => ['invoice.duplicate', $invoice->id]]) !!}
                                                                <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para " data-bs-toggle="tooltip" title="{{__('Duplicate')}}"
                                                                    data-original-title="{{ __('Duplicate') }}" >
                                                                    <i class="ti ti-copy text-white"></i>
                                                                </a>
                                                            {!! Form::close() !!}
                                                        </div>  --}}


{{--                                                        <div class="action-btn bg-success ms-2">--}}
{{--                                                            <a href="#" class="mx-3 btn btn-sm align-items-center" data-toggle="tooltip"--}}
{{--                                                                data-original-title="{{ __('Duplicate') }}" data-bs-toggle="tooltip" title="Duplicate Invoice"--}}
{{--                                                                data-original-title="{{ __('Delete') }}"--}}
{{--                                                                data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back"--}}
{{--                                                                data-confirm-yes="document.getElementById('duplicate-form-{{ $invoice->id }}').submit();">--}}
{{--                                                                <i class="ti ti-copy text-white"></i>--}}
{{--                                                                {!! Form::open(['method' => 'get', 'route' => ['invoice.duplicate', $invoice->id], 'id' => 'duplicate-form-' . $invoice->id]) !!}--}}
{{--                                                                {!! Form::close() !!}--}}
{{--                                                            </a>--}}
{{--                                                        </div>--}}
                                                    {{--  @endcan  --}}
                                                    {{--  @can('show invoice')
                                                        @if (\Auth::guard('customer')->check())
                                                                <div class="action-btn bg-warning ms-2">
                                                                    <a href="{{ route('customer.invoice.return.show', \Crypt::encrypt($invoice->id)) }}"
                                                                        class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="Show "
                                                                        data-original-title="{{ __('Detail') }}">
                                                                        <i class="ti ti-eye text-white"></i>
                                                                    </a>
                                                                </div>
                                                        @else
                                                                <div class="action-btn bg-warning ms-2">
                                                                    <a href="{{ route('customer.invoice.return.show', \Crypt::encrypt($invoice->id)) }}"
                                                                        class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="Show "
                                                                        data-original-title="{{ __('Detail') }}">
                                                                        <i class="ti ti-eye text-white"></i>
                                                                    </a>
                                                                </div>
                                                        @endif
                                                    @endcan  --}}
                                                    @can('edit invoice')
                                                            <div class="action-btn bg-info ms-2">
                                                                <a href="{{ route('invoice.edit', \Crypt::encrypt($invoice->id)) }}"
                                                                    class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="Edit "
                                                                    data-original-title="{{ __('Edit') }}">
                                                                    <i class="ti ti-edit text-white"></i>
                                                                </a>
                                                            </div>
                                                    @endcan
                                                    @can('delete invoice')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.returndestroy', $invoice->id], 'id' => 'delete-form-' . $invoice->id]) !!}
                                                                    <a href="#" class="mx-3 btn btn-sm align-items-center bs-pass-para " data-bs-toggle="tooltip" title="{{__('Delete')}}"
                                                                        data-original-title="{{ __('Delete') }}"
                                                                        data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="document.getElementById('delete-form-{{ $invoice->id }}').submit();">
                                                                        <i class="ti ti-trash text-white"></i>
                                                                    </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                    @endcan
                                                </span>
                                            </td>
                                        @endif
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
