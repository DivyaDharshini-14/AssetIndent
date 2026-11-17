@extends('layouts/contentNavbarLayout')

@section('title', 'Asset & Procurement Dashboard')

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
<style>
  /* Custom modern dashboard styles */
  .hero-gradient {
    background: linear-gradient(135deg, #696cff 0%, #3f51b5 50%, #03c3ec 100%);
    color: #fff;
    border-radius: 16px;
    padding: 2.25rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px 0 rgba(105, 108, 255, 0.25);
  }
  .hero-gradient::after {
    content: '';
    position: absolute;
    top: -20%;
    right: -5%;
    width: 250px;
    height: 250px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    filter: blur(40px);
    pointer-events: none;
  }
  .hero-gradient::before {
    content: '';
    position: absolute;
    bottom: -20%;
    left: 10%;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(3, 195, 236, 0.2);
    filter: blur(45px);
    pointer-events: none;
  }
  .hero-metric-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    color: #fff;
    transition: transform 0.2s;
  }
  .hero-metric-card:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.22);
  }
  .modern-card {
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05) !important;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #fff;
  }
  .modern-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px 0 rgba(0, 0, 0, 0.08) !important;
  }
  .accent-card-red {
    background: linear-gradient(135deg, #ff3e1d 0%, #ffab00 100%);
    color: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 10px 25px 0 rgba(255, 62, 29, 0.2);
    position: relative;
    overflow: hidden;
  }
  .accent-card-red::before {
    content: '';
    position: absolute;
    top: -20px;
    left: -20px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    pointer-events: none;
  }
  .kpi-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 1rem;
  }
  .kpi-card {
    padding: 1.5rem;
  }
  .table-responsive::-webkit-scrollbar {
    height: 6px;
  }
  .table-responsive::-webkit-scrollbar-thumb {
    background: #e1e1e6;
    border-radius: 4px;
  }
