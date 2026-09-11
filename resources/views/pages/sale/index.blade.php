@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />
@endsection
@section('content')
    @php $leadPerm = \App\Helpers\GlobalHelper::modulePermission(auth()->user(), 'lead'); @endphp
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> All</h4>

            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3 py-3">
                    <h5 class="card-title mb-0">Sales</h5>
                </div>
                <div class="card-datatable table-responsive">
                    <table id="salesTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Business Name</th>
                                <th>Number</th>
                                <th>Closers</th>
                                <th>Customer Support</th>
                                <th>Sale Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $item)
                                <tr @if(isset($item->chargeback)) style="background-color: rgb(255, 222, 222)" @endif>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $item->business_name_adv }}</td>
                                    <td>{{ $item->business_number_adv }}</td>
                                    <td>
                                        @foreach($item->closers as $closer)
                                            @if($closer->user)
                                                <span class="badge rounded-pill bg-label-primary">{{ explode(' -', $closer->user->name)[0] }}</span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($item->sale->Customer_support as $cs)
                                            @if($cs->user)
                                                <span class="badge rounded-pill bg-label-info">{{ explode(' -', $cs->user->name)[0] }}</span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if(isset($item->chargeback))
                                            <span class="badge rounded-pill bg-danger">Chargeback</span>
                                        @elseif($item->sale->status == 1)
                                            <span class="badge rounded-pill bg-success">Active</span>
                                        @else
                                            <span class="badge rounded-pill bg-label-info">Charged</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-inline-block text-nowrap">
                                            <a href="{{ route('sale.create', $item->id) }}"
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                                                data-bs-toggle="tooltip" title="Sale"><i
                                                    class="ri-send-plane-2-line ri-20px"></i></a>
                                            <button
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                @if($leadPerm->edit)
                                                    <a href="{{ route('lead.edit', $item->id) }}" class="dropdown-item"><i
                                                        class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                                @endif
                                                <a href="{{ route('sale.detail', $item->sale->id) }}"
                                                    class="dropdown-item" target="_blank"><i
                                                        class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                @if($leadPerm->delete)
                                                    <a type="button" data-id="{{ $item->id }}" data-route="lead"
                                                        data-bs-toggle="modal" data-bs-target="#basicModal"
                                                        class="dropdown-item delete-record"><i
                                                            class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
    <script>
        $('#salesTable').DataTable({
            autoWidth: false,
            language: window.rsEmptyStateHTML ? { emptyTable: rsEmptyStateHTML('sales') } : undefined
        });
    </script>
@endsection
