@extends('adminlte::page')

@section('title', 'Record Payment')

@section('content_header')
    <h1>Record Payment</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment Information</h3>
        </div>
        <form action="{{ route('admin.payments.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="invoice_id">Invoice <span class="text-danger">*</span></label>
                            <select class="form-control @error('invoice_id') is-invalid @enderror" id="invoice_id" name="invoice_id" required>
                                <option value="">Select Invoice</option>
                                @foreach($invoices as $invoice)
                                    <option value="{{ $invoice->id }}" 
                                            data-total="{{ $invoice->total_amount }}" 
                                            data-paid="{{ $invoice->paid_amount }}" 
                                            data-remaining="{{ $invoice->remaining_balance }}"
                                            {{ old('invoice_id', request('invoice')) == $invoice->id ? 'selected' : '' }}>
                                        {{ $invoice->invoice_number }} - {{ $invoice->client->user->name }} 
                                        (Remaining: ${{ number_format($invoice->remaining_balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('invoice_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted" id="invoice-info"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount">Amount ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount') }}" required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                   id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Select Method</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="paypal" {{ old('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>Check</option>
                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('payment_method')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="transaction_id">Transaction ID / Reference</label>
                    <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" 
                           id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}">
                    @error('transaction_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Record Payment
                </button>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#invoice_id').on('change', function() {
            var selected = $(this).find('option:selected');
            var remaining = selected.data('remaining');
            
            if (remaining !== undefined) {
                $('#invoice-info').text('Remaining balance: $' + parseFloat(remaining).toFixed(2));
                $('#amount').attr('max', remaining);
                $('#amount').val(remaining);
            } else {
                $('#invoice-info').text('');
                $('#amount').removeAttr('max');
            }
        });
        
        // Trigger change on page load if invoice is pre-selected
        if ($('#invoice_id').val()) {
            $('#invoice_id').trigger('change');
        }
    });
</script>
@stop
