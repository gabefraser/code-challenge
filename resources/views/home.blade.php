@extends('layout')

@section('main')
<section class="upload-csv">
    <h1 class="heading">Upload new CSV</h1>

    <form action="{{ route('upload') }}" class="form-upload-csv" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="csv-file">Select a CSV to be uploaded:</label>
            <input class="form-control-file" type="file" name="csv" id="csv">
        </div><!--/.form-group-->
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Upload CSV</button>
        </div><!--/.form-group-->
    </form><!--/.form-upload-csv-->
</section><!--/.upload-csv-->

@if (count($transactions) > 0)
<table class="table table-striped table-hover table-responsive">
    <thead>
        <tr>
            <th scope="col">Date</th>
            <th scope="col">Transaction Code</th>
            <th scope="col">Valid Transaction?</th>
            <th scope="col">Customer Number</th>
            <th scope="col">Reference</th>
            <th scope="col">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $transaction)
        <tr>
            <td>{{ $transaction['date'] }}</td>
            <td>{{ $transaction['transaction_code'] }}</td>
            <td>{{ $transaction['transaction_valid'] }}</td>
            <td>{{ $transaction['customer_number'] }}</td>
            <td>{{ $transaction['reference'] }}</td>
            @if ($transaction['amount_type'] === 'debit')
            <td><span class="text-success">{{ $transaction['amount'] }}</span></td>
            @elseif ($transaction['amount_type'] === 'credit')
            <td><span class="text-danger">{{ $transaction['amount'] }}</span></td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table><!--/.table table-stripped-->
@else
<h3>There are currently no transactions.</h3>
@endif
@endsection