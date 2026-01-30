@extends('adminlte::page')

@section('title', 'Share Salary Slip')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Share Salary Slip</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('team-member.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('team-member.salaries.index') }}">Salaries</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('team-member.salaries.show', $salary) }}">Details</a></li>
                    <li class="breadcrumb-item active">Share</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">Share Salary Slip - {{ $salary->month->format('F Y') }}</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Share your salary slip for <strong>{{ $salary->month->format('F Y') }}</strong> 
                        via WhatsApp or Email
                    </div>

                    <!-- Salary Summary -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5>Salary Summary</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Month:</strong></td>
                                    <td>{{ $salary->month->format('F Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Base Amount:</strong></td>
                                    <td>${{ number_format($salary->base_amount, 2) }}</td>
                                </tr>
                                @if($salary->bonus > 0)
                                <tr>
                                    <td><strong>Bonus:</strong></td>
                                    <td class="text-success">+${{ number_format($salary->bonus, 2) }}</td>
                                </tr>
                                @endif
                                @if($salary->deduction > 0)
                                <tr>
                                    <td><strong>Deductions:</strong></td>
                                    <td class="text-danger">-${{ number_format($salary->deduction, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="bg-light">
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong>${{ number_format($salary->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge badge-success">Paid</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Share Options -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fab fa-whatsapp fa-4x text-success mb-3"></i>
                                    <h5>Share via WhatsApp</h5>
                                    <p class="text-muted">Send salary slip details to your WhatsApp</p>
                                    @php
                                        $whatsappText = "Salary Slip - " . $salary->month->format('F Y') . "\n\n";
                                        $whatsappText .= "Employee: " . $user->name . "\n";
                                        $whatsappText .= "Month: " . $salary->month->format('F Y') . "\n";
                                        $whatsappText .= "Base Amount: $" . number_format($salary->base_amount, 2) . "\n";
                                        if($salary->bonus > 0) {
                                            $whatsappText .= "Bonus: +$" . number_format($salary->bonus, 2) . "\n";
                                        }
                                        if($salary->deduction > 0) {
                                            $whatsappText .= "Deductions: -$" . number_format($salary->deduction, 2) . "\n";
                                        }
                                        $whatsappText .= "Total Amount: $" . number_format($salary->total_amount, 2) . "\n";
                                        $whatsappText .= "Status: Paid\n";
                                        if($salary->payment_date) {
                                            $whatsappText .= "Payment Date: " . $salary->payment_date->format('M d, Y') . "\n";
                                        }
                                        $whatsappText .= "\nView Details: " . $shareUrl;
                                        $whatsappUrl = "https://wa.me/?text=" . urlencode($whatsappText);
                                    @endphp
                                    <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-success btn-lg">
                                        <i class="fab fa-whatsapp"></i> Share on WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-envelope fa-4x text-primary mb-3"></i>
                                    <h5>Share via Email</h5>
                                    <p class="text-muted">Send salary slip details to your email</p>
                                    @php
                                        $emailSubject = "Salary Slip - " . $salary->month->format('F Y');
                                        $emailBody = "Dear " . $user->name . ",\n\n";
                                        $emailBody .= "Please find below your salary details for " . $salary->month->format('F Y') . ":\n\n";
                                        $emailBody .= "Base Amount: $" . number_format($salary->base_amount, 2) . "\n";
                                        if($salary->bonus > 0) {
                                            $emailBody .= "Bonus: +$" . number_format($salary->bonus, 2) . "\n";
                                        }
                                        if($salary->deduction > 0) {
                                            $emailBody .= "Deductions: -$" . number_format($salary->deduction, 2) . "\n";
                                        }
                                        $emailBody .= "Total Amount: $" . number_format($salary->total_amount, 2) . "\n";
                                        $emailBody .= "Status: Paid\n";
                                        if($salary->payment_date) {
                                            $emailBody .= "Payment Date: " . $salary->payment_date->format('M d, Y') . "\n";
                                        }
                                        $emailBody .= "\nView full details: " . $shareUrl;
                                        $emailBody .= "\n\nBest regards,\nAsynchronous Digital";
                                        $mailtoUrl = "mailto:?subject=" . urlencode($emailSubject) . "&body=" . urlencode($emailBody);
                                    @endphp
                                    <a href="{{ $mailtoUrl }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-envelope"></i> Share via Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Copy Link -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5>Or Copy Link</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" id="shareLink" value="{{ $shareUrl }}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyLink()">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Share this link with anyone</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('team-member.salaries.show', $salary) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Salary Details
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function copyLink() {
        var copyText = document.getElementById("shareLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand("copy");
        
        // Show feedback
        $(document).Toasts('create', {
            class: 'bg-success',
            title: 'Link Copied!',
            body: 'The link has been copied to clipboard',
            autohide: true,
            delay: 3000,
        });
    }
</script>
@stop
