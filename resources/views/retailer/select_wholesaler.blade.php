@extends('layouts.app')

@section('content')
<div class="container">
    <h2>All Wholesalers</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Telephone</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wholesalers as $wholesaler)
                <tr>
                    <td>{{ $wholesaler->name }}</td>
                    <td>{{ $wholesaler->email }}</td>
                    <td>{{ $wholesaler->contact_info }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 