@extends('layouts.dashboard')
@section('content')
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="row invoice-preview">
        <!-- Invoice -->
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
          <div class="card invoice-preview-card">
            <div class="card-body">
              <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                <div class="mb-xl-0 pb-3">
                  <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                    <span class="app-brand-logo demo">
                      <span style="color: var(--bs-primary)">
                        <img style="height: 40px; width: 60px" src="{{ asset('assets/img/favicon/fts-logo.svg') }}" alt="">
                      </span>
                    </span>
                    <span class="h4 mb-0 app-brand-text fw-bold">MY FTS</span>
                  </div>
                  <p class="mb-1">{{ $invoice->sale->lead->client_address }}</p>
                  <p class="mb-0">{{ $invoice->sale->lead->business_number_adv }}</p>
                </div>
                <div>
                    <h4>{{ $invoice->invoice_number }}</h4>
                    <div class="mb-1">
                    <span>Date Issues:</span>
                    <span>{{ $invoice->activation_date }}</span>
                  </div>
                  <div>
                    <span>Date Due:</span>
                    <span>{{ $invoice->invoice_due_date }}</span>
                  </div>
                </div>
              </div>
            </div>
            <hr class="my-0" />
            <div class="card-body">
              <div class="d-flex justify-content-between flex-wrap">
                <div class="my-3">
                  <h6 class="pb-2">Invoice To:</h6>
                  <p class="mb-1">{{ $invoice->sale->lead->client_name }}</p>
                  <p class="mb-1">{{ $invoice->sale->lead->business_name_adv }}</p>
                  <p class="mb-1">{{ $invoice->sale->lead->client_address }}</p>

                </div>
                <div class="my-3">
                    <h6 class="pb-2">&nbsp;</h6>
                    <p class="mb-1">{{ $invoice->sale->lead->business_number_adv }}</p>
                    <p class="mb-0">{{ $invoice->sale->lead->off_email }}</p>
                </div>
              </div>
            </div>
            <div class="table-responsive">
                <table id="invoice_service_table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoiced Service</th>
                            <th>Is Complementary</th>
                            <th>Service Service ($)</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if(isset($invoice) && count($invoice->servicecharges) > 0)
                        @foreach ($invoice->servicecharges as $item)
                        <tr>
                            <td>{{ $item->service_name->name }}
                            </td>
                            <td><input class="form-check-input" name="is_complementary" type="checkbox" value="true"
                                    id="defaultCheck1" @if($item->is_complementary === 1) checked @endif readonly>
                            </td>
                            @if($item->is_complementary == 0)
                            <td>{{ $item->charged_price }}
                            </td>
                            @else
                            <td>0</td>
                            @endif

                        </tr>
                        @endforeach
                        @endif

                    </tbody>
                </table>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 mb-md-0 mb-3">
                  <div>
                    <p class="mb-2">
                      <span class="me-1 text-heading fw-medium">Salesperson:</span>
                      <span>{{ $invoice->sale->lead->saler->name }}</span>
                    </p>
                    <p class="mb-2">
                      <span class="me-1 text-heading fw-medium">Closers:</span>
                      <span>@foreach ($invoice->sale->lead->closers as $item)
                        {{ $item->user->name }},
                      @endforeach</span>
                    </p>
                    <span>Thanks for your business</span>
                  </div>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end mt-2">
                  <div class="invoice-calculations">
                    <div class="d-flex justify-content-between mb-2">
                        @php
                        $subtotal = $invoice->total_amount + $invoice->discount_amount ;
                        @endphp
                      <span class="w-px-150">Subtotal:</span>
                      <h6 class="mb-0 pt-1">${{ number_format($subtotal, 2) }}</h6>
                      <h6 class="mb-0 pt-1"></h6>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                      <span class="w-px-150">Discount:</span>
                      <h6 class="mb-0 pt-1">${{ number_format($invoice->discount_amount, 2) }}</h6>
                    </div>
                    <hr />
                    <div class="d-flex justify-content-between">
                      <span class="w-px-150">Total:</span>
                      <h6 class="mb-0 pt-1">${{ number_format($invoice->total_amount, 2) }}</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <hr class="my-0" />
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <span class="fw-medium text-heading">Note:</span>
                  <span
                    >It was a pleasure working with you and your team. We hope you will keep us in mind for
                    future  projects. Thank You!</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- /Invoice -->

        <!-- Invoice Actions -->
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
              <div class="card-body">
                <a
                  class="btn btn-outline-secondary d-grid w-100 mb-3"
                  target="_blank"
                  href="{{ route('front.invoicePrint', $invoice->invoice_number) }}">
                  Print
                </a>

              </div>
            </div>
          </div>
          <!-- /Invoice Actions -->

      </div>



      <!-- /Offcanvas -->
    </div>
    <!-- / Content -->



    <div class="content-backdrop fade"></div>
  </div>
@endsection