</style>
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // 6-Month Spend Trend ApexChart
    var options = {
      series: [{
        name: 'Spend (INR)',
        data: [{{ $spendTrend->map(fn($item) => $item['total'])->implode(',') }}]
      }],
      chart: {
        type: 'area',
        height: 330,
        parentHeightOffset: 0,
        toolbar: {
          show: false
        },
        dropShadow: {
          enabled: true,
          top: 10,
          left: 0,
          blur: 8,
          color: '#696cff',
          opacity: 0.15
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 3.5,
        colors: ['#696cff']
      },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.5,
          opacityTo: 0.05,
          colorStops: [
            {
              offset: 0,
              color: '#696cff',
              opacity: 0.5
            },
            {
              offset: 100,
              color: '#ffffff',
              opacity: 0.01
            }
          ]
        }
      },
      xaxis: {
        categories: [{!! $spendTrend->map(fn($item) => "'".$item['label']."'")->implode(',') !!}],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: '#a1acb8',
            fontSize: '13px'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: '#a1acb8',
            fontSize: '13px'
          },
          formatter: function (value) {
            return "₹" + Math.round(value).toLocaleString('en-IN');
          }
        }
      },
      grid: {
        borderColor: '#eceef1',
        strokeDashArray: 5,
        xaxis: {
          lines: {
            show: false
          }
        },
        padding: {
          top: 10,
          right: 20,
          bottom: 0,
          left: 10
        }
      },
      tooltip: {
        theme: 'light',
        x: {
          show: true
        },
        y: {
          formatter: function (val) {
            return "₹" + val.toLocaleString('en-IN');
          }
        }
      },
      markers: {
        size: 0,
        hover: {
          size: 6
        },
        discrete: [{
          seriesIndex: 0,
          dataPointIndex: {{ $spendTrend->search(fn($item) => $item['total'] == $spendTrend->max('total')) ?? 5 }},
          fillColor: '#696cff',
          strokeColor: '#ffffff',
          size: 8,
          shape: "circle"
        }]
      }
    };

    var chart = new ApexCharts(document.querySelector("#spendTrendChart"), options);
    chart.render();
  });
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y dashboard-container">
    
    <!-- 1. Top Hero Section with dynamic data -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="hero-gradient d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                <div>
                    <h3 class="text-white mb-2 fw-semibold">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-white opacity-75 mb-0 fs-5">Here is your corporate Asset & Procurement Overview. System status is active.</p>
                </div>
                <div class="d-flex gap-3 align-self-start align-self-md-center">
                    <div class="hero-metric-card d-flex align-items-center gap-3">
                        <div class="avatar rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(255, 255, 255, 0.2);">
                            <i class="bx bx-check-shield text-white fs-4"></i>
                        </div>
                        <div>
                            <small class="d-block opacity-75">Pending Approvals</small>
                            <h4 class="mb-0 text-white fw-bold">{{ $pendingApprovals }}</h4>
                        </div>
                    </div>
                    
                    <div class="hero-metric-card d-flex align-items-center gap-3">
                        <div class="avatar rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(255, 255, 255, 0.2);">
                            <i class="bx bx-file text-white fs-4"></i>
                        </div>
                        <div>
                            <small class="d-block opacity-75">Open RFQs</small>
                            <h4 class="mb-0 text-white fw-bold">{{ $openRfqs }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Primary Spend Trend Chart & Side Info Cards -->
    <div class="row mb-5">
        <!-- Procurement Spend Trend Card (Area Chart) -->
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card modern-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center pb-2">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark">Procurement Spend Trend</h5>
                        <small class="text-muted">Monthly spend values with highlighted current peak</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="spendTrendOpt" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded text-muted fs-4"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="spendTrendOpt">
                            <a class="dropdown-item" href="{{ route('purchase-orders.index') }}">All Purchase Orders</a>
                            <a class="dropdown-item" href="{{ route('dashboard.asset') }}">Asset Analytics</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="spendTrendChart"></div>
                </div>
            </div>
        </div>

        <!-- Right Side Task & Accent Cards -->
        <div class="col-lg-4 d-flex flex-column justify-content-between gap-4">
            <!-- PO Approval Progress Card -->
            <div class="card modern-card p-2 flex-grow-1">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-label-primary px-3 py-2 rounded">Active Procurement</span>
                            <small class="text-muted fw-semibold">PO Progress</small>
                        </div>
                        <h5 class="fw-bold mb-1 text-dark">{{ $poApprovalProgress['po_no'] }}</h5>
                        <p class="text-muted mb-4 small">{{ $poApprovalProgress['vendor_name'] }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold text-dark small">Approval Phase</span>
                            <span class="text-primary fw-bold small">{{ $poApprovalProgress['progress'] }}%</span>
                        </div>
                        <div class="progress mb-3" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $poApprovalProgress['progress'] }}%" aria-valuenow="{{ $poApprovalProgress['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div>
                            <small class="d-block text-muted">Estimated Value</small>
                            <span class="fw-bold text-dark">₹{{ number_format($poApprovalProgress['amount'], 2) }}</span>
                        </div>
                        <span class="badge bg-light text-secondary">{{ $poApprovalProgress['status'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Bold Red/Orange Accent Card -->
            <div class="accent-card-red d-flex flex-column justify-content-between flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-white opacity-75 small fw-semibold d-block mb-1">Total Procurement Cost</span>
                        <h3 class="text-white fw-bold mb-0">₹{{ number_format($totalSpend, 2) }}</h3>
                    </div>
                    <div class="avatar rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.25);">
                        <i class="bx bx-bar-chart-alt-2 text-white fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-white opacity-75 mb-3 small">Total accumulated PO procurement cost currently processed in system.</p>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-white w-100 fw-bold text-danger border-0 bg-white shadow-sm">
                        <i class="bx bx-file-find me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. row of 4 KPI Cards -->
    <div class="row mb-5">
        <!-- Card 1: Procurement Spend -->
        <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
            <div class="card modern-card kpi-card h-100">
                <div class="kpi-icon-wrapper bg-label-primary">
                    <i class="bx bx-wallet text-primary"></i>
                </div>
                <small class="text-muted d-block mb-1 fw-semibold">Procurement Spend</small>
                <h3 class="card-title mb-2 fw-bold text-dark">₹{{ number_format($totalSpend, 0) }}</h3>
                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Active System Total</small>
            </div>
        </div>

        <!-- Card 2: Asset Book Value -->
        <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
            <div class="card modern-card kpi-card h-100">
                <div class="kpi-icon-wrapper bg-label-success">
                    <i class="bx bx-cube text-success"></i>
                </div>
                <small class="text-muted d-block mb-1 fw-semibold">Asset Book Value</small>
                <h3 class="card-title mb-2 fw-bold text-dark">₹{{ number_format($assetBookValue, 0) }}</h3>
                <small class="text-success fw-semibold"><i class="bx bx-trending-up"></i> Net Current Valuation</small>
            </div>
        </div>

        <!-- Card 3: Depreciation % -->
        <div class="col-lg-3 col-md-6 col-12 mb-4 mb-lg-0">
            <div class="card modern-card kpi-card h-100">
                <div class="kpi-icon-wrapper bg-label-warning">
                    <i class="bx bx-shield-quarter text-warning"></i>
                </div>
                <small class="text-muted d-block mb-1 fw-semibold">Depreciation Rate</small>
                <h3 class="card-title mb-2 fw-bold text-dark">{{ number_format($depreciationPercentage, 1) }}%</h3>
                <small class="text-muted fw-semibold">Accumulated Rate</small>
            </div>
        </div>

        <!-- Card 4: Payment Requests -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="card modern-card kpi-card h-100">
                <div class="kpi-icon-wrapper bg-label-danger">
                    <i class="bx bx-credit-card text-danger"></i>
                </div>
                <small class="text-muted d-block mb-1 fw-semibold">Payment Requests</small>
                <h3 class="card-title mb-2 fw-bold text-dark">₹{{ number_format($totalPaymentRequests, 0) }}</h3>
                <small class="text-danger fw-semibold"><i class="bx bx-history"></i> Vendor Claims Pending</small>
            </div>
        </div>
    </div>

    <!-- 4. Recent Payment Requests Table -->
    <div class="row">
        <div class="col-12">
            <div class="card modern-card">
                <div class="card-header d-flex justify-content-between align-items-center pb-3">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark">Recent Payment Requests</h5>
                        <small class="text-muted">Vendor billing claims status tracker</small>
                    </div>
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All Claims</a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover border-top mb-0">
                        <thead>
                            <tr class="table-light">
                                <th class="fw-bold text-dark">Request No</th>
                                <th class="fw-bold text-dark">Purchase Order</th>
                                <th class="fw-bold text-dark">Vendor</th>
                                <th class="fw-bold text-dark">Date Requested</th>
                                <th class="fw-bold text-dark">Amount</th>
                                <th class="fw-bold text-dark">Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($latestPaymentRequests as $payment)
                            <tr>
                                <td><span class="fw-bold text-dark">{{ $payment->payment_request_no }}</span></td>
                                <td>{{ $payment->purchaseOrder?->po_no ?? 'N/A' }}</td>
                                <td>{{ $payment->purchaseOrder?->vendor?->name ?? 'N/A' }}</td>
                                <td>{{ $payment->request_date ? $payment->request_date->format('d M Y') : 'N/A' }}</td>
                                <td><span class="fw-semibold text-dark">₹{{ number_format($payment->amount, 2) }}</span></td>
                                <td><span class="badge {{ $payment->statusBadgeClass() }}">{{ $payment->status }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No recent payment requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
