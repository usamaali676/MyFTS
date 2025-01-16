@extends('layouts.dashboard')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />
@endsection
@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Lead/</span> All</h4>
            <!-- Responsive Datatable -->
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title">Leads</h5>
                    <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                        <div class="col-md-4 user_role">&nbsp;</div>
                        <div class="col-md-4 user_plan">&nbsp;</div>
                        <div class="col-md-2 user_status">
                            <a class="dt-button add-new btn btn-primary waves-effect waves-light" tabindex="0"
                                href="{{ route('lead.create') }}" style="color: #fff"><span><i
                                        class="mdi mdi-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">Add
                                        Lead</span></span></a>
                        </div>
                    </div>
                </div>
                <div class="card-datatable table-responsive">
                    <table id="recodetable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Business Name</th>
                                <th>Number</th>
                                <th>Email</th>
                                <th>Category</th>
                                <th>Saler</th>
                                <th>Call Status</th>
                                <th>Closers</th>
                                <th>Sale Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @auth
                                @php
                                    $user = Auth::user();
                                @endphp
                                {{-- <p>{{ $sale }}</p> --}}

                                @if ($user->role_id == 1 || $user->role->name == 'Executives' || $user->role->name == 'QA')
                                    {{-- <p>fdgsdfg</p> --}}
                                    @foreach ($leads as $item)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $item->business_name_adv }}</td>
                                            <td>{{ $item->business_number_adv }}</td>
                                            @if (isset($item->off_email))
                                                <td>{{ $item->off_email }}</td>
                                            @else
                                                <td>N/A</td>
                                            @endif
                                            <td><span
                                                    class="badge rounded-pill bg-label-primary me-1">{{ $item->category->name }}</span>
                                            </td>
                                            <td>{{ explode(' -',   $item->saler->name )[0] }}</td>
                                            <td>{{ $item->call_status }}</td>
                                            <td>
                                                {{-- <p>{{ $item->closers }}</p> --}}
                                                @if (isset($item->closers) && count($item->closers) > 0)
                                                    <div class="d-flex" style="gap: 10px; flex-direction: column;">
                                                        @foreach ($item->closers as $list)
                                                        <span
                                                            class="badge rounded-pill bg-label-primary me-1">{{ explode(' -',  $list->user->name)[0] }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                {{-- <p>{{ $item->sale }}</p> --}}
                                                @if (isset($item->sale) && $item->sale->status == 1)
                                                    <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-inline-block text-nowrap">
                                                    <a href="{{ route('sale.create', $item->id) }}"
                                                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                                                        data-bs-toggle="tooltip" title="Active"><i
                                                            class="ri-send-plane-2-line ri-20px"></i></a>
                                                    <button
                                                        class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown" aria-expanded="false"><i
                                                            class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                                    <div class="dropdown-menu dropdown-menu-end m-0" style=""><a
                                                            href="{{ route('lead.edit', $item->id) }}" class="dropdown-item"><i
                                                                class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                                        @if (isset($item) && isset($item->sale))
                                                            <a href="{{ route('sale.detail', $item->sale->id) }}"
                                                                class="dropdown-item" target="_blank"><i
                                                                    class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                        @else
                                                            <a href="#" class="dropdown-item" disable><i
                                                                    class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                        @endif
                                                        {{-- <button type="button" class="btn btn-primary" id="confirm-color">Alert</button> --}}
                                                        <a type="button" data-id="{{ $item->id }}" data-route="lead"
                                                            data-bs-toggle="modal" data-bs-target="#basicModal"
                                                            class="dropdown-item delete-record"><i
                                                                class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @elseif ($user->role->name == 'Customer Support')
                                    @foreach ($leads as $item)
                                        {{-- @if (isset($item->sale) && $item->sale->status == 1) --}}
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $item->business_name_adv }}</td>
                                                <td>{{ $item->business_number_adv }}</td>
                                                @if (isset($item->off_email))
                                                    <td>{{ $item->off_email }}</td>
                                                @else
                                                    <td>N/A</td>
                                                @endif
                                                <td><span
                                                        class="badge rounded-pill bg-label-primary me-1">{{ $item->category->name }}</span>
                                                </td>
                                                <td>{{ explode(' -',   $item->saler->name )[0] }}</td>
                                                <td>{{ $item->call_status }}</td>
                                                <td>
                                                    {{-- <p>{{ $item->closers }}</p> --}}
                                                    @if (isset($item->closers) && count($item->closers) > 0)
                                                        <div class="d-flex" style="gap: 10px; flex-direction: column;">
                                                            @foreach ($item->closers as $list)
                                                            <span
                                                                class="badge rounded-pill bg-label-primary me-1">{{ explode(' -',  $list->user->name)[0] }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    {{-- <p>{{ $item->sale }}</p> --}}
                                                    @if (isset($item->sale) && $item->sale->status == 1)
                                                        <span class="badge rounded-pill bg-success">Active</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-inline-block text-nowrap">
                                                        <a href="{{ route('sale.create', $item->id) }}"
                                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                                                            data-bs-toggle="tooltip" title="Active"><i
                                                                class="ri-send-plane-2-line ri-20px"></i></a>
                                                        <button
                                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                                        <div class="dropdown-menu dropdown-menu-end m-0" style=""><a
                                                                href="{{ route('lead.edit', $item->id) }}"
                                                                class="dropdown-item"><i
                                                                    class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                                            @if (isset($item) && isset($item->sale))
                                                                <a href="{{ route('sale.detail', $item->sale->id) }}"
                                                                    class="dropdown-item" target="_blank"><i
                                                                        class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                            @else
                                                                <a href="#" class="dropdown-item" disable><i
                                                                        class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                            @endif
                                                            {{-- <button type="button" class="btn btn-primary" id="confirm-color">Alert</button> --}}
                                                            <a type="button" data-id="{{ $item->id }}" data-route="lead"
                                                                data-bs-toggle="modal" data-bs-target="#basicModal"
                                                                class="dropdown-item delete-record"><i
                                                                    class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        {{-- @endif --}}
                                    @endforeach
                                @elseif($user->role->name == 'TSR')
                                    @foreach ($leads as $item)
                                        @if ($user->id == $item->saler_id && $item->sale == null)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $item->business_name_adv }}</td>
                                                <td>{{ $item->business_number_adv }}</td>
                                                @if (isset($item->off_email))
                                                    <td>{{ $item->off_email }}</td>
                                                @else
                                                    <td>N/A</td>
                                                @endif
                                                <td><span
                                                        class="badge rounded-pill bg-label-primary me-1">{{ $item->category->name }}</span>
                                                </td>
                                                <td>{{ explode(' -',   $item->saler->name )[0] }}</td>
                                                <td>{{ $item->call_status }}</td>
                                                <td>
                                                    {{-- <p>{{ $item->closers }}</p> --}}
                                                    @if (isset($item->closers) && count($item->closers) > 0)
                                                        <div class="d-flex" style="gap: 10px; flex-direction: column;">
                                                            @foreach ($item->closers as $list)
                                                            <span
                                                                class="badge rounded-pill bg-label-primary me-1">{{ explode(' -',  $list->user->name)[0] }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($item->sale) && $item->sale->status == 1)
                                                        <span class="badge rounded-pill bg-success">Active</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-inline-block text-nowrap">
                                                        <a href="{{ route('sale.create', $item->id) }}"
                                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                                                            data-bs-toggle="tooltip" title="Active"><i
                                                                class="ri-send-plane-2-line ri-20px"></i></a>
                                                        <button
                                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                                        <div class="dropdown-menu dropdown-menu-end m-0" style="">
                                                            <a href="{{ route('lead.edit', $item->id) }}"
                                                                class="dropdown-item"><i
                                                                    class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                                            @if (isset($item) && isset($item->sale))
                                                                <a href="{{ route('sale.detail', $item->sale->id) }}"
                                                                    class="dropdown-item"><i
                                                                        class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                            @else
                                                                <a href="#" class="dropdown-item"><i
                                                                        class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                            @endif
                                                            {{-- <button type="button" class="btn btn-primary" id="confirm-color">Alert</button> --}}
                                                            <a type="button" data-id="{{ $item->id }}"
                                                                data-route="lead" data-bs-toggle="modal"
                                                                data-bs-target="#basicModal"
                                                                class="dropdown-item delete-record"><i
                                                                    class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach ($leads as $item)

                                        {{-- @if ($item->closers->contains('closer_id', $user->id) && $item->sale == null) --}}
                                        @if ($item->closers->contains('closer_id', $user->id) || $item->saler_id == $user->id)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $item->business_name_adv }}</td>
                                                <td>{{ $item->business_number_adv }}</td>
                                                @if (isset($item->off_email))
                                                    <td>{{ $item->off_email }}</td>
                                                @else
                                                    <td>N/A</td>
                                                @endif
                                                <td><span
                                                        class="badge rounded-pill bg-label-primary me-1">{{ $item->category->name }}</span>
                                                </td>
                                                <td>{{ explode(' -',   $item->saler->name )[0] }}</td>
                                                <td>{{ $item->call_status }}</td>
                                                <td>
                                                    {{-- <p>{{ $item->closers }}</p> --}}
                                                    @if (isset($item->closers) && count($item->closers) > 0)
                                                        <div class="d-flex" style="gap: 10px; flex-direction: column;">
                                                            @foreach ($item->closers as $list)
                                                            <span
                                                                class="badge rounded-pill bg-label-primary me-1">{{ explode(' -',  $list->user->name)[0] }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (isset($item->sale) && $item->sale->status == 1)
                                                        <span class="badge rounded-pill bg-success">Active</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-inline-block text-nowrap">
                                                        <a href="{{ route('sale.create', $item->id) }}"
                                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect"
                                                            data-bs-toggle="tooltip" title="Active"><i
                                                                class="ri-send-plane-2-line ri-20px"></i></a>
                                                        <button
                                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                                class="mdi mdi-dots-vertical mdi-20px"></i></button>
                                                        <div class="dropdown-menu dropdown-menu-end m-0" style="">
                                                            <a href="{{ route('lead.edit', $item->id) }}"
                                                                class="dropdown-item"><i
                                                                    class="mdi mdi-pencil-outline me-2"></i><span>Edit</span></a>
                                                            @if (isset($item) && isset($item->sale))
                                                                <a href="{{ route('sale.detail', $item->sale->id) }}"
                                                                    class="dropdown-item"><i
                                                                        class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                            @else
                                                                <a href="#" class="dropdown-item"><i
                                                                        class="mdi mdi-eye me-2"></i><span>Preview</span></a>
                                                            @endif
                                                            {{-- <button type="button" class="btn btn-primary" id="confirm-color">Alert</button> --}}
                                                            <a type="button" data-id="{{ $item->id }}"
                                                                data-route="lead" data-bs-toggle="modal"
                                                                data-bs-target="#basicModal"
                                                                class="dropdown-item delete-record"><i
                                                                    class="mdi mdi-delete-outline me-2"></i><span>Delete</span></a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endauth
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Number</th>
                                <th>Email</th>
                                <th>Saler</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!--/ Responsive Datatable -->
        </div>
        <!-- / Content -->

        <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->

@endsection
@section('js')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-advanced.js') }}"></script>
    <script>
        $('#recodetable').DataTable();
    </script>
@endsection
