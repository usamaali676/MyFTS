<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    @include('layouts.partials.head')
</head>
<body>
    <div class="invoice-print p-4">
        <div class="d-flex justify-content-between flex-row">
          <div class="mb-4">
            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
              <span class="app-brand-logo demo">
                <span style="color: var(--bs-primary)">
                    <img style="height: 40px; width: 60px" src="{{ asset('assets/img/favicon/fts-logo.svg') }}" alt="">
                </span>
              </span>
              <span class="h4 mb-0 app-brand-text fw-bold">FTS</span>
            </div>
            <p class="mb-1">{{ $invoice->sale->lead->client_address }}</p>
            <p class="mb-0">{{ $invoice->sale->lead->business_number_adv }}</p>
          </div>
          <div>
            <h4>{{ $invoice->invoice_number }}</h4>
            <div class="mb-2">
              <span>Date Issues:</span>
              <span>{{ $invoice->activation_date }}</span>
            </div>
            <div>
              <span>Date Due:</span>
              <span>{{ $invoice->invoice_due_date }}</span>
            </div>
          </div>
        </div>

        <hr />

        <div class="d-flex justify-content-between mb-4">
          <div class="my-2">
            <h6>Invoice To:</h6>
            <p class="mb-1">{{ $invoice->sale->lead->client_name }}</p>
                  <p class="mb-1">{{ $invoice->sale->lead->business_name_adv }}</p>
                  <p class="mb-1">{{ $invoice->sale->lead->client_address }}</p>
          </div>
          <div class="my-2">
            <h6 class="pb-2">&nbsp;</h6>
            <p class="mb-1">{{ $invoice->sale->lead->business_number_adv }}</p>
            <p class="mb-0">{{ $invoice->sale->lead->off_email }}</p>

          </div>
        </div>

        <div class="table-responsive">
          <table class="table m-0">
            <thead class="table-light border-top">
              <tr>
                <th>Item</th>
                <th>Complementary</th>
                <th>Price</th>
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

              <tr>
                <td colspan="3" class="align-top px-4 py-3">
                  <p class="mb-2">
                    <span class="me-1 fw-medium">Salesperson:</span>
                    <span>{{ $invoice->sale->lead->saler->name }}</span>
                </p>
                <p class="mb-2">
                    <span class="me-1 text-heading fw-medium">Closers:</span>
                    <span>@foreach ($invoice->sale->lead->closers as $item)
                      {{ $item->user->name }},
                    @endforeach</span>
                  </p>
                  <span>Thanks for your business</span>
                </td>
                @php
                        $subtotal = $invoice->total_amount + $invoice->discount_amount ;
                @endphp
                <td class="text-end px-4 py-3">
                  <p class="mb-2">Subtotal:</p>
                  <p class="mb-2">Discount:</p>
                  <p class="mb-0">Total:</p>
                </td>
                <td class="px-4 py-3">
                  <p class="fw-medium mb-2">${{ number_format($subtotal, 2) }}</p>
                  <p class="fw-medium mb-2">${{ number_format($invoice->discount_amount, 2) }}</p>
                  <p class="fw-medium mb-0">${{ number_format($invoice->total_amount, 2) }}</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="row">
          <div class="col-12">
            <span class="fw-medium">Note:</span>
            <span
              >It was a pleasure working with you and your team. We hope you will keep us in mind for future
              projects. Thank You!</span
            >
          </div>
        </div>
      </div>
@include('layouts.partials.scripts')
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('assets/js/app-invoice-print.js') }}"></script>
</body>
</html>
