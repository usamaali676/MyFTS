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
                  <svg width="268" height="150" viewBox="0 0 38 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M30.0944 2.22569C29.0511 0.444187 26.7508 -0.172113 24.9566 0.849138C23.1623 1.87039 22.5536 4.14247 23.5969 5.92397L30.5368 17.7743C31.5801 19.5558 33.8804 20.1721 35.6746 19.1509C37.4689 18.1296 38.0776 15.8575 37.0343 14.076L30.0944 2.22569Z"
                      fill="currentColor" />
                    <path
                      d="M30.171 2.22569C29.1277 0.444187 26.8274 -0.172113 25.0332 0.849138C23.2389 1.87039 22.6302 4.14247 23.6735 5.92397L30.6134 17.7743C31.6567 19.5558 33.957 20.1721 35.7512 19.1509C37.5455 18.1296 38.1542 15.8575 37.1109 14.076L30.171 2.22569Z"
                      fill="url(#paint0_linear_2989_100980)"
                      fill-opacity="0.4" />
                    <path
                      d="M22.9676 2.22569C24.0109 0.444187 26.3112 -0.172113 28.1054 0.849138C29.8996 1.87039 30.5084 4.14247 29.4651 5.92397L22.5251 17.7743C21.4818 19.5558 19.1816 20.1721 17.3873 19.1509C15.5931 18.1296 14.9843 15.8575 16.0276 14.076L22.9676 2.22569Z"
                      fill="currentColor" />
                    <path
                      d="M14.9558 2.22569C13.9125 0.444187 11.6122 -0.172113 9.818 0.849138C8.02377 1.87039 7.41502 4.14247 8.45833 5.92397L15.3983 17.7743C16.4416 19.5558 18.7418 20.1721 20.5361 19.1509C22.3303 18.1296 22.9391 15.8575 21.8958 14.076L14.9558 2.22569Z"
                      fill="currentColor" />
                    <path
                      d="M14.9558 2.22569C13.9125 0.444187 11.6122 -0.172113 9.818 0.849138C8.02377 1.87039 7.41502 4.14247 8.45833 5.92397L15.3983 17.7743C16.4416 19.5558 18.7418 20.1721 20.5361 19.1509C22.3303 18.1296 22.9391 15.8575 21.8958 14.076L14.9558 2.22569Z"
                      fill="url(#paint1_linear_2989_100980)"
                      fill-opacity="0.4" />
                    <path
                      d="M7.82901 2.22569C8.87231 0.444187 11.1726 -0.172113 12.9668 0.849138C14.7611 1.87039 15.3698 4.14247 14.3265 5.92397L7.38656 17.7743C6.34325 19.5558 4.04298 20.1721 2.24875 19.1509C0.454514 18.1296 -0.154233 15.8575 0.88907 14.076L7.82901 2.22569Z"
                      fill="currentColor" />
                    <defs>
                      <linearGradient
                        id="paint0_linear_2989_100980"
                        x1="5.36642"
                        y1="0.849138"
                        x2="10.532"
                        y2="24.104"
                        gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-opacity="1" />
                        <stop offset="1" stop-opacity="0" />
                      </linearGradient>
                      <linearGradient
                        id="paint1_linear_2989_100980"
                        x1="5.19475"
                        y1="0.849139"
                        x2="10.3357"
                        y2="24.1155"
                        gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-opacity="1" />
                        <stop offset="1" stop-opacity="0" />
                      </linearGradient>
                    </defs>
                  </svg>
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
